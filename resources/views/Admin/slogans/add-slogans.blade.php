@extends('Admin.layouts.main')
@push('page_title') Add Slogan @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Add New Slogan</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('slogan_list') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('save_slogan') }}" method="POST">
                @csrf
                <div class="form-floating">
                    <input class="form-control @error('slogan_name') border border-danger @enderror" placeholder="Enter your slogan" id="slogan_name" name="slogan_name">
                    <label for="slogan">Slogan Name</label>
                    @error('slogan_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
