@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Chỉnh sửa chi nhánh</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('addresses.update', $address) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Tên</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Tên" value="{{ old('name', $address->name) }}" required>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Địa chỉ</label>
        <input type="text" name="address" id="address" class="form-control" placeholder="Địa chỉ" value="{{ old('address', $address->address) }}" required>
    </div>
    <div class="mb-3">
        <label for="note" class="form-label">Ghi chú</label>
        <input type="text" name="note" id="note" class="form-control" placeholder="Ghi chú" value="{{ old('note', $address->note) }}">
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('addresses.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
