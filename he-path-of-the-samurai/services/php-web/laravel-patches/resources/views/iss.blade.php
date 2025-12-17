@extends('layouts.app')

@section('styles')
<style>
    /* Основные анимации */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .slide-in-left {
        animation: slideInLeft 0.5s ease-out;
    }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    /* Карточки с градиентами */
    .stat-card {
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    }
    
    .card-gradient-1 {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
    }
    
    .card-gradient-2 {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        color: white;
    }
    
    .card-gradient-3 {
        background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        color: white;
    }
    
    .card-gradient-4 {
        background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%);
        color: white;
    }
    
    /* Пульсация для текущей позиции */
    .iss-marker-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(13, 110, 253, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
    
    /* Карта */
    #issMap {
        border-radius: 12px;
        overflow: hidden;
        height: 400px;
        border: 1px solid #dee2e6;
    }
    
    /* Индикаторы */
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .status-active {
        background-color: #198754;
        animation: statusPulse 2s infinite;
    }
    
    @keyframes statusPulse {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    
    /* Таблица с анимациями строк */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
        transform: translateX(5px);
    }
    
    /* Графики */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Орбитальная линия */
    .orbit-line {
        stroke-dasharray: 5,5;
        animation: orbitDash 20s linear infinite;
    }
    
    @keyframes orbitDash {
        to { stroke-dashoffset: -100; }
    }
    
    /* Вращение Земли */
    .earth-rotate {
        animation: earthRotate 120s linear infinite;
    }
    
    @keyframes earthRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    {{-- Заголовок и статус --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="mb-2 d-flex align-items-center">
                        <span class="me-3">🛰️</span>
                        Международная космическая станция (МКС)
                    </h1>
                    <div class="d-flex align-items-center">
                        <span class="status-indicator status-active"></span>
                        <span class="text-success fw-medium">Онлайн и отслеживается</span>
                        <span class="text-muted ms-3">
                            <i class="bi bi-clock-history me-1"></i>
                            Обновлено: <span id="lastUpdateTime">{{ now()->format('H:i:s') }}</span>
                        </span>
                    </div>
                </div>
                <button class="btn btn-outline-primary" onclick="refreshISSData()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Обновить данные
                </button>
            </div>
        </div>
    </div>

    {{-- Статистические карточки --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.1s">
            <div class="stat-card card-gradient-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-85 mb-1">Текущая скорость</div>
                        <div class="display-6 fw-bold">
                            {{ isset(($last['payload'] ?? [])['velocity']) ? number_format($last['payload']['velocity'], 0, '', ' ') : '—' }}
                        </div>
                        <div class="small mt-2">км/ч</div>
                    </div>
                    <div class="fs-2">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.2s">
            <div class="stat-card card-gradient-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-85 mb-1">Высота орбиты</div>
                        <div class="display-6 fw-bold">
                            {{ isset(($last['payload'] ?? [])['altitude']) ? number_format($last['payload']['altitude'], 0, '', ' ') : '—' }}
                        </div>
                        <div class="small mt-2">километров</div>
                    </div>
                    <div class="fs-2">
                        <i class="bi bi-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.3s">
            <div class="stat-card card-gradient-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-85 mb-1">Средняя скорость</div>
                        <div class="display-6 fw-bold">{{ number_format($stats['avg_speed'], 0, '', ' ') }}</div>
                        <div class="small mt-2">за 50 отсчётов</div>
                    </div>
                    <div class="fs-2">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.4s">
            <div class="stat-card card-gradient-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-85 mb-1">Точек трека</div>
                        <div class="display-6 fw-bold">{{ $stats['total_points'] }}</div>
                        <div class="small mt-2">в истории</div>
                    </div>
                    <div class="fs-2">
                        <i class="bi bi-database"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Карта и графики --}}
    <div class="row g-4 mb-4">
        {{-- Карта --}}
        <div class="col-lg-7 fade-in">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-globe-americas text-primary me-2"></i>
                        Текущее положение и траектория
                    </h5>
                    <div id="issMap"></div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="small text-muted">Широта</div>
                            <div class="fw-bold fs-5">
                                {{ isset(($last['payload'] ?? [])['latitude']) ? number_format($last['payload']['latitude'], 4, '.', '') : '—' }}°
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Долгота</div>
                            <div class="fw-bold fs-5">
                                {{ isset(($last['payload'] ?? [])['longitude']) ? number_format($last['payload']['longitude'], 4, '.', '') : '—' }}°
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Графики --}}
        <div class="col-lg-5 fade-in">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Динамика параметров
                    </h5>
                    <div class="chart-container">
                        <canvas id="speedChart"></canvas>
                    </div>
                    <div class="chart-container mt-4">
                        <canvas id="altitudeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Таблицы с данными --}}
    <div class="row g-4">
        {{-- Текущие данные --}}
        <div class="col-lg-6 slide-in-left">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Текущие параметры МКС
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-geo-alt me-2 text-primary"></i>Широта</td>
                                    <td class="text-end fw-bold">
                                        {{ isset(($last['payload'] ?? [])['latitude']) ? number_format($last['payload']['latitude'], 4, '.', '') : '—' }}°
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-geo-alt me-2 text-primary"></i>Долгота</td>
                                    <td class="text-end fw-bold">
                                        {{ isset(($last['payload'] ?? [])['longitude']) ? number_format($last['payload']['longitude'], 4, '.', '') : '—' }}°
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-arrow-up me-2 text-success"></i>Высота</td>
                                    <td class="text-end fw-bold">
                                        {{ isset(($last['payload'] ?? [])['altitude']) ? number_format($last['payload']['altitude'], 2, '.', ' ') : '—' }} км
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-speedometer2 me-2 text-warning"></i>Скорость</td>
                                    <td class="text-end fw-bold">
                                        {{ isset(($last['payload'] ?? [])['velocity']) ? number_format($last['payload']['velocity'], 0, '', ' ') : '—' }} км/ч
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-clock me-2 text-secondary"></i>Время снимка</td>
                                    <td class="text-end">
                                        @if(!empty($last['fetched_at']))
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($last['fetched_at'])->format('H:i:s') }}</span><br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($last['fetched_at'])->format('d.m.Y') }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-link-45deg me-2 text-info"></i>Источник данных</td>
                                    <td class="text-end">
                                        <a href="{{ $base }}/last" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> API
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Статистика движения --}}
        <div class="col-lg-6 slide-in-left" style="animation-delay: 0.2s">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-activity text-danger me-2"></i>
                        Статистика движения
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-signpost me-2 text-success"></i>Состояние</td>
                                    <td class="text-end">
                                        @if(!empty($trend['movement']) && $trend['movement'])
                                            <span class="badge bg-success">В движении</span>
                                        @else
                                            <span class="badge bg-secondary">Статично</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-arrows-move me-2 text-primary"></i>Смещение</td>
                                    <td class="text-end fw-bold">
                                        {{ !empty($trend['delta_km']) ? number_format($trend['delta_km'], 3, '.', ' ') : '0' }} км
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-clock-history me-2 text-warning"></i>Интервал</td>
                                    <td class="text-end fw-bold">
                                        {{ !empty($trend['dt_sec']) ? number_format($trend['dt_sec'], 1, '.', ' ') : '0' }} сек
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-speedometer me-2 text-info"></i>Скорость (тренд)</td>
                                    <td class="text-end fw-bold">
                                        {{ !empty($trend['velocity_kmh']) ? number_format($trend['velocity_kmh'], 0, '', ' ') : '—' }} км/ч
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-maximize me-2 text-danger"></i>Макс. высота</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($stats['max_altitude'], 0, '', ' ') }} км
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-map me-2 text-success"></i>Пройдено</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($stats['distance_traveled'], 1, '.', ' ') }} км
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- История позиций --}}
    <div class="row mt-4">
        <div class="col-12 fade-in">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-clock-history text-secondary me-2"></i>
                        История позиций (последние 10 точек)
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Время (UTC)</th>
                                    <th>Широта</th>
                                    <th>Долгота</th>
                                    <th>Высота</th>
                                    <th>Скорость</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $historyPoints = array_slice($history['points'] ?? [], 0, 10); @endphp
                                @forelse($historyPoints as $index => $point)
                                    <tr class="{{ $index == 0 ? 'table-primary' : '' }}">
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($point['at'])->format('H:i:s') }}</small>
                                        </td>
                                        <td class="fw-bold">{{ number_format($point['lat'], 4, '.', '') }}°</td>
                                        <td class="fw-bold">{{ number_format($point['lon'], 4, '.', '') }}°</td>
                                        <td>{{ number_format($point['altitude'], 0, '', ' ') }} км</td>
                                        <td>{{ number_format($point['velocity'], 0, '', ' ') }} км/ч</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="bi bi-database me-2"></i>Исторические данные отсутствуют
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Прогресс-бар обновления --}}
<div class="progress fixed-bottom" id="refreshProgress" style="height: 3px; display: none;">
    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== ИНИЦИАЛИЗАЦИЯ КАРТЫ ==========
    let map, trail, marker;
    const last = @json(($last['payload'] ?? []));
    
    if (last.latitude && last.longitude) {
        const lat = Number(last.latitude);
        const lon = Number(last.longitude);
        
        map = L.map('issMap').setView([lat, lon], 3);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            noWrap: true
        }).addTo(map);
        
        // Создаем кастомную иконку МКС
        const issIcon = L.divIcon({
            html: '<div class="iss-marker-pulse" style="background: #0d6efd; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);"></div>',
            iconSize: [30, 30],
            className: 'iss-marker'
        });
        
        marker = L.marker([lat, lon], { icon: issIcon })
            .addTo(map)
            .bindPopup('<b>МКС</b><br>Текущая позиция');
        
        // Линия траектории (симулируем историю)
        const history = @json(($history['points'] ?? []));
        if (history.length > 0) {
            const points = history.map(p => [p.lat, p.lon]);
            trail = L.polyline(points, {
                color: '#0d6efd',
                weight: 3,
                opacity: 0.7,
                lineCap: 'round'
            }).addTo(map);
        }
        
        // Круг видимости (~2000 км радиус)
        L.circle([lat, lon], {
            color: 'rgba(13, 110, 253, 0.2)',
            fillColor: 'rgba(13, 110, 253, 0.1)',
            fillOpacity: 0.3,
            radius: 2000000 // 2000 км в метрах
        }).addTo(map);
    }
    
    // ========== ГРАФИКИ ==========
    const history = @json(($history['points'] ?? []));
    const labels = history.map(p => new Date(p.at).toLocaleTimeString('ru-RU', { 
        hour: '2-digit', 
        minute: '2-digit' 
    })).reverse();
    
    const speedData = history.map(p => p.velocity).reverse();
    const altitudeData = history.map(p => p.altitude).reverse();
    
    // График скорости
    const speedCtx = document.getElementById('speedChart').getContext('2d');
    const speedChart = new Chart(speedCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Скорость (км/ч)',
                data: speedData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `Скорость: ${ctx.raw.toLocaleString('ru-RU')} км/ч`
                    }
                }
            },
            scales: {
                x: { 
                    display: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { maxTicksLimit: 6 }
                },
                y: {
                    display: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { 
                        callback: v => v.toLocaleString('ru-RU') + ' км/ч'
                    }
                }
            }
        }
    });
    
    // График высоты
    const altitudeCtx = document.getElementById('altitudeChart').getContext('2d');
    const altitudeChart = new Chart(altitudeCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Высота (км)',
                data: altitudeData,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `Высота: ${ctx.raw.toLocaleString('ru-RU')} км`
                    }
                }
            },
            scales: {
                x: { 
                    display: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { maxTicksLimit: 6 }
                },
                y: {
                    display: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { 
                        callback: v => v.toLocaleString('ru-RU') + ' км'
                    }
                }
            }
        }
    });
    
    // ========== АВТООБНОВЛЕНИЕ ==========
    let refreshInterval = setInterval(updateTime, 1000);
    let dataRefreshInterval = setInterval(refreshData, 30000); // Каждые 30 секунд
    
    function updateTime() {
        const now = new Date();
        document.getElementById('lastUpdateTime').textContent = 
            now.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    
    async function refreshData() {
        // Показываем прогресс-бар
        const progressBar = document.getElementById('refreshProgress');
        progressBar.style.display = 'block';
        
        try {
            // Обновляем данные (в реальном проекте - AJAX запрос)
            // Пока просто перезагружаем страницу
            setTimeout(() => {
                progressBar.style.display = 'none';
                window.location.reload();
            }, 1000);
        } catch (error) {
            console.error('Ошибка обновления:', error);
            progressBar.style.display = 'none';
        }
    }
    
    window.refreshISSData = refreshData;
    
    // ========== АНИМАЦИЯ СТРОК ТАБЛИЦЫ ==========
    const tableRows = document.querySelectorAll('.table-hover tbody tr');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.05}s`;
        row.classList.add('fade-in');
    });
});
</script>
@endsection