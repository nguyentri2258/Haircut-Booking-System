@extends('layouts.dashboard')

@section('content')
<h2 class="mb-4">Chi nhánh</h2>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="mb-3">
    <a href="{{ route('addresses.create') }}" class="btn btn-primary">Tạo địa chỉ mới</a>
</div>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr class="text-center">
            <th>Tên</th>
            <th>Địa chỉ</th>
            <th>Ghi chú</th>
            <th>Thao tác</th>

        </tr>
    </thead>
    <tbody>
        @forelse($addresses as $address)
            <tr class="text-center">
                <td>{{ $address->name }}</td>
                <td>{{ $address->address }}</td>
                <td>{{ $address->note }}</td>
                <td>
                    <div class='d-inline-flex gap-1'>
                        <a href="{{ route('addresses.edit', $address) }}" class="btn btn-sm btn-warning">✏️ Sửa</a>
                
                        <form method="POST" action="{{ route('addresses.destroy', $address) }}" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Xoá</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Không tìm thấy địa chỉ</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection