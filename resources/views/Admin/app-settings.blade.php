@extends('Admin.layouts.main')
@push('page_title') App Settings @endpush
@section('contents')
<style>
    .ck .ck-editor__main {
        height: 350px;
        overflow-y: auto;
    }
</style>
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="col-auto">
                    <h5 class="mb-0 text-capitalize">Mobile App Settings</h5>
                </div>
            </div>
            <form action="{{ route('app_settings_save') }}" method="POST">
                @csrf
                <div class="mb-4 w-100">
                    <label for="terms_and_conditions" class="form-label fw-bold">Terms & Conditions</label>
                    <textarea name="terms_and_conditions" id="terms_and_conditions" style="height:300px;" class="form-control @error('terms_and_conditions') border border-danger @enderror" rows="2">{!! $data->terms_and_conditions ?? '' !!}</textarea>
                    @error('terms_and_conditions') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4 w-100">
                    <label for="privacy_and_policy" class="form-label fw-bold">Privacy & Policy</label>
                    <textarea name="privacy_and_policy" id="privacy_and_policy" style="height:300px;" class="form-control @error('privacy_and_policy') border border-danger @enderror" rows="2">{!! $data->privacy_and_policy ?? '' !!}</textarea>
                    @error('privacy_and_policy') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4 w-100">
                    <label for="help" class="form-label fw-bold">Help</label>
                    <textarea name="help" id="help" style="height:300px;" class="form-control @error('help') border border-danger @enderror" rows="2">{!! $data->help ?? '' !!}</textarea>
                    @error('help') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        ClassicEditor.create(document.querySelector('#terms_and_conditions'))
        .then(editor => {
            editor.ui.view.editable.setStyle('min-height', '300px');
            editor.ui.view.editable.on('render', () => { editor.ui.view.editable.setStyle('min-height','auto');});
        });
        ClassicEditor.create(document.querySelector('#privacy_and_policy'))
        .then(editor => {
            editor.ui.view.editable.setStyle('min-height', '300px');
            editor.ui.view.editable.on('render', () => { editor.ui.view.editable.setStyle('min-height','auto');});
        });
        ClassicEditor.create(document.querySelector('#help'))
        .then(editor => {
            editor.ui.view.editable.setStyle('min-height', '300px');
            editor.ui.view.editable.on('render', () => { editor.ui.view.editable.setStyle('min-height','auto');});
        });
    });
</script>
@endsection
