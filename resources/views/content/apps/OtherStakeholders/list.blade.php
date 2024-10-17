@extends('layouts/contentLayoutMaster')

@section('title', 'Other Stakeholders')

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
            <div class="card-header border rounded-3">
                <h4 class="card-title">Other Stakeholders List</h4>
                <div>
                    <a href="{{ route('app-other-stakeholders-add') }}" class="btn-sm btn btn-primary">Add Other
                        Stakeholders</a>
                    <button id="delete-selected" class="btn btn-danger btn-sm">Bulk Delete</button>
                </div>
            </div>
            <div class="card-body border-bottom">
                <div class="card border mt-1">
                
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="user-list-table table dt-responsive" id="other-stakeholders-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all" /></th>
                                        <th>Actions</th>
                                        <th>Client</th>
                                        <th>Advisor</th>
                                        <th>Role Name</th>
                                        <th>Created At</th>
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
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}


@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            let selectedIds = [];
            $('#other-stakeholders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('app-other-stakeholders-get-all') }}",
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="record-checkbox" data-id="${row.id}" />`;
                        }
                    }, {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-left',
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'attached_user_id',
                        name: 'attached_user_id',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'role_name',
                        name: 'role_name',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-left',
                        render: function(data) {
                            if (data) {
                                // Create a new Date object
                                var date = new Date(data);

                                // Format the date as "Month day, year hours:minutes"
                                var formattedDate = date.toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });

                                return formattedDate;
                            }
                            return '-';
                        }
                    },

                ],
                order: [
                    [0, 'desc']
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

            // Delete selected records
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
                            url: '{{ route('app-other-stakeholders-bluk-destroy') }}', // Update to your delete endpoint
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
                                $('#other-stakeholders-table').DataTable().ajax
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
                    window.location.href = '/app/our-services/destroy/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Your file has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary file is safe :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });
    </script>

    {{-- Page js files --}}
@endsection
