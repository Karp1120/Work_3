<!doctype html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Space Dashboard</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛰️</text></svg>">
    <meta name="theme-color" content="#0d6efd">
    
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Leaflet для карт --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- Стили для аккордеонов и компонентов --}}
    <style>
        :root {
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-purple: #6f42c1;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link.active {
            color: var(--bs-primary) !important;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 10%;
            width: 80%;
            height: 2px;
            background: var(--bs-primary);
            border-radius: 2px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .card-header {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #0a58ca 100%);
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        
        /* Тёмная тема */
        @media (prefers-color-scheme: dark) {
            [data-bs-theme="light"] {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                color: #e9ecef;
            }
            
            .card {
                background: rgba(30, 30, 46, 0.9);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .card-header {
                background: rgba(40, 40, 60, 0.9);
                color: #fff;
            }
            
            .text-muted {
                color: #adb5bd !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    {{-- Навигация --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/dashboard">
                <div class="bg-primary rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-rocket-takeoff-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold">Space Dashboard</div>
                    <div class="small opacity-75">HE-PATH-OF-THE-SAMURAI</div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-1">
                        <a class="nav-link d-flex align-items-center {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" 
                           href="/dashboard">
                            <i class="bi bi-speedometer2 me-2"></i>
                            <span>Дашборд</span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link d-flex align-items-center {{ request()->is('iss') ? 'active' : '' }}" 
                           href="/iss">
                            <i class="bi bi-globe-americas me-2"></i>
                            <span>МКС</span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link d-flex align-items-center {{ request()->is('osdr') ? 'active' : '' }}" 
                           href="/osdr">
                            <i class="bi bi-database me-2"></i>
                            <span>OSDR</span>
                        </a>
                    </li>
                </ul>
                
                <div class="ms-3">
                    <button class="btn btn-outline-light btn-sm" onclick="toggleTheme()">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- Основной контент --}}
    <main class="container-fluid px-3 px-lg-4">
        @yield('content')
    </main>

    {{-- Футер --}}
    <footer class="mt-5 py-4 bg-dark text-white" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-rocket-takeoff-fill text-primary fs-4 me-2"></i>
                        <span class="h5 mb-0">Кассиопея</span>
                    </div>
                    <p class="small text-muted mb-0">
                        Система сбора и визуализации космических данных. 
                        Проект HE-PATH-OF-THE-SAMURAI
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        <span id="serverTime">{{ now()->format('d.m.Y H:i:s') }}</span>
                    </div>
                    <div class="small text-muted mt-1">
                        Статус системы: 
                        <span class="text-success">
                            <i class="bi bi-circle-fill me-1"></i>Активна
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Скрипты --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Переключение темы
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            
            // Сохраняем в localStorage
            localStorage.setItem('theme', newTheme);
            
            // Меняем иконку
            const icon = event.currentTarget.querySelector('i');
            icon.className = newTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
        
        // Восстановление темы при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            // Устанавливаем правильную иконку
            const themeButton = document.querySelector('[onclick="toggleTheme()"]');
            if (themeButton) {
                const icon = themeButton.querySelector('i');
                icon.className = savedTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
            }
            
            // Обновление времени сервера
            function updateServerTime() {
                const now = new Date();
                const timeStr = now.toLocaleDateString('ru-RU') + ' ' + now.toLocaleTimeString('ru-RU');
                const el = document.getElementById('serverTime');
                if (el) el.textContent = timeStr;
            }
            setInterval(updateServerTime, 1000);
        });
        
        // Плавная прокрутка для якорей
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>