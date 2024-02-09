@extends('Admin.layouts.guest')
@push('page_title') Login @endpush
@section('contents')
<div class="wrapper">
    <section class="position-relative bg-white overflow-hidden w-100 d-flex align-items-center vh-100">
        <img class="d-none d-md-block position-absolute top-0 start-0 col-5 h-100 img-fluid" style="object-fit: cover;" src="{{ asset('assets/images/login_img.jpg') }}" alt="">
        <div class="container position-relative" style="z-index:1;">
            <form action="{{ route('authentication') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-7 col-lg-8 ms-auto">
                        <div class="ps-md-10 ps-lg-36 pt-16 pb-14 pb-md-16">
                            {{-- <div class="d-block mb-8">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="Filing Buddy Logo" class="">
                            </div> --}}
                            <div class="mw-xl mb-10">
                                <h3 class="display-6 mb-6">Welcome To Dyscorse Admin!</h3>
                            </div>
                            <div class="form-floating mb-6">
                                <input type="email" placeholder="john@filingbuddy.com" name="email" class="@error('email'){{'border border-danger'}}@enderror form-control" value="{{ Cookie::get('adymsicnorse_edmymsaciolrse') }}">
                                <label for="">Email Address</label>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-floating mb-10 position-relative">
                                <input type="password" placeholder="******" name="password" class="@error('password'){{'border border-danger'}}@enderror form-control password-input" value="{{ Cookie::get('adymsicnorse_pdayssscwoorrsde') }}">
                                <i class="bi bi-eye toggle-password" style="cursor: pointer;position: absolute;top:12px;right:12px;font-size:25px;font-weight:bold" id="passHideShow"></i>
                                <label for="">Password</label>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-md-flex mb-10 align-items-center justify-content-between">
                                <div class="mb-0 form-check">
                                    <input class="form-check-input" type="checkbox" name="remember_me" value="1" @if(Cookie::get('adymsicnorse_edmymsaciolrse') && Cookie::get('adymsicnorse_pdayssscwoorrsde')) checked @endif>
                                    <label class="form-check-label">Remember me</label>
                                </div>
                            </div>
                            <button class="btn btn-primary-blue" type="submit">Log In</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <img class="d-md-none img-fluid" style="object-fit: cover;" src="{{ asset('assets/images/login_img.jpg') }}" alt="">
        <img class="d-none d-md-block position-absolute bottom-0 end-0" style="object-fit: cover;" src="{{ asset('assets/images/smile_yellow.svg') }}" alt="">
    </section>
</div>
@endsection
