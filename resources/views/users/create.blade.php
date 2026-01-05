@extends('layouts.dashboard')

@section('content')
<div class="container" style="max-width:600px">
    <h2 class="mb-4">Tạo tài khoản nhân sự</h2>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email đăng nhập</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   required>
        </div>
        @if(auth()->user()->isOwner())
            <select name="role">
                <option value="manager">Manager</option>
                <option value="staff">Staff</option>
            </select>
        @else
            <input type="hidden" name="role" value="staff">
        @endif
        <div class="mb-3">
            <label class="form-label">Chi nhánh</label>
            <select name="address_id" class="form-control" required>
                <option value="">-- Chọn chi nhánh --</option>
                @foreach($addresses as $address)
                    <option value="{{ $address->id }}"
                        {{ old('address_id') == $address->id ? 'selected' : '' }}>
                        {{ $address->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <hr>
        <div class="alert alert-info">
            <small>
                Tài khoản sẽ được tạo <b>không có mật khẩu</b>.<br>
                Nhân sự sẽ tự thiết lập mật khẩu khi đăng nhập lần đầu.
            </small>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                💾 Tạo tài khoản
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Huỷ
            </a>
        </div>
    </form>
</div>
@endsection
