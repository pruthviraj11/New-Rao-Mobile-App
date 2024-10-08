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


    @if ($page_data['page_title'] == 'Client Type Add')
        <form action="{{ route('app-client-types-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-client-types-update', encrypt($clientType->id)) }}" method="POST"
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
                        <a href="{{ route('app-client-types-list') }}" class="col-md-2 btn btn-primary float-end">client types
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Name</label>
                                <input type="text" id="name" class="form-control" placeholder="Name Of Of Client Type"
                                    name="name" value="{{ old('name') ?? ($clientType ? $clientType->name : '') }}">
                                <span class="text-danger">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                          <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Display Name</label>
                                <input type="text" id="displayname" class="form-control" placeholder="Display Name Of Client Type"
                                    name="displayname" value="{{ old('displayname') ?? ($clientType ? $clientType->displayname : '') }}">
                                <span class="text-danger">
                                    @error('displayname')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>



                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                        {{ $clientType != '' && $clientType->status ? 'checked' : '' }} class="form-check-input"
                                        id="customSwitch4" @if (empty($clientType)) checked @endif />
                                </div>
                                <span class="text-danger">
                                    @error('status')
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
@endsection
