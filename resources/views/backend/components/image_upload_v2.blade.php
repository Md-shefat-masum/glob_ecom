{{-- Reusable Image Upload Component V2 with Progress Bar --}}
<div class="image-upload-v2-wrapper">
    <label for="{{ $inputName ?? 'image' }}">
        {{ $label ?? 'Image' }}
        @if(isset($width) && isset($height))
            <span style="color: #666; font-size: 0.9em;">(h:{{ $height }} w:{{ $width }})</span>
        @endif
        @if(isset($required) && $required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <div class="position-relative image-upload-wrapper" style="margin-top: 10px;">
        <!-- Image Preview or Placeholder -->
        <div id="{{ $inputName ?? 'image' }}_preview" class="image-preview-container" style="display: none;">
            <img id="{{ $inputName ?? 'image' }}_img" 
                src="" 
                alt="Image" 
                class="uploaded-image"
                style="width: 100%; max-width: {{ $maxWidth ?? '500px' }}; height: {{ $previewHeight ?? '300px' }}; object-fit: contain; border-radius: 8px; cursor: pointer; border: 2px solid #e0e0e0;"
                onload="updateImageDimensions('{{ $inputName ?? 'image' }}', this)"
                onclick="document.getElementById('{{ $inputName ?? 'image' }}_input').click()">
            <div id="{{ $inputName ?? 'image' }}_dimensions" style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: none;"></div>
            <button type="button" 
                onclick="removeImageV2('{{ $inputName ?? 'image' }}')"
                class="btn-remove-image"
                style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                title="Remove image">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="{{ $inputName ?? 'image' }}_placeholder" 
            class="image-placeholder"
            style="width: 100%; max-width: {{ $maxWidth ?? '500px' }}; height: {{ $previewHeight ?? '300px' }}; border: 2px dashed #ccc; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f8f9fa;"
            onclick="document.getElementById('{{ $inputName ?? 'image' }}_input').click()">
            <i class="fas fa-camera" style="font-size: 48px; color: #999; margin-bottom: 10px;"></i>
            <span class="placeholder-text" style="color: #666;">Click to upload</span>
        </div>
        
        <!-- Progress Bar -->
        <div id="{{ $inputName ?? 'image' }}_progress" class="upload-progress" style="display: none; margin-top: 10px;">
            <div class="progress" style="height: 30px; border-radius: 4px; background: #f0f0f0;">
                <div id="{{ $inputName ?? 'image' }}_progress_bar" 
                    class="progress-bar progress-bar-striped progress-bar-animated" 
                    role="progressbar" 
                    style="width: 0%; background: #4f46e5; transition: width 0.3s ease;"
                    aria-valuenow="0" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                    <span id="{{ $inputName ?? 'image' }}_progress_text" style="line-height: 30px; color: white; font-weight: bold; padding: 0 10px;">Uploading... 0%</span>
                </div>
            </div>
            <div id="{{ $inputName ?? 'image' }}_progress_status" style="margin-top: 5px; text-align: center; font-size: 12px; color: #666; display: none;">
                <span id="{{ $inputName ?? 'image' }}_status_text"></span>
            </div>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" 
            id="{{ $inputName ?? 'image' }}_input"
            name="{{ $inputName ?? 'image' }}_file"
            onchange="handleImageUploadV2(event, '{{ $inputName ?? 'image' }}', {{ $width ?? 1920 }}, {{ $height ?? 800 }}, '{{ $directory ?? '' }}')"
            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
            style="display: none;">
    </div>
    
    <!-- Hidden input for storing media ID or path -->
    <input type="hidden" 
        id="{{ $inputName ?? 'image' }}_id" 
        name="{{ $inputName ?? 'image' }}" 
        value="{{ isset($value) ? (str_replace(env('FILE_URL').'/', '', $value)) : '' }}">
    <!-- Hidden input for storing media file ID -->
    <input type="hidden" 
        id="{{ $inputName ?? 'image' }}_media_id" 
        value="">
</div>

@push('footer_js')

<style>
    .image-preview-container {
        position: relative;
        display: inline-block;
    }
    
    .btn-remove-image:hover {
        background: #c82333 !important;
    }
    
    .upload-progress {
        width: 100%;
        max-width: {{ $maxWidth ?? '500px' }};
    }
    
    .image-upload-v2-wrapper {
        width: 100%;
    }
</style>

<script>
    function handleImageUploadV2(event, inputName, width, height, directory) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            toastr.error('Please select a valid image file (JPG, PNG, GIF, WEBP)');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            toastr.error('Image size should be less than 5MB');
            return;
        }

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(inputName + '_preview');
            const placeholder = document.getElementById(inputName + '_placeholder');
            const img = document.getElementById(inputName + '_img');
            
            img.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);

        // Show progress bar
        const progressContainer = document.getElementById(inputName + '_progress');
        const progressBar = document.getElementById(inputName + '_progress_bar');
        const progressText = document.getElementById(inputName + '_progress_text');
        const progressStatus = document.getElementById(inputName + '_progress_status');
        const statusText = document.getElementById(inputName + '_status_text');
        
        progressContainer.style.display = 'block';
        progressStatus.style.display = 'block';
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', 0);
        progressText.textContent = 'Uploading... 0%';
        statusText.textContent = 'Uploading file to server...';
        progressBar.classList.add('progress-bar-animated');
        progressBar.style.background = '#4f46e5';

        // Upload to server with progress tracking
        const formData = new FormData();
        formData.append('file', file);
        formData.append('width', width);
        formData.append('height', height);
        if (directory) {
            formData.append('directory', directory);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                         document.querySelector('input[name="_token"]')?.value;

        const xhr = new XMLHttpRequest();

        // Track upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                // Cap at 90% during upload phase
                const displayPercent = Math.min(percentComplete, 90);
                progressBar.style.width = displayPercent + '%';
                progressBar.setAttribute('aria-valuenow', displayPercent);
                progressText.textContent = 'Uploading... ' + displayPercent + '%';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    
                    if (data.success) {
                        // File upload complete, now processing (FTP upload)
                        progressBar.style.width = '95%';
                        progressBar.setAttribute('aria-valuenow', 95);
                        progressText.textContent = 'Processing...';
                        statusText.textContent = 'Processing image and uploading to FTP...';
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.style.background = '#ff9800';
                        
                        // Wait a bit to show processing, then complete
                        setTimeout(function() {
                            // Processing complete
                            progressBar.style.width = '100%';
                            progressBar.setAttribute('aria-valuenow', 100);
                            progressText.textContent = 'Completed!';
                            statusText.textContent = 'Image uploaded and processed successfully';
                            progressBar.style.background = '#28a745';
                            
                            // Store the path and media ID
                            document.getElementById(inputName + '_id').value = data.path;
                            if (data.id) {
                                document.getElementById(inputName + '_media_id').value = data.id;
                            }
                            
                            // Update preview with server URL
                            if (data.url) {
                                const imgElement = document.getElementById(inputName + '_img');
                                imgElement.src = data.url;
                                // Update title with dimensions if available
                                if (data.width && data.height) {
                                    imgElement.title = 'h:' + data.height + ' w:' + data.width;
                                    updateImageDimensions(inputName, imgElement);
                                }
                            }
                            
                            // Hide progress bar after 1 second
                            setTimeout(function() {
                                progressContainer.style.display = 'none';
                                progressStatus.style.display = 'none';
                            }, 1000);
                            
                            toastr.success('Image uploaded successfully!');
                        }, 500); // Small delay to show processing state
                    } else {
                        throw new Error(data.error || 'Upload failed');
                    }
                } catch (e) {
                    progressContainer.style.display = 'none';
                    progressStatus.style.display = 'none';
                    toastr.error('Failed to upload image. Please try again.');
                    removeImageV2(inputName);
                }
            } else {
                progressContainer.style.display = 'none';
                progressStatus.style.display = 'none';
                toastr.error('Upload failed. Please try again.');
                removeImageV2(inputName);
            }
        });

        xhr.addEventListener('error', function() {
            progressContainer.style.display = 'none';
            progressStatus.style.display = 'none';
            toastr.error('Network error. Please try again.');
            removeImageV2(inputName);
        });

        xhr.open('POST', '/media/upload');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.send(formData);
    }

    function updateImageDimensions(inputName, imgElement) {
        const dimensionsDiv = document.getElementById(inputName + '_dimensions');
        if (dimensionsDiv && imgElement) {
            // Get natural dimensions (original size)
            const width = imgElement.naturalWidth || imgElement.width;
            const height = imgElement.naturalHeight || imgElement.height;
            
            if (width && height) {
                dimensionsDiv.textContent = 'h:' + height + ' w:' + width;
                dimensionsDiv.style.display = 'block';
                // Update title attribute
                imgElement.title = 'h:' + height + ' w:' + width;
            }
        }
    }

    function removeImageV2(inputName) {
        const preview = document.getElementById(inputName + '_preview');
        const placeholder = document.getElementById(inputName + '_placeholder');
        const progressContainer = document.getElementById(inputName + '_progress');
        const progressStatus = document.getElementById(inputName + '_progress_status');
        const dimensionsDiv = document.getElementById(inputName + '_dimensions');
        const input = document.getElementById(inputName + '_input');
        const hiddenInput = document.getElementById(inputName + '_id');
        const mediaIdInput = document.getElementById(inputName + '_media_id');
        
        // Get media ID and path for deletion
        const mediaId = mediaIdInput ? mediaIdInput.value : '';
        const filePath = hiddenInput ? hiddenInput.value : '';
        
        // Delete from server if media ID or path exists
        if (mediaId || filePath) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                             document.querySelector('input[name="_token"]')?.value;
            
            const deleteData = new FormData();
            if (mediaId) {
                deleteData.append('media_id', mediaId);
            }
            if (filePath) {
                deleteData.append('file_path', filePath);
            }
            
            // Send delete request
            fetch('/media/delete-by-path', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: deleteData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Image deleted successfully');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                // Continue with UI reset even if delete fails
            });
        }
        
        // Reset UI
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
        progressContainer.style.display = 'none';
        if (progressStatus) {
            progressStatus.style.display = 'none';
        }
        if (dimensionsDiv) {
            dimensionsDiv.style.display = 'none';
        }
        input.value = '';
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        if (mediaIdInput) {
            mediaIdInput.value = '';
        }
    }

    // Initialize preview if value exists (for edit mode)
    @if(isset($value) && $value)
        document.addEventListener('DOMContentLoaded', function() {
            const preview = document.getElementById('{{ $inputName ?? 'image' }}_preview');
            const placeholder = document.getElementById('{{ $inputName ?? 'image' }}_placeholder');
            const img = document.getElementById('{{ $inputName ?? 'image' }}_img');
            const mediaIdInput = document.getElementById('{{ $inputName ?? 'image' }}_media_id');
            
            @if(isset($imageUrl) && $imageUrl)
                img.src = '{{ $imageUrl }}';
            @elseif($value)
                // Fallback: construct URL from value
                const baseUrl = '{{ env("FILE_URL", env("APP_URL", url("/"))) }}';
                const imagePath = '{{ $value }}';
                if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                    img.src = imagePath;
                } else if (imagePath.startsWith('uploads/')) {
                    img.src = baseUrl.replace(/\/$/, '') + '/' + imagePath;
                } else {
                    img.src = baseUrl.replace(/\/$/, '') + '/uploads/' + imagePath.replace(/^\//, '');
                }
            @endif
            
            if (img.src) {
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                // Update dimensions when image loads
                img.onload = function() {
                    updateImageDimensions('{{ $inputName ?? 'image' }}', this);
                };
                
                // Try to find media file ID by path (for edit mode)
                @if($value)
                    const imagePath = '{{ $value }}';
                    if (imagePath && !imagePath.startsWith('http://') && !imagePath.startsWith('https://')) {
                        // Fetch media file info by path
                        fetch('/media/find-by-path?path=' + encodeURIComponent(imagePath), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || 
                                               document.querySelector('input[name="_token"]')?.value
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.id && mediaIdInput) {
                                mediaIdInput.value = data.id;
                            }
                        })
                        .catch(error => {
                            console.log('Could not find media file ID:', error);
                        });
                    }
                @endif
            }
        });
    @endif
</script>

@endpush