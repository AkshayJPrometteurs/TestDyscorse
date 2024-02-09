@extends('Admin.layouts.main')
@push('page_title') Splash Screen Questions @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Splash Screen Questions - User Answers</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Sr No.</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">Attempted Answers Count</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ans_data as $data)
                            <tr>
                                <td class="align-middle">{{$loop->iteration}}</td>
                                <td class="text-capitalize align-middle">{{ $data['name'] }}</td>
                                <td class="align-middle text-capitalize fw-bold">{{ $data['ans'] }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('ssq_view_user_answers_list',['id'=>$data['user_id']]) }}"><button type="button" class="btn btn-primary" style="padding: 6px 10px">View Answers</button></a>
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
