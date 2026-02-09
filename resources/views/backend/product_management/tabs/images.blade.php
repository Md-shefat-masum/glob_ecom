<div class="product-images-container">
    <!-- Main Images Section -->
    <div class="images-main-section">
        <!-- Product Image (Left) -->
        <div class="product-image-section">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-image"></i> Product Image 
                    <span class="text-danger">*</span>
                </h6>
            </div>
            <div class="image-upload-area">
                <div class="position-relative image-upload-wrapper">
                    <!-- Image Preview or Placeholder -->
                    <div v-if="product.product_image_url" class="image-preview-container">
                        <img :src="product.product_image_url" 
                            alt="Product Image" 
                            class="uploaded-image"
                            @click="$refs.productImageInput.click()">
                        <button type="button" 
                            @click="removeProductImage()"
                            class="btn-remove-image"
                            title="Remove image">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div v-else 
                        class="image-placeholder"
                        @click="$refs.productImageInput.click()">
                        <i class="fas fa-camera"></i>
                        <span class="placeholder-text">Click to upload</span>
                    </div>
                    
                    <!-- Hidden File Input -->
                    <input type="file" 
                        ref="productImageInput"
                        @change="handleProductImageUpload($event)"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        style="display: none;">
                </div>
            </div>
            <input type="hidden" name="product_image_id" v-model="product.product_image_id">
        </div>

        <!-- Gallery Images (Right) -->
        <div class="gallery-images-section">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-images"></i> Gallery Images
                    <span class="badge-optional">Optional</span>
                </h6>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item" v-for="(image, index) in 6" :key="index">
                    <div class="position-relative gallery-upload-wrapper">
                        <!-- Image Preview or Placeholder -->
                        <div v-if="product.gallery_images[index] && product.gallery_images[index].url" 
                            class="gallery-image-preview">
                            <img :src="product.gallery_images[index].url" 
                                alt="Gallery Image" 
                                class="uploaded-image"
                                @click="$refs['galleryImageInput' + index][0].click()">
                            <button type="button" 
                                @click="removeGalleryImage(index)"
                                class="btn-remove-image"
                                title="Remove image">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div v-else 
                            class="gallery-placeholder"
                            @click="$refs['galleryImageInput' + index][0].click()">
                            <i class="fas fa-images"></i>
                            <span class="placeholder-label">Gallery @{{ index + 1 }}</span>
                        </div>
                        
                        <!-- Hidden File Input -->
                        <input type="file" 
                            :ref="'galleryImageInput' + index"
                            @change="handleGalleryImageUpload($event, index)"
                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                            style="display: none;">
                    </div>
                </div>
            </div>
            <input type="hidden" name="gallery_image_ids" v-model="product.gallery_image_ids">
        </div>
    </div>

    <!-- Hints Section -->
    <div class="images-hints-section">
        <div class="hint-item">
            <div class="hint-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="hint-content">
                <strong>Product Image:</strong> Use high-quality images with a white or transparent background for best results. Recommended size: 800x800px. Max file size: 5MB.
            </div>
        </div>
        <div class="hint-item">
            <div class="hint-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="hint-content">
                <strong>Gallery Images:</strong> Show different angles, details, or usage scenarios of your product. Upload up to 6 images. Recommended size: 800x800px.
            </div>
        </div>
    </div>

    <!-- Image Guidelines -->
    <div class="image-guidelines-section">
        <div class="guidelines-header">
            <i class="fas fa-lightbulb"></i>
            <span>Image Guidelines</span>
        </div>
        <div class="guidelines-grid">
            <div class="guideline-item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Format:</strong>
                    <span>JPG, PNG, GIF, WEBP</span>
                </div>
            </div>
            <div class="guideline-item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Size:</strong>
                    <span>Maximum 5MB per file</span>
                </div>
            </div>
            <div class="guideline-item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Dimensions:</strong>
                    <span>Minimum 200x200px</span>
                </div>
            </div>
            <div class="guideline-item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Quality:</strong>
                    <span>Clear, well-lit photos</span>
                </div>
            </div>
        </div>
    </div>
</div>
