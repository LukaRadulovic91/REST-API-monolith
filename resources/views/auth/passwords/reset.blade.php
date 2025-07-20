@extends('layouts.empty')
@push('css')
    <style>
        .custom-form {
            max-width: 400px; /* Adjust this value based on your design */
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>body { background-image: url({{asset('images/login.png')}}); background-size: cover;}</style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid flex-lg-row">
            <!--begin::Aside-->
            <div class="d-flex flex-center w-lg-50 pt-10 pt-lg-0 px-8">
                <!--begin::Aside-->
                <div class="d-flex flex-center  flex-column">
                    <!--begin::Logo-->
                    <a href="{{route('login')}}" class="mb-15" style="margin-right: auto;">
                        <img alt="Logo" src="{{asset('images/logos/login-logo.png')}}" />
                    </a>
                    <div class="position-relative">
                        <img alt="intersect" src="{{asset('images/intersect.png')}}">

                        <!-- New Image (Top Right Corner) -->
                        <img src="{{asset('images/tooth.png')}}" alt="New Image" class="position-absolute top-5 end-0 mt-0" style = "left:310px; top: -150px;">
                    </div>
                    <!--end::Logo-->

                    <!--begin::Title-->
                    <div class="d-inline-flex">
                        <span class="vertical-line"></span>
                        <h5 class="text-white fw-normal m-0 mt-15" style="color:white !important; width: 560px; font-family: 'Inter', sans-serif;">
                            The Marshall Group has been providing staffing solutions within Toronto & the GTA for over <strong style = "color: #869D13;"> 20 years </strong>.
                        </h5>
                    </div>
                    <!--end::Title-->
                </div>
                <!--begin::Aside-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
                <!--begin::Card-->
                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
                        <!--begin::Form-->
                        <form method="POST" action="{{ route('password.update') }}" class="custom-form">
                            @csrf
                            <div class="flex-column ml-5 mb-8 d-flex justify-content-center align-items-center">
                                <img class="m-lg-14" src="{{asset('images/logos/mg-logo.png')}}" alt="logo"/>
                            </div>
                            <div class="text-center mb-3">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Reset password</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <!--end::Subtitle-->
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label for="email">{{ __('Email Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary" style="color: #ffff;">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </form>


                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
@endsection


