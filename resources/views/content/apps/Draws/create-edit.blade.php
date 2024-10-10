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


    @if ($page_data['page_title'] == 'Draws Add')
        <form action="{{ route('app-draws-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-draws-update', encrypt($draws->id)) }}" method="POST"
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
                        <a href="{{ route('app-draws-list') }}" class="col-md-2 btn btn-primary float-end">Draws
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="date-column">Date</label>
                                <input type="date" id="date" class="form-control" name="date"
                                       value="{{ old('date') ?? ($draws ? $draws->date : '') }}">
                                <span class="text-danger">
                                    @error('date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="crs_cutoff-column">CRS Cutoff</label>
                                <input type="number" id="crs_cutoff" class="form-control" name="crs_cutoff"
                                       value="{{ old('crs_cutoff') ?? ($draws ? $draws->crs_cutoff : '') }}">
                                <span class="text-danger">
                                    @error('crs_cutoff')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="type-column">Type</label>
                                <textarea id="type" class="form-control" name="type" rows="3">{{ old('type') ?? ($draws ? $draws->type : '') }}</textarea>
                                <span class="text-danger">
                                    @error('type')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="ita_issue-column">ITA Issue</label>
                                <input type="number" id="ita_issue" class="form-control" name="ita_issue"
                                       value="{{ old('ita_issue') ?? ($draws ? $draws->ita_issue : '') }}">
                                <span class="text-danger">
                                    @error('ita_issue')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                        {{ $draws != '' && $draws->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($draws)) checked @endif />
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
