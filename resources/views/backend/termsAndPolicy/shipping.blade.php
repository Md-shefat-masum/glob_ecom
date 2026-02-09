@extends('backend.master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('page_title')
    Shipping Policy
@endsection
@section('page_heading')
    Update Shipping Policy
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Shipping Policy Update Form</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/shipping/policy')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12 border-right">
                                <div class="form-group">
                                    <label for="shipping">Write Shipping Policies Here :</label>
                                    <textarea id="shipping" name="shipping" class="form-control">{!! $data->shipping_policy !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('shipping')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Shipping Policy</button>
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
        $('#shipping').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });
    </script>
@endsection