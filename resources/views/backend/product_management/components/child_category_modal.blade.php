<!-- Child Category Create Modal -->
<div class="modal fade" id="childCategoryModal" tabindex="-1" aria-labelledby="childCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="childCategoryModalLabel">Create New Child Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createChildCategory">
                    <div class="mb-3">
                        <label for="childCategoryCategory" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="childCategoryCategory" v-model="newChildCategory.category_id" required disabled>
                            <option value="">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                @{{ category.name }}
                            </option>
                        </select>
                        <small class="text-muted">Category is automatically selected from the form</small>
                    </div>
                    <div class="mb-3">
                        <label for="childCategorySubcategory" class="form-label">Subcategory <span class="text-danger">*</span></label>
                        <select class="form-select" id="childCategorySubcategory" v-model="newChildCategory.subcategory_id" required disabled>
                            <option value="">Select Subcategory</option>
                            <option v-for="subcategory in subcategories" :key="subcategory.id" :value="subcategory.id">
                                @{{ subcategory.name }}
                            </option>
                        </select>
                        <small class="text-muted">Subcategory is automatically selected from the form</small>
                    </div>
                    <div class="mb-3">
                        <label for="childCategoryName" class="form-label">Child Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="childCategoryName" v-model="newChildCategory.name" required>
                        <div class="invalid-feedback d-block" v-if="childCategoryErrors.name">
                            @{{ childCategoryErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="childCategorySlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="childCategorySlug" v-model="newChildCategory.slug" placeholder="auto-generated">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        <div class="invalid-feedback d-block" v-if="childCategoryErrors.slug">
                            @{{ childCategoryErrors.slug[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="childCategoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="childCategoryStatus" v-model.number="newChildCategory.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createChildCategory" :disabled="childCategoryCreating">
                    <span v-if="childCategoryCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ childCategoryCreating ? 'Creating...' : 'Create Child Category' }}
                </button>
            </div>
        </div>
    </div>
</div>

