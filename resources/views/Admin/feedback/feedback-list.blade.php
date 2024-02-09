@extends('Admin.layouts.main')
@push('page_title') App Feedback @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">App Feedback Lists</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>User Name</th>
                            <th>Feedback</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feedback as $data)
                            <tr>
                                <td class="align-middle">{{$loop->iteration}}</td>
                                <td class="align-middle">{{$data->first_name." ".$data->last_name}}</td>
                                <td class="align-middle">{{$data->feedback}}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('edit_feedback',['id'=>$data->id]) }}"><button data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" type="button" class="btn btn-warning" style="padding: 6px 10px"><i class="fas fa-edit"></i></button></a>
                                        <button href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="deleteFeedback({{ $data->id }})" type="button" class="btn btn-danger" style="padding: 6px 10px"><i class="fas fa-trash"></i></button>
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
<script>
    function deleteFeedback(id){
        Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('delete_feedback') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "{{ route('feedback_list') }}";
                    }
                });
            }
        })
    }
</script>
@endsection
