<div>
    <input type="file" class="{{ $selector_class }}" name="file" accept="{{ $accept }}" />
    <input type="hidden" name="{{ $input_name }}">
</div>

@push('js')
    <link rel="stylesheet" href="/assets/plugins/filepond/css/filepond.css">
    <link rel="stylesheet" href="/assets/plugins/filepond/css/preview.css">

    <script src="/assets/plugins/filepond/js/filepond.js"></script>
    <script src="/assets/plugins/filepond/js/filepond_preview.js"></script>
    <script src="/assets/plugins/filepond/js/filepond_meta.js"></script>

    <script src="/assets/plugins/filepond/initFilePondUploader.js"></script>

    <script>
        FilePond.registerPlugin(FilePondPluginFileMetadata);
        FilePond.registerPlugin(FilePondPluginImagePreview);

        initFilePondUploader({
            selector: '.{{ $selector_class }}',
            uploadUrl: '/api/v1/media/upload?bucket=chhatrasangbadbd',
            deleteUrl: '/api/v1/media/delete?bucket=chhatrasangbadbd',
            height: `{{ $height }}`,
            width: `{{ $width }}`,
            folder: `{{ $folder }}`,
            disk: '{{ $disk }}',
            media_folder_id: '{{ $media_folder_id }}',
            upload_callback: (path) => document.querySelector('[name="{{ $input_name }}"]').value = path,
            revert_callback: () => document.querySelector('[name="{{ $input_name }}"]').value = '',
        });
    </script>
@endpush
