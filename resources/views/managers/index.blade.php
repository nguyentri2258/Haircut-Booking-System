@extends('layouts.dashboard')

@section('content')
@php
    $auth = auth()->user();
    $isOwner = $auth->role === 'owner';
    $isManager = $auth->role === 'manager';
    $isStaff = $auth->role === 'staff';
@endphp

<h2 class="mb-4">Lịch được đặt</h2>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($isOwner)
<div class="row mb-3">
    <div class="col-md-4">
        <select id="filter_address" class="form-select">
            <option value="">— Tất cả lịch đặt —</option>
            @foreach($addresses as $address)
                <option value="{{ $address->id }}"
                    {{ request('address_id') == $address->id ? 'selected' : '' }}>
                    {{ $address->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@endif

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr class="text-center">
            <th>Tên</th>
            <th>SĐT</th>
            <th>Dịch vụ</th>
            <th>Stylist</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            @if(!$isStaff)
                <th>Thao tác</th>
                <th>Sửa</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $booking)
        <tr class="text-center">
            <td>{{ $booking->name }}</td>
            <td>{{ $booking->phone }}</td>
            <td>
                @foreach($booking->services as $service)
                    <div>{{ $service->name }}</div>
                @endforeach
            </td>
            <td>{{ $booking->user->name }}</td>
            <td>{{ $booking->date->format('H:i, d/m/Y') }}</td>
            <td>{{ ucfirst($booking->status) }}</td>
            <td>{{ $booking->notes }}</td>
            @if(!$isStaff)
                <td>
                    <form method="POST"
                        action="{{ route('managers.updateStatus', $booking) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                                onchange="this.form.submit()"
                                class="form-select form-select-sm">
                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>
                                Confirmed
                            </option>
                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>
                        </select>
                    </form>
                </td>
                <td>
                    <a href="{{ route('managers.edit', $booking) }}"
                    class="btn btn-sm btn-warning">
                        Sửa
                    </a>
                </td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">Không có lịch đặt</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($isOwner)
<script>
document.getElementById('filter_address')?.addEventListener('change', function () {
    const addressId = this.value;
    const url = new URL(window.location.href);
    if (addressId) {
        url.searchParams.set('address_id', addressId);
    } else {
        url.searchParams.delete('address_id');
    }
    window.location.href = url.toString();
});
</script>
@endif

@endsection
