<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Product FAQs</h5>
            <button type="button" @click="addFaqItem" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add FAQ
            </button>
        </div>
        
        <p class="text-muted mb-4">Add frequently asked questions and answers for this product.</p>

        <!-- FAQ Items -->
        <div v-if="faq.length === 0" class="alert alert-info">
            <i class="fas fa-info-circle"></i> No FAQ items added yet. Click "Add FAQ" to get started.
        </div>

        <div v-for="(item, index) in faq" :key="index" class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">FAQ #@{{ index + 1 }}</h6>
                <button type="button" @click="removeFaqItem(index)" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            v-model="item.question" 
                            class="form-control" 
                            placeholder="Enter the question"
                        >
                    </div>
                    <div class="col-12">
                        <label class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea 
                            v-model="item.answer" 
                            class="form-control" 
                            rows="4"
                            placeholder="Enter the answer"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

