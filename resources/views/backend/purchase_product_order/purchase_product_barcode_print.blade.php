@extends('backend.master')

@section('header_css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.15/vue.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<style>
    .barcode-card {
        border: 1px solid #ddd;
        padding: 10px;
        margin: 5px;
        text-align: center;
        display: inline-block;
        page-break-inside: avoid;
    }
    .barcode-card .product-name {
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 5px;
    }
    .barcode-card .sales-price {
        font-size: 14px;
        font-weight: bold;
        margin: 5px 0;
    }
    .barcode-card .sku {
        font-size: 10px;
        color: #666;
        margin-top: 5px;
    }
    .product-name-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .product-name-text {
        display: inline-block;
        max-width: calc(100% - 25px);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }
    .product-name-edit-icon {
        display: none;
        cursor: pointer;
        color: #007bff;
        margin-left: 5px;
        vertical-align: middle;
    }
    .product-name-container:hover .product-name-edit-icon {
        display: inline-block;
    }
    .product-name-input {
        width: 100%;
        font-size: 12px;
        padding: 2px 5px;
    }
    .variant-title-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .variant-title-text {
        display: inline-block;
        max-width: calc(100% - 25px);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }
    .variant-edit-icon {
        display: none;
        cursor: pointer;
        color: #007bff;
        margin-left: 5px;
        vertical-align: middle;
    }
    .variant-title-container:hover .variant-edit-icon {
        display: inline-block;
    }
    .variant-title-input {
        width: 100%;
        font-size: 10px;
        padding: 2px 5px;
    }
    .barcode-card svg {
        max-width: 100%;
        height: auto;
    }
    .controls-panel {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .controls-panel .form-group {
        margin-bottom: 10px;
    }
    .barcode-container-wrapper {
        width: 100%;
    }
    @media print {
        .controls-panel {
            display: none;
        }
        .no-print {
            display: none;
        }
        @page {
            size: A4;
            margin: 0mm;
        }
        body {
            width: 100% !important;
            height: auto !important;
        }
    }
</style>
@endsection

@section('page_title')
    Purchase Product Order Barcode Print
@endsection
@section('page_heading')
    Generate Barcodes for Purchase Order #{{ $purchase_id }}
@endsection

@section('content')
    <div class="row" id="purchase_product_barcode_print">
        <div class="col-lg-12 col-xl-12">
            <!-- Units Table -->
            <div class="card no-print">
                <div class="card-header">
                    <h5>Purchase Order Units</h5>
                </div>
                <div class="card-body">
                    <div>
                        <button type="button" class="btn btn-sm btn-primary" @click="resetCodes">
                            reset codes
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height: 505px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead style="position: sticky; top: 0; background-color: #fff;">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Variant</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Sales Price</th>
                                    <th>SKU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!loading && units.length === 0">
                                    <td colspan="7" class="text-center text-muted">No units found</td>
                                </tr>
                                <tr v-for="(unit, index) in units" :key="unit.id">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div>
                                                <img :src="unit.product_image" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                            </div>
                                            <div style="margin-left: 10px;">
                                                @{{ unit.product_name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span v-if="unit.variant_title" class="badge badge-info">@{{ unit.variant_title }}</span>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td>
                                        <code>@{{ unit.code }}</code>
                                        <div v-if="confirmingCodeId !== unit.id" class="mt-1">
                                            <input type="text" v-model="unit.editCode" @focus="$event.target.select()" class="form-control form-control-sm d-inline-block" style="width: 120px;">
                                            <button class="btn btn-sm btn-primary" @click="showConfirmUpdate(unit)">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                        <div v-else-if="confirmingCodeId === unit.id" class="mt-2">
                                            <div class="alert alert-warning p-2 mb-2">
                                                <small>Update code to "@{{ unit.editCode }}"?</small>
                                            </div>
                                            <button class="btn btn-sm btn-success" @click="confirmUpdateCode(unit)">
                                                <i class="fas fa-check"></i> Yes
                                            </button>
                                            <button class="btn btn-sm btn-danger" @click="cancelUpdateCode(unit)">
                                                <i class="fas fa-times"></i> No
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" :class="getStatusClass(unit.unit_status)">
                                            @{{ unit.unit_status }}
                                        </span>
                                    </td>
                                    <td>৳@{{ formatPrice(unit.sales_price) }}</td>
                                    <td>@{{ unit.sku || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Controls Panel -->
            <div class="controls-panel no-print">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Items Per Row</label>
                            <input type="number" v-model.number="itemsPerRow" min="1" max="10" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Card Width (%)</label>
                            <input type="number" v-model.number="cardWidth" min="10" max="100" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Page Size</label>
                            <select v-model="pageSize" class="form-control form-control-sm">
                                <option value="A4">A4 (210mm × 297mm)</option>
                                <option value="4x6">4x6 inch (Barcode Printer)</option>
                                <option value="3x2">3x2 inch (Small Barcode)</option>
                                <option value="2x1">2x1 inch (Tiny Barcode)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button @click="generateBarcodes" class="btn btn-primary btn-sm">
                                <i class="fas fa-barcode"></i> Generate Barcodes
                            </button>
                            <button @click="printBarcodes" class="btn btn-success btn-sm ml-2">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barcode Cards Container -->
            <div id="barcode-container" class="barcode-container-wrapper" style="margin-top: 20px;">
                <div v-for="unit in units" :key="'barcode-' + unit.id" 
                     class="barcode-card" 
                     :style="{ width: cardWidth + '%', display: 'inline-block' }">
                    <div class="product-name-container">
                        <span v-if="editingProductNameId !== unit.id" class="product-name-text">@{{ unit.short_product_name || unit.product_name }}</span>
                        <i v-if="editingProductNameId !== unit.id" class="fas fa-edit product-name-edit-icon" @click="startEditProductName(unit)"></i>
                        <input v-else 
                               v-model="unit.editShortProductName" 
                               @blur="saveShortProductName(unit)"
                               @keyup.enter="saveShortProductName(unit)"
                               @keyup.esc="cancelEditProductName(unit)"
                               class="product-name-input form-control form-control-sm"
                               placeholder="Short name">
                    </div>
                    <div v-if="unit.variant_title" class="variant-title-container" style="font-size: 10px; color: #666; margin-bottom: 5px;">
                        <span v-if="editingVariantId !== unit.id" class="variant-title-text">@{{ unit.short_variant_title || unit.variant_title }}</span>
                        <i v-if="editingVariantId !== unit.id" class="fas fa-edit variant-edit-icon" @click="startEditVariant(unit)"></i>
                        <input v-else 
                               v-model="unit.editShortVariantTitle" 
                               @blur="saveShortVariantTitle(unit)"
                               @keyup.enter="saveShortVariantTitle(unit)"
                               @keyup.esc="cancelEditVariant(unit)"
                               class="variant-title-input form-control form-control-sm"
                               placeholder="Short name">
                    </div>
                    <div class="sales-price">৳@{{ formatPrice(unit.sales_price) }}</div>
                    <div :id="'barcode-' + unit.id" class="barcode-svg"></div>
                    <div class="sku">SKU: @{{ unit.sku || unit.code }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    new Vue({
        el: '#purchase_product_barcode_print',
        data: {
            purchase_product_order_id: {{ $purchase_id }},
            units: [],
            loading: true,
            itemsPerRow: 3,
            cardWidth: 30,
            pageSize: 'A4',
            editingCodeId: null,
            confirmingCodeId: null,
            editingVariantId: null,
            editingProductNameId: null
        },
        mounted() {
            this.loadUnits();
        },
        methods: {
            resetCodes() {
                let that = this;
                Swal.fire({
                    title: 'Reset Codes',
                    text: 'Are you sure you want to reset the codes?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        that.units.forEach(unit => {
                            unit.code = null;
                            unit.editCode = null;
                        });
                    }
                });
            },
            loadUnits() {
                this.loading = true;
                const url = `{{ url('api/purchase-barcode-units') }}/${this.purchase_product_order_id}`;
                axios.get(url)
                    .then(response => {
                        if (response.data.success) {
                            this.units = response.data.data.map(unit => {
                                // Initialize editCode for each unit
                                unit.editCode = unit.code;
                                // Initialize short_variant_title and editShortVariantTitle
                                unit.short_variant_title = unit.short_variant_title || null;
                                unit.editShortVariantTitle = unit.short_variant_title || unit.variant_title || '';
                                // Initialize short_product_name and editShortProductName
                                unit.short_product_name = unit.short_product_name || null;
                                unit.editShortProductName = unit.short_product_name || unit.product_name || '';
                                return unit;
                            });
                            this.$nextTick(() => {
                                this.generateBarcodes();
                            });
                        }
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error loading units:', error);
                        this.loading = false;
                        alert('Error loading units. Please try again.');
                    });
            },
            generateBarcodes() {
                this.$nextTick(() => {
                    this.units.forEach(unit => {
                        const containerId = 'barcode-' + unit.id;
                        const container = document.getElementById(containerId);
                        if (container) {
                            container.innerHTML = '';
                            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                            svg.setAttribute('class', 'barcode');
                            
                            try {
                                JsBarcode(svg, unit.barcode_value || unit.code, {
                                    format: "CODE128",
                                    width: 1,
                                    height: 40,
                                    fontSize: 12,
                                    displayValue: true,
                                    margin: 2
                                });
                                container.appendChild(svg);
                            } catch (error) {
                                console.error('Error generating barcode for unit:', unit.id, error);
                                container.innerHTML = '<div class="text-danger">Barcode Error</div>';
                            }
                        }
                    });
                });
            },
            printBarcodes() {
                // Ensure page size is applied before printing
                this.$nextTick(() => {
                    window.print();
                });
            },
            formatPrice(price) {
                return parseFloat(price || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },
            getStatusClass(status) {
                const classes = {
                    'instock': 'badge-success',
                    'sold': 'badge-primary',
                    'returned': 'badge-warning',
                    'lost': 'badge-danger',
                    'damaged': 'badge-danger'
                };
                return classes[status] || 'badge-secondary';
            },
            showConfirmUpdate(unit) {
                if (!unit.editCode || unit.editCode.trim() === '') {
                    alert('Please enter a code');
                    return;
                }
                if (unit.editCode === unit.code) {
                    alert('Code is the same');
                    return;
                }
                this.editingCodeId = unit.id;
                this.confirmingCodeId = unit.id;
            },
            cancelUpdateCode(unit) {
                unit.editCode = unit.code;
                this.editingCodeId = null;
                this.confirmingCodeId = null;
            },
            confirmUpdateCode(unit) {
                const url = `{{ url('api/purchase-barcode-unit/update-code') }}`;
                const data = {
                    unit_id: unit.id,
                    code: unit.editCode.trim()
                };

                axios.post(url, data)
                    .then(response => {
                        if (response.data.success) {
                            // Update local state
                            unit.code = response.data.data.code;
                            unit.barcode_value = response.data.data.code;
                            unit.editCode = response.data.data.code;
                            
                            // Re-render barcode for this specific unit
                            this.$nextTick(() => {
                                this.generateBarcodeForUnit(unit.id);
                            });
                            
                            this.editingCodeId = null;
                            this.confirmingCodeId = null;
                            
                            // Show success alert
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Code updated successfully',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating code:', error);
                        alert('Error updating code. Please try again.');
                    });
            },
            generateBarcodeForUnit(unitId) {
                const unit = this.units.find(u => u.id === unitId);
                if (!unit) return;

                const containerId = 'barcode-' + unit.id;
                const container = document.getElementById(containerId);
                if (container) {
                    container.innerHTML = '';
                    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.setAttribute('class', 'barcode');
                    
                    try {
                        JsBarcode(svg, unit.barcode_value || unit.code, {
                            format: "CODE128",
                            width: 1,
                            height: 40,
                            fontSize: 12,
                            displayValue: true,
                            margin: 2
                        });
                        container.appendChild(svg);
                    } catch (error) {
                        console.error('Error generating barcode for unit:', unit.id, error);
                        container.innerHTML = '<div class="text-danger">Barcode Error</div>';
                    }
                }
            },
            startEditVariant(unit) {
                this.editingVariantId = unit.id;
                unit.editShortVariantTitle = unit.short_variant_title || unit.variant_title || '';
                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('.variant-title-input');
                    inputs.forEach(input => {
                        if (input.value === unit.editShortVariantTitle) {
                            input.focus();
                            input.select();
                        }
                    });
                });
            },
            cancelEditVariant(unit) {
                unit.editShortVariantTitle = unit.short_variant_title || unit.variant_title || '';
                this.editingVariantId = null;
            },
            saveShortVariantTitle(unit) {
                const shortName = unit.editShortVariantTitle.trim();
                const originalVariantTitle = unit.variant_title;
                
                // Update all units with the same variant_title
                this.units.forEach(u => {
                    if (u.variant_title === originalVariantTitle) {
                        u.short_variant_title = shortName || null;
                        u.editShortVariantTitle = shortName || u.variant_title || '';
                    }
                });
                
                this.editingVariantId = null;
            },
            startEditProductName(unit) {
                this.editingProductNameId = unit.id;
                unit.editShortProductName = unit.short_product_name || unit.product_name || '';
                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('.product-name-input');
                    inputs.forEach(input => {
                        if (input.value === unit.editShortProductName) {
                            input.focus();
                            input.select();
                        }
                    });
                });
            },
            cancelEditProductName(unit) {
                unit.editShortProductName = unit.short_product_name || unit.product_name || '';
                this.editingProductNameId = null;
            },
            saveShortProductName(unit) {
                const shortName = unit.editShortProductName.trim();
                const originalProductName = unit.product_name;
                
                // Update all units with the same product_name
                this.units.forEach(u => {
                    if (u.product_name === originalProductName) {
                        u.short_product_name = shortName || null;
                        u.editShortProductName = shortName || u.product_name || '';
                    }
                });
                
                this.editingProductNameId = null;
            }
        },
        watch: {
            itemsPerRow() {
                this.cardWidth = Math.floor(100 / this.itemsPerRow);
            },
            pageSize() {
                // Remove any existing page size style tag
                const existingStyle = document.getElementById('dynamic-page-size-style');
                if (existingStyle) {
                    existingStyle.remove();
                }
                
                // Apply page size only for print media
                const pageSizes = {
                    'A4': 'A4',
                    '4x6': '4in 6in',
                    '3x2': '3in 2in',
                    '2x1': '2in 1in'
                };
                
                const size = pageSizes[this.pageSize];
                if (size) {
                    const style = document.createElement('style');
                    style.id = 'dynamic-page-size-style';
                    style.textContent = `@media print { @page { size: ${size}; } }`;
                    document.head.appendChild(style);
                }
            }
        }
    });
</script>
@endsection
