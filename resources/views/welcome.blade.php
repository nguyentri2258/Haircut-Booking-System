@extends('layouts.default')

@section('content')
<div class="d-flex align-items-center justify-content-center text-center"
     style="min-height:80vh;">
    <div class="text-white px-3">
        <h1 class="fw-bold mb-3">
            Chào Mừng Đến Với
        </h1>
        <h3 class="fw-semibold mb-5">
            Hệ Thống Đặt Lịch Dịch Vụ Cắt Tóc
        </h3>
        <div class="d-flex flex-column gap-3">
            <a href="{{ route('login') }}"
               class="btn btn-outline-light px-4 py-2">
                Admin Login
            </a>
            <a href="{{ route('booking.index') }}"
               class="btn btn-outline-light px-4 py-2">
                Booking Form
            </a>
        </div>
    </div>
</div>
@endsection