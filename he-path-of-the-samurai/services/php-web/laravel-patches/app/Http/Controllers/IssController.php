<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IssController extends Controller
{
    public function index(Request $request)
    {
        $base = getenv('RUST_BASE') ?: 'http://rust_iss:3000';
        
        // Получаем последние данные ISS с кэшированием на 10 секунд
        $cacheKey = 'iss_data_' . md5($base);
        $last = Cache::remember($cacheKey, 10, function () use ($base) {
            $content = @file_get_contents($base.'/last');
            return $content ? json_decode($content, true) : [];
        });
        
        // Получаем тренд движения с кэшированием
        $trendCacheKey = 'iss_trend_' . md5($base);
        $trend = Cache::remember($trendCacheKey, 30, function () use ($base) {
            $content = @file_get_contents($base.'/iss/trend');
            return $content ? json_decode($content, true) : [];
        });
        
        // Получаем историю для графиков (последние 50 точек)
        $historyCacheKey = 'iss_history_' . md5($base);
        $history = Cache::remember($historyCacheKey, 60, function () use ($base) {
            // В реальном проекте нужно добавить эндпоинт /iss/history в Rust
            // Пока используем тренд или симулируем данные
            return $this->generateMockHistory($base);
        });
        
        // Статистика для карточек
        $stats = [
            'total_points' => count($history['points'] ?? []),
            'avg_speed' => $this->calculateAverageSpeed($history),
            'max_altitude' => $this->findMaxAltitude($history),
            'distance_traveled' => $trend['delta_km'] ?? 0,
        ];
        
        return view('iss', [
            'last' => $last,
            'trend' => $trend,
            'history' => $history,
            'stats' => $stats,
            'base' => $base,
        ]);
    }
    
    private function generateMockHistory($base)
    {
        // Заглушка для истории - в реальности нужно добавить эндпоинт в Rust
        $points = [];
        $now = time();
        
        for ($i = 0; $i < 50; $i++) {
            $timestamp = $now - ($i * 60); // Каждую минуту
            $points[] = [
                'at' => date('Y-m-d H:i:s', $timestamp),
                'lat' => 50 + (sin($i * 0.1) * 10),
                'lon' => 30 + (cos($i * 0.1) * 20),
                'altitude' => 400 + rand(-5, 5),
                'velocity' => 27600 + rand(-100, 100),
            ];
        }
        
        return ['points' => $points];
    }
    
    private function calculateAverageSpeed($history)
    {
        if (empty($history['points'])) return 0;
        
        $sum = 0;
        foreach ($history['points'] as $point) {
            $sum += $point['velocity'] ?? 0;
        }
        
        return round($sum / count($history['points']));
    }
    
    private function findMaxAltitude($history)
    {
        if (empty($history['points'])) return 0;
        
        $max = 0;
        foreach ($history['points'] as $point) {
            $alt = $point['altitude'] ?? 0;
            if ($alt > $max) $max = $alt;
        }
        
        return $max;
    }
}