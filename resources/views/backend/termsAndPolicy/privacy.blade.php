@extends('backend.master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('page_title')
    Privacy Policy
@endsection
@section('page_heading')
    Update Privacy Policy
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Privacy Policy Update Form</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/privacy/policy')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12 border-right">
                                <div class="form-group">
                                    <label for="privacy">Write Privacy Policies Here :</label>
                                    <textarea id="privacy" name="privacy" class="form-control">{!! $data->privacy_policy !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('privacy')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Privacy Policy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $('#privacy').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });
    </script>
@endsection