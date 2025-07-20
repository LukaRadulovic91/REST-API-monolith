@extends('layouts.empty')

@push('css')
    <style>
        .vertical-line {
            border: 3px solid #869D13; /* Remove the default horizontal line */
            border-left: 1px solid #000; /* Set the left border to create a vertical line */
            height: 50px; /* Adjust the height as needed */
            margin:41px 20px 10px 5px; /* Add some margin for spacing */
            border-radius: 10px;
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
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="flex-column ml-5 mb-8 d-flex justify-content-center align-items-center">
                                <img class = "m-lg-14" src="{{asset('images/logos/mg-logo.png')}}" alt="logo"/>
                            </div>
                            <div class="text-center mb-3">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Welcome</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <!--end::Subtitle=-->
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Separator-->
                            <div class="d-flex justify-content-center align-items-center mb-5">
                                <span class="text-gray fw-semibold fs-7">Please login to our app</span>
                            </div>

                            <!--end::Separator-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->

                                <!--label::Email-->
                                <label for="email" class="col-md-4 col-form-label pl-2">{{ __('Email') }}</label>
                                <!--label::Email-->
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->

                                <!--label::Password-->
                                <label for="password" class="col-md-4 col-form-label pl-2 pt-0">{{ __('Password') }}</label>
                                <!--label::Password-->
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                <!--end::Password-->
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                @if (Route::has('password.request'))
                                    <a class="link-primary" href="{{ route('password.request') }}">
                                      Forgot Password ?
                                    </a>
                                @endif
                                <!--begin::Link-->
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Sign In</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
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
