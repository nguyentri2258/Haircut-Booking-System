@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Điều chỉnh lịch đặt</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('managers.update', $booking) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name">Tên</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $booking->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="phone">SĐT</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $booking->phone) }}" required>
    </div>

    <div class="mb-3">
        <label for="address_id">Chi nhánh</label>
        <select name="address_id" id="address_id" class="form-control" required>
            <option value="">Chọn địa chỉ</option>
            @foreach ($addresses as $address)
                <option value="{{ $address->id }}" {{ (old('address_id', $booking->address_id) == $address->id) ? 'selected' : '' }}>
                    {{ $address->name }}
                </option>
            @endforeach
        </select>
        <input type="hidden" id="original_address_id" value="{{ $booking->address_id }}">
    </div>

    <div class="mb-3">
        <label for="service_id">Dịch vụ</label>
        <select name="service_id[]" id="service_id" class="form-control ts-multi" multiple>
            @foreach ($services as $service)
                <option value="{{ $service->id }}"
                    {{ in_array($service->id, old('service_id', $booking->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                    {{ $service->name }} – {{ number_format($service->price) }}đ
                </option>
            @endforeach
        </select>
        <div class="mt-2 text-end fw-bold" id="service-total">Tổng: 0đ</div>
    </div>

    <div class="mb-3">
        <label for="date">Ngày</label>
        <input type="text" name="date" id="date" class="form-control"
            value="{{ old('date', \Carbon\Carbon::parse($booking->date)->format('Y-m-d')) }}">
    </div>

    <div class="mb-3">
        <label for="user_id">Stylist</label>
        <select name="user_id" id="user_id" class="form-control" required data-old="{{ old('user_id', $booking->user_id) }}">
            <option value="">Chọn thợ làm tóc</option>
            <option value="auto" {{ old('user_id', $booking->user_id) == 'auto' ? 'selected' : '' }}>Để chúng tôi chọn cho bạn</option>
        </select>
        <input type="hidden" id="original_user_id" value="{{ $booking->user_id }}">
    </div>

    <div class="mb-3">
        <label for="time">Giờ</label>
        <select name="time" id="time_of_day" class="form-control" required>
            <option value="">Chọn giờ</option>
        </select>
        @php
            $defaultTime = $booking->user_id === 'auto'
                ? $booking->time_of_day . '|' . $booking->user_id
                : $booking->time_of_day;
            $oldTimeValue = old('time') !== null ? old('time') : $defaultTime;
        @endphp

        <input type="hidden" id="oldTime" value="{{ $oldTimeValue }}">
    </div>

    <div class="mb-3">
        <label for="notes">Ghi chú</label>
        <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $booking->notes) }}">
    </div>

    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="{{ route('managers.index') }}" class="btn btn-secondary">Hủy</a>
</form>

<script>
    window.bookingData = {
        bookingId: {{ $booking->id }},
        originalUserId: {{ $booking->user_id ?? 'null' }},
        originalAddressId: {{ $booking->address_id }},
        oldTime: @json($booking->time_of_day),

        services: @json(
            $services->map(fn($s) => [
                'id' => $s->id,
                'price' => $s->price
            ])
        ),

        routes: {
            stylists: "{{ route('getStylistsByAddress') }}",
            times: "{{ route('getAvailableTimes') }}"
        }
    };
</script>

@endsection