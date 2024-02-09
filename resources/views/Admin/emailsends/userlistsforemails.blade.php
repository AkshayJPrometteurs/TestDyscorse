@extends('Admin.layouts.main')
@push('page_title') Emails Sends @endpush
@section('contents')
<section class="section d-block position-relative w-100">
    <div class="card">
        <div class="card-body pt-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="mb-0 text-capitalize">User Lists For Email Sends</h5>
                <button id="send_mail_users" class="btn btn-primary"><i class="fas fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;Send Mail Now</button>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>
                                <input class="form-check-input" type="checkbox" id="selectAll" name="selectAll" value="1">
                            </th>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Email-ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $data)
                            <tr>
                                <td class="align-middle"><input class="form-check-input single-check-box" type="checkbox" name="send_email" id="send_email" value="{{ $data->email }}"></td>
                                <td class="align-middle">{{$loop->iteration}}</td>
                                <td class="align-middle">{{ $data->first_name." ".$data->last_name }}</td>
                                <td class="align-middle text-capitalize">{{ $data->gender }}</td>
                                <td class="align-middle">{{ $data->age }} Yrs.</td>
                                <td class="align-middle user_email">{{ $data->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        var selectedItems = [];
        // Select All Checkbox
        $("#selectAll").click(function() {
            if ($(this).prop("checked")) {
                $(".single-check-box").prop("checked", true);
                $(".single-check-box").each(function() {
                    var itemId = $(this).val();
                    if (selectedItems.indexOf(itemId) === -1){selectedItems.push(itemId);}
                });
            } else {
                $(".single-check-box").prop("checked", false);
                $(".single-check-box").each(function() {
                    var itemId = $(this).val();
                    var index = selectedItems.indexOf(itemId);
                    if (index !== -1){selectedItems.splice(index, 1);}
                });
            }
        });
        // Individual Checkboxes
        $(".single-check-box").click(function() {
            var itemId = $(this).val();
            if($(this).prop("checked")){
                selectedItems.push(itemId);
            }else{
                var index = selectedItems.indexOf(itemId);
                if (index !== -1){
                    selectedItems.splice(index, 1);
                }
            }
        });

        $('#send_mail_users').click(function(){
            var selectedItemsString = selectedItems.join(',');
            if(selectedItemsString){
                sessionStorage.setItem('selected_items', selectedItemsString);
                window.location.href = "{{ route('sent_now_emails') }}";
            }else{
                alert('Select at least one record.');
            }
        });
    });
</script>
@endsection
