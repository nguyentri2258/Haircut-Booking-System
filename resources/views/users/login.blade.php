@extends('layouts.default')
@section('title','Login')
@section('content')
    <main class="mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{session('success')}}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-success">
                            {{session('error')}}
                        </div>
                    @endif
                    <div class="card">
                        <h3 class="card-header text-center">Đăng nhập</h3>
                        <div class="card-body">
                            <form method="POST" action="{{ route('login.post') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Email" id="email" class="form-control" name="email" required autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password" class="form-control" name="password" required autofocus>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">
                                            {{ $errors->first('password') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                        <input type="checkbox" name="remember" style="width:16px;height:16px;">
                                        Ghi nhớ mật khẩu
                                    </label>
                                </div>
                                <div class="mb-3 text-end">
                                    <a href="{{ route('activate') }}">Kích hoạt tài khoản</a>
                                </div>
                                <div class="d-grid mx-auto">
                                    <button type="submit" class="btn btn-dark btn-block">Đăng nhập</button>
                                </div>         
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection