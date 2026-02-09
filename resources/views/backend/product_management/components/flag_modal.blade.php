<!-- Flag Create Modal -->
<div class="modal fade" id="flagModal" tabindex="-1" aria-labelledby="flagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flagModalLabel">Create New Flag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createFlag">
                    <div class="mb-3">
                        <label for="flagName" class="form-label">Flag Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="flagName" v-model="newFlag.name" required placeholder="e.g., New, Hot, Sale">
                        <div class="invalid-feedback d-block" v-if="flagErrors.name">
                            @{{ flagErrors.name[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="flagIcon" class="form-label">Icon (Optional)</label>
                        <input type="file" class="form-control" id="flagIcon" @change="handleFlagIconChange" accept="image/*">
                        <small class="text-muted">Upload an icon for this flag</small>
                        <div v-if="newFlag.iconPreview" class="mt-2">
                            <img :src="newFlag.iconPreview" alt="Icon Preview" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="flagStatus" class="form-label">Status</label>
                        <select class="form-select" id="flagStatus" v-model.number="newFlag.status">
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="createFlag" :disabled="flagCreating">
                    <span v-if="flagCreating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    @{{ flagCreating ? 'Creating...' : 'Create Flag' }}
                </button>
            </div>
        </div>
    </div>
</div>

