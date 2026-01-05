@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Quản lý nhân sự</h2>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        @can('create-user', auth()->user()->isManager() ? 'staff' : 'manager')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                ➕ Tạo tài khoản
            </a>
        @else
            <div></div>
        @endcan
        @if(auth()->user()->isOwner())
            <div style="width:260px">
                <select class="form-select"
                        onchange="window.location = this.value">
                    <option value="{{ route('users.index') }}">
                        — Tất cả chi nhánh —
                    </option>
                    @foreach($addresses as $address)
                        <option value="{{ route('users.index', ['address_id' => $address->id]) }}"
                            {{ request('address_id') == $address->id ? 'selected' : '' }}>
                            {{ $address->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr class="text-center">
                <th width="80">Avatar</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th width="120">Vai trò</th>
                <th>Chi nhánh</th>
                @if(auth()->user()->isOwner() || auth()->user()->isManager())
                    <th width="160">Thao tác</th>
                @endif
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td class="text-center">
                    <img src="{{ $user->avatar_url }}"
                         width="60" height="60"
                         class="rounded-circle"
                         style="object-fit:cover;">
                </td>
                <td>{{ $user->name ?? '—' }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    <span class="badge bg-{{ $user->role === 'owner'
                        ? 'danger'
                        : ($user->role === 'manager' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="text-center">{{ $user->address->name ?? '—' }}</td>
                @if(auth()->user()->isOwner() || auth()->user()->isManager())
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            @can('edit-user', $user)
                                <a href="{{ route('users.edit', $user) }}"
                                   class="btn btn-sm btn-warning">
                                    ✏️ Sửa
                                </a>
                            @endcan
                            @can('delete-user', $user)
                                <form method="POST"
                                      action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá tài khoản này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        🗑️ Xoá
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Không có nhân sự nào
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection