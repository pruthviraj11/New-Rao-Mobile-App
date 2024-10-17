@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection

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


    @if ($page_data['page_title'] == 'Our Services Add')
        <form action="{{ route('app-our-services-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-our-services-update', encrypt($ourServices->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border rounded-3">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <div><a href="{{ route('app-our-services-list') }}" class="btn-sm btn btn-primary float-end">Our
                                Services List
                            </a></div>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border">
                                <h6>{{ $page_data['form_title'] }}</h6>
                            </div>
                            <div class="card-body mt-1">
                                <div class="row">

                                    <!-- File Upload Field -->
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="file">Featured Image</label>
                                        <input type="file" id="file" class="form-control" name="file">
                                        <span class="text-danger">
                                            @error('file')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <!-- Title Field -->
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="title">Title</label>
                                        <input type="text" id="title" class="form-control" placeholder="Title"
                                            name="title"
                                            value="{{ old('title') ?? ($ourServices ? $ourServices->title : '') }}">
                                        <span class="text-danger">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    {{-- <!-- Short Description Field -->
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="short_description">Short Description</label>
                                <textarea id="short_description" class="form-control" placeholder="Short Description" name="short_description">{{ old('short_description') ?? ($ourServices ? $ourServices->short_description : '') }}</textarea>
                                <span class="text-danger">
                                    @error('short_description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div> --}}

                                    <!-- Long Description Field -->
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="short_description">Description</label>
                                        <textarea id="short_description" class="form-control" placeholder="Long Description" name="short_description">{{ old('short_description') ?? ($ourServices ? $ourServices->short_description : '') }}</textarea>
                                        <span class="text-danger">
                                            @error('short_description')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="contact_no">Contact Number</label>
                                        <input type="text" id="contact_no" class="form-control"
                                            placeholder="Contact Number" name="contact_no"
                                            value="{{ old('contact_no') ?? ($ourServices ? $ourServices->contact_no : '') }}">
                                        <span class="text-danger">
                                            @error('contact_no')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>



                                    <!-- Status Field -->
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="status">
                                            Status</label>
                                        <div class="form-check form-check-success form-switch">
                                            <input type="checkbox" name="status"
                                                {{ $ourServices != '' && $ourServices->status ? 'checked' : '' }}
                                                class="form-check-input" id="customSwitch4"
                                                @if (empty($ourServices)) checked @endif />
                                        </div>
                                        <span class="text-danger">
                                            @error('status')
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

@endsection
