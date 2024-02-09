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
                                <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-decoration-none">Forget Password</a>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin_forget_password') }}" method="POST" id="user_email_form_submit">
                    @csrf
                    <div class="modal-header justify-content-center">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Forget Password</h1>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-center">Enter registered information to get credentials</h6>
                        <div class="p-5">
                            <label>Email-ID</label>
                            <input type="email" name="user_email" id="user_email" class="form-control password-input">
                            <span style="color: red;" id="user_email_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" id="user_email_submit">Send</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#user_email_submit').on('click',function(){
            var email = $('#user_email').val();
            if(email === ''){
                $('#user_email').css('border','1px solid red');
                $('#user_email_error').text('The email-id field required.');
            }else{
                $('#user_email_form_submit').submit();
            }
        });
    });
</script>
@endsection
