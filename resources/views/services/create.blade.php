@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Tạo dịch vụ</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('services.store') }}">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Tên</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Tên" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Giá tiền</label>
        <input type="text" name="price" id="price" class="form-control" placeholder="Giá tiền" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <input type="text" name="description" id="description" class="form-control" placeholder="Mô tả" value="{{ old('name') }}">
    </div>
    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="{{ route('services.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection