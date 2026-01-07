@extends('layouts.default')

@section('content')
<main class="mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-3 border border-dark">
                    <h3 class="card-header text-white text-center bg-dark">Đặt lịch tại đây</h3>
                    <div class="card-body px-4 py-3">
                        <form id="bookingForm" method="post" action="{{ route('booking.index') }}">
                            @csrf            
                            <div class="form-group mb-3">
                                <label for="name">Tên</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Tên của bạn">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="Số điện thoại">
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Email">
                                @error('email') <small id="email-error" class="text-danger d-none"></small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="address_id">Địa chỉ</label>
                                <select name="address_id" id="address_id" class="form-control">
                                    <option value="">Chọn địa chỉ</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}" {{ old('address_id') == $address->id ? 'selected' : '' }}>{{ $address->name }}</option>
                                    @endforeach
                                </select>
                                @error('address_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="service_id">Dịch vụ</label>
                                <select name="service_id[]" id="service_id" class="form-control ts-multi" multiple placeholder="Chọn dịch vụ">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ (collect(old('service_id'))->contains($service->id)) ? 'selected' : '' }}>
                                            {{ $service->name }} – {{ number_format($service->price) }}đ
                                        </option>
                                    @endforeach
                                </select>
                                <div id="total-price" class="mt-2 fw-bold text-end text-primary">Tổng: 0đ</div>
                                @error('service_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="date">Ngày</label>
                                <input type="text" name="date" id="date" class="form-control" value="{{ old('date') }}" placeholder="Chọn ngày" readonly>
                                @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="user_id">Stylist</label>
                                <select name="user_id" id="user_id" class="form-control d-none">
                                    <option value="">Chọn thợ làm tóc</option>
                                    <option value="auto">Để chúng tôi chọn cho bạn</option>
                                </select>
                                <div id="userBoxContainer" class="d-flex flex-wrap gap-3 mt-2"></div>
                                @error('user_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="time">Chọn giờ</label>
                                <select name="time" id="time_of_day" class="form-control">
                                    <option value="">Chọn giờ</option>
                                </select>
                                @error('time') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="notes">Ghi chú</label>
                                <input type="text" name="notes" id="notes" class="form-control" placeholder="Ghi chú" value="{{ old('notes') }}">
                            </div>
                            <div class="d-grid">
                                <button type="button" id="submit-btn" class="btn btn-dark btn-block">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


@include('bookings.email')

@include('bookings.success')

@if(session('success'))
    <script>
        window.showBookingSuccess = true;
    </script>
@endif

<script>
    window.BOOKING_CREATE = {
        oldUser: @json(old('user_id')),
        oldTime: @json(old('time')),
        routes: {
            stylists: "{{ route('getStylistsByAddress') }}",
            holidays: "{{ route('getAllHolidays') }}",
            times: "{{ route('getAvailableTimes') }}"
        }
    };
</script>
@endsection
