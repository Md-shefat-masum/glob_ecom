<!-- Subcategory Create Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1" aria-labelledby="subcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subcategoryModalLabel">Create New Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createSubcategory">
                    <div class="mb-3">
                        <label for="subcategoryCategory" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="subcategoryCategory" v-model="newSubcategory.category_id" required disabled>
                            <option value="">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                @{{ category.name }}
                            </option>
                        </select>
                        <small class="text-muted">Category is automatically selected from the form</small>
                    </div>
                    <div class="mb-3">
                        <label for="subcategoryName" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subcategoryName" v-model="newSubcategory.name" required>
                        <div class="invalid-feedback d-block" v-if="subcategoryErrors.name">
                            @{{ subcategoryErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subcategorySlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="subcategorySlug" v-model="newSubcategory.slug" placeholder="auto-generated">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        <div class="invalid-feedback d-block" v-if="subcategoryErrors.slug">
                            @{{ subcategoryErrors.slug[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subcategoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="subcategoryStatus" v-model.number="newSubcategory.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createSubcategory" :disabled="subcategoryCreating">
                    <span v-if="subcategoryCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ subcategoryCreating ? 'Creating...' : 'Create Subcategory' }}
                </button>
            </div>
        </div>
    </div>
</div>

