@extends('layouts.app')

@section('styles')
<style>
    /* Анимации */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .loading-pulse {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 0.6; transform: scale(0.98); }
        50% { opacity: 1; transform: scale(1.02); }
        100% { opacity: 0.6; transform: scale(0.98); }
    }
    
    .slide-in-left {
        animation: slideInLeft 0.5s ease-out;
    }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    /* JWST галерея */
    .jwst-slider {
        position: relative;
    }
    
    .jwst-track {
        display: flex; 
        gap: .75rem; 
        overflow: auto; 
        scroll-snap-type: x mandatory; 
        padding: .25rem;
        scrollbar-width: thin;
    }
    
    .jwst-item {
        flex: 0 0 180px; 
        scroll-snap-align: start;
        transition: transform 0.3s ease;
    }
    
    .jwst-item:hover {
        transform: scale(1.05);
    }
    
    .jwst-item img {
        width: 100%; 
        height: 180px; 
        object-fit: cover; 
        border-radius: .5rem;
        transition: transform 0.3s ease;
    }
    
    .jwst-cap {
        font-size: .85rem; 
        margin-top: .25rem;
        color: #666;
    }
    
    .jwst-nav {
        position: absolute; 
        top: 40%; 
        transform: translateY(-50%); 
        z-index: 2;
        background: rgba(255,255,255,0.9);
        border: 1px solid #dee2e6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .jwst-nav:hover {
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .jwst-prev {
        left: -20px;
    }
    
    .jwst-next {
        right: -20px;
    }
    
    /* ISS маркер на карте */
    .iss-marker-pulse {
        animation: markerPulse 1s ease-in-out;
    }
    
    @keyframes markerPulse {
        0% { transform: scale(1); filter: drop-shadow(0 0 5px rgba(13, 110, 253, 0.5)); }
        50% { transform: scale(1.2); filter: drop-shadow(0 0 15px rgba(13, 110, 253, 0.8)); }
        100% { transform: scale(1); filter: drop-shadow(0 0 5px rgba(13, 110, 253, 0.5)); }
    }
    
    /* Статусные индикаторы */
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
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
    
    /* Градиенты */
    .gradient-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
    }
    
    /* Улучшенные скроллбары */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection

@section('content')
<div class="container pb-5">
    {{-- Статусная строка --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center p-3 rounded gradient-card fade-in">
                <div>
                    <span class="status-indicator status-active"></span>
                    <span class="text-muted">Система активна</span>
                </div>
                <div class="text-muted small">
                    Последнее обновление: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Верхние карточки --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 slide-in-left" style="animation-delay: 0.1s">
            <div class="border rounded p-3 text-center card-hover gradient-card">
                <div class="small text-muted mb-1">Скорость МКС</div>
                <div class="fs-3 fw-bold text-primary">
                    {{ isset(($iss['payload'] ?? [])['velocity']) ? number_format($iss['payload']['velocity'],0,'',' ') : '—' }}
                </div>
                <div class="small text-muted mt-1">км/ч</div>
            </div>
        </div>
        <div class="col-6 col-md-3 slide-in-left" style="animation-delay: 0.2s">
            <div class="border rounded p-3 text-center card-hover gradient-card">
                <div class="small text-muted mb-1">Высота МКС</div>
                <div class="fs-3 fw-bold text-success">
                    {{ isset(($iss['payload'] ?? [])['altitude']) ? number_format($iss['payload']['altitude'],0,'',' ') : '—' }}
                </div>
                <div class="small text-muted mt-1">км</div>
            </div>
        </div>
        <div class="col-6 col-md-3 slide-in-left" style="animation-delay: 0.3s">
            <div class="border rounded p-3 text-center card-hover gradient-card">
                <div class="small text-muted mb-1">Широта</div>
                <div class="fs-3 fw-bold text-info">
                    {{ isset(($iss['payload'] ?? [])['latitude']) ? number_format($iss['payload']['latitude'],2,'.', '') : '—' }}
                </div>
                <div class="small text-muted mt-1">°</div>
            </div>
        </div>
        <div class="col-6 col-md-3 slide-in-left" style="animation-delay: 0.4s">
            <div class="border rounded p-3 text-center card-hover gradient-card">
                <div class="small text-muted mb-1">Долгота</div>
                <div class="fs-3 fw-bold text-warning">
                    {{ isset(($iss['payload'] ?? [])['longitude']) ? number_format($iss['payload']['longitude'],2,'.', '') : '—' }}
                </div>
                <div class="small text-muted mt-1">°</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Левая колонка: JWST наблюдение --}}
        <div class="col-lg-7 fade-in">
            <div class="card shadow-sm h-100 card-hover">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="bi bi-stars text-warning me-2"></i>
                        JWST — выбранное наблюдение
                    </h5>
                    <div class="text-muted">Этот блок остаётся как был (JSON/сводка). Основная галерея ниже.</div>
                </div>
            </div>
        </div>

        {{-- Правая колонка: карта МКС --}}
        <div class="col-lg-5 fade-in">
            <div class="card shadow-sm h-100 card-hover">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="bi bi-globe-americas text-primary me-2"></i>
                        МКС — положение и движение
                    </h5>
                    <div id="map" class="rounded mb-3 border" style="height:300px"></div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="small text-muted mb-1">Тренд скорости</div>
                            <canvas id="issSpeedChart" height="100"></canvas>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted mb-1">Тренд высоты</div>
                            <canvas id="issAltChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- НИЖНЯЯ ПОЛОСА: НОВАЯ ГАЛЕРЕЯ JWST --}}
        <div class="col-12 fade-in">
            <div class="card shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <i class="bi bi-images text-purple me-2"></i>
                            JWST — последние изображения
                        </h5>
                        <form id="jwstFilter" class="row g-2 align-items-center">
                            <div class="col-auto">
                                <select class="form-select form-select-sm" name="source" id="srcSel">
                                    <option value="jpg" selected>Все JPG</option>
                                    <option value="suffix">По суффиксу</option>
                                    <option value="program">По программе</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <input type="text" class="form-control form-control-sm" name="suffix" id="suffixInp" placeholder="_cal / _thumb" style="width:140px;display:none">
                                <input type="text" class="form-control form-control-sm" name="program" id="progInp" placeholder="2734" style="width:110px;display:none">
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" name="instrument" style="width:130px">
                                    <option value="">Любой инструмент</option>
                                    <option>NIRCam</option><option>MIRI</option><option>NIRISS</option><option>NIRSpec</option><option>FGS</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" name="perPage" style="width:90px">
                                    <option>12</option><option selected>24</option><option>36</option><option>48</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <i class="bi bi-search me-1"></i>Показать
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="jwst-slider">
                        <button class="btn btn-light border jwst-nav jwst-prev" type="button" aria-label="Prev">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div id="jwstTrack" class="jwst-track border rounded custom-scrollbar"></div>
                        <button class="btn btn-light border jwst-nav jwst-next" type="button" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div id="jwstInfo" class="small text-muted mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox для изображений --}}
<div id="lightbox" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightbox-title"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightbox-img" class="img-fluid" src="" alt="" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer border-0">
                <a id="lightbox-link" href="#" target="_blank" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-up-right me-2"></i>Открыть оригинал
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    // Обновление времени последнего обновления
    function updateLastUpdateTime() {
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('ru-RU');
    }
    setInterval(updateLastUpdateTime, 1000);

    // ====== карта и графики МКС ======
    if (typeof L !== 'undefined' && typeof Chart !== 'undefined') {
        const last = @json(($iss['payload'] ?? []));
        let lat0 = Number(last.latitude || 0), lon0 = Number(last.longitude || 0);
        const map = L.map('map', { 
            attributionControl: false,
            zoomControl: true 
        }).setView([lat0||0, lon0||0], lat0?3:2);
        
        L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', { 
            noWrap: true,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        const trail = L.polyline([], {
            weight: 4,
            color: '#0d6efd',
            opacity: 0.7,
            lineCap: 'round'
        }).addTo(map);
        
        // Создаем кастомную иконку для МКС
        const issIcon = L.divIcon({
            html: '<div class="iss-marker"><i class="bi bi-satellite text-primary" style="font-size: 24px;"></i></div>',
            iconSize: [30, 30],
            className: 'iss-marker-pulse'
        });
        
        const marker = L.marker([lat0||0, lon0||0], { icon: issIcon })
            .addTo(map)
            .bindPopup('<b>Международная космическая станция</b><br>Текущее положение');

        // Графики с улучшенным дизайном
        const speedChart = new Chart(document.getElementById('issSpeedChart'), {
            type: 'line', 
            data: { 
                labels: [], 
                datasets: [{ 
                    label: 'Скорость',
                    data: [],
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
                scales: { 
                    x: { display: false },
                    y: { 
                        display: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: v => v + ' км/ч' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
        
        const altChart = new Chart(document.getElementById('issAltChart'), {
            type: 'line', 
            data: { 
                labels: [], 
                datasets: [{ 
                    label: 'Высота',
                    data: [],
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
                scales: { 
                    x: { display: false },
                    y: { 
                        display: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: v => v + ' км' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });

        async function loadTrend() {
            try {
                const r = await fetch('/api/iss/trend?limit=240');
                const js = await r.json();
                const pts = Array.isArray(js.points) ? js.points.map(p => [p.lat, p.lon]) : [];
                
                if (pts.length) {
                    // Анимация обновления линии
                    trail.setLatLngs(pts);
                    const lastPoint = pts[pts.length - 1];
                    
                    // Плавное перемещение маркера
                    marker.setLatLng(lastPoint);
                    marker._icon.classList.add('iss-marker-pulse');
                    setTimeout(() => {
                        marker._icon.classList.remove('iss-marker-pulse');
                    }, 1000);
                    
                    // Центрирование карты на последней точке
                    map.setView(lastPoint, map.getZoom());
                }
                
                const t = (js.points||[]).map(p => new Date(p.at).toLocaleTimeString());
                
                // Плавное обновление графиков
                animateChartUpdate(speedChart, {
                    labels: t,
                    data: (js.points||[]).map(p => p.velocity)
                });
                
                animateChartUpdate(altChart, {
                    labels: t,
                    data: (js.points||[]).map(p => p.altitude)
                });
                
            } catch(e) {
                console.error('Ошибка загрузки тренда:', e);
            }
        }

        // Функция для анимированного обновления графиков
        function animateChartUpdate(chart, { labels, data }) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            
            chart.update({
                duration: 800,
                easing: 'easeOutQuart'
            });
        }

        // Загружаем данные сразу и затем каждые 15 секунд
        await loadTrend();
        setInterval(loadTrend, 15000);
    }

    // ====== JWST ГАЛЕРЕЯ ======
    const track = document.getElementById('jwstTrack');
    const info = document.getElementById('jwstInfo');
    const form = document.getElementById('jwstFilter');
    const srcSel = document.getElementById('srcSel');
    const sfxInp = document.getElementById('suffixInp');
    const progInp = document.getElementById('progInp');

    // Инициализация Lightbox
    const lightbox = new bootstrap.Modal(document.getElementById('lightbox'));
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxTitle = document.getElementById('lightbox-title');
    const lightboxLink = document.getElementById('lightbox-link');

    function toggleInputs() {
        sfxInp.style.display = (srcSel.value === 'suffix') ? '' : 'none';
        progInp.style.display = (srcSel.value === 'program') ? '' : 'none';
    }
    
    srcSel.addEventListener('change', toggleInputs); 
    toggleInputs();

    async function loadFeed(qs) {
        // Показываем анимированный индикатор загрузки
        track.innerHTML = `
            <div class="d-flex justify-content-center align-items-center p-5 w-100">
                <div class="text-center">
                    <div class="spinner-border text-primary loading-pulse mb-2" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <div class="text-muted small">Загружаем изображения...</div>
                </div>
            </div>`;
        
        info.textContent = '';
        
        try {
            const url = '/api/jwst/feed?' + new URLSearchParams(qs).toString();
            const r = await fetch(url);
            const js = await r.json();
            
            track.innerHTML = '';
            
            if (!js.items || js.items.length === 0) {
                track.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center p-5 w-100">
                        <div class="text-center text-muted">
                            <i class="bi bi-image fs-1 mb-2"></i>
                            <div>Изображения не найдены</div>
                        </div>
                    </div>`;
                return;
            }
            
            // Создаем элементы галереи с анимацией появления
            js.items.forEach((it, index) => {
                setTimeout(() => {
                    const fig = document.createElement('figure');
                    fig.className = 'jwst-item m-0 fade-in';
                    fig.style.opacity = '0';
                    fig.style.animation = 'fadeIn 0.3s ease forwards';
                    fig.style.animationDelay = `${index * 0.05}s`;
                    
                    fig.innerHTML = `
                        <a href="#" class="d-block overflow-hidden rounded gallery-item" 
                           data-img="${it.url}" 
                           data-title="${it.caption || 'JWST Изображение'}"
                           data-link="${it.link || it.url}">
                            <img loading="lazy" src="${it.url}" alt="JWST" 
                                 class="img-fluid gallery-img"
                                 onload="this.parentElement.parentElement.style.opacity='1'"
                                 onerror="this.src='https://via.placeholder.com/180x180?text=Ошибка+загрузки'">
                        </a>
                        <figcaption class="jwst-cap">${it.caption || ''}</figcaption>`;
                    
                    track.appendChild(fig);
                    
                    // Обработчик для Lightbox
                    const link = fig.querySelector('.gallery-item');
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        lightboxImg.src = this.dataset.img;
                        lightboxTitle.textContent = this.dataset.title;
                        lightboxLink.href = this.dataset.link;
                        lightbox.show();
                    });
                    
                }, 0);
            });
            
            info.innerHTML = `
                <div class="fade-in">
                    <i class="bi bi-info-circle me-1"></i>
                    Источник: <code>${js.source}</code> · Показано: ${js.count || 0} изображений
                </div>`;
                
        } catch(e) {
            track.innerHTML = `
                <div class="d-flex justify-content-center align-items-center p-5 w-100">
                    <div class="text-center text-danger">
                        <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                        <div>Ошибка загрузки данных</div>
                        <div class="small mt-1">Попробуйте обновить страницу</div>
                    </div>
                </div>`;
        }
    }

    // Обработчик формы фильтрации
    form.addEventListener('submit', function(ev) {
        ev.preventDefault();
        const fd = new FormData(form);
        const q = Object.fromEntries(fd.entries());
        loadFeed(q);
    });

    // Навигация по галерее
    document.querySelector('.jwst-prev').addEventListener('click', () => {
        track.scrollBy({ left: -600, behavior: 'smooth' });
    });
    
    document.querySelector('.jwst-next').addEventListener('click', () => {
        track.scrollBy({ left: 600, behavior: 'smooth' });
    });

    // Автоматическая прокрутка галереи каждые 10 секунд
    let autoScrollInterval = setInterval(() => {
        if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            track.scrollBy({ left: 200, behavior: 'smooth' });
        }
    }, 10000);

    // Останавливаем автоскролл при взаимодействии
    track.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
    track.addEventListener('mouseleave', () => {
        autoScrollInterval = setInterval(() => {
            if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: 200, behavior: 'smooth' });
            }
        }, 10000);
    });

    // Загружаем стартовые данные
    await loadFeed({ source: 'jpg', perPage: 24 });
});
</script>
@endsection