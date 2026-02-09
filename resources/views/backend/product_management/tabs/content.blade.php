<div class="row">
    
    <!-- Additional Content Fields -->
    <div class="col-12">
        <div class="row">
            <!-- Video URL -->
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">
                    <i class="fab fa-youtube"></i> Video URL
                </label>
                <input type="url" v-model="product.video_url" @change="normalizeVideoUrl" class="form-control" 
                    placeholder="https://youtube.com/watch?v=...">
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle"></i> Add YouTube or Vimeo product video URL
                </small>
            </div>

            <!-- Tags -->
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">
                    <i class="fas fa-tags"></i> Product Tags
                </label>
                <input type="text" v-model="product.tags" class="form-control" 
                    placeholder="fashion, clothing, summer, cotton">
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle"></i> Separate tags with commas for better search results
                </small>
            </div>
        </div>
    </div>
    
    <!-- Short Description -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">
            <i class="fas fa-align-left"></i> Short Description
        </h5>
        <p class="text-muted">Brief overview of the product (recommended 100-255 characters)</p>
        <div id="shortDescriptionEditor"></div>
    </div>

    <!-- Full Description -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">
            <i class="fas fa-file-alt"></i> Full Description
        </h5>
        <p class="text-muted">Detailed product information, features, benefits, and usage instructions</p>
        
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#descriptionContent" role="tab">
                    <i class="fas fa-file-text"></i> Description
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="specification-tab" data-toggle="tab" href="#specificationContent" role="tab">
                    <i class="fas fa-list-ul"></i> Specification
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="warranty-tab" data-toggle="tab" href="#warrantyContent" role="tab">
                    <i class="fas fa-shield-alt"></i> Warranty Policy
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sizechart-tab" data-toggle="tab" href="#sizeChartContent" role="tab">
                    <i class="fas fa-ruler-combined"></i> Size Chart
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Description Tab -->
            <div class="tab-pane fade show active" id="descriptionContent" role="tabpanel">
                <div id="descriptionEditor"></div>
            </div>

            <!-- Specification Tab -->
            <div class="tab-pane fade" id="specificationContent" role="tabpanel">
                <div id="specificationEditor"></div>
            </div>

            <!-- Warranty Policy Tab -->
            <div class="tab-pane fade" id="warrantyContent" role="tabpanel">
                <div id="warrantyPolicyEditor"></div>
            </div>

            <!-- Size Chart Tab -->
            <div class="tab-pane fade" id="sizeChartContent" role="tabpanel">
                <div id="sizeChartEditor"></div>
            </div>
        </div>
    </div>

    

    <!-- Content Tips -->
    <div class="col-12 mt-3">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Content Writing Tips</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Short Description:</strong></h6>
                        <ul class="small">
                            <li>Keep it brief and engaging (100-255 characters)</li>
                            <li>Highlight key features or benefits</li>
                            <li>Make it compelling to encourage clicks</li>
                        </ul>

                        <h6 class="mt-3"><strong>Full Description:</strong></h6>
                        <ul class="small">
                            <li>Provide detailed product information</li>
                            <li>Include features, benefits, and use cases</li>
                            <li>Use formatting (bold, lists) for readability</li>
                            <li>Add images if relevant</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Specification:</strong></h6>
                        <ul class="small">
                            <li>List technical details and measurements</li>
                            <li>Use tables for organized data</li>
                            <li>Include material, dimensions, weight, etc.</li>
                        </ul>

                        <h6 class="mt-3"><strong>Warranty & Size Chart:</strong></h6>
                        <ul class="small">
                            <li>Clearly state warranty terms and duration</li>
                            <li>Provide accurate size measurements</li>
                            <li>Use tables for size charts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

