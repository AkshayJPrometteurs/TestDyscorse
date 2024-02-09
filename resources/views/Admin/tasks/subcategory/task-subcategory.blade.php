@extends('Admin.layouts.main')
@push('page_title') Task Sub-Category Lists @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Task Sub-Category Lists</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('add_task_subcategory') }}"><button type="button" class="btn btn-success btn-sm small"><i class="fas fa-plus-circle"></i>&nbsp; Add New
                    </button></a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>User</th>
                            <th>Category Name</th>
                            <th>Sub-Category Image</th>
                            <th>Sub-Category Name</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subcategory as $data)
                            <tr>
                                <td class="text-center align-middle">{{$loop->iteration}}</td>
                                <td class="align-middle">
                                    @php
                                        if($data->user_id == 0){
                                            echo "Admin";
                                        }else{
                                            $user = DB::table('users')->where('id',$data->user_id)->first();
                                            echo isset($user->first_name)." ".isset($user->last_name);
                                        }
                                    @endphp
                                </td>
                                <td class="align-middle">{{ $data->task_category_name }}</td>
                                <td class="text-center align-middle">
                                    <img src="@if ($data->task_sub_category_image)
                                        {{ asset('assets/images/tasks')."/".$data->task_sub_category_image }}
                                    @else
                                        {{ asset('assets/images/user_avatar.png') }}
                                    @endif" alt="profile" style="height:40px;width:40px;border-radius:100%;">
                                </td>
                                <td class="align-middle">{{ $data->task_sub_category_name }}</td>
                                <td class="text-capitalize align-middle">{{ $data->task_sub_category_status }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('edit_task_subcategory',['slug'=>$data->task_sub_category_slug]) }}"><button data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" type="button" class="btn btn-warning" style="padding: 6px 10px"><i class="fas fa-edit"></i></button></a>
                                        <button href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="deleteTaskSubCategory({{ $data->id }})" type="button" class="btn btn-danger" style="padding: 6px 10px"><i class="fas fa-trash"></i></button>
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
    function deleteTaskSubCategory(id){
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
                    url:"{{ route('delete_task_subcategory') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "{{ route('task_subcategory_list') }}";
                    }
                });
            }
        })
    }
</script>
@endsection
