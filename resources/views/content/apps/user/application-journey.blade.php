@extends('layouts/contentLayoutMaster')

@section('title', 'User List')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
    <!-- users list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-user-list">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Application Journey</h4>
                <a href="{{ route('app-users-list') }}" class="col-md-2 btn btn-primary">Users List</a>
            </div>

            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <div class="page-content read container-fluid">
                        <div class="panel panel-bordered">
                            <div class="panel-body" id="messageContainer" style="padding: 30px;">
                                @foreach ($application_statuses as $application_status)
                                    <div class="row mb-3">
                                        <h4 class="col-md-2">{{ $application_status->name }}</h4>
                                        <div class="col-md-3">
                                            <label for="status_value_{{ $application_status->id }}">Status Value:</label>
                                            <select name="status_value" id="status_value_{{ $application_status->id }}" class="form-select">
                                                @foreach ($status_value as $status)
                                                    <option value="{{ $status }}" @if ($application_status->status_value == $status) selected @endif>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="status_order_{{ $application_status->id }}">Order:</label>
                                            <input type="number" id="status_order_{{ $application_status->id }}" class="form-control" value="{{ $application_status->status_order ?? $application_status->order }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status_date_{{ $application_status->id }}">Date:</label>
                                            <input type="date" id="status_date_{{ $application_status->id }}" name="date" class="form-control" value="{{ $application_status->status_date ?? '' }}">
                                            <button class="btn btn-primary mt-2 submitStep" data-user-id="{{ $user->id }}" data-application-status-id="{{ $application_status->id }}">Save</button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="text-center">
                                    <button class="btn btn-primary submitAllStep">Save All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
@endsection

@section('page-script')
    <script>
$(document).ready(function() {
    function handleSubmitStep(userId, applicationStatusId) {
        let statusValue = $(`#status_value_${applicationStatusId}`).val();
        let statusOrder = $(`#status_order_${applicationStatusId}`).val();
        let statusDate = $(`#status_date_${applicationStatusId}`).val();

        if (!statusValue) {
            toastr.error('Please select a status value before submitting.');
            return;
        }
        if (!statusOrder) {
            toastr.error('Please write an order.');
            return;
        }

        $.ajax({
            url: '{{ route('users.store_status') }}', // Ensure this route is defined in your routes
            method: 'POST',
            data: {
                user_id: userId,
                application_status_id: applicationStatusId,
                status_value: statusValue,
                status_order: statusOrder,
                status_date: statusDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error('Error occurred while storing status!');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Something went wrong!');
                }
            }
        });
    }

    $(".submitStep").click(function() {
        let userId = $(this).data('user-id');
        let applicationStatusId = $(this).data('application-status-id');
        handleSubmitStep(userId, applicationStatusId);
    });

    $(".submitAllStep").click(function() {
        $(".submitStep").each(function() {
            let userId = $(this).data('user-id');
            let applicationStatusId = $(this).data('application-status-id');
            handleSubmitStep(userId, applicationStatusId);
        });
    });
});
</script>

@endsection
