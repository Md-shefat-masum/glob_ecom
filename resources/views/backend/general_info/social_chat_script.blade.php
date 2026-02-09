@extends('backend.master')

@section('header_css')
    <style>
        #v-pills-tabContent .tab-pane{
            border: 1px solid #dddd;
            padding: 12px 20px;
            border-radius: 4px;
        }

        a.nav-link{
            border: 1px solid #dddd;
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('page_title')
    Website Config
@endsection
@section('page_heading')
    Social Login & Chat Scripts
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Social Login & Chat Scripts</h4>
                    <p class="card-subtitle mb-4">Manage all your Social Login Scripts and Third Party Chat API</p>

                    <div class="row">
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active show" id="v-pills-ga-tab" data-toggle="pill" href="#v-pills-ga" role="tab" aria-controls="v-pills-ga"
                                    aria-selected="false">
                                    <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Google Analytic</span>
                                </a>
                                <a class="nav-link" id="v-pills-gt-tab" data-toggle="pill" href="#v-pills-gt" role="tab" aria-controls="v-pills-gt"
                                    aria-selected="false">
                                    <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Google Tag Manager</span>
                                </a>
                                {{-- <a class="nav-link" id="v-pills-fp-tab" data-toggle="pill" href="#v-pills-fp" role="tab" aria-controls="v-pills-fp"
                                    aria-selected="false">
                                    <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Facebook Pixel</span>
                                </a>

                                <a class="nav-link" id="v-pills-gr-tab" data-toggle="pill" href="#v-pills-gr" role="tab" aria-controls="v-pills-gr"
                                    aria-selected="true">
                                    <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Google Recaptcha</span>
                                </a> --}}
                                <a class="nav-link" id="v-pills-sl-tab" data-toggle="pill" href="#v-pills-sl" role="tab" aria-controls="v-pills-sl"
                                    aria-selected="false">
                                    <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Social Login</span>
                                </a>
                                {{-- <a class="nav-link" id="v-pills-messenger-chat-tab" data-toggle="pill" href="#v-pills-messenger-chat" role="tab" aria-controls="v-pills-messenger-chat"
                                    aria-selected="false">
                                    <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Messenger Chat Plugin</span>
                                </a> --}}
                                <a class="nav-link" id="v-pills-tawk-tab" data-toggle="pill" href="#v-pills-tawk" role="tab" aria-controls="v-pills-tawk"
                                    aria-selected="false">
                                    <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Tawk.to Live Chat</span>
                                </a>
                                {{-- <a class="nav-link" id="v-pills-crisp-tab" data-toggle="pill" href="#v-pills-crisp" role="tab" aria-controls="v-pills-crisp"
                                    aria-selected="false">
                                    <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                    <span class="d-none d-lg-block">Crisp Live Chat</span>
                                </a> --}}
                            </div>
                        </div> <!-- end col-->

                        <div class="col-sm-9">
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade active show" id="v-pills-ga" role="tabpanel" aria-labelledby="v-pills-ga-tab">
                                    <form action="{{url('update/google/analytic')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="google_analytic_status">Allow Google Analytic</label>
                                            <select id="google_analytic_status" class="form-control" name="google_analytic_status" required>
                                                <option value="1" @if($generalInfo->google_analytic_status == 1) selected @endif>Enable Google Analytic</option>
                                                <option value="0" @if($generalInfo->google_analytic_status == 0) selected @endif>Disable Google Analytic</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="google_analytic_tracking_id">Analytic Tracking ID</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->google_analytic_tracking_id}}" id="google_analytic_tracking_id" name="google_analytic_tracking_id" placeholder="ex. UA-842191520-6">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-gt" role="tabpanel" aria-labelledby="v-pills-gt-tab">
                                    <form action="{{url('update/google/tag/manager')}}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="google_tag_manager_status">Allow Google Tag Manager</label>
                                            <select id="google_tag_manager_status" class="form-control" name="google_tag_manager_status" required>
                                                <option value="1" @if($generalInfo->google_tag_manager_status == 1) selected @endif>Enable Google Tag Manager</option>
                                                <option value="0" @if($generalInfo->google_tag_manager_status == 0) selected @endif>Disable Google Tag Manager</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="google_analytic_tracking_id">Google Tag Manager ID</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->google_tag_manager_id}}" id="google_tag_manager_id" name="google_tag_manager_id" placeholder="ex. GTM-546FMKZS">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-fp" role="tabpanel" aria-labelledby="v-pills-fp-tab">
                                    <form action="{{url('update/facebook/pixel')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="fb_pixel_status">Allow Facebook Pixel</label>
                                            <select id="fb_pixel_status" class="form-control" name="fb_pixel_status" required>
                                                <option value="1" @if($generalInfo->fb_pixel_status == 1) selected @endif>Enable Facebook Pixel</option>
                                                <option value="0" @if($generalInfo->fb_pixel_status == 0) selected @endif>Disable Facebook Pixel</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="fb_pixel_app_id">Facebook App Id</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->fb_pixel_app_id}}" id="fb_pixel_app_id" name="fb_pixel_app_id" placeholder="ex. 97291160691059">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>


                                <div class="tab-pane fade" id="v-pills-gr" role="tabpanel" aria-labelledby="v-pills-gr-tab">
                                    <form action="{{url('update/google/recaptcha')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="captcha_status">Allow Recaptcha</label>
                                            <select id="captcha_status" class="form-control" name="captcha_status" required>
                                                <option value="1" @if($googleRecaptcha->status == 1) selected @endif>Enable Recaptcha</option>
                                                <option value="0" @if($googleRecaptcha->status == 0) selected @endif>Disable Recaptcha</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="captcha_site_key">Captcha Site Key</label>
                                            <input type="text" class="form-control" value="{{$googleRecaptcha->captcha_site_key}}" id="captcha_site_key" name="captcha_site_key" placeholder="ex. 6LcVO6cbAAAAOzIEwPlU66nL1rxD4VAS38tjpBX">
                                        </div>

                                        <div class="form-group">
                                            <label for="captcha_secret_key">Captcha Secret Key</label>
                                            <input type="text" class="form-control" value="{{$googleRecaptcha->captcha_secret_key}}" id="captcha_secret_key" name="captcha_secret_key" placeholder="ex. 6LcVO6cbAAAALVNrpZfNRfd0Gy_9a_fJRLiMVUI">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-sl" role="tabpanel" aria-labelledby="v-pills-sl-tab">
                                    <form action="{{url('update/social/login/info')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="fb_login_status">Allow Login with Facebook</label>
                                            <select id="fb_login_status" class="form-control" name="fb_login_status" required>
                                                <option value="1" @if($socialLoginInfo->fb_login_status  == 1) selected @endif>Enable Facebook Login</option>
                                                <option value="0" @if($socialLoginInfo->fb_login_status  == 0) selected @endif>Disable Facebook Login</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="fb_app_id">Facebook App Id</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->fb_app_id}}" id="fb_app_id" name="fb_app_id" placeholder="ex. 1844188565781706">
                                        </div>

                                        <div class="form-group">
                                            <label for="fb_app_secret">Facebook App Secret</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->fb_app_secret}}" id="fb_app_secret" name="fb_app_secret" placeholder="ex. f32f45abcf57a4dc23ac6f2b2e8e2241">
                                        </div>

                                        <div class="form-group">
                                            <label for="fb_redirect_url">Facebook Redirect Url</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->fb_redirect_url}}" id="fb_redirect_url" name="fb_redirect_url" placeholder="ex. http://localhost/web-solution-us/ecommerce_ibrahim/callback/google">
                                        </div>

                                        <hr style="border-color: #dddd; margin: 25px 0px;">

                                        <div class="form-group">
                                            <label for="gmail_login_status">Allow Login with Gmail</label>
                                            <select id="gmail_login_status" class="form-control" name="gmail_login_status" required>
                                                <option value="1" @if($socialLoginInfo->gmail_login_status  == 1) selected @endif>Allow Gmail Login</option>
                                                <option value="0" @if($socialLoginInfo->gmail_login_status  == 0) selected @endif>Disallow Gmail Login</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gmail_client_id">Gmail Client Id</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->gmail_client_id}}" id="gmail_client_id" name="gmail_client_id" placeholder="e.g. 123456789-xxxxxxxx.apps.googleusercontent.com">
                                        </div>

                                        <div class="form-group">
                                            <label for="gmail_secret_id">Gmail Secret Id</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->gmail_secret_id}}" id="gmail_secret_id" name="gmail_secret_id" placeholder="e.g. GOCSPX-xxxxxxxxxxxxxxxxxxxxx">
                                        </div>

                                        <div class="form-group">
                                            <label for="gmail_redirect_url">Gmail Redirect Url</label>
                                            <input type="text" class="form-control" value="{{$socialLoginInfo->gmail_redirect_url}}" id="gmail_redirect_url" name="gmail_redirect_url" placeholder="ex. http://localhost/web-solution-us/ecommerce_ibrahim/callback/google">
                                        </div>

                                        <div class="form-group mb-2 pt-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messenger-chat" role="tabpanel" aria-labelledby="v-pills-messenger-chat-tab">
                                    <form action="{{url('update/messenger/chat/info')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="tawk_chat_status">Allow Messenger Chat Plugin</label>
                                            <select id="tawk_chat_status" class="form-control" name="messenger_chat_status" required>
                                                <option value="1" @if($generalInfo->messenger_chat_status == 1) selected @endif>Enable Messenger Chat Plugin</option>
                                                <option value="0" @if($generalInfo->messenger_chat_status == 0) selected @endif>Disable Messenger Chat Plugin</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tawk_chat_link">Facebook Page ID</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->fb_page_id}}" id="fb_page_id" name="fb_page_id" placeholder="e.g. 65498765432165">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-tawk" role="tabpanel" aria-labelledby="v-pills-tawk-tab">
                                    <form action="{{url('update/tawk/chat/info')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="tawk_chat_status">Allow Tawk.to Live Chat</label>
                                            <select id="tawk_chat_status" class="form-control" name="tawk_chat_status" required>
                                                <option value="1" @if($generalInfo->tawk_chat_status == 1) selected @endif>Enable Tawk.to Live Chat</option>
                                                <option value="0" @if($generalInfo->tawk_chat_status == 0) selected @endif>Disable Tawk.to Live Chat</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tawk_chat_link">Tawk.to Direct Chat Link</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->tawk_chat_link}}" id="tawk_chat_link" name="tawk_chat_link" placeholder="ex. https://embed.tawk.to/5a7c31ed7591465c7077c48/default">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-crisp" role="tabpanel" aria-labelledby="v-pills-crisp-tab">
                                    <form action="{{url('update/crisp/chat/info')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="crisp_chat_status">Allow Crisp Live Chat</label>
                                            <select id="crisp_chat_status" class="form-control" name="crisp_chat_status" required>
                                                <option value="1" @if($generalInfo->crisp_chat_status == 1) selected @endif>Enable Crisp Live Chat</option>
                                                <option value="0" @if($generalInfo->crisp_chat_status == 0) selected @endif>Disable Crisp Live Chat</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="crisp_website_id">Crisp Website_ID</label>
                                            <input type="text" class="form-control" value="{{$generalInfo->crisp_website_id}}" id="crisp_website_id" name="crisp_website_id" placeholder="ex. 7b6ec17d-256a-41e8-9732-17ff58bd515t">
                                        </div>

                                        <div class="form-group mb-2">
                                            <button type="submit" class="btn btn-info">✓ Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- end tab-content-->
                        </div> <!-- end col-->
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection