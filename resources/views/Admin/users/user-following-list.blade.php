@extends('Admin.layouts.main')
@push('page_title') User Following Lists @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">User Following Lists</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($following_list as $data)
                            <tr>
                                <td class="text-center align-middle">{{$loop->iteration}}</td>
                                <td class="text-center align-middle">
                                    <img src="@if ($data->profile_image)
                                        {{ asset('assets/images/profile')."/".$data->profile_image }}
                                    @else
                                        {{ asset('assets/images/user_avatar.png') }}
                                    @endif" alt="profile" style="height:40px;width:40px;border-radius:100%;">
                                </td>
                                <td class="align-middle text-capitalize">{{ $data->first_name." ".$data->last_name }}</td>
                                <td class="align-middle">{{ $data->email }}</td>
                                <td class="text-capitalize align-middle">{{ $data->privacy }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
