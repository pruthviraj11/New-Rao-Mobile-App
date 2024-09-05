@extends('layouts/contentLayoutMaster')

@section('title', 'Account')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">


            <!-- profile -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <!-- header section -->
                    <form class="validate-form mt-2 pt-50"action="{{ route('profile-update', encrypt($data->id)) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="d-flex">
                            {{-- <a href="#" class="me-25">
                                <img src="{{ asset('images/portrait/small/avatar-s-11.jpg') }}" id="account-upload-img"
                                    class="uploadedAvatar rounded me-50" alt="profile image" height="100"
                                    width="100" />
                            </a> --}}
                            <!-- upload and reset button -->
                            {{-- <div class="d-flex align-items-end mt-75 ms-1">
                                <div>
                                    <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75">Upload</label>
                                    <input type="file" id="account-upload" hidden accept="image/*" />
                                    <button type="button" id="account-reset"
                                        class="btn btn-sm btn-outline-secondary mb-75">Reset</button>
                                    <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                </div>
                            </div> --}}
                            <!--/ upload and reset button -->
                        </div>
                        <!--/ header section -->

                        <!-- form -->
                        <div class="row">
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountFirstName">Name</label>
                                <input type="text" class="form-control" id="accountFirstName" name="name"
                                    placeholder="John" value="{{ $data->name }}" data-msg="Please enter name" />
                                <span class="text-danger">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountEmail">Email</label>
                                <input type="email" class="form-control" id="accountEmail" name="email"
                                    placeholder="Email" value="{{ $data->email }}" />
                                <span class="text-danger">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Phone Number</label>
                                <input type="text" class="form-control account-number-mask" id="accountPhoneNumber"
                                    name="phone_number" placeholder="Phone Number" value="{{ $data->phone_number }}" />
                                     <span class="text-danger">
                                    @error('phone_number')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mt-1 me-1">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary mt-1">Discard</button>
                            </div>
                        </div>
                    </form>
                    <!--/ form -->
                </div>
            </div>

            <!-- deactivate account  -->
            {{-- <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Delete Account</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Are you sure you want to delete your account?</h4>
                        <div class="alert-body fw-normal">
                            Once you delete your account, there is no going back. Please be certain.
                        </div>
                    </div>

                    <form id="formAccountDeactivation" class="validate-form" onsubmit="return false">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accountActivation"
                                id="accountActivation" data-msg="Please confirm you want to delete account" />
                            <label class="form-check-label font-small-3" for="accountActivation">
                                I confirm my account deactivation
                            </label>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-danger deactivate-account mt-1">Deactivate
                                Account</button>
                        </div>
                    </form>
                </div>
            </div> --}}
            <!--/ profile -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>


@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/components/components-bs-toast.js')) }}"></script>
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/page-account-settings-account.js')) }}"></script>
@endsection
