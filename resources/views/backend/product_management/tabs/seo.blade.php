<div class="row">
    
    <!-- SEO Meta Information -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">SEO Meta Information</h5>
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label d-block">Meta Title</label>
                <input type="text" v-model="metaInfo.title" class="form-control" 
                    placeholder="SEO optimized title (50-60 characters)" maxlength="60">
                <small class="text-muted d-block">
                    Characters: @{{ metaInfo.title ? metaInfo.title.length : 0 }}/60
                </small>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label d-block">Meta Keywords</label>
                <input type="text" v-model="metaInfo.keywords" class="form-control" 
                    placeholder="keyword1, keyword2, keyword3">
                <small class="text-muted d-block">Separate keywords with commas</small>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label d-block">Meta Description</label>
                <textarea v-model="metaInfo.description" class="form-control" rows="4" 
                    placeholder="SEO optimized description (150-160 characters)" maxlength="160"></textarea>
                <small class="text-muted d-block">
                    Characters: @{{ metaInfo.description ? metaInfo.description.length : 0 }}/160
                </small>
            </div>
        </div>
    </div>

    <!-- SEO Preview -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Search Engine Preview</h5>
        <div class="card bg-light">
            <div class="card-body">
                <div class="seo-preview">
                    <h6 class="text-primary mb-1">
                        @{{ metaInfo.title || product.name || 'Your Product Title' }}
                    </h6>
                    <small class="text-success d-block mb-2">
                        {{ url('/') }}/product/@{{ product.slug || 'product-name' }}
                    </small>
                    <p class="text-muted mb-0" style="font-size: 14px;">
                        @{{ metaInfo.description || 'Your product meta description will appear here...' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Tips -->
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> SEO Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Meta Title:</strong> Keep it between 50-60 characters. Include your main keyword.</li>
                    <li><strong>Meta Description:</strong> Keep it between 150-160 characters. Make it compelling to increase click-through rate.</li>
                    <li><strong>Keywords:</strong> Use relevant keywords that your customers might search for.</li>
                    <li><strong>URL:</strong> The product slug is automatically generated from the product name.</li>
                    <li><strong>Images:</strong> Use descriptive alt text for images (handled automatically).</li>
                </ul>
            </div>
        </div>
    </div>

</div>

