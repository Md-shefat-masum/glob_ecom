@extends('backend.master')

@section('header_css')
    <link rel="stylesheet" href="{{ versioned_url('codeMirror/css/codemirror.css') }}">
    <link rel="stylesheet" href="{{ versioned_url('codeMirror/css/themes/material.css') }}">
    <style>
        .CodeMirror {
            border-radius: 6px;
            padding: 12px 3px;
        }
        .CodeMirror-scroll{
            width: 100%;
        }
    </style>
@endsection

@section('header_js')
    <script src="{{ versioned_url('codeMirror/js/codemirror.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/xml.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/php.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/javascript.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/python.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/addons/closetag.js') }}"></script>
    <script src="{{ versioned_url('codeMirror/js/addons/closebrackets.js') }}"></script>
@endsection

@section('page_title')
    Content Module
@endsection
@section('page_heading')
    Custom CSS & JS
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">

                    <form class="needs-validation" method="POST" action="{{url('update/custom/css/js')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-3">Custom CSS & JS Form</h4>
                            </div>
                            <div class="col-lg-6 text-right">
                                <div class="form-group">
                                    <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" type="submit" style="width: 140px;"><i class="fas fa-save"></i> Update Code</button>
                                </div>
                            </div>
                        </div>


                        <div class="row border-top">
                            <div class="col-lg-4">
                                <div class="form-group mt-3">
                                    <label for="custom_css" class="col-form-label">Write Custom CSS</label>
                                    <textarea name="custom_css" class="form-control" id="code_editor_css" style="cursor: pointer">{{$data->custom_css}}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mt-3">
                                    <label for="header_script" class="col-form-label">Header Custom Script</label>
                                    <textarea name="header_script" class="form-control" id="header_script" style="cursor: pointer">{{$data->header_script}}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mt-3">
                                    <label for="footer_script" class="col-form-label">Footer Custom Script</label>
                                    <textarea name="footer_script" class="form-control" id="footer_script" style="cursor: pointer">{{$data->footer_script}}</textarea>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script>
        var textareas = document.getElementById("code_editor_css");
        editor = CodeMirror.fromTextArea(textareas, {
            mode: "javascript",
            theme: "material",
            lineNumbers: true,
            autoCloseTags: true,
            autoCloseBrackets: true
        });
        editor.setSize("100%", "600");

        var textareas = document.getElementById("header_script");
        editor = CodeMirror.fromTextArea(textareas, {
            mode: "javascript",
            theme: "material",
            lineNumbers: true,
            autoCloseTags: true,
            autoCloseBrackets: true
        });
        editor.setSize("100%", "600");

        var textareas = document.getElementById("footer_script");
        editor = CodeMirror.fromTextArea(textareas, {
            mode: "javascript",
            theme: "material",
            lineNumbers: true,
            autoCloseTags: true,
            autoCloseBrackets: true
        });
        editor.setSize("100%", "600");
    </script>
@endsection