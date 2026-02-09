@extends('backend.master')

@section('header_css')
    <style>
        ol{
            font-weight: 500;
        }

        ol li {
            border: 1px solid;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            box-shadow: 2px 2px 5px rgb(199, 199, 199);
            width: 50%;
        }

        .clearfix::before,
        .clearfix::after {
            content: ' ';
            display: table;
        }

        .clearfix::after {
            clear: both;
        }

        small.instruction_text{
            color: #1e1e1e;
            font-weight: 500;
            font-size: 13px;
            display: block;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('page_title')
    Category
@endsection
@section('page_heading')
    Rearrange Categories
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Rearrange Sliders by Dragging them</h4>
                    <small class="instruction_text">[N/B: Drag the Item using your Mouse Cursor to Rearrange their Order. Then Press the save button to save the Rearranged Order]</small>

                    <form action="{{url('update/slider/rearranged/order')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <ol class="clearfix">
                            @php
                                $sl = 1;
                            @endphp
                            @foreach ($data as $item)
                            <li style="background: #{{rand(1000,9999)}};">
                                <input type="hidden" value="{{$item->slug}}" name="slug[]">
                                {{$sl++}}) <img src="{{url($item->image)}}" width="70"> {{$item->slug}}
                            </li>
                            @endforeach
                        </ol>
                        <button type="submit" class="btn rounded btn-primary">Save Rearranged Order</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('js')}}/jquery.dragndrop.js"></script>
    <script>
        $(function(){
            $('ol').dragndrop({
                onDrop: function( element, droppedElement ) {
                    console.log( 'element dropped: ' );
                    console.log( droppedElement );
                }
            });
        });
    </script>
@endsection
