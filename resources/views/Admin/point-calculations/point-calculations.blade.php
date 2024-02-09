@extends('Admin.layouts.main')
@push('page_title') Point Calculations @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="row align-items-center justify-content-between mb-4">
                <div class="col-12 col-md-auto">
                    <h5 class="mb-0 text-capitalize">Point Calculations Lists</h5>
                </div>
            </div>
            <form action="{{ route('point_calculation_store') }}" method="POST">
                @csrf
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="task_completion_point" class="form-label mb-0">Task Completion Point</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Enter award points for each task or project the user completes."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="task_completion_point" class="form-control @error('task_completion') border border-danger @enderror" name="task_completion" value="@isset($points->task_completion){{ $points->task_completion }}@else{{ old('task_completion')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="max_streak" class="form-label mb-0">Max Streak</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Provide bonus points for consecutive days of using the app or maintaining a task completion streak."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="max_streak" class="form-control @error('max_streak') border border-danger @enderror" name="max_streak" value="@isset($points->max_streak){{ $points->max_streak }}@else{{ old('max_streak')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="se_assigning_task_to_family_member" class="form-label mb-0">Social Engagement Assigning Tasks</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Give points when users collaborate or share tasks with teammates or friends."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="se_assigning_task_to_family_member" class="form-control @error('se_assigning_task_to_family_member') border border-danger @enderror" name="se_assigning_task_to_family_member" value="@isset($points->se_assigning_task_to_family_member){{ $points->se_assigning_task_to_family_member }}@else{{ old('se_assigning_task_to_family_member')}}@endisset">
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-4">
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="se_follow" class="form-label mb-0">Social Engagement Follow</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Give points when users collaborate or share tasks with teammates or friends."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="se_follow" class="form-control @error('se_follow') border border-danger @enderror" name="se_follow" value="@isset($points->se_follow){{ $points->se_follow }}@else{{ old('se_follow')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="feedback" class="form-label mb-0">Feedback</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Award points to users who provide feedback or report bugs, helping you improve the app."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="feedback" class="form-control @error('feedback') border border-danger @enderror" name="feedback" value="@isset($points->feedback){{ $points->feedback }}@else{{ old('feedback')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="app_sharing" class="form-label mb-0">App Sharing</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Provide points when users recommend the app to friends or colleagues and they sign up."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="app_sharing" class="form-control @error('app_sharing') border border-danger @enderror" name="app_sharing" value="@isset($points->app_sharing){{ $points->app_sharing }}@else{{ old('app_sharing')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="reflection_and_review" class="form-label mb-0">Reflection and Review</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="Encourage users to review and reflect on their weekly or monthly progress and award points for it."><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="reflection_and_review" class="form-control @error('reflection_and_review') border border-danger @enderror" name="reflection_and_review" value="@isset($points->reflection_and_review){{ $points->reflection_and_review }}@else{{ old('reflection_and_review')}}@endisset">
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2">
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="total_category_completion" class="form-label mb-0">Total Category Completion</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="If the user has tasks in x number of categories on a given day and if they finish all tasks on a given day"><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="total_category_completion" class="form-control @error('total_category_completion') border border-danger @enderror" name="total_category_completion" value="@isset($points->total_category_completion){{ $points->total_category_completion }}@else{{ old('total_category_completion')}}@endisset">
                    </div>
                    <div class="col mb-4">
                        <div class="d-flex gap-3 align-items-center justify-content-between mb-2">
                            <label for="category_completion" class="form-label mb-0">Category Completion Points</label>
                            <span class="d-inline-block me-1" tabindex="0" data-bs-toggle="tooltip" title="If the user has tasks in x number of categories on a given day and if they finish all tasks on a given day"><i class="bi bi-info-circle-fill"></i></span>
                        </div>
                        <input type="number" min="0" id="category_completion" class="form-control @error('category_completion') border border-danger @enderror" name="category_completion" value="@isset($points->category_completion){{ $points->category_completion }}@else{{ old('category_completion')}}@endisset">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h5>Milestone Points</h5>
                    <button type="button" id="add-button" class="btn btn-info">Add Rows</button>
                </div>
                <div id="input-container">
                    @forelse ($milestone as $data)
                        <div class="d-flex gap-3 mb-3">
                            <input type="number" min="0" name="milestone_start[]" class="form-control" value="{{ $data->milestone_start }}">
                            <input type="number" min="0" name="milestone_end[]" class="form-control" value="{{ $data->milestone_end }}">
                            <input type="number" min="0" name="milestone_points[]" class="form-control" value="{{ $data->milestone_points }}">
                            <a href="#" onclick="deleteMilestone({{ $data->id }})" class="remove-button btn btn-danger">Remove</a>
                        </div>
                    @empty
                        <div class="d-flex gap-3 mb-3">
                            <input type="number" min="0" name="milestone_start[]" class="form-control">
                            <input type="number" min="0" name="milestone_end[]" class="form-control">
                            <input type="number" min="0" name="milestone_points[]" class="form-control">
                            <button type="button" class="remove-button btn btn-danger">Remove</button>
                        </div>
                    @endforelse
                </div>
                <button class="btn btn-primary w-100 mt-2">Save</button>
            </form>
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script>
    $(document).ready(function () {
        const inputContainer = $('#input-container');
        const addButton = $('#add-button');
        addButton.on('click', function () {
            const inputRow = $('<div class="d-flex gap-3 mb-3"></div>');
            const inputField = $('<input type="number" min="0" name="milestone_start[]" class="form-control"><input type="number" min="0" name="milestone_end[]" class="form-control"><input type="number" min="0" name="milestone_points[]" class="form-control">');
            const removeButton = $('<button type="button" class="remove-button btn btn-danger">Remove</button>');
            removeButton.on('click', function () {inputRow.remove();});
            inputRow.append(inputField);
            inputRow.append(removeButton);
            inputContainer.append(inputRow);
        });
    });
</script>
<script>
    function deleteMilestone(id){
        Swal.fire({
        title: 'Are you sure to delete?',
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
                    url:"{{ route('delete_milestone') }}",
                    data:{"_token": "{{ csrf_token() }}","id": id},
                    success:function(data) {
                        window.location.href = "{{ route('point_calculation_view') }}";
                    }
                });
            }
        })
    }
</script>
@endsection
