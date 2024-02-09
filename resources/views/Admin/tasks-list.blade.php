@extends('Admin.layouts.main')
@push('page_title') Tasks @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Tasks Lists</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>User Name</th>
                            <th>Date</th>
                            <th class="w-25">Task</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $data)
                            <tr>
                                <td class="align-middle">{{$loop->iteration}}</td>
                                <td class="align-middle">{{$data->first_name." ".$data->last_name}}</td>
                                <td class="align-middle">{{$data->date}}</td>
                                <td class="align-middle w-50 text-nowrap overflow-hidden">{{$data->task_sub_category_name}}</td>
                                <td class="align-middle">{{$data->task_start_time}}</td>
                                <td class="align-middle">{{$data->task_end_time}}</td>
                                <td class="align-middle">
                                    @if ($data->task_status == 0)
                                        <span class="bg-primary p-1 ps-2 pe-2 rounded text-white" style="font-size: 13px;">Not Completed</span>
                                    @elseif($data->task_status == 1)
                                        <span class="bg-success p-1 ps-2 pe-2 rounded text-white" style="font-size: 13px;">Completed</span>
                                    @elseif($data->task_status == 2)
                                        <span class="bg-danger p-1 ps-2 pe-2 rounded text-white" style="font-size: 13px;">Cancelled</span>
                                    @else
                                        <span class="bg-warning p-1 ps-2 pe-2 rounded text-white" style="font-size: 13px;">Reschedule</span>
                                    @endif
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
