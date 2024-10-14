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
        <!--User List Report-->
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
        <!--User List Report-->


        <!--User List Report-->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Summary Reports</h4>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <div class="row panel-body">
                        <div class="col-md-12 ">
                            <div class="panel panel-bordered ">
                                <div class="panel-body">
                                    <div class="row">

                                        <!--Summary Report-->
                                        <div class="card">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="financial_year" class="form-label">Financial Year</label>
                                                    <select name="financial_year" id="financial_year" class="form-control">
                                                        @foreach ($financialYears as $key => $financial_year)
                                                            <option value="{{ $key }}"
                                                                {{ $key == now()->year ? 'selected' : '' }}>
                                                                {{ $financial_year }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <table id="dynamicTable" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Description</th>
                                                                <th colspan="2">Today</th>
                                                                <th colspan="2">Current Week</th>
                                                                <th colspan="2">Current Month</th>
                                                                <th colspan="2">Financial Year</th>
                                                                <th colspan="2">Overall</th>
                                                            </tr>
                                                            <tr>
                                                                <th></th>
                                                                <th>FE</th>
                                                                <th>IV</th>
                                                                <th>FE</th>
                                                                <th>IV</th>
                                                                <th>FE</th>
                                                                <th>IV</th>
                                                                <th>FE</th>
                                                                <th>IV</th>
                                                                <th>FE</th>
                                                                <th>IV</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Data will be dynamically populated here -->
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                        <!--Summary Report-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--User List Report-->




    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')

    <script>
        $(document).ready(function() {
            $('#category, #user, #application_status','#financial_year').select2({
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
    <script>
        $(document).ready(function() {
            var table = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get-data-users') }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val(); // Add start date filter
                        d.end_date = $('#end_date').val(); // Add end date filter
                        d.category_id = $('#category').val(); // Add category filter
                        d.user_id = $('#user').val(); // Add user filter
                        d.application_status = $('#application_status')
                            .val(); // Existing application status filter
                        d.status_value = $('#status_value').val(); // Existing status value filter
                    },

                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'done_count',
                        name: 'done_count'
                    },
                    {
                        data: 'pending_count',
                        name: 'pending_count'
                    },
                    {
                        data: 'na_count',
                        name: 'na_count'
                    },
                    {
                        data: 'total_count',
                        name: 'total_count'
                    },
                    // Add more columns as needed
                ],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i data-feather="download"></i> Export',
                    className: 'btn btn-success btn-sm'
                }],
                drawCallback: function() {
                    feather.replace();
                }
            });


            // Re-draw the table when the filter form is submitted
            $('#filter-form').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission
                table.draw(); // Redraw the DataTable with new filters
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            fetchData();

            function fetchData() {
                let year = $('#financial_year').val();
                console.log(year);

                $.ajax({
                    url: 'admin/get-summry', // Replace with your route
                    method: 'GET',
                    data: {
                        year: year
                    },
                    success: function(response) {
                        let tbody = '';

                        // Loop through each data row
                        response.data.forEach(function(row) {
                            tbody += `
                        <tr>
                            <td>${row.description}</td>
                            <td>${row.today_fe}</td>
                            <td>${row.today_iv}</td>
                            <td>${row.week_fe}</td>
                            <td>${row.week_iv}</td>
                            <td>${row.month_fe}</td>
                            <td>${row.month_iv}</td>
                            <td>${row.fiscal_fe}</td>
                            <td>${row.fiscal_iv}</td>
                            <td>${row.overall_fe}</td>
                            <td>${row.overall_iv}</td>
                        </tr>
                    `;
                        });

                        $('#dynamicTable tbody').html(tbody);
                    },
                    error: function(error) {
                        console.error("There was an error fetching the data:", error);
                    }
                });
            }

            $('#financial_year').on('change', function() {
                fetchData();
            });
        });
    </script>

@endsection

@section('page-script')


@endsection
