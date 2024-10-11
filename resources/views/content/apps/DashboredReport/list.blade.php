@extends('layouts/contentLayoutMaster')

@section('title', 'Dashbored Reports')

@section('vendor-style')
    {{-- Page Css files --}}
@endsection
<style>
    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }
</style>
@section('page-style')
    {{-- Page Css files --}}
@endsection

@section('content')
    <!-- users list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-user-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Dashbored Reports</h4>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <div class="row panel-body">
                        <div class="col-md-12 ">
                            <div class="panel panel-bordered ">
                                <div class="panel-body">
                                    <div class="row">

                                        <div class="card">
                                            <div class="m-5 padding_useer_table">
                                                <form id="filter-form">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="start_date">Start Date</label>
                                                                <input type="date" id="start_date" name="start_date"
                                                                    class="form-control">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="end_date">End Date</label>
                                                                <input type="date" id="end_date" name="end_date"
                                                                    class="form-control">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="category">Category</label>
                                                                <select name="category" id="category" class="form-control">
                                                                    <option value="">Select Category</option>
                                                                    @foreach ($categorys as $cat)
                                                                        <option value="{{ $cat->id }}">
                                                                            {{ $cat->displayname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="user">User</label>
                                                                <select id="user" name="user" class="form-control">
                                                                    <option value="">Select User</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mt-2">
                                                            <div class="form-group">
                                                                <label for="application_status">Application Status</label>
                                                                <select id="application_status" name="application_status"
                                                                    class="form-control">
                                                                    <option value="">Select Application Status
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>



                                                        <div class="col-md-3 mt-2">
                                                            <div class="form-group"><br>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Filter</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div class="table-responsive mt-3">
                                                    <table id="user-table" class="table table-hover ">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Done</th>
                                                                <th>Pending</th>
                                                                <th>N/A</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

    <script>
        $(document).ready(function() {
            $('#category, #user, #application_status').select2({
                placeholder: 'Select an option',
                allowClear: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#category').change(function() {
                var categoryId = $(this).val();

                // Clear previous options
                $('#user').empty().append('<option value="">Select User</option>');
                $('#application_status').empty().append(
                    '<option value="">Select Application Status</option>');

                if (categoryId) {
                    // Fetch users
                    $.ajax({
                        url: '{{ route('getUsers.list') }}', // Use named route
                        type: 'GET',
                        data: {
                            categoryId: categoryId
                        }, // Pass category ID here
                        success: function(data) {
                            if (data.message) {
                                alert(data.message);
                            } else {
                                $.each(data, function(index, user) {
                                    $('#user').append('<option value="' + user.id +
                                        '">' + user.name + '</option>');
                                });
                            }
                        },

                    });

                    // Fetch application statuses
                    $.ajax({
                        url: '{{ route('getApplicationStatuses.list') }}', // Use named route
                        type: 'GET',
                        data: {
                            categoryId: categoryId
                        }, // Pass category ID here
                        success: function(data) {
                            if (data.message) {
                                alert(data.message);
                            } else {
                                $.each(data, function(index, status) {
                                    $('#application_status').append('<option value="' +
                                        status.id + '">' + status.name + '</option>'
                                    );
                                });
                            }
                        },

                    });
                }
            });
        });
    </script>


@endsection

@section('page-script')


@endsection
