@extends('backend.master')

@section('page_title')
    FAQ
@endsection
@section('page_heading')
    Add New FAQ
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">FAQ Create Form</h4>
                        <a href="{{ route('ViewAllFaqs')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/faq')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="question" class="col-sm-2 col-form-label">Question <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="question" class="form-control" id="question" placeholder="Question" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('question')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="answer" class="col-sm-2 col-form-label">Answer <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="answer" class="form-control" id="answer" rows="10"></textarea>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('answer')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Create FAQ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
