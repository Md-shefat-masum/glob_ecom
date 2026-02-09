@extends('backend.master')

@section('page_title')
    Notification
@endsection
@section('page_heading')
    Push Notification
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Send Push Notification to Mobile Devices</h4>
                         <a href="{{ route('ViewAllNotifications')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{url('send/push/notification')}}" enctype="multipart/form-data">
                        @csrf

                        {{-- <div class="form-group row">
                            <label for="server_key" class="col-sm-2 col-form-label">Firebase Server Key : <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="server_key" class="form-control" id="server_key" value="{{env('FIREBASE_PUSH_NOTIFICATION_SERVER_KEY')}}" placeholder="AAAAN7ShpFk:APA91bF-0kcNVFSYr1J-bq3UvWcPv6tbvNFfOyZvbtOqmrYk3-3qst0IjaDX9SYzj" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('server_key')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="fcm_url" class="col-sm-2 col-form-label">FCM Notification URL : <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="fcm_url" class="form-control" id="fcm_url" value="{{env('FIREBASE_PUSH_NOTIFICATION_FCM_URL')}}" placeholder="https://fcm.googleapis.com/fcm/send" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('fcm_url')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="topic" class="col-sm-2 col-form-label">FCM Notification Topic : <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="topic" class="form-control" id="topic" value="{{env('FIREBASE_PUSH_NOTIFICATION_TOPIC')}}" placeholder="/topics/example" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('topic')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label for="title" class="col-sm-2 col-form-label">Notification Title : <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" id="title" placeholder="Notification Title" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Notification Description :</label>
                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" rows="3" id="description" placeholder="Write Description Here"></textarea>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="topic" class="col-sm-2 col-form-label">FCM Notification URL : </label>
                            <div class="col-sm-10">
                                <input type="text" name="topic" class="form-control" id="topic" value="" placeholder="">
                                <div class="invalid-feedback" style="display: block;">
                                    @error('topic')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send Push Notification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
