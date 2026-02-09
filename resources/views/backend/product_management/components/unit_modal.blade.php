<!-- Unit Create Modal -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitModalLabel">Create New Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createUnit">
                    <div class="mb-3">
                        <label for="unitName" class="form-label">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unitName" v-model="newUnit.name" required placeholder="e.g., pcs, kg, gm, box">
                        <div class="invalid-feedback d-block" v-if="unitErrors.name">
                            @{{ unitErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="unitStatus" class="form-label">Status</label>
                        <select class="form-select" id="unitStatus" v-model.number="newUnit.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createUnit" :disabled="unitCreating">
                    <span v-if="unitCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ unitCreating ? 'Creating...' : 'Create Unit' }}
                </button>
            </div>
        </div>
    </div>
</div>

