@extends('layouts.dashboard')

@section('content')

<h2 class="mb-4">Ngày nghỉ</h2>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="mb-3">
    <a href="{{ route('holidays.create') }}" class="btn btn-primary">
        Tạo ngày nghỉ
    </a>
</div>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr class="text-center">
            <th>Ngày</th>
            <th>Áp dụng</th>
            <th>Lý do</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse($holidays as $holiday)
            <tr class="text-center">
                <td>{{ $holiday->date }}</td>
                <td>
                    {{ $holiday->address?->name ?? 'Tất cả chi nhánh' }}
                </td>
                <td>{{ $holiday->note }}</td>
                <td>
                    <div class="d-inline-flex gap-1">
                        <a href="{{ route('holidays.edit', $holiday) }}"
                           class="btn btn-sm btn-warning">
                            ✏️ Sửa
                        </a>

                        <form method="POST"
                              action="{{ route('holidays.destroy', $holiday) }}"
                              onsubmit="return confirm('Bạn chắc chứ?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger">
                                🗑️ Xoá
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">
                    Không tìm thấy ngày nghỉ
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<h5 class="mb-1 text-muted">
    * Ngày nghỉ sẽ được ẩn khỏi lịch làm việc & booking
</h5>

@endsection
