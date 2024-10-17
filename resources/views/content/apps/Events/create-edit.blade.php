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


    @if ($page_data['page_title'] == 'Events Add')
        <form action="{{ route('app-events-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-events-update', encrypt($events->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border rounded-3">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-events-list') }}" class="btn-sm btn btn-primary float-end">Events
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header">
                                <h6>{{ $page_data['form_title'] }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">
                                            Title</label>
                                        <input type="text" id="title" class="form-control" placeholder="title"
                                            name="title" value="{{ old('title') ?? ($events ? $events->title : '') }}">
                                        <span class="text-danger">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="date-column">Date</label>
                                        <input type="date" id="date" class="form-control" name="date"
                                            value="{{ old('date') ?? ($events ? $events->date : '') }}">
                                        <span class="text-danger">
                                            @error('date')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="image-column">Image</label>
                                        <input type="file" id="image" class="form-control" name="image">
                                        <span class="text-danger">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="status">
                                            Status</label>
                                        <div class="form-check form-check-success form-switch">
                                            <input type="checkbox" name="status"
                                                {{ $events != '' && $events->status ? 'checked' : '' }}
                                                class="form-check-input" id="customSwitch4"
                                                @if (empty($events)) checked @endif />
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
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Client Type",
                allowClear: true
            });
        });
    </script>

@endsection
