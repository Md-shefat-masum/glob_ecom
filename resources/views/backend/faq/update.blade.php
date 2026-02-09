@extends('backend.master')

@section('page_title')
    FAQ
@endsection
@section('page_heading')
    Update FAQ
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">FAQ Update Form</h4>
                        <a href="{{ route('ViewAllFaqs')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/faq')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="slug" value="{{$data->slug}}">
                        <div class="form-group row">
                            <label for="question" class="col-sm-2 col-form-label">Question <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="question" class="form-control" value="{{$data->question}}" id="question" placeholder="Question" required>
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
                                <textarea name="answer" class="form-control" id="answer" rows="10">{{$data->answer}}</textarea>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('answer')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="status" class="form-control" id="colFormLabe0" required>
                                    <option value="">Select One</option>
                                    <option value="1" @if($data->status == 1) selected @endif>Active</option>
                                    <option value="0" @if($data->status == 0) selected @endif>Inactive</option>
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Update FAQ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
