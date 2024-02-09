@extends('Admin.layouts.main')
@push('page_title') Splash Screen Questions @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Splash Screen Questions Lists</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('add_question') }}"><button type="button" class="btn btn-success btn-sm small"><i class="fas fa-plus-circle"></i>&nbsp; Add New
                    </button></a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ssq as $data)
                            <tr>
                                <td class="text-center align-middle">{{$loop->iteration}}</td>
                                <td class="text-capitalize align-middle">{{ $data->questions }}</td>
                                <td class="align-middle text-capitalize">{{ $data->status }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('edit_question',['slug'=>$data->questions_slug]) }}"><button type="button" class="btn btn-warning" style="padding: 6px 10px"><i class="fas fa-edit"></i></button></a>
                                        <button href="javascript:void(0);" onclick="deleteQuestion({{ $data->id }})" type="button" class="btn btn-danger" style="padding: 6px 10px"><i class="fas fa-trash"></i></button>
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
    function deleteQuestion(id){
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
                    url:"{{ route('delete_question') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "{{ route('splash_screen_questions') }}";
                    }
                });
            }
        })
    }
</script>
@endsection
