@extends('Admin.layouts.main')
@push('page_title') Splash Screen Questions @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Splash Screen Questions - View Answers</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('ssq_user_answers_list') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Answers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ssq as $item)
                            <tr>
                                <td class="align-middle">{{$loop->iteration}}</td>
                                <td class="text-capitalize align-middle">{{ $item->questions }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
