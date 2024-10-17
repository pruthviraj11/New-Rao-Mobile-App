@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection

@section('content')

    @if ($page_data['form_title'] == 'Add New User')
        <form action="{{ route('app-users-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-users-update', encrypt($user->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border rounded-3">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-users-list') }}" class="col-md-2 btn btn-primary float-end">User
                            List</a>

                        {{-- <h4 class="card-title">{{$page_data['form_title']}}</h4> --}}
                    </div>
                    <div class="card-body">
                        <div class="card border mt-1">
                            <div class="card-header border">
                                <h6>{{ $page_data['form_title'] }}</h6>
                            </div>
                            <div class="card-body mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="username">
                                            Username</label>
                                        <input type="text" id="username" class="form-control" placeholder="Username"
                                            name="username"
                                            value="{{ old('username') ?? ($user != '' ? $user->username : '') }}">
                                        <span class="text-danger">
                                            @error('username')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="first_name">
                                            First Name</label>
                                        <input type="text" id="first_name" class="form-control" placeholder="First Name"
                                            name="first_name"
                                            value="{{ old('first_name') ?? ($user != '' ? $user->first_name : '') }}">
                                        <span class="text-danger">
                                            @error('first_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="last_name">
                                            Last Name</label>
                                        <input type="text" id="last_name" class="form-control" placeholder="Last Name"
                                            name="last_name"
                                            value="{{ old('last_name') ?? ($user != '' ? $user->last_name : '') }}">
                                        <span class="text-danger">
                                            @error('last_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="phone_no">
                                            Phone No</label>
                                        <input type="text" id="phone_no" class="form-control" placeholder="Phone No"
                                            name="phone_no"
                                            value="{{ old('phone_no') ?? ($user != '' ? $user->phone_no : '') }}">
                                        <span class="text-danger">
                                            @error('phone_no')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="email">
                                            Email</label>
                                        <input type="email" id="email" class="form-control" placeholder="Email"
                                            name="email" value="{{ old('email') ?? ($user != '' ? $user->email : '') }}">
                                        <span class="text-danger">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-6">
                                        <div class="row align-items-md-end">
                                            <div class="col-md-9 col-sm-12">
                                                <label class="form-label" for="password">
                                                    Password</label>

                                                <input type="text" id="password" class="form-control"
                                                    placeholder="{{ $user ? ($user->password ? 'Enter or Click Generate to Change Password' : 'Password') : '' }}"
                                                    name="password" value="{{ old('password') ?? old('password') }}">
                                                <span class="text-danger">
                                                    @error('password')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <button type="button" class="btn btn-outline-primary"
                                                    id="generatePassword">Generate Password
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="dob">
                                            Date of Birth</label>
                                        <input type="date" name="dob" id="dob" class="form-control"
                                            value="{{ old('dob') ?? ($user != '' ? $user->dob : '') }}">
                                        <span class="text-danger">
                                            @error('dob')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="report_to">
                                            Report To</label>
                                        <select class="select2 form-select" name="report_to">
                                            <option value="" hidden>Select Report To</option>
                                            @foreach ($data['reports_to'] as $report_to)
                                                <option value="{{ $report_to->id }}"
                                                    {{ old('report_to') ? (old('report_to') == $report_to->id ? 'selected' : '') : ($user ? ($user->report_to == $report_to->id ? 'selected' : '') : '') }}>
                                                    {{ $report_to->first_name . ' ' . $report_to->last_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">
                                            @error('report_to')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="address">
                                            Address</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="address"
                                            placeholder="Address">{{ old('address') ?? ($user != '' ? $user->address : '') }}</textarea>
                                        <span class="text-danger">
                                            @error('address')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mb-1">
                                        <label class="form-label" for="role">
                                            Select Role</label>
                                        <select class="select2 form-select" name="role" id="role">
                                            <option value="" selected disabled>Select Role</option>
                                            @forelse($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ $user != '' ? ($role->display_name == $user->role ? 'selected' : '') : '' }}>
                                                    {{ $role->display_name }}</option>
                                            @empty
                                                <option value="" selected disabled>No Roles Found</option>
                                            @endforelse
                                        </select>
                                        <span class="text-danger">
                                            @error('role')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" name="submit" value="submit" class="btn btn-primary me-1">Submit
                            </button>
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
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
    <script>
        // Function to generate a random password
        function generateRandomPassword(length) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            let password = "";
            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset.charAt(randomIndex);
            }
            return password;
        }

        $(document).ready(function() {
            // Generate password when the button is clicked
            $("#generatePassword").click(function() {
                const generatedPassword = generateRandomPassword(
                    10); // You can adjust the length of the password here
                $("#password").val(generatedPassword);
                $("#password").select();
                document.execCommand("copy");
                alert("Password copied to clipboard!");
            });
        });
    </script>
@endsection
