@extends('backend.master')

@section('header_css')
    <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Products
@endsection
@section('page_heading')
    Barcode Generator
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <style>
                            .barcode_area{
                                display:grid; 
                                grid-template-columns: repeat(4, 1fr); 
                                gap:30px; 
                                margin-top:16px;
                                line-height: 1;
                            }
                            @media print{
                                *{
                                    color: black;
                                }
                                .card{
                                    border: 0 !important;
                                    box-shadow: unset !important;
                                }
                                .card-body{
                                    padding: 0;
                                }
                                .selection_area{
                                    display:none;
                                }
                                /*.barcode_area{*/
                                /*    grid-template-columns: 1fr;*/
                                /*}*/
                            }
                            .select2-container{
                                max-width: 250px !important;
                            }
                            .gap-3{
                                gap: 20px;
                            }
                        </style>
                        @php
                            $products = \App\Models\Product::get();
                        @endphp
                        <div class="selection_area">
                            <div class="d-flex flex-wrap gap-3 align-items-end">
                                <div style="max-width: 320px;">
                                    <h5>Select product</h5>
                                    <select class="select2" id="product_id">
                                        <option value="">select</option>
                                        @foreach($products as $product)
                                            <option 
                                                data-name="{{$product->name}}" 
                                                data-slug="{{$product->slug}}" 
                                                value="{{$product->id}}">
                                                {{$product->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary" id="make-barcodes">
                                        Generate barcodes
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger ml-2" id="clear-barcodes">
                                        Clear
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="variantTable" style="min-width: 600px;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 50px;">Select</th>
                                                <th>Product / Variant</th>
                                                <th style="width: 140px;">Stock</th>
                                                <th style="width: 160px;">Barcode Source</th>
                                                <th style="width: 140px;">Qty to print</th>
                                            </tr>
                                        </thead>
                                        <tbody id="variantRows">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    Select a product to load variants.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="barcodes" class="barcode_area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
<script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
    let productInfo = null;
    let variantState = [];

    const selectEl = document.getElementById('product_id');
    const variantRowsEl = document.getElementById('variantRows');
    const barcodeContainer = document.getElementById('barcodes');

    $('.select2')
        .select2()
        .on('select2:select', function (e) {
            const data = e.params.data;
            const slug = data.element.dataset.slug;
            if (!slug) {
                resetVariantTable('Product slug missing.');
                return;
            }
            fetchProduct(slug);
        });

    function resetVariantTable(message = 'Select a product to load variants.') {
        variantState = [];
        variantRowsEl.innerHTML = `<tr><td colspan="5" class="text-center text-muted">${message}</td></tr>`;
    }

    function fetchProduct(slug) {
        resetVariantTable('Loading variants...');
        fetch(`/inventory/products/${encodeURIComponent(slug)}/barcode-data`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Failed to load');
                }
                return res.json();
            })
            .then(payload => {
                productInfo = payload?.product || {};
                variantState = buildVariantState(payload);
                renderVariantRows();
            })
            .catch(() => {
                resetVariantTable('Unable to load product details.');
            });
    }

    function buildVariantState(payload) {
        const product = payload?.product || {};
        const hasVariant = Boolean(product.has_variant);
        const baseName = product.name || '';
        const baseCode = product.code || product.slug || '';
        const basePrice = parseFloat(product.discount_price ?? product.price ?? 0);
        const baseStock = parseFloat(product.stock ?? 0);

        const primaryVariants = Array.isArray(payload?.variants) ? payload.variants : [];
        const legacyVariants = Array.isArray(payload?.legacy_variants) ? payload.legacy_variants : [];

        const workingSet = primaryVariants.length ? primaryVariants : legacyVariants;

        if (!workingSet.length && hasVariant) {
            return [];
        }

        if (!workingSet.length) {
            return [
                {
                    key: 'product',
                    displayName: baseName,
                    barcodeValue: product.barcode || baseCode || product.sku || baseName,
                    price: basePrice,
                    stock: baseStock,
                    meta: {
                        variant_label: null,
                    },
                },
            ];
        }

        return workingSet.map((item, index) => {
            const label = item.label || item.variant_label || item.name || item.combination_key || `Variant ${index + 1}`;
            const key = item.combination_key || item.key || item.id || `${baseName}-${index}`;
            const price = parseFloat(item.discount_price ?? item.price ?? basePrice ?? 0);
            const stock = parseFloat(item.stock ?? 0);
            const barcodeValue = item.barcode || item.sku || item.combination_key || key;

            const storagePath = item.storage_path
                ? item.storage_path
                : (item.product_warehouse?.title || item.product_warehouse_room?.title || item.product_warehouse_room_cartoon?.title)
                    ? [
                        item.product_warehouse?.title,
                        item.product_warehouse_room?.title,
                        item.product_warehouse_room_cartoon?.title
                    ].filter(Boolean).join(' 9 ')
                    : null;

            return {
                key,
                displayName: `${baseName} — ${label}`,
                barcodeValue,
                price,
                stock,
                meta: {
                    variant_label: label,
                    storage_path: storagePath
                },
            };
        });
    }

    function buildVariantLabel(item, fallback) {
        if (!item) {
            return fallback;
        }

        if (item.variant_values && Array.isArray(item.variant_values) && item.variant_values.length) {
            return item.variant_values
                .map(value => value.value ?? value.name ?? value)
                .filter(Boolean)
                .join(' / ');
        }

        if (item.variant_value && typeof item.variant_value === 'string') {
            return item.variant_value;
        }

        const sizeName = item.size?.name || item.size_name;
        const colorName = item.color?.name || item.color_name;
        const regionName = item.region?.name || item.region_name;

        const parts = [sizeName, colorName, regionName].filter(Boolean);
        if (parts.length) {
            return parts.join(' / ');
        }

        return item.name || item.variant_name || fallback;
    }

    function renderVariantRows() {
        if (!variantState.length) {
            resetVariantTable('No variant information available for this product.');
            return;
        }

        variantRowsEl.innerHTML = '';

        variantState.forEach((variant, index) => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-index', index);
            tr.innerHTML = `
                <td class="text-center align-middle">
                    <input type="checkbox" class="variant-select" data-index="${index}" checked />
                </td>
                <td>
                    <div class="font-weight-bold">${variant.displayName}</div>
                    <div class="text-muted small">${variant.meta.variant_label || 'Standard product'}</div>
                </td>
                <td class="align-middle">
                    <div>Current: <strong>${formatNumber(variant.stock ?? 0, 2)}</strong></div>
                </td>
                <td class="align-middle">
                    <code>${variant.barcodeValue}</code>
                </td>
                <td class="align-middle">
                    <input type="number" min="0" class="form-control form-control-sm print-qty" data-index="${index}" placeholder="0" value="${Math.max(0, variant.stock ?? 0)}" />
                </td>
            `;
            variantRowsEl.appendChild(tr);
        });
    }

    function addLabel(wrap, text, bold = false, fontSize = '14px') {
        if (!text) return;
        const label = document.createElement('div');
        label.style.fontSize = fontSize;
        if (bold) {
            const b = document.createElement('b');
            b.textContent = text;
            label.appendChild(b);
        } else {
            label.textContent = text;
        }
        wrap.appendChild(label);
    }

    function generateBarcodes() {
        if (!variantState.length) {
            return alert('Please select a product first.');
        }

        barcodeContainer.innerHTML = '';

        const rows = Array.from(document.querySelectorAll('#variantRows tr'));
        let totalGenerated = 0;

        rows.forEach(row => {
            const index = row.getAttribute('data-index');
            if (index === null) return;
            const variant = variantState[index];
            const checkbox = row.querySelector('.variant-select');
            const qtyInput = row.querySelector('.print-qty');

            const qty = parseInt(qtyInput?.value ?? 0, 10);
            const isChecked = checkbox?.checked;

            if (!variant || !isChecked || !qty || qty <= 0) {
                return;
            }

            const finalQty = qty > 0 ? qty : 1;
            const price = variant.price ?? productInfo?.discount_price ?? productInfo?.price ?? 0;

            for (let i = 0; i < finalQty; i++) {
                const wrap = document.createElement('div');
                wrap.style.textAlign = 'center';

                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.classList.add('barcode');

                addLabel(wrap, productInfo?.brand?.name || 'Warehouse', true, '18px');
                addLabel(wrap, variant.displayName?.substring(0, 42) || productInfo?.name, true, '14px');
                if (variant.meta.storage_path) {
                    addLabel(wrap, variant.meta.storage_path, false, '11px');
                }

                wrap.appendChild(svg);

                JsBarcode(svg, variant.barcodeValue, {
                    format: "CODE128",
                    width: 1,
                    height: 40,
                    fontSize: 14,
                    displayValue: true,
                    margin: 4
                });

                svg.style.width = "120px";
                svg.style.height = "auto";

                addLabel(wrap, productInfo?.code || productInfo?.sku || '', true);

                if (price) {
                    addLabel(wrap, `BDT ${new Intl.NumberFormat('en-US').format(price)} /=`, true, '16px');
                }

                barcodeContainer.appendChild(wrap);
                totalGenerated += 1;
            }
        });

        if (!totalGenerated) {
            alert('Please select at least one variant and specify a quantity.');
        }
    }

    function formatNumber(value, decimals = 0) {
        const number = Number(value) || 0;
        return Number(number.toFixed(decimals)).toLocaleString(undefined, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    }

    document.getElementById('make-barcodes').addEventListener('click', generateBarcodes);
    document.getElementById('clear-barcodes').addEventListener('click', () => {
        if (selectEl) {
            $(selectEl).val(null).trigger('change');
        }
        resetVariantTable();
        barcodeContainer.innerHTML = '';
        productInfo = null;
        variantState = [];
    });
</script>
@endsection