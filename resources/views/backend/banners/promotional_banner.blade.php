@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <style>
        .banner {
            background: rgb(240, 240, 240);
            border-radius: 10px;
            padding: 60px;
        }

        .banner h5 {
            font-style: normal;
            font-weight: 500;
            font-size: 20px;
            margin-bottom: 20px;
            color: #222831;
        }

        .banner h5 img {
            width: 35px;
            margin-right: 10px
        }

        .banner h1 {
            font-style: normal;
            font-weight: 700;
            font-size: 50px;
            line-height: 120%;
            color: #222831;
            margin-bottom: 25px
        }

        .banner a {
            display: inline-block;
            background: #0074E4;
            padding: 10px 24px;
            border-radius: 4px;
            color: white
        }

        .banner .product_info {
            padding-top: 140px;
            position: relative;
        }

        .banner .product_info img {
            border-style: none;
            position: absolute;
            top: -155px;
            width: 250px;
            left: 50%;
            transform: translateX(-50%);
        }

        .banner .product_info .product_timer {
            display: inline-flex;
        }

        .banner .product_info .product_timer .circle {
            background: white;
            height: 100px;
            width: 100px;
            border-radius: 50%;
            margin: 0px 10px;
            padding-top: 20px
        }

        .banner .product_info .product_timer .circle h3 {
            margin: 0;
            padding: 0;
            font-size: 32px;
        }

        .banner .product_info .product_timer .circle span {
            font-weight: 400;
            color: gray;
        }
    </style>
@endsection

@section('page_title')
    Banner
@endsection
@section('page_heading')
    Promotional Banner
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Set Info for Promotional Banner</h4>

                    {{-- <div class="row">
                        <div class="col-lg-12 p-5">
                            @if ($promotionalBanner)

                            <div class="banner" style="@if($promotionalBanner->background_image) 
                                            background: url('{{url($promotionalBanner->background_image)}}'); 
                                            background-repeat: no-repeat; background-size: cover; 
                                        @else 
                                            background: {{$promotionalBanner->background_color}}; 
                                        @endif">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5
                                            style="@if($promotionalBanner->heading_color) color: {{$promotionalBanner->heading_color}} @endif;">
                                            @if($promotionalBanner->icon &&
                                            file_exists(public_path($promotionalBanner->icon)))
                                            <img src="{{url($promotionalBanner->icon)}}">
                                            @endif
                                            {{$promotionalBanner->heading}}
                                        </h5>
                                        <h2
                                            style="@if($promotionalBanner->title_color) color: {{$promotionalBanner->title_color}} @endif; margin-bottom: 20px">
                                            {{$promotionalBanner->title}}</h2>
                                        <h6
                                            style="@if($promotionalBanner->description_color) color: {{$promotionalBanner->description_color}} @endif; margin-bottom: 50px">
                                            {{$promotionalBanner->description}}</h6>
                                        <a href="{{$promotionalBanner->url}}" target="_blank"
                                            style="@if($promotionalBanner->btn_text_color) color: {{$promotionalBanner->btn_text_color}}; @endif @if($promotionalBanner->btn_bg_color) background: {{$promotionalBanner->btn_bg_color}}; @endif">{{$promotionalBanner->btn_text}}</a>
                                    </div>
                                    <div class="col-lg-6 text-center">
                                        <div class="product_info">
                                            @if($promotionalBanner->product_image &&
                                            file_exists(public_path($promotionalBanner->product_image)))
                                            <img src="{{url($promotionalBanner->product_image)}}">
                                            @endif

                                            @php
                                            $datetime1 = new DateTime($promotionalBanner->started_at);
                                            $datetime2 = new DateTime($promotionalBanner->end_at);
                                            $interval = $datetime1->diff($datetime2);
                                            @endphp

                                            <div class="product_timer">
                                                <div class="circle"
                                                    style="@if($promotionalBanner->time_bg_color) background: {{$promotionalBanner->time_bg_color}}; @endif">
                                                    <h3>{{$interval->format('%d')}}</h3>
                                                    <span>Days</span>
                                                </div>
                                                <div class="circle"
                                                    style="@if($promotionalBanner->time_bg_color) background: {{$promotionalBanner->time_bg_color}}; @endif">
                                                    <h3>{{$interval->format('%H')}}</h3>
                                                    <span>Hours</span>
                                                </div>
                                                <div class="circle"
                                                    style="@if($promotionalBanner->time_bg_color) background: {{$promotionalBanner->time_bg_color}}; @endif">
                                                    <h3>{{$interval->format('%i')}}</h3>
                                                    <span>Mins</span>
                                                </div>
                                                <div class="circle"
                                                    style="@if($promotionalBanner->time_bg_color) background: {{$promotionalBanner->time_bg_color}}; @endif">
                                                    <h3>{{$interval->format('%s')}}</h3>
                                                    <span>Sec</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div> --}}

                    <form class="needs-validation" method="POST" action="{{url('update/promotional/banner')}}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-2">
                                {{-- <div class="form-group row">
                                    <label for="icon" class="col-sm-12">Header Icon: <a
                                            href="{{url('remove/promotional/banner/header/icon')}}"
                                            class="btn btn-sm d-inline-block">❌ Remove</a></label>
                                    <div class="col-sm-12">
                                        <input type="file" name="icon" class="dropify" data-height="112"
                                            data-max-file-size="1M" accept="image/*" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="product_image" class="col-sm-12">Product Image: <a
                                            href="{{url('remove/promotional/banner/product/image')}}"
                                            class="btn btn-sm d-inline-block">❌ Remove</a></label>
                                    <div class="col-sm-12">
                                        <input type="file" name="product_image" class="dropify" data-height="150"
                                            data-max-file-size="1M" accept="image/*" />
                                    </div>
                                </div> --}}
                                <div class="form-group row">
                                    <label for="background_image" class="col-sm-12">Background Image: (1531 × 500)<a
                                            href="{{url('remove/promotional/banner/bg/image')}}"
                                            class="btn btn-sm d-inline-block">❌ Remove</a></label>
                                    <div class="col-sm-12">
                                        <input type="file" name="background_image" class="dropify" data-height="150"
                                            data-max-file-size="1M" accept="image/*" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-10">

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="heading_color" class="col-sm-12 col-form-label">Heading Text
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="heading_color"
                                                    value="{{$promotionalBanner->heading_color ?? ''}}" class="form-control"
                                                    id="heading_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('heading_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group row">
                                            <label for="heading" class="col-sm-12 col-form-label">Heading Text</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="heading"
                                                    value="{{$promotionalBanner->heading ?? ''}}" class="form-control"
                                                    id="heading" placeholder="Heading Text">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('heading')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="title_color" class="col-sm-12 col-form-label">Title Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="title_color"
                                                    value="{{$promotionalBanner->title_color ?? ''}}" class="form-control"
                                                    id="title_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('title_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group row">
                                            <label for="title" class="col-sm-12 col-form-label">Title Text</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="title" value="{{$promotionalBanner->title ?? ''}}"
                                                    class="form-control" id="title" placeholder="Title Text">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('title')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="description_color" class="col-sm-12 col-form-label">Description
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="description_color"
                                                    value="{{$promotionalBanner->description_color ?? ''}}"
                                                    class="form-control" id="description_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('description_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group row">
                                            <label for="description" class="col-sm-12 col-form-label">Description</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="description"
                                                    value="{{$promotionalBanner->description ?? ''}}" class="form-control"
                                                    id="description" placeholder="Description">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('description')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="time_bg_color" class="col-sm-12 col-form-label">Time Background
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="time_bg_color"
                                                    value="{{$promotionalBanner->time_bg_color ?? ''}}" class="form-control"
                                                    id="time_bg_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('time_bg_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="time_font_color" class="col-sm-12 col-form-label">Time Font
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="time_font_color"
                                                    value="{{$promotionalBanner->time_font_color ?? ''}}"
                                                    class="form-control" id="time_font_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('time_font_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group row">
                                            <label for="started_at" class="col-sm-12 col-form-label">Time Start From</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="datetimepicker"
                                                    value="{{$promotionalBanner->started_at ?? ''}}" name="started_at">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('started_at')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group row">
                                            <label for="end_at" class="col-sm-12 col-form-label">Time End At</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="datetimepicker2"
                                                    value="{{$promotionalBanner->end_at ?? ''}}" name="end_at">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('end_at')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="btn_text_color" class="col-sm-12 col-form-label">Button Text
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="btn_text_color"
                                                    value="{{$promotionalBanner->btn_text_color ?? ''}}"
                                                    class="form-control" id="btn_text_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('btn_text_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="btn_bg_color" class="col-sm-12 col-form-label">Button Background
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="btn_bg_color"
                                                    value="{{$promotionalBanner->btn_bg_color ?? ''}}" class="form-control"
                                                    id="btn_bg_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('btn_bg_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group row">
                                            <label for="btn_text" class="col-sm-12 col-form-label">Button Text</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="btn_text"
                                                    value="{{$promotionalBanner->btn_text ?? ''}}" class="form-control"
                                                    id="btn_text" placeholder="Button Text">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('btn_text')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group row">
                                            <label for="url" class="col-sm-12 col-form-label">Button URL</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="url" value="{{$promotionalBanner->url ?? ''}}"
                                                    class="form-control" id="url" placeholder="https://">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('url')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group row">
                                            <label for="background_color" class="col-sm-12 col-form-label">Background
                                                Color</label>
                                            <div class="col-sm-12">
                                                <input type="color" name="background_color"
                                                    value="{{$promotionalBanner->background_color ?? ''}}"
                                                    class="form-control" id="background_color">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('background_color')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                                <small class="text-danger">Background Image has the High Priority</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-10">
                                        <div class="form-group row">
                                            <label for="video_url" class="col-sm-12 col-form-label">Video URL</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="video_url"
                                                    value="{{$promotionalBanner->video_url ?? ''}}" class="form-control"
                                                    id="video_url" placeholder="https://">
                                                <div class="invalid-feedback" style="display: block;">
                                                    @error('video_url')
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>


                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Banner Info</button>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/js/jquery.datetimepicker.full.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script>
        $("#datetimepicker").datetimepicker({
            // theme: "dark",
            // timepicker: false,
            minDate: "-1970/01/01",
            // maxDate: "+1970/01/2",
            // datepicker: false,
            // allowTimes: [
            //     "12:00",
            //     "13:00",
            //     "15:00",
            //     "17:00",
            //     "17:05",
            //     "17:20",
            //     "19:00",
            //     "20:00",
            // ],
            format: "d/m/Y H:i:s",
            // formatDate: "Y/m/d H:i:s",
            // disabledDates: ["1986/01/08", "1986/01/09", "1986/01/10"],
            // startDate: "1986/01/05",
            // value: "2015/04/15 05:03",
        });
        $("#datetimepicker2").datetimepicker({
            // theme: "dark",
            // timepicker: false,
            minDate: "-1970/01/01",
            // maxDate: "+1970/01/2",
            // datepicker: false,
            // allowTimes: [
            //     "12:00",
            //     "13:00",
            //     "15:00",
            //     "17:00",
            //     "17:05",
            //     "17:20",
            //     "19:00",
            //     "20:00",
            // ],
            format: "Y-m-d H:i:s",
            // formatDate: "Y-m-d H:i:s",
            // disabledDates: ["1986/01/08", "1986/01/09", "1986/01/10"],
            // startDate: "1986/01/05",
            // value: "2015/04/15 05:03",
        });
        // $("#datetimepicker4").datetimepicker("show");
        // $("#datetimepicker4").datetimepicker("hide");
        // $("#datetimepicker4").datetimepicker("reset");
    </script>

    <script>
        @if (!is_null($promotionalBanner))
            @if($promotionalBanner->background_image && file_exists(public_path($promotionalBanner->background_image)))
                $(".dropify-preview").eq(0).css("display", "block");
                $(".dropify-clear").eq(0).css("display", "block");
                $(".dropify-filename-inner").eq(0).html("{{$promotionalBanner->background_image}}");
                $("span.dropify-render").eq(0).html("<img src='{{url($promotionalBanner->background_image)}}'>");
            @endif

            // @if($promotionalBanner->icon && file_exists(public_path($promotionalBanner->icon)))
            //     $(".dropify-preview").eq(0).css("display", "block");
            //     $(".dropify-clear").eq(0).css("display", "block");
            //     $(".dropify-filename-inner").eq(0).html("{{$promotionalBanner->icon}}");
            //     $("span.dropify-render").eq(0).html("<img src='{{url($promotionalBanner->icon)}}'>");
            // @endif

            // @if($promotionalBanner->product_image && file_exists(public_path($promotionalBanner->product_image)))
            //     $(".dropify-preview").eq(1).css("display", "block");
            //     $(".dropify-clear").eq(1).css("display", "block");
            //     $(".dropify-filename-inner").eq(1).html("{{$promotionalBanner->product_image}}");
            //     $("span.dropify-render").eq(1).html("<img src='{{url($promotionalBanner->product_image)}}'>");
            // @endif


        @endif
    </script>
@endsection