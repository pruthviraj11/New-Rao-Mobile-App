@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/themes/lark.css" />
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


    @if ($page_data['page_title'] == 'news Add')
        <form action="{{ route('app-news-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-news-update', encrypt($news->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif


    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border rounded-3">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-news-list') }}" class="btn-sm btn btn-primary float-end">News
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border">
                                <h6>{{ $page_data['form_title'] }}</h6>
                            </div>
                            <div class="card-body mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">
                                            Title</label>
                                        <input type="text" id="title" class="form-control" placeholder="title"
                                            name="title" value="{{ old('title') ?? ($news ? $news->title : '') }}">
                                        <span class="text-danger">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="category_id">
                                            News Category</label>
                                        <select id="category_id" class="form-control select2" name="category_id">
                                            <option value="">Select News Category</option>
                                            @foreach ($NewsCategories as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ old('category_id') == $client->id ? 'selected' : ($news && $news->category_id == $client->id ? 'selected' : '') }}>
                                                    {{ $client->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">
                                            @error('category_id')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <div class="col-12 mb-1">
                                        <label class="form-label" for="long_description">
                                            Long Description
                                        </label>
                                        <div id="editor"></div>
                                        <input type="hidden" name="long_description" id="long_description"
                                            value="{{ old('long_description') ?? ($news ? $news->long_description : '') }}">
                                        <span class="text-danger">
                                            @error('long_description')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="file">News Photo</label>
                                        <input type="file" id="file" class="form-control" name="file"
                                            accept="file/*">
                                        <span class="text-danger">
                                            @error('file')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">Current Image</label>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ !empty($news->file) ? asset('storage/' . $news->file) : asset('default/default.jpg') }}"
                                                alt="News Image" class="img-fluid"
                                                style="width: auto; height: 80px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('default/default.jpg') }}';">

                                            @if (!empty($news->file))
                                                <button type="button"
                                                    class="btn btn-link text-danger delete-icon confirm-delete"
                                                    data-idos="{{ $news->id }}" title="Delete Image">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-trash-2 ficon">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                        </path>
                                                        <line x1="10" y1="11" x2="10" y2="17">
                                                        </line>
                                                        <line x1="14" y1="11" x2="14" y2="17">
                                                        </line>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>




                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="date">Date</label>
                                        <input type="date" id="date" class="form-control datepicker" name="date"
                                            value="{{ old('date') ?? ($news ? $news->date : '') }}"
                                            placeholder="YYYY-MM-DD">
                                        <span class="text-danger">
                                            @error('date')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">
                                            News Button Title</label>
                                        <input type="text" id="news_button_text" class="form-control"
                                            placeholder="news button text" name="news_button_text"
                                            value="{{ old('news_button_text') ?? ($news ? $news->news_button_text : '') }}">
                                        <span class="text-danger">
                                            @error('news_button_text')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="status">
                                            Status</label>
                                        <div class="form-check form-check-success form-switch">
                                            <input type="checkbox" name="status"
                                                {{ $news != '' && $news->status ? 'checked' : '' }}
                                                class="form-check-input" id="customSwitch4"
                                                @if (empty($news)) checked @endif />
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
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
                    window.location.href = '/app/news/' + id; // Adjusted to use the correct route
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
                placeholder: "Select News Category",
                allowClear: true
            });

            ClassicEditor.create(document.querySelector("#editor"), {
                    // Optional configuration options
                })
                .then(editor => {
                    // Set initial data for the editor
                    const initialData = document.querySelector('#long_description').value;
                    editor.setData(initialData);

                    editor.model.document.on('change:data', () => {
                        document.querySelector('#long_description').value = editor.getData();
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        });
    </script>

@endsection
