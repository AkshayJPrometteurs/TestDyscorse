@extends('Admin.layouts.main')
@push('page_title') Edit Task Category @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Edit Task Category</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('task_subcategory_list') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_task_subcategory',['id'=>$subcategory->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4 w-100">
                    <label for="task_category_name" class="form-label">Task Category Name</label>
                    <select class="form-control @error('task_category_name') border border-danger @enderror" id="task_category_name" name="task_category_name">
                        <option value="">Choose Task Category</option>
                        @foreach ($category as $data)
                            <option value="{{ $data->id }}" @if ($data->task_category_slug == $subcategory->task_category_slug) selected @endif>{{ $data->task_category_name }}</option>
                        @endforeach
                    </select>
                    @error('task_category_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4 w-100">
                    <label for="task_sub_category_name" class="form-label">Task Sub-Category Name</label>
                    <input type="text" id="task_sub_category_name" class="form-control @error('task_sub_category_name') border border-danger @enderror" name="task_sub_category_name" value="{{ $subcategory->task_sub_category_name }}">
                    @error('task_sub_category_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                @if ($subcategory->task_sub_category_image)
                    <img src="{{ asset('assets/images/tasks')."/".$subcategory->task_sub_category_image }}" alt="sub-cate" style="max-width:150px">
                @endif
                <div class="mb-4 w-100 mt-3">
                    <label for="task_sub_category_image" class="form-label">Task Sub-Category Image</label>
                    <input type="file" id="task_sub_category_image" class="form-control @error('task_sub_category_image') border border-danger @enderror" name="task_sub_category_image">
                    @error('task_sub_category_image') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
