@extends('Admin.layouts.main')
@push('page_title') User Lists @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">User Lists</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email-ID</th>
                            <th>Privacy</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $data)
                            <tr>
                                <td class="text-center align-middle">{{$loop->iteration}}</td>
                                <td class="text-center align-middle">
                                    <img src="@if ($data->profile_image)
                                        {{ asset('assets/images/profile')."/".$data->profile_image }}
                                    @else
                                        {{ asset('assets/images/user_avatar.png') }}
                                    @endif" alt="profile" style="height:40px;width:40px;border-radius:100%;">
                                </td>
                                <td class="align-middle text-nowrap text-capitalize">{{ $data->first_name." ".$data->last_name }}</td>
                                <td class="align-middle text-nowrap">{{ $data->email }}</td>
                                <td class="text-capitalize align-middle">{{ $data->privacy }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center align-items-center">
                                        <a href="{{ route('user_following_list',['id'=>$data->id]) }}" class="text-decoration-none">
                                            <button type="button" class="btn btn-primary d-flex gap-2 text-nowrap">
                                                <span>Followings</span>
                                                <span class="badge bg-white text-black fw-bold">
                                                    @php
                                                        echo DB::table('friends_followings')
                                                        ->join('users','users.id','friend_user_id')
                                                        ->where('friends_followings.user_id', $data->id)
                                                        ->where('friends_followings.following_status','approved')
                                                        ->count();
                                                    @endphp
                                                </span>
                                            </button>
                                        </a>
                                        <a href="{{ route('user_followers_list',['id'=>$data->id]) }}" class="text-decoration-none">
                                            <button type="button" class="btn btn-primary d-flex gap-2 text-nowrap">
                                                <span>Followers</span>
                                                <span class="badge bg-white text-black fw-bold">
                                                    @php
                                                        echo DB::table('friends_followers')
                                                        ->join('users','users.id','friend_user_id')
                                                        ->where('friends_followers.user_id', $data->id)
                                                        ->where('friends_followers.followers_status','approved')
                                                        ->count();
                                                    @endphp
                                                </span>
                                            </button>
                                        </a>
                                        <a href="{{ route('user_request_pending_list',['id'=>$data->id]) }}" class="text-decoration-none">
                                            <button type="button" class="btn btn-danger d-flex gap-2 text-nowrap">
                                                <span>Pending Reqs.</span>
                                                <span class="badge bg-white text-black fw-bold">
                                                    @php
                                                        echo DB::table('friends_followers')
                                                        ->join('users','users.id','friend_user_id')
                                                        ->where('friends_followers.user_id', $data->id)
                                                        ->where('friends_followers.followers_status','pending')
                                                        ->count();
                                                    @endphp
                                                </span>
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
