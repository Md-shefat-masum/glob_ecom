@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/fancybox.css" rel="stylesheet" type="text/css" />
    <style>
        .single-gallery {
            position: relative;
            transition: all .2s linear;
        }
        .gallery-img {
            position: relative;
            height: 220px;
        }
        .gallery-img::before {
            position: absolute;
            content: "";
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(
                180deg,
                rgba(44, 51, 51, 0) 0%,
                rgba(44, 51, 51, 0) 48.75%,
                #20262e 100%
            );
            border-radius: 8px;
            z-index: 1;
        }
        .single-gallery .gallery-img {
            overflow: hidden;
            border-radius: 8px;
            transition: all .2s linear;
        }
        .single-gallery:hover .gallery-img img {
            transform: scale(1.03);
        }
        .gallery-img img {
            width: 100% !important;
            height: 400px !important;
            object-fit: cover;
            object-position: top left;
            border-radius: 8px;
            transition: all .2s linear;
        }
        .image-view-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 36px;
            height: 36px;
            background: #019267;
            border-radius: 100%;
            text-align: center;
            line-height: 38px;
            color: white;
            font-size: 18px;
            z-index: 2;
            opacity: 0;
            visibility: hidden;
        }
        .image-view-btn:hover {
            background: #019267;
            color: white;
        }
        .single-gallery:hover .image-view-btn {
            opacity: 1;
            visibility: visible;
        }
    </style>
@endsection

@section('page_title')
    Email
@endsection

@section('page_heading')
    Email Templates
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Choose Your Default Order Placed Mail Templates</h4>
                    <div class="row">

                        @foreach ($orderPlacedTemplates as $template)
                        <div class="col-lg-3 col-xl-3">
                            <div class="card" style="height: 300px; @if($template->status == 1) border: 2px solid green; @endif box-shadow: 2px 2px 5px #b5b5b5; overflow:hidden">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8"><i class="feather-mail" @if($template->status == 1) style="color: green" @endif></i> {{$template->title}}</div>
                                            <div class="col-lg-4 text-right">
                                                <input type="checkbox" class="switchery_checkbox" value="{{$template->id}}" @if($template->status == 1) checked @endif onchange="changeTemplateStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-12 text-center">
                                            <div class="single-gallery">
                                                <div class="gallery-img">
                                                    <img src="{{url($template->template_image)}}" class="img-fluid">
                                                    <a href="{{url($template->template_image)}}" data-fancybox="photo" class="image-view-btn"><i class="fa fa-eye"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/switchery/switchery.min.js"></script>
    <script src="{{url('assets')}}/js/fancybox.min.js"></script>
    <script>
        $('[data-toggle="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });

        function changeTemplateStatus(id){
            var templateId = id;
            $.ajax({
                type: "GET",
                url: "{{ url('change/mail/template/status') }}"+'/'+templateId,
                success: function (data) {
                    toastr.success("Status Changed", "Updated Successfully");
                    setTimeout(function() {
                        console.log("Wait For 1 Sec");
                        location.reload(true);
                    }, 1000);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    </script>
@endsection
