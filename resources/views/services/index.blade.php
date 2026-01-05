@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Dịch vụ</h2>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="mb-3">
    <a href="{{ route('services.create') }}" class="btn btn-primary">Tạo dịch vụ</a>
</div>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr class="text-center">
            <th>Tên</th>
            <th>Giá tiền</th>
            <th>Mô tả</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse($services as $service)
            <tr class="text-center">
                <td>{{ $service->name }}</td>
                <td>{{ $service->price }}</td>
                <td>{{ $service->description }}</td>
                <td>
                    <div class='d-inline-flex gap-1'>
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-warning">✏️ Sửa</a>

                        <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Xoá</button>
                        </form>
                    <div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Không tìm thấy dịch vụ</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection