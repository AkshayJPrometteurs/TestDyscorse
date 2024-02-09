@extends('Admin.layouts.main')
@push('page_title') Edit Splash Screen Question @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Edit Question</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('splash_screen_questions') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_question',['id'=>$ssq->id]) }}" method="POST">
                @csrf
                <div class="form-floating">
                    <textarea class="form-control @error('questions') border border-danger @enderror" placeholder="Enter your question" id="question" name="questions" style="height: 150px">{{ $ssq->questions }}</textarea>
                    <label for="question">Question</label>
                    @error('questions') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
