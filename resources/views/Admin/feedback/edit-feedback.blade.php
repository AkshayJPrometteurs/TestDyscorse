@extends('Admin.layouts.main')
@push('page_title') App Feedback @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">App Feedback Data</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('feedback_list') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_feedback',['id'=>$feedback->id]) }}" method="POST">
                @csrf
                <div class="mb-4 w-100">
                    <label for="user_name" class="form-label">User Name</label>
                    <input type="text" id="user_name" class="form-control" value="{{ $feedback->first_name." ".$feedback->last_name }}" disabled>
                </div>
                <div class="mb-4 w-100">
                    <label for="feedback" class="form-label">Feedback</label>
                    <textarea name="feedback" id="feedback" class="form-control" cols="20" rows="10" name="feedback">{{ $feedback->feedback }}</textarea>
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
