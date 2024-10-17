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


    @if ($page_data['page_title'] == 'Sliders Add')
        <form action="{{ route('app-sliders-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-sliders-update', encrypt($slider->id)) }}" method="POST"
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
                        <a href="{{ route('app-sliders-list') }}" class="btn-sm btn btn-primary float-end">Slider
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border">{{ $page_data['form_title'] }}</div>
                            <div class="card-body">
                                <div class="row mt-1">
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">
                                            Name</label>
                                        <input type="text" id="name" class="form-control"
                                            placeholder="Name Of Slider" name="name"
                                            value="{{ old('name') ?? ($slider ? $slider->name : '') }}">
                                        <span class="text-danger">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="image">Slide Photo (1024*640)</label>
                                        <input type="file" id="image" class="form-control" name="image"
                                            accept="image/*">
                                        <span class="text-danger">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">Current Image</label>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ !empty($slider->image) ? asset('storage/' . $slider->image) : asset('default/default.jpg') }}"
                                                alt="Slider Image" class="img-fluid"
                                                style="width: auto; height: 80px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('default/default.jpg') }}';">

                                            @if (!empty($slider->image))
                                                <button type="button"
                                                    class="btn btn-link text-danger delete-icon confirm-delete"
                                                    data-idos="{{ $slider->id }}" title="Delete Image">
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
                                        <label class="form-label" for="name-column">
                                            Sequence</label>
                                        <input type="number" id="sequence" class="form-control"
                                            placeholder="Number Of Sequence" name="sequence"
                                            value="{{ old('sequence') ?? ($slider ? $slider->sequence : '') }}">
                                        <span class="text-danger">
                                            @error('sequence')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>


                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="status">
                                            Status</label>
                                        <div class="form-check form-check-success form-switch">
                                            <input type="checkbox" name="status"
                                                {{ $slider != '' && $slider->status ? 'checked' : '' }}
                                                class="form-check-input" id="customSwitch4"
                                                @if (empty($slider)) checked @endif />
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
@endsection
