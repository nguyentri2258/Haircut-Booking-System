@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Sửa ngày nghỉ</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('holidays.update', $holiday) }}">
    @csrf
    @method('PUT')

    {{-- Ngày --}}
    <div class="mb-3">
        <label class="form-label">Ngày</label>
        <input type="date"
               name="date"
               class="form-control"
               value="{{ old('date', $holiday->date) }}"
               required>
    </div>

    {{-- Áp dụng cho --}}
    <div class="mb-3">
        <label class="form-label">Áp dụng cho</label>
        <select name="address_id" class="form-select">
            <option value="">
                Tất cả chi nhánh
            </option>

            @foreach($addresses as $address)
                <option value="{{ $address->id }}"
                    {{ old('address_id', $holiday->address_id) == $address->id ? 'selected' : '' }}>
                    {{ $address->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Lý do --}}
    <div class="mb-3">
        <label class="form-label">Lý do</label>
        <input type="text"
               name="note"
               class="form-control"
               value="{{ old('note', $holiday->note) }}"
               required>
    </div>

    <button type="submit" class="btn btn-primary">
        Cập nhật
    </button>
    <a href="{{ route('holidays.index') }}" class="btn btn-secondary">
        Hủy
    </a>
</form>
@endsection
