@extends('Auth.layout')
@section('title', $title)
@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <form class="login100-form validate-form" method="POST" action="{{ route('auth.registerProcess') }}">
                    @csrf
                    <span class="login100-form-title p-b-26">
                        Đăng ký
                    </span>
                    <span class="login100-form-title p-b-48">
                        <i class="zmdi zmdi-font"></i>
                    </span>
                    <div class="wrap-input100 validate-input">
                        <input class="input100" type="text" name="name">
                        <span class="focus-input100" data-placeholder="Họ và tên"></span>
                    </div>
                    <div class="wrap-input100 validate-input">
                        <input class="input100" type="text" name="phone_number">
                        <span class="focus-input100" data-placeholder="Số điện thoại"></span>
                    </div>
                    <div class="wrap-input100 validate-input">
                        <input class="input100" type="text" name="email">
                        <span class="focus-input100" data-placeholder="Email"></span>
                    </div>
                    <div class="wrap-input100 validate-input">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        <input class="input100" type="password" name="password">
                        <span class="focus-input100" data-placeholder="Mật khẩu"></span>
                    </div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                Đăng ký
                            </button>
                        </div>
                    </div>
                    <div class="text-center p-t-115">
                        <span class="txt1">
                            Đã có tài khoản ?
                        </span>
                        <a class="txt2" href="{{ route('auth.login') }}">
                            Đăng nhập ngay
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection