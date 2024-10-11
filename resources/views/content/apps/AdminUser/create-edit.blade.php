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


    @if ($page_data['page_title'] == 'Admin User Add')
        <form action="{{ route('app-admin-user-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-admin-user-update', encrypt($adminUsers->id)) }}" method="POST"
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
                        <a href="{{ route('app-admin-user-list') }}" class="col-md-2 btn btn-primary float-end">Admin Users
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="avatar">Image</label>
                                <input type="file" id="avatar" class="form-control" name="avatar">
                                <span class="text-danger">
                                    @error('avatar')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="current-image">Current Image</label>
                                <div class="d-flex align-items-center">
                                    <!-- Display Image or Default Image -->
                                    <img src="{{ !empty($adminUsers->avatar) ? asset('storage/' . $adminUsers->avatar) : asset('default/default.jpg') }}"
                                        alt="Current Image" class="img-fluid"
                                        style="width: auto; height: 80px; object-fit: cover;"
                                        onerror="this.onerror=null; this.src='{{ asset('default/default.jpg') }}';">

                                    <!-- Delete Button if Image Exists -->
                                    @if (!empty($adminUsers->avatar))
                                        <button type="button" class="btn btn-link text-danger delete-icon confirm-delete"
                                            data-idos="{{ $adminUsers->id }}" title="Delete Image">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-trash-2 ficon">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="name-column">
                                    Name</label>
                                <input type="text" id="name" class="form-control" placeholder="name" name="name"
                                    value="{{ old('name') ?? ($adminUsers ? $adminUsers->name : '') }}">
                                <span class="text-danger">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="email-column">Email</label>
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email"
                                    value="{{ old('email') ?? ($adminUsers ? $adminUsers->email : '') }}">
                                <span class="text-danger">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="user-category-column">User Category</label>
                                <select id="user_category" class="form-control" name="user_category">
                                    @foreach($userCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('user_category') == $category->id ? 'selected' : ($adminUsers && $adminUsers->user_category == $category->id ? 'selected' : '') }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('user_category')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="password-column">Password</label>
                                <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                                <span class="text-danger">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="phone-number-column">Phone Number</label>
                                <input type="text" id="phone_number" class="form-control" placeholder="Phone Number" name="phone_number"
                                    value="{{ old('phone_number') ?? ($adminUsers ? $adminUsers->phone_number : '') }}">
                                <span class="text-danger">
                                    @error('phone_number')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="reporting-to-column">Reporting To</label>
                                <select id="reporting_to" class="form-control" name="reporting_to">
                                    <option value="">Select Reporting Manager</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('reporting_to') == $user->id ? 'selected' : ($adminUsers && $adminUsers->reporting_to == $user->id ? 'selected' : '') }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('reporting_to')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="is-download-column">Is Download</label>
                                <select id="is_download" class="form-control" name="is_download">
                                    <option value="yes" {{ old('is_download') == 'yes' ? 'selected' : ($adminUsers && $adminUsers->is_download == 'yes' ? 'selected' : '') }}>Yes</option>
                                    <option value="no" {{ old('is_download') == 'no' ? 'selected' : ($adminUsers && $adminUsers->is_download == 'no' ? 'selected' : '') }}>No</option>
                                </select>
                                <span class="text-danger">
                                    @error('is_download')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="download-date-column">Download Date</label>
                                <input type="date" id="download_date" class="form-control" name="download_date"
                                    value="{{ old('download_date') ?? ($adminUsers ? $adminUsers->download_date : '') }}">
                                <span class="text-danger">
                                    @error('download_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>



                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="status">
                                    Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                        {{ $adminUsers != '' && $adminUsers->status ? 'checked' : '' }}
                                        class="form-check-input" id="customSwitch4"
                                        @if (empty($adminUsers)) checked @endif />
                                </div>
                                <span class="text-danger">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 mb-1">
                            <label class="form-label" for="role-id-column">Default Role</label>
                            <select id="role_id" class="form-control" name="role_id">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ (old('role_id') == $role->id || (isset($adminUsers) && $adminUsers->role_id == $role->id)) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                @error('role_id')
                                    {{ $message }}
                                @enderror
                            </span>
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
