@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
<div class="bg-white min-h-screen pb-28 font-sans">

    {{-- Header --}}
    <header class="sticky top-0 z-10 bg-white px-6 pt-8 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Penjualan</h1>

        {{-- Filter Pills --}}
        <div class="flex gap-2 mb-4 overflow-x-auto no-scrollbar">
            @foreach(['daily' => 'Harian', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $key => $label)
            <a href="{{ route('reports.index', ['filter' => $key]) }}"
                class="px-5 py-2 rounded-full text-sm font-medium transition-all whitespace-nowrap
                {{ $filter === $key ? 'bg-[#37967D] text-white shadow-lg shadow-[#37967D]/20' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Download Button --}}
        <a href="{{ route('reports.export') }}" class="inline-flex items-center gap-2 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-download-simple text-lg text-[#37967D]"></i>
            Unduh Laporan
        </a>
    </header>

    <div class="px-6 space-y-6">

        {{-- Grid Cards Summary (TIDAK BERUBAH DARI CODE AWAL KAMU) --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Card 1: Total Pesanan --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-[#EBF8F5] flex items-center justify-center text-[#37967D]">
                        <i class="ph-fill ph-book-open text-lg"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700 leading-tight">Total Pesanan</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($totalOrders) }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-500">Dari {{ $labelPeriod }}</span>
                    @include('reports.partials.percentage', ['value' => $percentages['orders']])
                </div>
            </div>

            {{-- Card 2: Laba Kotor Per Pesanan --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-[#EBF8F5] flex items-center justify-center text-[#37967D]">
                        <i class="ph-fill ph-receipt text-lg"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700 leading-tight">Laba Kotor<br>Per Pesanan</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Rp{{ number_format($avgRevenuePerOrder, 0, ',', '.') }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-500">Dari {{ $labelPeriod }}</span>
                    @include('reports.partials.percentage', ['value' => $percentages['avg_revenue']])
                </div>
            </div>

            {{-- Card 3: Total Laba Kotor --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-[#EBF8F5] flex items-center justify-center text-[#37967D]">
                        <i class="ph-fill ph-book-bookmark text-lg"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700 leading-tight">Total Laba Kotor</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-500">Dari {{ $labelPeriod }}</span>
                    @include('reports.partials.percentage', ['value' => $percentages['revenue']])
                </div>
            </div>

            {{-- Card 4: Estimasi Modal --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-[#EBF8F5] flex items-center justify-center text-[#37967D]">
                        <i class="ph-fill ph-book-open-text text-lg"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700 leading-tight">Estimasi Modal</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Rp{{ number_format($totalCapital, 0, ',', '.') }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-500">Dari {{ $labelPeriod }}</span>
                    @include('reports.partials.percentage', ['value' => $percentages['capital']])
                </div>
            </div>
        </div>

        {{-- SECTION GRAFIK --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6">
            <h3 class="font-bold text-gray-900 mb-4">Grafik Penjualan</h3>
            <div class="relative h-64 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Hasil Akhir Summary --}}
        <div class="mt-6 mb-6">
            <h4 class="text-sm font-bold text-gray-800 mb-4">Hasil Akhir</h4>
            
            <div class="bg-white p-0 space-y-4">
                {{-- Row 1 --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">Total Laba Kotor</span>
                    <span class="text-sm font-bold text-gray-900">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                
                {{-- Row 2 --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">Estimasi Modal</span>
                    <span class="text-sm font-bold text-gray-900">Rp{{ number_format($totalCapital, 0, ',', '.') }}</span>
                </div>

                <hr class="border-gray-100">

                {{-- Row 3 --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">Laba Bersih</span>
                    <span class="text-sm font-bold text-gray-900">Rp{{ number_format($netProfit, 0, ',', '.') }}</span>
                </div>

                {{-- Row 4 --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">Persentase Keuntungan</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($profitMargin, 1) }}%</span>
                </div>
            </div>
        </div>

    </div> {{-- End of px-6 space-y-6 --}}
</div> {{-- End of bg-white --}}

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="application/json" id="chart-data">
    {
        "labels": {!! json_encode($chartLabels) !!},
        "revenue": {!! json_encode($chartRevenue) !!},
        "orders": {!! json_encode($chartOrders) !!}
    }
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const chartData = JSON.parse(
        document.getElementById("chart-data").textContent
    );

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: "Pendapatan (Rp)",
                    data: chartData.revenue,
                    borderColor: "#4CAF50",
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: "Jumlah Pesanan",
                    data: chartData.orders,
                    borderColor: "#2196F3",
                    borderWidth: 2,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
</script>


@endsection