@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Tạo ngày nghỉ mới</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('holidays.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Ngày</label>
        <input type="date"
               name="date"
               class="form-control"
               value="{{ old('date') }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Áp dụng cho</label>
        <select name="address_id" class="form-select">
            <option value="">Tất cả chi nhánh</option>
            @foreach($addresses as $address)
                <option value="{{ $address->id }}"
                    {{ old('address_id') == $address->id ? 'selected' : '' }}>
                    {{ $address->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Lý do</label>
        <input type="text"
               name="note"
               class="form-control"
               value="{{ old('note') }}"
               required>
    </div>

    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="{{ route('holidays.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
