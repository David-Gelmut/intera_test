<?php

namespace App\Http\Controllers;

use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        $masterId = auth()->id();

        // 1. Собираем просмотры за последние 7 дней с группировкой по дням
        $viewsData = ProfileView::where('master_id', $masterId)
            ->where('viewed_at', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date'); // превращаем в массив [дата => количество]

        // 2. Заполняем пустые дни нулями, чтобы график не ломался, если в какой-то день не было визитов
        $chartLabels = [];
        $chartValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $humanLabel = now()->subDays($i)->translatedFormat('D, d M'); // Напр: "Пн, 13 июл"

            $chartLabels[] = $humanLabel;
            $chartValues[] = $viewsData[$date] ?? 0;
        }

        // 3. Дополнительно собираем ТОП-городов, откуда смотрят мастера
        $topCities = ProfileView::where('master_id', $masterId)
            ->select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'labels' => $chartLabels,
            'values' => $chartValues,
            'top_cities' => $topCities,
            'total_views' => array_sum($chartValues)
        ]);
    }
}
