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


    @if ($page_data['page_title'] == 'success stories Add')
        <form action="{{ route('app-success-stories-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-success-stories-update', encrypt($successStories->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-success-stories-list') }}" class="col-md-2 btn btn-primary float-end">Success Stories
                            </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Title Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" id="title" class="form-control" placeholder="Title" name="title"
                                    value="{{ old('title') ?? ($successStories ? $successStories->title : '') }}">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Short Description Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="short_description">Short Description</label>
                                <textarea id="short_description" class="form-control" placeholder="Short Description" name="short_description">{{ old('short_description') ?? ($successStories ? $successStories->short_description : '') }}</textarea>
                                <span class="text-danger">
                                    @error('short_description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Long Description Field -->
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="long_description">Long Description</label>
                                <textarea id="long_description" class="form-control" placeholder="Long Description" name="long_description">{{ old('long_description') ?? ($successStories ? $successStories->long_description : '') }}</textarea>
                                <span class="text-danger">
                                    @error('long_description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- File Upload Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="file">File</label>
                                <input type="file" id="file" class="form-control" name="file">
                                <span class="text-danger">
                                    @error('file')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Date Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="date">Date</label>
                                <input type="date" id="date" class="form-control" name="date"
                                    value="{{ old('date') ?? ($successStories ? $successStories->date : '') }}">
                                <span class="text-danger">
                                    @error('date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Candidate Name Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="candidate_name">Candidate Name</label>
                                <input type="text" id="candidate_name" class="form-control" placeholder="Candidate Name" name="candidate_name"
                                    value="{{ old('candidate_name') ?? ($successStories ? $successStories->candidate_name : '') }}">
                                <span class="text-danger">
                                    @error('candidate_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Candidate Image Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="candidate_image">Candidate Image URL</label>
                                <input type="text" id="candidate_image" class="form-control" placeholder="Candidate Image URL" name="candidate_image"
                                    value="{{ old('candidate_image') ?? ($successStories ? $successStories->candidate_image : '') }}">
                                <span class="text-danger">
                                    @error('candidate_image')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Candidate Type Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="candidate_type">Candidate Type</label>
                                <input type="text" id="candidate_type" class="form-control" placeholder="Candidate Type" name="candidate_type"
                                    value="{{ old('candidate_type') ?? ($successStories ? $successStories->candidate_type : '') }}">
                                <span class="text-danger">
                                    @error('candidate_type')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Status Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                        {{ $successStories != '' && $successStories->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($successStories)) checked @endif />
                                </div>
                                <span class="text-danger">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <!-- For Home Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="for_home">For Home</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="for_home"
                                        {{ $successStories != '' && $successStories->for_home ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitchForHome"
                                        @if (empty($successStories)) checked @endif />
                                </div>
                                <span class="text-danger">
                                    @error('for_home')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                            <!-- Ratings Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="ratings">Ratings</label>
                                <input type="text" id="ratings" class="form-control" placeholder="Ratings" name="ratings"
                                    value="{{ old('ratings') ?? ($successStories ? $successStories->ratings : '') }}">
                                <span class="text-danger">
                                    @error('ratings')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Video Thumbnail Field -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="video_thumbnail">Video Thumbnail URL</label>
                                <input type="text" id="video_thumbnail" class="form-control" placeholder="Video Thumbnail URL" name="video_thumbnail"
                                    value="{{ old('video_thumbnail') ?? ($successStories ? $successStories->video_thumbnail : '') }}">
                                <span class="text-danger">
                                    @error('video_thumbnail')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
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
