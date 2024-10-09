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


    @if ($page_data['page_title'] == 'faq categories Add')
        <form action="{{ route('app-faq-categories-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-faq-categories-update', encrypt($faqCategories->id)) }}" method="POST"
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
                        <a href="{{ route('app-faq-categories-list') }}" class="col-md-2 btn btn-primary float-end">Faq Categories
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Name</label>
                                <input type="text" id="title" class="form-control" placeholder="Category Name" name="name"
                                    value="{{ old('name') ?? ($faqCategories ? $faqCategories->name : '') }}">
                                <span class="text-danger">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Category Description</label>
                                <input type="text" id="description" class="form-control" placeholder="Category Description" name="description"
                                    value="{{ old('description') ?? ($faqCategories ? $faqCategories->description : '') }}">
                                <span class="text-danger">
                                    @error('description')
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
                                            {{ old('category_id') == $client->id ? 'selected' : ($faqCategories && $faqCategories->category_id == $client->id ? 'selected' : '') }}>
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
                                        {{ $faqCategories != '' && $faqCategories->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($faqCategories)) checked @endif />
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
