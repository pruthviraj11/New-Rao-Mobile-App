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


    @if ($page_data['page_title'] == 'internal Program Status Add')
        <form action="{{ route('app-internal-program-statuses-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-internal-program-statuses-update', encrypt($internalProgramStatus->id)) }}" method="POST"
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
                        <a href="{{ route('app-internal-program-statuses-list') }}" class="col-md-2 btn btn-primary float-end">Slider
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Name</label>
                                <input type="text" id="name" class="form-control" placeholder="name" name="name"
                                    value="{{ old('name') ?? ($internalProgramStatus ? $internalProgramStatus->name : '') }}">
                                <span class="text-danger">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            
                             <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Description</label>
                                <input type="text" id="description" class="form-control" placeholder="description" name="description"
                                    value="{{ old('description') ?? ($internalProgramStatus ? $internalProgramStatus->description : '') }}">
                                <span class="text-danger">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                             <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Order</label>
                                <input type="text" id="order" class="form-control" placeholder="order" name="order"
                                    value="{{ old('order') ?? ($internalProgramStatus ? $internalProgramStatus->order : '') }}">
                                <span class="text-danger">
                                    @error('order')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="category_id">
                                    Client Type</label>
                                <select id="category_id" class="form-control select2" name="category_id">
                                    <option value="">Select Client Type</option>
                                    @foreach ($ClientType as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('category_id') == $client->id ? 'selected' : ($internalProgramStatus && $internalProgramStatus->category_id == $client->id ? 'selected' : '') }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('category_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                        {{ $internalProgramStatus != '' && $internalProgramStatus->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($internalProgramStatus)) checked @endif />
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
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Client Type",
                allowClear: true
            });
        });
    </script>

@endsection
