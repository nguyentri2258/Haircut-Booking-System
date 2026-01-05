@extends('layouts.default')
@section('title','Activate')
@section('content')
    <main class="mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="card shadow-sm">
                        <h3 class="card-header text-center">Kích hoạt tài khoản</h3>
                        <div class="card-body">
                            <form method="POST" action="{{ route('activate') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Email được cấp"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus
                                    >
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        placeholder="Họ và tên"
                                        value="{{ old('name') }}"
                                        required
                                    >
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Mật khẩu"
                                        required
                                        autocomplete="new-password"
                                    >
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-4">
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Nhập lại mật khẩu"
                                        required
                                    >
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark">
                                        Hoàn thành
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection