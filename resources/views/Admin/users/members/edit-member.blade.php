@extends('Admin.layouts.main')
@push('page_title') Edit Member @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Edit Member Data</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('members_lists') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('update_member',['id'=>$member->id]) }}" method="POST">
                @csrf
                <div class="mb-4 w-100">
                    <label for="member_name" class="form-label">First Name</label>
                    <input type="text" id="member_name" class="form-control" value="{{ $member->member_name }}" name="member_name">
                </div>
                <div class="mb-4 w-100">
                    <label for="member_email" class="form-label">Email-ID</label>
                    <input type="text" id="member_email" class="form-control" value="{{ $member->member_email }}" name="member_email">
                </div>
                <div class="mb-4 w-100">
                    <label for="member_mobile" class="form-label">Mobile</label>
                    <input type="text" id="member_mobile" class="form-control" value="{{ $member->member_mobile }}" name="member_mobile">
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection
