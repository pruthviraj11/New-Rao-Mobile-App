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


    {{-- @if ($page_data['page_title'] == 'Notifications Add') --}}
    <form action="{{ route('app-notifications-store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- @else
            <form action="{{ route('app-notifications-update', encrypt($events->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif --}}

        <section id="multiple-column-form" class="mt-2">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $page_data['form_title'] }}</h4>
                            <a href="{{ route('app-notifications-list') }}"
                                class="col-md-2 btn btn-primary float-end">Notifications
                                List</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="name-column">
                                        Title</label>
                                    <input type="text" id="title" class="form-control" placeholder="title"
                                        name="title"
                                        value="{{ old('title') ?? ($notifications ? $notifications->title : '') }}">
                                    <span class="text-danger">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="message-column">Message</label>
                                    <textarea id="message" class="form-control" placeholder="Enter your message" name="message" rows="3">{{ old('message') ?? ($notifications ? $notifications->message : '') }}</textarea>
                                    <span class="text-danger">
                                        @error('message')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="client-type">Client Type</label>
                                    <select id="client-type" class="form-control select2" name="client_type">
                                        <option value="">Select Client Type</option>
                                        @foreach ($ClientType as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_type') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('client_type')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- User ID Dropdown -->
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="user_id">User</label>
                                    <select id="user_id" class="form-control select2" name="user_id[]" multiple>
                                        <option value="">Select User</option>
                                        <option value="select_all">Select All</option>
                                        <!-- Initially empty; users will be populated based on client type selection -->
                                    </select>
                                    <span class="text-danger">
                                        @error('user_id')
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
            $('#user_id').select2({
                placeholder: "Select Users",
                allowClear: true
            });
        });


        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Client Type",
                allowClear: true
            });
            $('.select2Users').select2({
                placeholder: "Select Users Type",
                allowClear: true
            });
        });
    </script>
    <script>
    $(document).ready(function() {
        $('#client-type').change(function() {
            var clientTypeId = $(this).val();
            if (clientTypeId) {
                $.ajax({
                    url: '{{ route('users.by.client', '') }}/' + clientTypeId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#user_id').empty();
                        $('#user_id').append('<option value="">Select User</option>');
                        $('#user_id').append('<option value="all">Select All</option>'); // Add Select All option
                        $.each(data, function(key, user) {
                            $('#user_id').append('<option value="' + user.id + '">' + user.name + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error fetching users');
                    }
                });
            } else {
                $('#user_id').empty();
                $('#user_id').append('<option value="">Select User</option>');
            }
        });

        // Handle user selection
        $('#user_id').change(function() {
            if ($(this).find('option[value="all"]').is(':selected')) {
                // Confirm action before selecting all
                if (confirm('Are you sure you want to select all users? This may take a while.')) {
                    // If "Select All" is checked, select all users
                    $('#user_id option').prop('selected', true);
                    $(this).trigger('change'); // Trigger change event to refresh select2
                } else {
                    // If the user cancels, unselect "Select All"
                    $(this).find('option[value="all"]').prop('selected', false);
                }
            }
        });
    });
</script>

@endsection
