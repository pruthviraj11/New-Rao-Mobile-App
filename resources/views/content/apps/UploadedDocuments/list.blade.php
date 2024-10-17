@extends('layouts/contentLayoutMaster')

@section('title', 'Uploaded Documents')

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
                <h4 class="card-title">Uploaded Documents List</h4>
                {{-- <a href="{{ route('app-advisor-add') }}" class="col-md-2 btn btn-primary">Add Advisor</a> --}}
            </div>
            <div class="card-body border-bottom">
                <div class="card  mt-1 border rounded-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="user-list-table table dt-responsive" id="uploaded-documents-table">
                                <thead>
                                    <tr>
                                        <th>Client Name</th>
                                        <th>Document Name</th>
                                        <th>Document Type</th>
                                        <th>Uploaded On</th>
                                        <th>Download File</th>

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
            $('#uploaded-documents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('app-uploaded-documents-get-all') }}",
                columns: [{
                        data: 'user_id',
                        name: 'user_id',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'file_name',
                        name: 'file_name',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'document_type',
                        name: 'document_type',
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
                    {
                        data: 'file_name', // Define the download link data
                        name: 'file_name',
                        className: 'text-left',
                        render: function(data, type, row) {
                            if (data) {
                                // Generating the full URL using the storage path for public files
                                var downloadUrl = "{{ url('storage') }}" + '/' + data;
                                return '<a href="' + downloadUrl +
                                    '" target="_blank" class="btn btn-sm btn-primary">Download</a>';
                            }
                            return '-';
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                drawCallback: function() {
                    feather.replace();
                }
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
                    window.location.href = '/app/advisor/destroy/' + id;
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
