<!-- Category Create Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createCategory">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" v-model="newCategory.name" required>
                        <div class="invalid-feedback" v-if="categoryErrors.name">
                            @{{ categoryErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categorySlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="categorySlug" v-model="newCategory.slug" placeholder="auto-generated">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        <div class="invalid-feedback" v-if="categoryErrors.slug">
                            @{{ categoryErrors.slug[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" v-model.number="newCategory.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createCategory" :disabled="categoryCreating">
                    <span v-if="categoryCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ categoryCreating ? 'Creating...' : 'Create Category' }}
                </button>
            </div>
        </div>
    </div>
</div>

