<!-- Brand Create Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Create New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createBrand">
                    <div class="mb-3">
                        <label for="brandName" class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="brandName" v-model="newBrand.name" required>
                        <div class="invalid-feedback d-block" v-if="brandErrors.name">
                            @{{ brandErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="brandSlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="brandSlug" v-model="newBrand.slug" placeholder="auto-generated">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        <div class="invalid-feedback d-block" v-if="brandErrors.slug">
                            @{{ brandErrors.slug[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="brandStatus" class="form-label">Status</label>
                        <select class="form-select" id="brandStatus" v-model.number="newBrand.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createBrand" :disabled="brandCreating">
                    <span v-if="brandCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ brandCreating ? 'Creating...' : 'Create Brand' }}
                </button>
            </div>
        </div>
    </div>
</div>

