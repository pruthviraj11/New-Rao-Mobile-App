@extends('layouts/contentLayoutMaster')

@section('title', 'Restrict Screen')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
    <!-- users list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-user-list">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Restrict Screen</h4>
                <a href="{{ route('app-users-list') }}" class="col-md-2 btn btn-primary">Users List</a>
            </div>

            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <form id="screen_form" action="{{ route('users.restricted.screen.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="panel-body messageContainerHeight" id="messageContainer"
                            style="padding:30px; width: unset !important;">
                            <div class="form-group col-md-12">
                                <label class="control-label" for="home_screen">
                                    <input type="checkbox" id="home_screen" name="home_screen" class="custom-check"
                                        value="0" {{ $user->home_screen == 1 ? 'checked' : '' }}> <span
                                        class="check-name">Home Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="profile_screen">
                                    <input type="checkbox" id="profile_screen" name="profile_screen" class="custom-check"
                                        value="0" {{ $user->profile_screen == 1 ? 'checked' : '' }}> <span
                                        class="check-name">Profile
                                        Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="consultant_screen">
                                    <input type="checkbox" id="consultant_screen" name="consultant_screen"
                                        class="custom-check" value="0"
                                        {{ $user->consultant_screen == 1 ? 'checked' : '' }}> <span
                                        class="check-name">Consultant Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="consulting_journy_screen">
                                    <input type="checkbox" id="consulting_journy_screen" name="consulting_journy_screen"
                                        class="custom-check" value="0"
                                        {{ $user->consulting_journy_screen == 1 ? 'checked' : '' }}>
                                    <span class="check-name">My Consulting Journey Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="our_services_screen">
                                    <input type="checkbox" id="our_services_screen" name="our_services_screen"
                                        class="custom-check" value="0"
                                        {{ $user->our_services_screen == 1 ? 'checked' : '' }}> <span
                                        class="check-name">Our Services Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="success_stories_screen">
                                    <input type="checkbox" id="success_stories_screen" name="success_stories_screen"
                                        class="custom-check" value="0"
                                        {{ $user->success_stories_screen == 1 ? 'checked' : '' }}>
                                    <span class="check-name">Success Stories Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="faq_screen">
                                    <input type="checkbox" id="faq_screen" name="faq_screen" class="custom-check"
                                        value="0" {{ $user->faq_screen == 1 ? 'checked' : '' }}> <span
                                        class="check-name">FAQ Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="need_help_screen">
                                    <input type="checkbox" id="need_help_screen" name="need_help_screen"
                                        class="custom-check" value="0"
                                        {{ $user->need_help_screen == 1 ? 'checked' : '' }}> <span class="check-name">Need
                                        Help Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="privacy_policy_screen">
                                    <input type="checkbox" id="privacy_policy_screen" name="privacy_policy_screen"
                                        class="custom-check" value="0"
                                        {{ $user->privacy_policy_screen == 1 ? 'checked' : '' }}>
                                    <span class="check-name">Privacy Policy Screen</span>
                                </label>
                            </div>
                            <div class="form-group col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
@endsection

@section('page-script')


@endsection
