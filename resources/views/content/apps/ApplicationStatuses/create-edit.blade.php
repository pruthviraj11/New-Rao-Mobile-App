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


    @if ($page_data['page_title'] == 'Application Statuses Add')
        <form action="{{ route('app-application-statuses-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-application-statuses-update', encrypt($applicationStatuses->id)) }}" method="POST"
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
                        <a href="{{ route('app-application-statuses-list') }}"
                            class="btn-sm btn btn-primary float-end">Application Statuses
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border"><h6>{{ $page_data['form_title'] }}</h6></div>
                            <div class="card-body mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="name-column">
                                            Name</label>
                                        <input type="text" id="name" class="form-control" placeholder="name"
                                            name="name"
                                            value="{{ old('name') ?? ($applicationStatuses ? $applicationStatuses->name : '') }}">
                                        <span class="text-danger">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="description-column">
                                            Description
                                        </label>
                                        <input type="text" id="description" class="form-control"
                                            placeholder="Description" name="description"
                                            value="{{ old('description') ?? ($applicationStatuses ? $applicationStatuses->description : '') }}">
                                        <span class="text-danger">
                                            @error('description')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="order-column">
                                            Order
                                        </label>
                                        <input type="number" id="order" class="form-control" placeholder="Order"
                                            name="order"
                                            value="{{ old('order') ?? ($applicationStatuses ? $applicationStatuses->order : '') }}">
                                        <span class="text-danger">
                                            @error('order')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="category-column">
                                            Category
                                        </label>
                                        <select id="category" class="form-control" name="category">
                                            <option value="" disabled selected>Select Category</option>
                                            <option value="FE"
                                                {{ old('category') == 'FE' || ($applicationStatuses && $applicationStatuses->category == 'FE') ? 'selected' : '' }}>
                                                FE</option>
                                            <option value="IV"
                                                {{ old('category') == 'IV' || ($applicationStatuses && $applicationStatuses->category == 'IV') ? 'selected' : '' }}>
                                                IV</option>
                                            <option value="ALL"
                                                {{ old('category') == 'ALL' || ($applicationStatuses && $applicationStatuses->category == 'ALL') ? 'selected' : '' }}>
                                                ALL</option>
                                        </select>
                                        <span class="text-danger">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="status">
                                            Status</label>
                                        <div class="form-check form-check-success form-switch">
                                            <input type="checkbox" name="status"
                                                {{ $applicationStatuses != '' && $applicationStatuses->status ? 'checked' : '' }}
                                                class="form-check-input" id="customSwitch4"
                                                @if (empty($applicationStatuses)) checked @endif />
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
