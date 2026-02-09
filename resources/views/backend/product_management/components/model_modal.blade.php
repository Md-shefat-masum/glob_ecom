<!-- Model Create Modal -->
<div class="modal fade" id="modelModal" tabindex="-1" aria-labelledby="modelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelModalLabel">Create New Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createModel">
                    <div class="mb-3">
                        <label for="modelBrand" class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select" id="modelBrand" v-model="newModel.brand_id" required disabled>
                            <option value="">Select Brand</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                @{{ brand.name }}
                            </option>
                        </select>
                        <small class="text-muted">Brand is automatically selected from the form</small>
                    </div>
                    <div class="mb-3">
                        <label for="modelName" class="form-label">Model Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modelName" v-model="newModel.name" required>
                        <div class="invalid-feedback d-block" v-if="modelErrors.name">
                            @{{ modelErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modelCode" class="form-label">Model Code</label>
                        <input type="text" class="form-control" id="modelCode" v-model="newModel.code" placeholder="Optional model code">
                        <div class="invalid-feedback d-block" v-if="modelErrors.code">
                            @{{ modelErrors.code[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modelSlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="modelSlug" v-model="newModel.slug" placeholder="auto-generated">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        <div class="invalid-feedback d-block" v-if="modelErrors.slug">
                            @{{ modelErrors.slug[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modelStatus" class="form-label">Status</label>
                        <select class="form-select" id="modelStatus" v-model.number="newModel.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createModel" :disabled="modelCreating">
                    <span v-if="modelCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ modelCreating ? 'Creating...' : 'Create Model' }}
                </button>
            </div>
        </div>
    </div>
</div>

