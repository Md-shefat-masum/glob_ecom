@extends('backend.master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('page_title')
    Return Policy
@endsection
@section('page_heading')
    Update Return Policy
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Return Policy Update Form</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/return/policy')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12 border-right">
                                <div class="form-group">
                                    <label for="return">Write Return Policies Here :</label>
                                    <textarea id="return" name="return" class="form-control">{!! $data->return_policy !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('return')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Return Policy</button>
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
        $('#return').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });
    </script>
@endsection