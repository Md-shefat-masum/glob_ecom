{{-- Reusable Slider Image Upload Component --}}
<div class="slider-image-upload-wrapper">
    <label for="{{ $inputName ?? 'slider_image' }}">{{ $label ?? 'Slider Image' }} 
        @if(isset($required) && $required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <div class="position-relative image-upload-wrapper" style="margin-top: 10px;">
        <!-- Image Preview or Placeholder -->
        <div id="{{ $inputName ?? 'slider_image' }}_preview" class="image-preview-container" style="display: none;">
            <img id="{{ $inputName ?? 'slider_image' }}_img" 
                src="" 
                alt="Slider Image" 
                class="uploaded-image"
                style="width: 100%; max-width: 500px; height: 300px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #e0e0e0;"
                onclick="document.getElementById('{{ $inputName ?? 'slider_image' }}_input').click()">
            <button type="button" 
                onclick="removeSliderImage('{{ $inputName ?? 'slider_image' }}')"
                class="btn-remove-image"
                style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                title="Remove image">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="{{ $inputName ?? 'slider_image' }}_placeholder" 
            class="image-placeholder"
            style="width: 100%; max-width: 500px; height: 300px; border: 2px dashed #ccc; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f8f9fa;"
            onclick="document.getElementById('{{ $inputName ?? 'slider_image' }}_input').click()">
            <i class="fas fa-camera" style="font-size: 48px; color: #999; margin-bottom: 10px;"></i>
            <span class="placeholder-text" style="color: #666;">Click to upload</span>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" 
            id="{{ $inputName ?? 'slider_image' }}_input"
            name="{{ $inputName ?? 'slider_image' }}_file"
            onchange="handleSliderImageUpload(event, '{{ $inputName ?? 'slider_image' }}')"
            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
            style="display: none;">
    </div>
    
    <!-- Hidden input for storing media ID or path -->
    <input type="hidden" 
        id="{{ $inputName ?? 'slider_image' }}_id" 
        name="{{ $inputName ?? 'image' }}" 
        value="{{ $value ?? '' }}">
</div>

<style>
    .image-preview-container {
        position: relative;
        display: inline-block;
    }
    
    .btn-remove-image:hover {
        background: #c82333 !important;
    }
</style>

<script>
    function handleSliderImageUpload(event, inputName) {
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

        // Show uploading notification
        toastr.info('Uploading image...', 'Please wait', {
            timeOut: 0,
            extendedTimeOut: 0,
            closeButton: false,
            progressBar: true
        });

        // Upload to server
        const formData = new FormData();
        formData.append('file', file);
        formData.append('width', 1920);
        formData.append('height', 800);

        fetch('/media/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Upload failed');
            }
            return response.json();
        })
        .then(data => {
            // Clear previous toastr
            toastr.clear();
            
            if (data.success) {
                // Store the path
                document.getElementById(inputName + '_id').value = data.path;
                
                // Update preview with server URL
                if (data.url) {
                    document.getElementById(inputName + '_img').src = data.url;
                }
                
                toastr.success('Image uploaded successfully!');
            } else {
                throw new Error(data.error || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            toastr.clear();
            toastr.error('Failed to upload image. Please try again.');
            removeSliderImage(inputName);
            event.target.value = '';
        });
    }

    function removeSliderImage(inputName) {
        const preview = document.getElementById(inputName + '_preview');
        const placeholder = document.getElementById(inputName + '_placeholder');
        const input = document.getElementById(inputName + '_input');
        const hiddenInput = document.getElementById(inputName + '_id');
        
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
        input.value = '';
        hiddenInput.value = '';
    }

    // Initialize preview if value exists (for edit mode)
    @if(isset($value) && $value)
        document.addEventListener('DOMContentLoaded', function() {
            const preview = document.getElementById('{{ $inputName ?? 'slider_image' }}_preview');
            const placeholder = document.getElementById('{{ $inputName ?? 'slider_image' }}_placeholder');
            const img = document.getElementById('{{ $inputName ?? 'slider_image' }}_img');
            
            if ('{{ $value }}') {
                img.src = '{{ asset($value) }}';
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
        });
    @endif
</script>

