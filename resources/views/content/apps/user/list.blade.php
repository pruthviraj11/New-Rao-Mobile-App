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
        <div class="row">
            <div class="col-lg-12 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $data['filtered_user_count'] }}</h3>
                            <span>Total Users</span>
                        </div>
                        <div class="avatar bg-light-primary p-50">
                            <span class="avatar-content">
                                <i data-feather="user" class="font-medium-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- list and filter start -->
        <div class="card">
            <div class="card-header border rounded-3">
                <h4 class="card-title">Users list</h4>
                <div>
                    <a href="{{ route('app-users-add') }}" class="btn-sm btn btn-primary">Add Users
                    </a>

                    <button id="delete-selected" class="btn btn-danger btn-sm">Bulk Delete</button>
                    <a href="{{ url('storage/sampleExcel/MobileAppExcel.xlsx') }}" class="btn btn-success btn-sm" download>
                        <i class="fa fa-download"></i> Download Sample File
                    </a>
                </div>

            </div>

            <div class="card-body border-bottom">
                <div class="card border mt-1">
                    <div class="card-body ">
                        <div class="card-datatable table-responsive pt-0">

                            <table class="user-list-table table" id="users-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all" /></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>App No</th>
                                        <th>is Download</th>
                                        <th>Download Date</th>
                                        <th>Reg Imm No</th>
                                        <th>Advisor</th>
                                        <th>User Categories</th> <!-- New column -->
                                        <th>Role</th> <!-- New column -->
                                        <th>Phone Number</th> <!-- New column -->
                                        <th>Passport Expiry</th> <!-- New column -->
                                        <th>Imm No</th> <!-- Existing column -->
                                        <th>Test Expiry</th> <!-- New column -->
                                        <th>Reporting To</th> <!-- New column -->
                                        <th>Date of Birth</th> <!-- New column -->
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- list and filter end -->
    </section>
    <div class="modal fade" id="blockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmBlockButton" data-id="">
                        <!-- Text will be set dynamically -->
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Users Sales</h5>
                    <button type="button" onclick="closeImportModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Import Form -->
                    <form action="{{ route('users.import.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="import_file">Import File</label>
                            <input type="file" class="form-control" id="import_file" name="import_file" required
                                accept=".xlsx">
                        </div>


                        <button type="submit" class="btn btn-primary mt-2">Import</button>
                        <button type="button" class="btn btn-danger mt-2" onclick="closeImportModal()">Close</button>
                    </form>


                </div>
            </div>
        </div>
    </div>


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
    @yield('links')
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('#advisor_assign_to').select2({
                placeholder: 'Select Advisor',
                allowClear: true
            });
        });
    </script>
    <script>
        function showImportModal() {
            $('#importModal').modal('show');
        }
    </script>
    <script>
        function closeImportModal() {
            $('#importModal').modal('hide');
        }
    </script>
    <script>
        $(document).ready(function() {
            let selectedIds = [];

            $('#users-table').DataTable({
                dom: 'lBfrtip',
                processing: true,
                serverSide: true,
                searching: true,
                buttons: [{
                    extend: 'excel',
                    text: '  <i class="ficon" data-feather="file-text"></i> Excel',
                    title: '',
                    filename: 'Users List',
                    action: newexportaction,
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        modifier: {
                            length: -1
                        },
                        columns: [0, 1]
                    }
                }, {
                    text: '<i class="ficon" data-feather="upload"></i> Import',
                    className: 'btn btn-success btn-sm',
                    action: function() {
                        showImportModal();
                    }
                }, ],
                "lengthMenu": [10, 25, 50, 100, 200],
                ajax: "{{ route('app-users-get-all') }}",
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="record-checkbox" data-id="${row.id}" />`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent: '-',
                    },
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent: '-',
                    },
                    {
                        data: 'app_no',
                        name: 'app_no',
                        defaultContent: '-',
                    },
                    {
                        data: 'is_download',
                        name: 'is_download',
                        defaultContent: '-',
                        render: function(data, type, row) { 
                            return data === 0 ? 'No' : 'Yes'; // Ternary operator
                        }
                    },

                    {
                        data: 'download_date',
                        name: 'download_date',
                        defaultContent: '-',
                    },
                    {
                        data: 'imm_no',
                        name: 'imm_no',
                        defaultContent: '-',
                    },
                    {
                        data: 'Advisor',
                        name: 'Advisor',
                        defaultContent: '-',
                    },
                    {
                        data: 'user_categories', // New column
                        name: 'user_categories',
                        defaultContent: '-',
                    },
                    {
                        data: 'role', // New column
                        name: 'role',
                        defaultContent: '-',
                    },
                    {
                        data: 'phone_number', // New column
                        name: 'phone_number',
                        defaultContent: '-',
                    },
                    {
                        data: 'imm_no', // New column
                        name: 'imm_no',
                        defaultContent: '-',
                    },
                    {
                        data: 'passport_expiry', // New column
                        name: 'passport_expiry',
                        defaultContent: '-',
                    },
                    {
                        data: 'test_expiry', // New column
                        name: 'test_expiry',
                        defaultContent: '-',
                    },
                    {
                        data: 'reporting_to', // New column
                        name: 'reporting_to',
                        defaultContent: '-',
                    },
                    {
                        data: 'dob', // New column
                        name: 'dob',
                        defaultContent: '-',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    feather.replace();
                }
            });
            // Select all checkboxes
            $('#select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.record-checkbox').prop('checked', isChecked);
                updateSelectedIds();
            });

            // Update selected IDs
            $(document).on('change', '.record-checkbox', function() {
                updateSelectedIds();
            });

            function updateSelectedIds() {
                selectedIds = $('.record-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();
            }
            $('#delete-selected').on('click', function() {
                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No records selected',
                        text: 'Please select at least one record to delete.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('app-users-bulk-destroy') }}', // Update to your delete endpoint
                            method: 'POST',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}' // Include CSRF token if necessary
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Selected records have been deleted.',
                                });
                                $('#users-table').DataTable().ajax
                                    .reload(); // Reload the table
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong while deleting records.',
                                });
                            }
                        });
                    }
                });
            });
        });


        function newexportaction(e, dt, button, config) {
            var self = this;
            var oldStart = dt.settings()[0]._iDisplayStart;
            dt.one('preXhr', function(e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = 2147483647;
                dt.one('preDraw', function(e, settings) {
                    // Call the original action function
                    if (button[0].className.indexOf('buttons-copy') >= 0) {
                        $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button,
                            config);
                    } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                        $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt,
                                button, config) :
                            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt,
                                button, config);
                    } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                        $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button,
                                config) :
                            $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button,
                                config);
                    } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                        $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button,
                                config) :
                            $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button,
                                config);
                    } else if (button[0].className.indexOf('buttons-print') >= 0) {
                        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                    }
                    dt.one('preXhr', function(e, s, data) {
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });
                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    setTimeout(dt.ajax.reload, 0);
                    // Prevent rendering of the full data to the DOM
                    return false;
                });
            });
            // Requery the server with the new one-time export settings
            dt.ajax.reload();
        }

        $(document).on("click", ".confirm-delete", function(e) {
            e.preventDefault();
            var id = $(this).data("idos");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '/app/users/destroy/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Record has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary record is safe :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.BlockButton', function(event) {
            event.preventDefault();
            var blockId = $(this).data("id");
            var isBlocked = $(this).data("is-blocked");

            // Set the modal title and button text based on block status
            if (isBlocked) {
                $('#modalTitle').text('Are you sure you want to Unblock this user?');
                $('#confirmBlockButton').text('Unblock').removeClass('btn-danger').addClass('btn-warning');
            } else {
                $('#modalTitle').text('Are you sure you want to Block this user?');
                $('#confirmBlockButton').text('Block').removeClass('btn-warning').addClass('btn-danger');
            }

            // Set the ID in the modal button
            $('#confirmBlockButton').data("id", blockId);

            // Show the modal
            $('#blockModal').modal('show');
        });

        // Confirm block/unblock action
        $(document).on('click', '#confirmBlockButton', function() {
            var blockId = $(this).data("id");
            var action = $(this).text() === 'Unblock' ? 'unblock' : 'block';

            $.ajax({
                url: '{{ route('block.users') }}', // Use the route helper
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add CSRF token
                    action: action,
                    blockId: blockId
                },
                success: function(response) {
                    $('#blockModal').modal('hide');
                    toastr.success(response.message); // Show success message
                    location.reload(); // Refresh the page to reflect changes
                },
                error: function(xhr) {
                    console.error(xhr);
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON
                        .error : 'An error occurred. Please try again.';
                    toastr.error(errorMessage); // Show error message
                }
            });



        });

        // Explicitly hide modal on close button click
        $(document).on('click', '.close, .btn-default', function() {
            $('#blockModal').modal('hide');
        });
    </script>
    <script>
        @if (session('message'))
            toastr.success("{{ session('message') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

    {{-- Page js files --}}
@endsection
