@extends('backend.master')

@section('page_title')
    Website Config
@endsection
@section('page_heading')
    Social Media Links
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Update Social Media Links</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/social/media/link')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="facebook" class="col-form-label"><i class="fab fa-facebook-square" style="color: #1877F2;"></i> Facebook Page Link :</label>
                                    <input type="text" name="facebook" class="form-control" value="{{$data->facebook}}" id="facebook" placeholder="https://facebook.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('facebook')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="twitter" class="col-form-label"><i class="fab fa-twitter" style="color: #00acee;"></i> Twitter Link :</label>
                                    <input type="text" name="twitter" class="form-control" value="{{$data->twitter}}" id="twitter" placeholder="https://twitter.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('twitter')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="instagram" class="col-form-label"><i class="fab fa-instagram" style="color: #ffb719;"></i> Instagram Link :</label>
                                    <input type="text" name="instagram" class="form-control" value="{{$data->instagram}}" id="instagram" placeholder="https://instagram.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('instagram')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="linkedin" class="col-form-label"><i class="fab fa-linkedin" style="color: #0072b1;"></i> Linkedin Profile :</label>
                                    <input type="text" name="linkedin" class="form-control" value="{{$data->linkedin}}" id="linkedin" placeholder="https://linkedin.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('linkedin')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="messenger" class="col-form-label"><i class="fab fa-facebook-messenger" style="color: #44bec7;"></i> Messenger :</label>
                                    <input type="text" name="messenger" class="form-control" value="{{$data->messenger}}" id="messenger" placeholder="https://m.me/username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('messenger')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="whatsapp" class="col-form-label"><i class="fab fa-whatsapp" style="color: #075e54;"></i> Whats App :</label>
                                    <input type="text" name="whatsapp" class="form-control" value="{{$data->whatsapp}}" id="whatsapp" placeholder="https://whatsapp.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('whatsapp')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="telegram" class="col-form-label"><i class="fab fa-telegram-plane" style="color: #0088cc;"></i> Telegram :</label>
                                    <input type="text" name="telegram" class="form-control" value="{{$data->telegram}}" id="telegram" placeholder="https://telegram.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('telegram')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="youtube" class="col-form-label"><i class="fab fa-youtube" style="color: #FF0000;"></i> Youtube Channel Link :</label>
                                    <input type="text" name="youtube" class="form-control" value="{{$data->youtube}}" id="youtube" placeholder="https://youtube.com/">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('youtube')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="tiktok" class="col-form-label">
                                        <i class="fab fa-call" style="color: #000000;"></i> Tiktok Link :</label>
                                    <input type="text" name="tiktok" class="form-control" value="{{$data->tiktok}}" id="tiktok" placeholder="https://www.tiktok.com/@username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('tiktok')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="pinterest" class="col-form-label"><i class="fab fa-pinterest" style="color: #B7081B;"></i> Pinterest Link :</label>
                                    <input type="text" name="pinterest" class="form-control" value="{{$data->pinterest}}" id="pinterest" placeholder="https://www.pinterest.com/ideas">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('pinterest')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="viber" class="col-form-label"><i class="fab fa-viber" style="color: #793BAA;"></i> Viber :</label>
                                    <input type="text" name="viber" class="form-control" value="{{$data->viber}}" id="viber" placeholder="https://www.viber.com">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('viber')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>







                        <div class="form-group text-center pt-3 mt-3">
                            <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" type="submit" style="width: 140px;"><i class="fas fa-save"></i> Update Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

