@extends('layouts/contentLayoutMaster')

@section('title', 'Home Service')

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
                <h4 class="card-title">Home Service List</h4>
                <a href="{{ route('app-home-services-add') }}" class="btn-sm btn btn-primary">Add Home Service</a>
            </div>
            <div class="card-body border-bottom">
                <div class="card mt-1">
                    <div class="card-body border">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="user-list-table table dt-responsive" id="home-services-table">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Title</th>
                                        <th>Logo</th>
                                        <th>Image</th>
                                        <th>Status</th>
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
            $('#home-services-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('app-home-services-get-all') }}",
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-left',
                    },
                    {
                        data: 'title',
                        name: 'title',
                        className: 'text-left',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                        className: 'text-left',
                        render: function(data) {
                            // Construct the file source path
                            const imgSrc = data ? '{{ asset('storage/') }}/' + data :
                                '{{ asset('default/default.jpg') }}';

                            // Create an file element with an onerror handler to fall back to the default image
                            return '<img src="' + imgSrc +
                                '" alt="Service icon Image" style="width: auto; height: 75px;" onerror="this.onerror=null; this.src=\'{{ asset('default/default.jpg') }}\';">';
                        }
                    },
                    {
                        data: 'service_image',
                        name: 'service_image',
                        className: 'text-left',
                        render: function(data) {
                            // Construct the file source path
                            const imgSrc = data ? '{{ asset('storage/') }}/' + data :
                                '{{ asset('default/default.jpg') }}';

                            // Create an file element with an onerror handler to fall back to the default image
                            return '<img src="' + imgSrc +
                                '" alt="service_image Image" style="width: auto; height: 75px;" onerror="this.onerror=null; this.src=\'{{ asset('default/default.jpg') }}\';">';
                        }
                    },

                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            if (data == '1') {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-danger">Inactive</span>';
                            }
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
                    window.location.href = '/app/home-services/destroy/' + id;
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
