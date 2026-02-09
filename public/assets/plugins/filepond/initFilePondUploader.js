function initFilePondUploader({
    selector = '.file_upload',
    uploadUrl = '/api/v1/media/upload',
    deleteUrl = '/api/v1/media/delete',
    height = 100,
    width = 100,
    folder = 'default-folder',
    disk = 's3',
    media_folder_id = '1',
    upload_callback = () => '',
    revert_callback = () => '',
}) {

    let file_selector = document.querySelector(selector);
    if (!selector) {
        return console.error('Filepond selector not found');
    }

    FilePond.create(file_selector, {
        server: {
            process: (fieldName, file, metadata, load, error, progress, abort) => {
                const formData = new FormData();
                formData.append(fieldName, file);
                formData.append('height', height);
                formData.append('width', width);
                formData.append('folder', folder);
                formData.append('disk', disk);
                formData.append('media_folder_id', media_folder_id);

                const source = axios.CancelToken.source();

                axios.post(uploadUrl, formData, {
                    cancelToken: source.token,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: (e) => {
                        progress(true, e.loaded, e.total);
                    }
                })
                    .then(async (response) => {
                        await load(response.data.data);
                        upload_callback(response.data.data);
                    })
                    .catch((err) => {
                        console.error(err);
                        error('Upload failed');
                    });

                return {
                    abort: () => {
                        source.cancel('Upload aborted by user');
                        abort();
                    }
                };
            },

            revert: (serverId, load, error) => {
                axios.delete(deleteUrl, {
                    data: {
                        path: serverId,
                        disk: disk
                    }
                })
                    .then(() => {
                        load();
                        revert_callback();
                    })
                    .catch(err => {
                        console.error(err);
                        error('Delete failed');
                    });
            }
        }
    });
}
