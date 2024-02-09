@extends('Admin.layouts.main')
@push('page_title') User Members Lists @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">User Members Lists</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>User Name</th>
                            <th>Member Name</th>
                            <th>Member Email-ID</th>
                            <th>Member Mobile</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $data)
                            <tr>
                                <td class="text-center align-middle">{{$loop->iteration}}</td>
                                <td class="align-middle">{{ $data->first_name." ".$data->last_name }}</td>
                                <td class="text-capitalize align-middle">{{ $data->member_name }}</td>
                                <td class="align-middle">{{ $data->member_email }}</td>
                                <td class="align-middle">{{ $data->member_mobile }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('edit_member',['id'=>$data->id]) }}"><button data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" type="button" class="btn btn-warning" style="padding: 6px 10px"><i class="fas fa-edit"></i></button></a>
                                        <button href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="deleteMember({{ $data->id }})" type="button" class="btn btn-danger" style="padding: 6px 10px"><i class="fas fa-trash"></i></button>
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
    function deleteMember(id){
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
                    url:"{{ route('delete_member') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "lists";
                    }
                });
            }
        })
    }
</script>
<div class="modal fade" id="viewUser" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalData">
            </div>
        </div>
    </div>
</div>
@endsection
