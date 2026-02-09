<div class="row g-4">

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label fw-semibold">Popup Title</label>
            <input type="text"
                   class="form-control"
                   v-model.trim="notification.title"
                   placeholder="Limited time offer">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label fw-semibold">Button Text</label>
            <input type="text"
                   class="form-control"
                   v-model.trim="notification.button_text"
                   placeholder="Shop Now">
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label class="form-label fw-semibold">Popup Description</label>
            <textarea class="form-control"
                      rows="3"
                      v-model.trim="notification.description"
                      placeholder="Highlight the key benefit or urgency for this product."></textarea>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label fw-semibold">Button URL</label>
            <input type="text"
                   class="form-control"
                   v-model.trim="notification.button_url"
                   placeholder="https://example.com/deal">
            <small class="text-muted">Provide an internal or external link for the popup button.</small>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold d-block">Popup Visibility</label>
        <div class="d-flex align-items-center gap-4">
            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       id="notification_show_yes"
                       :value="true"
                       v-model="notification.is_show">
                <label class="form-check-label" for="notification_show_yes">
                    Show popup
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       id="notification_show_no"
                       :value="false"
                       v-model="notification.is_show">
                <label class="form-check-label" for="notification_show_no">
                    Hide popup
                </label>
            </div>
        </div>
        <small class="text-muted">Enable to display this popup on the product detail page.</small>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Popup Image</label>
        <div class="position-relative border rounded p-3 text-center">
            <div v-if="notification.image_url" class="position-relative d-inline-block">
                <img :src="notification.image_url"
                     alt="Notification Image"
                     class="img-thumbnail"
                     style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                     @click="$refs.notificationImageInput.click()">
                <button type="button"
                        class="btn btn-danger btn-sm position-absolute"
                        style="top: -8px; right: -8px; padding: 4px 8px; font-size: 12px; border-radius: 50%;"
                        @click="removeNotificationImage(false)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div v-else class="w-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-2">Upload a featured image for the popup.</p>
                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        @click="$refs.notificationImageInput.click()">
                    <i class="fas fa-upload"></i> Upload Image
                </button>
            </div>
            <input type="file"
                   class="d-none"
                   ref="notificationImageInput"
                   accept="image/*"
                   @change="handleNotificationImageUpload">
        </div>
        <small class="text-muted d-block mt-2">Recommended size 600x600px. Supported formats: JPG, JPEG, PNG, GIF, WEBP.</small>
    </div>

</div>

