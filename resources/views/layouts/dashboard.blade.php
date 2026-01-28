<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
    <div class="d-flex">
        <div class="sidebar d-flex flex-column bg-dark text-white p-3 min-vh-100">
        <h4 class="text-center mb-4">
            <a href="{{ route('profile') }}" class="text-decoration-none text-white">
                <i class="bi bi-speedometer2 me-1"></i> Trang chủ
            </a>
        </h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('managers.index') }}" class="nav-link text-white">
                    <i class="bi bi-list-ul me-2"></i>Lịch đặt
                </a>
            </li>
            @if(auth()->user()->isOwner())
            <li class="nav-item">
                <a href="{{ route('addresses.index') }}" class="nav-link text-white">
                    <i class="bi bi-geo-alt me-2"></i>Chi nhánh
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('services.index') }}" class="nav-link text-white">
                    <i class="bi bi-scissors me-2"></i>Dịch vụ
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('holidays.index') }}" class="nav-link text-white">
                    <i class="bi bi-calendar-day me-2"></i>Ngày nghỉ
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('users.index') }}" class="nav-link text-white">
                    <i class="bi bi-person-badge me-2"></i>Nhân sự
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('availabilities.index') }}" class="nav-link text-white">
                    <i class="bi bi-kanban me-2"></i>Lịch làm
                </a>
            </li>
        </ul>
        <div class="mt-auto pt-3">
            <a href="{{ route('booking.index') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-warning w-100 fw-bold text-dark py-2"
            style="border-radius:8px;">
                <i class="bi bi-calendar-check me-2"></i> Form đặt lịch
            </a>
        </div>

    </div>
    <div class="content-wrapper">
        <div class="topbar">
            <h3 class="mb-0">Chào mừng đến trang quản lý</h3>
            <div class="logout-button">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
        <div class="card p-4">
            @yield('content')
        </div>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/vn.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script src="{{ asset('js/components/flatpickr.js') }}"></script>
        @stack('scripts')
    </div>
</div>
</body>
</html>
