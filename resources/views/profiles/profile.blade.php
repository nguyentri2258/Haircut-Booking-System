@extends('layouts.dashboard')

@section('content')
<div class="container" style="max-width:550px">
    <h3 class="text-center mb-4">Thông tin cá nhân</h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="text-center mb-3">
        <img id="avatarPreview"
             src="{{ $user->avatar ? asset('uploads/avatar/'.$user->avatar) : asset('images/default-avatar.png') }}"
             style="width:130px;height:130px;border-radius:50%;object-fit:cover;">
    </div>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Ảnh đại diện</label>
            <input type="file" name="avatar" class="form-control" accept="image/*"
                   onchange="previewImage(event)">
        </div>
        <div class="mb-3">
            <label>Họ & tên</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>
        <hr>
        <h5>Đổi mật khẩu (không bắt buộc)</h5>
        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
        </div>
        <div class="mb-3">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary w-100">💾 Lưu thay đổi</button>
    </form>
</div>

<script>
function previewImage(event){
    document.getElementById('avatarPreview').src = URL.createObjectURL(event.target.files[0]);
}
</script>
@endsection