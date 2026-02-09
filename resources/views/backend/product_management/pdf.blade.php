<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - {{ $product->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        
        .header img {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th,
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: left;
            vertical-align: top;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        .product-image {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .product-image img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 5px;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .text-primary {
            color: #007bff;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .description-content {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .col-6 {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .status-badge {
            float: right;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <div class="header">
            @if($generalInfo && $generalInfo->logo)
                <img src="{{ public_path($generalInfo->logo) }}" alt="Logo">
            @endif
            <h1>{{ $generalInfo->company_name ?? 'Company Name' }}</h1>
            <p>Product Information Document</p>
        </div>
        
        <!-- Basic Information -->
        <div class="section">
            <h2 class="section-title">
                Basic Information
                <span class="badge {{ $product->status == 1 ? 'badge-success' : 'badge-danger' }} status-badge">
                    {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                </span>
            </h2>
            
            <table>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 15px;">
                        @if($product->image)
                            <img src="{{ public_path('uploads/' . $product->image) }}" 
                                alt="{{ $product->name }}" 
                                style="max-width: 200px; max-height: 200px; border: 1px solid #dee2e6;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Product Name</th>
                    <td><strong style="font-size: 13px;">{{ $product->name }}</strong></td>
                </tr>
                <tr>
                    <th>Product Code</th>
                    <td><code>{{ $product->code ?? 'N/A' }}</code></td>
                </tr>
                <tr>
                    <th>SKU</th>
                    <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                </tr>
                <tr>
                    <th>Barcode</th>
                    <td><code>{{ $product->barcode ?? 'N/A' }}</code></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>
                        {{ $product->category->name ?? 'N/A' }}
                        @if($product->subcategory)
                            → {{ $product->subcategory->name }}
                        @endif
                        @if($product->childCategory)
                            → {{ $product->childCategory->name }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                </tr>
                @if($product->model)
                <tr>
                    <th>Model</th>
                    <td>{{ $product->model->name }}</td>
                </tr>
                @endif
                <tr>
                    <th>Unit</th>
                    <td>{{ $product->unit->name ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Pricing & Stock -->
        <div class="section">
            <h2 class="section-title">Pricing & Stock Information</h2>
            
            <table>
                <tr>
                    <th>Regular Price</th>
                    <td><strong class="text-success" style="font-size: 14px;">৳{{ number_format($product->price, 2) }}</strong></td>
                </tr>
                @if($product->discount_price > 0)
                <tr>
                    <th>Discount Price</th>
                    <td>
                        <strong class="text-danger" style="font-size: 14px;">৳{{ number_format($product->discount_price, 2) }}</strong>
                        @php
                            $discountPercent = (($product->price - $product->discount_price) / $product->price) * 100;
                        @endphp
                        <span class="badge badge-warning">{{ round($discountPercent) }}% OFF</span>
                    </td>
                </tr>
                @endif
                @if(!$product->has_variant)
                <tr>
                    <th>Stock Quantity</th>
                    <td>
                        <strong>{{ $product->stock }} {{ $product->unit->name ?? 'Units' }}</strong>
                    </td>
                </tr>
                @if($product->low_stock)
                <tr>
                    <th>Low Stock Alert</th>
                    <td>{{ $product->low_stock }}</td>
                </tr>
                @endif
                @else
                <tr>
                    <th>Product Type</th>
                    <td><span class="badge badge-primary">Variable Product</span></td>
                </tr>
                <tr>
                    <th>Total Variants</th>
                    <td>{{ $variantCombinations->count() }} Combinations</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Description -->
        @if($product->short_description || $product->description)
        <div class="section">
            <h2 class="section-title">Product Description</h2>
            
            @if($product->short_description)
            <div style="margin-bottom: 15px;">
                <strong>Short Description:</strong>
                <div class="description-content">
                    {{ strip_tags($product->short_description) }}
                </div>
            </div>
            @endif
            
            @if($product->description)
            <div>
                <strong>Full Description:</strong>
                <div class="description-content">
                    {!! strip_tags($product->description, '<p><br><strong><em><ul><ol><li>') !!}
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Specifications -->
        @if($product->specification)
        <div class="section">
            <h2 class="section-title">Specifications</h2>
            <div class="description-content">
                {!! strip_tags($product->specification, '<p><br><strong><em><ul><ol><li><table><tr><td><th>') !!}
            </div>
        </div>
        @endif
        
        <!-- Warranty -->
        @if($product->warrenty_policy)
        <div class="section">
            <h2 class="section-title">Warranty Policy</h2>
            <div class="description-content">
                {!! strip_tags($product->warrenty_policy, '<p><br><strong><em><ul><ol><li>') !!}
            </div>
        </div>
        @endif
        
        <!-- Unit Pricing -->
        @if($unitPricing->count() > 0)
        <div class="section page-break">
            <h2 class="section-title">Unit Pricing</h2>
            
            <table class="table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">Unit</th>
                        <th style="width: 10%;">Value</th>
                        <th style="width: 20%;">Label</th>
                        <th style="width: 15%;">Price</th>
                        <th style="width: 15%;">Discount</th>
                        <th style="width: 10%;">Disc %</th>
                        <th style="width: 10%;">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitPricing as $index => $pricing)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pricing->unit->name ?? 'N/A' }}</td>
                        <td>{{ $pricing->unit_value }}</td>
                        <td>{{ $pricing->unit_label ?? '—' }}</td>
                        <td class="text-success"><strong>৳{{ number_format($pricing->price, 2) }}</strong></td>
                        <td>
                            @if($pricing->discount_price > 0)
                                ৳{{ number_format($pricing->discount_price, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($pricing->discount_percent > 0)
                                {{ $pricing->discount_percent }}%
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $pricing->reward_points ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Variants -->
        @if($product->has_variant && $variantCombinations->count() > 0)
        <div class="section page-break">
            <h2 class="section-title">Product Variants</h2>
            
            <table class="table-striped">
                <thead>
                    <tr>
                        <th style="width: 20%;">Variant</th>
                        <th style="width: 25%;">Attributes</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 15%;">Price</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalStock = 0; @endphp
                    @foreach($variantCombinations as $variant)
                    @php $totalStock += $variant->stock; @endphp
                    <tr>
                        <td><strong>{{ $variant->combination_key }}</strong></td>
                        <td>
                            @if($variant->variant_values)
                                @foreach($variant->variant_values as $key => $value)
                                    {{ $value }}@if(!$loop->last), @endif
                                @endforeach
                            @endif
                        </td>
                        <td><code>{{ $variant->sku ?? 'N/A' }}</code></td>
                        <td>
                            @if($variant->price)
                                ৳{{ number_format($variant->price, 2) }}
                            @else
                                Base Price
                            @endif
                        </td>
                        <td><strong>{{ $variant->stock }}</strong></td>
                        <td>{{ $variant->status ? 'Active' : 'Inactive' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td colspan="4" style="text-align: right;">Total Stock:</td>
                        <td colspan="2">{{ $totalStock }} Units</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Filter Attributes -->
        @if($filterAttributes->count() > 0)
        <div class="section">
            <h2 class="section-title">Filter Attributes</h2>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Attribute Name</th>
                        <th style="width: 55%;">Attribute Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filterAttributes as $index => $attribute)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $attribute->attribute_name }}</strong></td>
                        <td>{{ $attribute->attribute_value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Additional Information -->
        <div class="section">
            <h2 class="section-title">Additional Information</h2>
            
            <table>
                @if($product->tags)
                <tr>
                    <th>Tags</th>
                    <td>{{ $product->tags }}</td>
                </tr>
                @endif
                @if($product->video_url)
                <tr>
                    <th>Video URL</th>
                    <td>{{ $product->video_url }}</td>
                </tr>
                @endif
                <tr>
                    <th>Created Date</th>
                    <td>{{ $product->created_at->format('d M Y, h:i A') }}</td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td>{{ $product->updated_at->format('d M Y, h:i A') }}</td>
                </tr>
                @if($product->meta_title)
                <tr>
                    <th>Meta Title</th>
                    <td>{{ $product->meta_title }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>
                Generated on {{ now()->format('d M Y, h:i A') }} | 
                Product ID: {{ $product->id }} | 
                {{ $generalInfo->company_name ?? 'Company Name' }}
            </p>
            <p style="margin-top: 5px;">
                This is a system-generated document. No signature required.
            </p>
        </div>
        
    </div>
</body>
</html>

