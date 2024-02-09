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
                    <a href="{{ route('task_category_list') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_task_category',['id'=>$category->id]) }}" method="POST">
                @csrf
                <div class="mb-4 w-100">
                    <label for="task_category_name" class="form-label">Task Category Name</label>
                    <input type="text" id="task_category_name" class="form-control @error('task_category_name') border border-danger @enderror" name="task_category_name" value="{{ $category->task_category_name }}">
                    @error('task_category_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
