@extends('layouts.dashboard')

@section('content')
<div class="container" style="max-width:600px">
    <h2 class="mb-4">Chỉnh sửa thông tin nhân sự</h2>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Email đăng nhập</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $user->email) }}"
                   required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-control" required>
                <option value="">-- Chọn vai trò --</option>
                <option value="manager"
                    {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>
                    Manager
                </option>
                <option value="staff"
                    {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>
                    Staff
                </option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Chi nhánh</label>
            <select name="address_id" class="form-control" required>
                <option value="">-- Chọn chi nhánh --</option>
                @foreach($addresses as $address)
                    <option value="{{ $address->id }}"
                        {{ old('address_id', $user->address_id) == $address->id ? 'selected' : '' }}>
                        {{ $address->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                💾 Lưu thông tin
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Huỷ
            </a>
        </div>
    </form>
</div>
@endsection
