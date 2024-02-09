@extends('Admin.layouts.main')
@push('page_title') User Lists @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Registered User Lists</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Image</th>
                            <th>Login Type</th>
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
                                <td class="text-center text-capitalize align-middle">{{ $data->auth_type }}</td>
                                <td class="align-middle">{{ $data->first_name." ".$data->last_name }}</td>
                                <td class="align-middle">{{ $data->email }}</td>
                                <td class="text-capitalize align-middle">{{ $data->privacy }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View" class="btn btn-info" onclick="viewUser({{ $data->id }})" style="padding: 6px 10px"><i class="fas fa-eye"></i></button>
                                        <a href="{{ route('edit_user',['id'=>$data->id]) }}"><button data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" type="button" class="btn btn-warning" style="padding: 6px 10px"><i class="fas fa-edit"></i></button></a>
                                        <button href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="deleteUser({{ $data->id }})" type="button" class="btn btn-danger" style="padding: 6px 10px"><i class="fas fa-trash"></i></button>
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
    function viewUser(id){
        $.ajax({
            type:'GET',
            url:"{{ route('view_user') }}",
            data:{"_token": "{{ csrf_token() }}","id": id},
            success:function(data) {
                $('#viewUser').modal('toggle');
                $('#modalData').html("<div class='d-flex gap-6'><div><h6 class='text-center'>Profile Image</h6>"+(data.user.profile_image ? "<img style='height:250px;width:250px;' src='{{ asset('assets/images/profile') }}/"+data.user.profile_image : "<img style='height:250px;width:250px;' src='{{ asset('assets/images/user_avatar.png') }}")+"'></div><div><div class='d-flex flex-column gap-2'><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Login Type</div><div>:</div><div class='text-capitalize'>"+data.user.auth_type+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Name</div><div>:</div><div>"+data.user.first_name+" "+data.user.last_name+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Email-ID</div><div>:</div><div>"+data.user.email+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Gender</div><div>:</div><div>"+data.user.gender+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Age</div><div>:</div><div>"+data.user.age+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>Privacy</div><div>:</div><div class='text-capitalize'>"+data.user.privacy+"</div></div><div class='d-flex gap-2'><div class='fw-bold' style='width: 90px;'>About</div><div></div></div><div>"+data.user.about+"</div></div></div></div>");
            }
        });
    }
    function deleteUser(id){
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
                    url:"{{ route('delete_user') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "{{ route('user_list_view') }}";
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
