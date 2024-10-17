@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@section('content')
    <style>
        .delete-icon svg {
            transition: all 0.3s ease;
            width: 28px;
            height: 28px;
        }

        .delete-icon:hover svg {
            color: red;
        }
    </style>


    @if ($page_data['page_title'] == 'Other Stakeholders Add')
        <form action="{{ route('app-other-stakeholders-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-other-stakeholders-update', encrypt($otherStakeholders->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border reounded-3">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <div>
                            <a href="{{ route('app-our-services-list') }}" class="btn-sm btn btn-primary float-end">Other
                                Stakeholders
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border">
                                <h6>{{ $page_data['form_title'] }}</h6>
                            </div>
                            <div class="card-body mt-1">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-1">
                                        <label class="form-label" for="user_id-column">Client</label>
                                        <select id="user_id" class="form-control select2-ajax" name="user_id">
                                            <option value="">Select User</option>
                                            @if ($otherStakeholders && $otherStakeholders->user)
                                                <!-- Preload the selected user if editing an existing entry -->
                                                <option value="{{ $otherStakeholders->user_id }}" selected>
                                                    {{ $otherStakeholders->user->name }}
                                                </option>
                                            @endif
                                        </select>
                                        <span class="text-danger">
                                            @error('user_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <div class="col-md-6 col-sm-12 mb-1">
                                        <label class="form-label" for="attached_user_id-column">Advisor</label>
                                        <select id="attached_user_id" class="form-control" name="attached_user_id">
                                            <option value="">Select Advisor</option>
                                            @foreach ($advisors as $advisor)
                                                <option value="{{ $advisor->id }}"
                                                    {{ (old('attached_user_id') ?? ($otherStakeholders ? $otherStakeholders->attached_user_id : '')) == $advisor->id ? 'selected' : '' }}>
                                                    {{ $advisor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">
                                            @error('attached_user_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-1">
                                        <label class="form-label" for="role_name">Role Name</label>
                                        <select id="role_name" class="form-control" name="role_name">
                                            <option value="">Select Role Name</option>
                                            <option value="Application Advisor(FE)"
                                                {{ (old('role_name') ?? ($otherStakeholders ? $otherStakeholders->role_name : '')) == 'Application Advisor(FE)' ? 'selected' : '' }}>
                                                Application Advisor (FE)
                                            </option>
                                            <option value="Visa Advisor"
                                                {{ (old('role_name') ?? ($otherStakeholders ? $otherStakeholders->role_name : '')) == 'Visa Advisor' ? 'selected' : '' }}>
                                                Visa Advisor
                                            </option>
                                        </select>
                                        <span class="text-danger">
                                            @error('role_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>





                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>
    </section>
    </form>



@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
@endsection

@section('page-script')
    <script>
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
                    // Update the URL to match your new route
                    window.location.href = '/app/sliders/' + id; // Adjusted to use the correct route
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
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Client Type",
                allowClear: true
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2 with AJAX loading
            $('#user_id').select2({
                placeholder: "Select User",
                allowClear: true,
                ajax: {
                    url: '#', // URL for the AJAX request
                    dataType: 'json',
                    delay: 250, // Delay to wait before triggering AJAX call
                    data: function(params) {
                        return {
                            search: params.term, // Search term entered by the user
                            page: params.page || 1 // Current page number
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data, // Format data for select2
                            pagination: {
                                more: data.pagination.more // Check if more pages are available
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1, // Minimum characters required before triggering search
                templateResult: formatUser, // Template for dropdown item
                templateSelection: formatUserSelection // Template for selected item
            });

            // Format the display of the user in the dropdown
            function formatUser(user) {
                if (user.loading) return user.text;
                return user.name;
            }

            // Format the display of the selected item
            function formatUserSelection(user) {
                return user.name || user.text;
            }
        });
    </script>
@endsection
