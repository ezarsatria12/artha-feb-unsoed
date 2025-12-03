<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tentukan Rentang Waktu (Hari Ini vs Kemarin)
        $todayStart = Carbon::today();
        $todayEnd = Carbon::now();
        
        $yesterdayStart = Carbon::yesterday();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        // 2. Query Helper untuk Meringkas Kodingan
        $getStats = function ($start, $end) {
            return Order::whereBetween('created_at', [$start, $end])
                ->where('status', 'selesai')
                ->select(
                    DB::raw('SUM(CASE WHEN payment_method = "tunai" THEN total_uang_masuk ELSE 0 END) as tunai'),
                    DB::raw('SUM(CASE WHEN payment_method = "qris" THEN total_uang_masuk ELSE 0 END) as qris'),
                    DB::raw('SUM(total_uang_masuk) as revenue'),
                    DB::raw('SUM(total_profit) as profit')
                )->first();
        };

        $todayStats = $getStats($todayStart, $todayEnd);
        $yesterdayStats = $getStats($yesterdayStart, $yesterdayEnd);

        // 3. Siapkan Data untuk View (Handle null dengan 0)
        $data = [
            'tunai' => $todayStats->tunai ?? 0,
            'qris' => $todayStats->qris ?? 0,
            'revenue' => $todayStats->revenue ?? 0,
            'profit' => $todayStats->profit ?? 0,
        ];

        // 4. Hitung Persentase Perubahan (vs Kemarin)
        $calcPct = function ($current, $past) {
            if ($past == 0) return $current > 0 ? 100 : 0;
            return (($current - $past) / $past) * 100;
        };

        $percentages = [
            'tunai' => $calcPct($data['tunai'], $yesterdayStats->tunai ?? 0),
            'qris' => $calcPct($data['qris'], $yesterdayStats->qris ?? 0),
            'revenue' => $calcPct($data['revenue'], $yesterdayStats->revenue ?? 0),
            'profit' => $calcPct($data['profit'], $yesterdayStats->profit ?? 0),
        ];

        // 5. Ambil Menu Paling Laris Hari Ini
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(jumlah) as total_sold'))
            ->whereHas('order', function($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('created_at', [$todayStart, $todayEnd])
                  ->where('status', 'selesai');
            })
            ->with('product') // Eager load relasi produk
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact('data', 'percentages', 'topProducts'));
    }
}