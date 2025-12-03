<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // Tambahkan ini
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'daily'); // daily, monthly, yearly
        
        // 1. Tentukan Rentang Waktu
        $currentDate = now();
        
        // Variabel untuk chart
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        switch ($filter) {
            case 'monthly':
                $startCurrent = $currentDate->copy()->startOfMonth();
                $endCurrent   = $currentDate->copy()->endOfMonth();
                $startPrevious = $currentDate->copy()->subMonth()->startOfMonth();
                $endPrevious   = $currentDate->copy()->subMonth()->endOfMonth();
                $labelPeriod = 'bulan ini';

                // Setup Chart: Tanggal 1 s/d Akhir Bulan
                $period = CarbonPeriod::create($startCurrent, $endCurrent);
                foreach ($period as $date) {
                    $chartLabels[] = $date->format('d M'); // Format: 01 Jan
                    $dateKey = $date->format('Y-m-d');
                    
                    // Query per tanggal (bisa dioptimasi dengan groupBy di SQL, tapi ini cara paling aman logic-nya)
                    $data = Order::whereDate('created_at', $dateKey)
                        ->where('status', 'selesai')
                        ->selectRaw('SUM(total_uang_masuk) as revenue, COUNT(*) as orders')
                        ->first();
                        
                    $chartRevenue[] = $data->revenue ?? 0;
                    $chartOrders[] = $data->orders ?? 0;
                }
                break;

            case 'yearly':
                $startCurrent = $currentDate->copy()->startOfYear();
                $endCurrent   = $currentDate->copy()->endOfYear();
                $startPrevious = $currentDate->copy()->subYear()->startOfYear();
                $endPrevious   = $currentDate->copy()->subYear()->endOfYear();
                $labelPeriod = 'tahun ini';

                // Setup Chart: Jan s/d Des
                for ($i = 1; $i <= 12; $i++) {
                    $date = Carbon::createFromDate($currentDate->year, $i, 1);
                    $chartLabels[] = $date->format('F'); // Format: January
                    
                    $data = Order::whereYear('created_at', $currentDate->year)
                        ->whereMonth('created_at', $i)
                        ->where('status', 'selesai')
                        ->selectRaw('SUM(total_uang_masuk) as revenue, COUNT(*) as orders')
                        ->first();

                    $chartRevenue[] = $data->revenue ?? 0;
                    $chartOrders[] = $data->orders ?? 0;
                }
                break;

            case 'daily':
            default:
                $startCurrent = $currentDate->copy()->startOfDay();
                $endCurrent   = $currentDate->copy()->endOfDay();
                $startPrevious = $currentDate->copy()->subDay()->startOfDay();
                $endPrevious   = $currentDate->copy()->subDay()->endOfDay();
                $labelPeriod = 'hari ini';

                // Setup Chart: Jam 00:00 s/d 23:00
                for ($i = 0; $i <= 23; $i++) {
                    $chartLabels[] = sprintf('%02d:00', $i); // Format: 09:00
                    
                    $data = Order::whereDate('created_at', $startCurrent)
                        ->whereTime('created_at', '>=', sprintf('%02d:00:00', $i))
                        ->whereTime('created_at', '<=', sprintf('%02d:59:59', $i))
                        ->where('status', 'selesai')
                        ->selectRaw('SUM(total_uang_masuk) as revenue, COUNT(*) as orders')
                        ->first();

                    $chartRevenue[] = $data->revenue ?? 0;
                    $chartOrders[] = $data->orders ?? 0;
                }
                break;
        }

        // 2. Query Data Statistik Total (CODE LAMA KAMU TETAP SAMA DISINI)
        $currentStats = Order::whereBetween('created_at', [$startCurrent, $endCurrent])
            ->where('status', 'selesai')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_uang_masuk) as total_revenue'),
                DB::raw('SUM(total_modal) as total_capital'),
                DB::raw('SUM(total_profit) as net_profit')
            )->first();

        $prevStats = Order::whereBetween('created_at', [$startPrevious, $endPrevious])
            ->where('status', 'selesai')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_uang_masuk) as total_revenue'),
                DB::raw('SUM(total_modal) as total_capital')
            )->first();

        // Hitung Nilai Real
        $totalOrders = $currentStats->total_orders ?? 0;
        $totalRevenue = $currentStats->total_revenue ?? 0;
        $totalCapital = $currentStats->total_capital ?? 0;
        $netProfit = $currentStats->net_profit ?? 0;
        $avgRevenuePerOrder = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Helper function percentage
        $calculatePercentageChange = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return (($current - $previous) / $previous) * 100;
        };

        $percentages = [
            'orders' => $calculatePercentageChange($totalOrders, $prevStats->total_orders ?? 0),
            'revenue' => $calculatePercentageChange($totalRevenue, $prevStats->total_revenue ?? 0),
            'avg_revenue' => $calculatePercentageChange(
                $avgRevenuePerOrder, 
                ($prevStats->total_orders ?? 0) > 0 ? ($prevStats->total_revenue / $prevStats->total_orders) : 0
            ),
            'capital' => $calculatePercentageChange($totalCapital, $prevStats->total_capital ?? 0),
        ];

        return view('reports.index', compact(
            'filter', 'labelPeriod',
            'totalOrders', 'totalRevenue', 'totalCapital', 'netProfit', 
            'avgRevenuePerOrder', 'profitMargin', 'percentages',
            // Kirim variable chart ke view
            'chartLabels', 'chartRevenue', 'chartOrders'
        ));
    }

    public function export(Request $request)
    {
        // ... (Kode export kamu biarkan tetap sama)
        // copy paste saja kode export yang lama
        $fileName = 'laporan-penjualan-' . date('Y-m-d') . '.csv';
        $orders = Order::where('status', 'selesai')->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Order', 'Tanggal', 'Pemesan', 'Metode', 'Total Bayar', 'Modal', 'Profit']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->nama_pemesan,
                    $order->payment_method,
                    $order->total_uang_masuk,
                    $order->total_modal,
                    $order->total_profit
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}