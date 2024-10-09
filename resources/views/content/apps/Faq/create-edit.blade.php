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


    @if ($page_data['page_title'] == 'faq Add')
        <form action="{{ route('app-faq-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-faq-update', encrypt($faq->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form" class="mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-faq-list') }}" class="col-md-2 btn btn-primary float-end">Faq
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Title</label>
                                <input type="text" id="title" class="form-control" placeholder="Faq Title"
                                    name="title" value="{{ old('title') ?? ($faq ? $faq->title : '') }}">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="answer">
                                    Answer
                                </label>
                                <div id="editor"></div>
                                <input type="hidden" name="answer" id="answer"
                                    value="{{ old('answer') ?? ($faq ? $faq->answer : '') }}">
                                <span class="text-danger">
                                    @error('answer')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Sequence</label>
                                <input type="text" id="sequence" class="form-control" placeholder="Faq sequence"
                                    name="sequence" value="{{ old('sequence') ?? ($faq ? $faq->sequence : '') }}">
                                <span class="text-danger">
                                    @error('sequence')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="faq_category_id">
                                    Faq Categorie</label>
                                <select id="faq_category_id" class="form-control select2" name="faq_category_id">
                                    <option value="">Select Faq</option>
                                    @foreach ($faqCat as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('faq_category_id') == $client->id ? 'selected' : ($faq && $faq->faq_category_id == $client->id ? 'selected' : '') }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('faq_category_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status" {{ $faq != '' && $faq->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($faq)) checked @endif />
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

@endsection

@section('page-script')

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Faq Categorie",
                allowClear: true
            });
        });
    </script>
<script>
        $(document).ready(function() {
           

            ClassicEditor.create(document.querySelector("#editor"), {
                // Optional configuration options
            })
            .then(editor => {
                // Set initial data for the editor
                const initialData = document.querySelector('#answer').value;
                editor.setData(initialData);

                editor.model.document.on('change:data', () => {
                    document.querySelector('#answer').value = editor.getData();
                });
            })
            .catch((error) => {
                console.error(error);
            });
        });
    </script>
@endsection
