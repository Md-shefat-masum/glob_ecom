@extends('backend.master')

@section('header_css')
    <style>
        .card-title-admin {
            font-size: 1rem;
            position: relative;
        }

        .card-title-admin::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0 !important;
            border-left: 2px solid #2e7ce4;
            border-bottom: 0;
            margin-right: -20px !important;
        }
    </style>
@endsection

@section('page_title')
    Support Ticket
@endsection
@section('page_heading')
    View All Support Chat
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body" id="div1" style="max-height: 400px; overflow-y: scroll">
                    <h4 class="card-title mb-1">Customer</h4>

                    <div class="w-75 text-left mb-3"
                        style="background: lightgoldenrodyellow; padding: 10px; border-radius: 5px;">
                        {!! $data->message !!}
                        <div class="row pt-1 border-top mt-2">
                            <div class="col-lg-6">
                                @if($data->attachment)
                                    <a href="{{url('/')}}/{{$data->attachment}}" stream target="_blank"><i
                                            class="feather-download"></i> Download Attachment</a>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <small
                                    style="display:block; text-align:right; font-size: 11px">{{date("h:i:s A, jS F Y", strtotime($data->created_at))}}</small>
                            </div>
                        </div>
                    </div>

                    @foreach ($messages as $msg)
                        @if($msg->sender_type == 1)
                            <h4 class="card-title-admin mb-1" style="text-align: right;">Support Agent</h4>
                            <div class="w-75 text-right mb-3"
                                style="margin-left:auto; background: lightcyan; padding: 10px; border-radius: 5px;">
                                {{$msg->message}}
                                <div class="row pt-1 border-top mt-2">
                                    <div class="col-lg-6 text-left">
                                        @if($msg->attachment)
                                            <a href="{{url('/')}}/{{$msg->attachment}}" stream target="_blank"><i
                                                    class="feather-download"></i> Download Attachment</a>
                                        @endif
                                    </div>
                                    <div class="col-lg-6">
                                        <small
                                            style="display:block; text-align:right; font-size: 11px">{{date("h:i:s A, jS F Y", strtotime($msg->created_at))}}</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <h4 class="card-title mb-1">Customer</h4>
                            <div class="w-75 text-left mb-3"
                                style="background: lightgoldenrodyellow; padding: 10px; border-radius: 5px;">
                                {{$msg->message}}
                                <div class="row pt-1 border-top mt-2">
                                    <div class="col-lg-6">
                                        @if($msg->attachment)
                                            <a href="{{url('/')}}/{{$msg->attachment}}" stream target="_blank"><i
                                                    class="feather-download"></i> Download Attachment</a>
                                        @endif
                                    </div>
                                    <div class="col-lg-6">
                                        <small
                                            style="display:block; text-align:right; font-size: 11px">{{date("h:i:s A, jS F Y", strtotime($msg->created_at))}}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{url('send/support/message')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="support_ticket_id" value="{{$data->id}}">
                        <div class="invalid-feedback" style="display: block;">
                            @error('support_ticket_id')
                                {{ $message }}
                            @enderror
                        </div>

                        <textarea class="form-control" name="message" required></textarea>
                        <div class="invalid-feedback" style="display: block;">
                            @error('message')
                                {{ $message }}
                            @enderror
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-8">
                                <input type="file" class="form-control" name="attachment">
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-info rounded w-100"><i class="feather-send"></i> Send
                                    Message</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer_js')

    {{-- js code for user crud --}}
    <script>

        $(document).ready(function () {
            $("#div1").animate({ scrollTop: $('#div1').prop("scrollHeight") }, 1000);
            $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/support/ticket') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Ticket has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.statusBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure want to Change the Status !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('support/status/change') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.suucess("Status has been Changed", "Changed Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection