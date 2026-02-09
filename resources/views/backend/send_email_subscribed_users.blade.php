@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" /> --}}
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(1) {
            text-align: center !important;
            font-weight: 600;
        }

        .checkmark {
            font-size: 18px;
            color: green;
        }

        .select2-results__option--highlighted .checkmark {
            color: white;
        }

        .list-group-item {
            cursor: pointer;
        }

        .select2-results__option .checkmark {
            color: green;
            font-size: 18px;
            float: right;
        }

        .select2-dropdown .select2-custom-actions {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            background: #f9f9f9;
        }

        .select2-container .select2-selection--multiple .select2-selection__choice {
            color: #000;
        }
    </style>
@endsection

@section('page_title')
    Send Email to Subscribed Users
@endsection
@section('page_heading')
    Send Email to Subscribed Users
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Subscribed Users Email List</h4>
                        <a href="{{ url('view/all/subscribed/users') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <form id="bulkEmailForm">
                        <div class="form-group">
                            <label for="emails-select">Select Emails</label>
                            <select id="emails-select" name="emails[]" class="form-control" multiple required>
                                @foreach ($subscribedUsers as $user)
                                    <option value="{{ $user->email }}">{{ $user->email }} ({{ $user->created_at }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <label for="email-subject">Subject</label>
                            <input type="text" class="form-control" id="email-subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="email-message">Message</label>
                            <textarea class="form-control" id="email-message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        {{-- <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script> --}}
    <script>
        function updateCheckmarks() {
            var selectedValues = $('#emails-select').val() || [];
            $('.option-container').each(function() {
                var email = $(this).data('email');
                var $checkmarkPlaceholder = $(this).find('.checkmark-placeholder');

                if (selectedValues.indexOf(email) !== -1) {
                    $checkmarkPlaceholder.html(
                        '<span class="checkmark" style="color: green; font-size: 18px;">✓</span>');
                } else {
                    $checkmarkPlaceholder.html('');
                }
            });
        }

        function renderSelect2Actions() {
            if ($('.select2-custom-actions').length === 0) {
                var actions = $('<div class="select2-custom-actions">' +
                    '<button type="button" class="btn btn-sm btn-success w-100" id="selectAllEmails">Select All</button>' +
                    '<button type="button" class="btn btn-sm btn-danger ml-2 w-100" id="deselectAllEmails">Deselect All</button>' +
                    '</div>');
                actions.prependTo('.select2-dropdown');
            }
        }
        $(document).ready(function() {
            $('#emails-select').select2({
                placeholder: 'Search or select emails',
                width: '100%',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#emails-select').parent(),
                templateResult: function(data) {
                    if (!data.id) return data.text;

                    var $result = $(
                        '<div class="option-container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;" data-email="' +
                        data.id + '">' +
                        '<span>' + data.text + '</span>' +
                        '<span class="checkmark-placeholder" style="width: 20px; text-align: right;"></span>' +
                        '</div>');

                    return $result;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    renderSelect2Actions();
                    updateCheckmarks();
                }, 50);
            }).on('select2:select select2:unselect', function(e) {
                // Update checkmarks without refreshing dropdown
                setTimeout(updateCheckmarks, 50);
            });
            // Delegate click for select/deselect all inside dropdown
            $(document).on('click', '#selectAllEmails', function(e) {
                e.preventDefault();
                var allVals = [];
                $('#emails-select option').each(function() {
                    allVals.push($(this).val());
                });
                $('#emails-select').val(allVals).trigger('change');

                // Update checkmarks without refreshing dropdown
                setTimeout(updateCheckmarks, 50);
            });

            $(document).on('click', '#deselectAllEmails', function(e) {
                e.preventDefault();
                $('#emails-select').val(null).trigger('change');

                // Update checkmarks without refreshing dropdown
                setTimeout(updateCheckmarks, 50);
            });
            // Form submit
            $('#bulkEmailForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Sending...');
                $.ajax({
                    type: 'POST',
                    url: '{{ url('subscribed/users/send-email') }}',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        toastr.success('Emails are being sent via queue.', 'Success');
                        submitBtn.prop('disabled', false).text('Send Email');
                        // Clear form fields
                        $('#email-subject').val('');
                        $('#email-message').val('');
                        $('#emails-select').val(null).trigger('change');
                    },
                    error: function(data) {
                        toastr.error('Failed to send emails.', 'Error');
                        submitBtn.prop('disabled', false).text('Send Email');
                    }
                });
            });
        });
    </script>
@endsection
