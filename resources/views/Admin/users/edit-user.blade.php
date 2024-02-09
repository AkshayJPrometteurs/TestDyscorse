@extends('Admin.layouts.main')
@push('page_title') Edit User @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Edit User Data</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('user_list_view') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_user',['id'=>$user->id]) }}" method="POST" class="d-flex flex-column flex-md-row gap-10" enctype="multipart/form-data">
                @csrf
                <div class="d-grid justify-content-center edit-row1">
                    <img src="@if ($user->profile_image){{ asset('assets/images/profile')."/".$user->profile_image }}@else{{ asset('assets/images/user_avatar.png') }}@endif" alt="profile" class="edit-img mx-auto">
                    <input type="file" name="profile_image" class="form-control mt-3">
                </div>
                <div class="edit-row2">
                    <div class="d-flex gap-4">
                        <div class="mb-4 w-100">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" id="first_name" class="form-control" value="{{ $user->first_name }}" name="first_name">
                        </div>
                        <div class="mb-4 w-100">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" id="last_name" class="form-control" value="{{ $user->last_name }}" name="last_name">
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="mb-4 w-100">
                            <label for="auth_type" class="form-label">Login Type</label>
                            <input type="text" id="auth_type" class="form-control text-capitalize" value="{{ $user->auth_type }}" name="auth_type" readonly>
                        </div>
                        <div class="mb-4 w-100">
                            <label for="email" class="form-label">Email-ID</label>
                            <input type="text" id="email" class="form-control" value="{{ $user->email }}" name="email">
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="mb-4 w-100">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" value="{{ $user->gender }}" name="gender">
                                <option value="">Choose Gender</option>
                                <option value="Male" @if($user->gender == 'Male') selected @endif>Male</option>
                                <option value="Female" @if($user->gender == 'Female') selected @endif>Female</option>
                            </select>
                        </div>
                        <div class="mb-4 w-100">
                            <label for="age" class="form-label">Age</label>
                            <input type="text" id="age" class="form-control" value="{{ $user->age }}" name="age">
                          	<span class="text-danger">@error('age'){{$message}}@enderror</span>
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="mb-4 w-100">
                            <label for="privacy" class="form-label">Privacy Status</label>
                            <select class="form-control" id="privacy" value="{{ $user->privacy }}" name="privacy">
                                <option value="">Choose Privacy</option>
                                <option value="Public" @if($user->privacy == 'Public') selected @endif>Public</option>
                                <option value="Private" @if($user->privacy == 'Private') selected @endif>Private</option>
                            </select>
                        </div>
                        <div class="mb-4 w-100"></div>
                    </div>
                    <button class="btn btn-primary w-100 mt-2">Save</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
