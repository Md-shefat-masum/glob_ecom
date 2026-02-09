@extends('backend.master')

@section('page_title')
    File Manager
@endsection
@section('page_heading')
    Manage Files in File Manager
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Manage Your Files</h4>
                    <iframe src="/laravel-filemanager?editor=src&type=Images" style="width: 100%; height: 600px; overflow: hidden; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("footer_js")
    <script>
        // var lfm = function(id, type, options) {
        //     let button = document.getElementById(id);

        //     button.addEventListener('click', function() {
        //         var route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
        //         var target_input = document.getElementById(button.getAttribute('data-input'));
        //         var target_preview = document.getElementById(button.getAttribute('data-preview'));

        //         window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager',
        //             'width=900,height=600');
        //         window.SetUrl = function(items) {
        //             var file_path = items.map(function(item) {
        //                 return item.url;
        //             }).join(',');

        //             // set the value of the desired input to image url
        //             target_input.value = file_path;
        //             target_input.dispatchEvent(new Event('change'));

        //             // clear previous preview
        //             target_preview.innerHtml = '';

        //             // set or change the preview image src
        //             items.forEach(function(item) {
        //                 let img = document.createElement('img')
        //                 img.setAttribute('style', 'height: 5rem')
        //                 img.setAttribute('src', item.thumb_url)
        //                 target_preview.appendChild(img);
        //             });

        //             // trigger change event
        //             target_preview.dispatchEvent(new Event('change'));
        //         };
        //     });
        // };

        // lfm('lfm2', 'file', {
        //     prefix: route_prefix
        // });
    </script>
@endsection
