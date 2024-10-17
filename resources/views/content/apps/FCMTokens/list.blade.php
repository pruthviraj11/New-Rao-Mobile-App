@extends('layouts/contentLayoutMaster')

@section('title', 'FCM Tokens')

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
                <h4 class="card-title">FCM Tokens List</h4>
                {{-- <a href="{{ route('app-advisor-add') }}" class="col-md-2 btn btn-primary">Add Advisor</a> --}}
            </div>
            <div class="card-body border-bottom">
                <div class="card  mt-1 border rounded-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="user-list-table table dt-responsive" id="fcmTokens-table">
                                <thead>
                                    <tr>
                                        {{-- <th>Actions</th> --}}
                                        {{-- <th>Token</th> --}}
                                        <th>Device Id</th>
                                        <th>Created At</th>
                                        {{-- <th>Status</th> --}}
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
            $('#fcmTokens-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('app-fcm-tokens-get-all') }}",
                columns: [
                    // {
                    //     data: 'token',
                    //     name: 'token',
                    //     className: 'text-left',
                    //     render: function(data) {
                    //         return data ? data : '-';
                    //     }
                    // },
                    {
                        data: 'device_id',
                        name: 'device_id',
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
