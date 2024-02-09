@extends('Admin.layouts.main')
@push('page_title') Emails Sends @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Email Format</h5>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('user_list_for_emails') }}"><button type="button" class="btn btn-primary btn-sm small"><i class="fas fa-arrow-circle-left"></i>&nbsp; Back To List
                    </button></a>
                </div>
            </div>
            <form action="{{ route('send_email_with_data') }}" method="POST">
                @csrf
                <div class="mb-4 w-100">
                    <label for="email_to" class="form-label">To</label>
                    <input type="text" id="email_to" class="form-control" name="email_to">
                </div>
                <div class="mb-4 w-100">
                    <label for="email_subject" class="form-label">Subject</label>
                    <input type="text" id="email_subject" class="form-control" name="email_subject">
                </div>
                <div class="mb-4 w-100">
                    <label for="email_body" class="form-label">Body</label>
                    <textarea id="email_body" rows="9" class="form-control ckeditor" name="email_body"></textarea>
                </div>
                <button class="btn btn-primary w-100 mt-2">Send</button>
            </form>
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#email_to').val(sessionStorage.getItem('selected_items'));
    });
</script>
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
@endsection
