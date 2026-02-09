@extends('backend.master')

@section('page_title')
    Config
@endsection
@section('page_heading')
    Setup Config
@endsection

@section('header_css')
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <style>
        .config_box{
            border: 1px solid #b7b7b7;
            border-radius: 4px;
            padding: 0px 15px;
            box-shadow: 1px 1px 2px #ddd;
        }
        .config_box input{
            margin-right: 8px;
        }
        .switchery {
            margin-right: 5px;
        }
        .config_box label{
            width: 100%;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Update Setup Config</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/config/setup')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            {{-- <div class="col-lg-3">
                                <div class="row">
                                    @foreach ($techConfigs as $config)
                                    <div class="col-lg-12">
                                        <div class="form-group config_box" @if($config->status == 1) style="background: linear-gradient(to right, #f2fcfe, #cfeeff);" @else style="background: linear-gradient(to right, #fff8f8, #ffd9de);" @endif>
                                            <label class="col-form-label">
                                                <input type="checkbox" class="switchery_checkbox" id="{{$config->code}}" @if($config->status == 1) checked @endif value="{{$config->code}}" onchange="changeStatus(this.value)" name="config_setup[]" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                                {{$config->name}} ({{$config->industry}})
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div> --}}
                            <div class="col-lg-3">
                                <div class="row">
                                    @foreach ($fashionConfigs as $config)
                                    <div class="col-lg-12">
                                        <div class="form-group config_box" @if($config->status == 1) style="background: linear-gradient(to right, #f2fcfe, #cfeeff);" @else style="background: linear-gradient(to right, #fff8f8, #ffd9de);" @endif>
                                            <label class="col-form-label">
                                                <input type="checkbox" class="switchery_checkbox" id="{{$config->code}}" @if($config->status == 1) checked @endif value="{{$config->code}}" onchange="changeStatus(this.value)" name="config_setup[]" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                                {{$config->name}} ({{$config->industry}})
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
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



@section('footer_js')
    <script src="{{url('assets')}}/plugins/switchery/switchery.min.js"></script>
    <script>
        $('[data-toggle="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });

        function changeStatus(value){
            var bgColor = $("#"+value).parent().parent().css("background-color");
            console.log(bgColor);
        }
    </script>
@endsection


