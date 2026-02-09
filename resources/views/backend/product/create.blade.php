@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('multipleImgUploadPlugin')}}/image-uploader.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .select2-selection{
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }
        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }

        .product-card-title .card-title::before{
            top: 13px
        }
    </style>
@endsection

@section('page_title')
    Product
@endsection
@section('page_heading')
    Add New Product
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">

                    <form class="needs-validation" method="POST" action="{{url('save/new/product')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row border-bottom mb-4 pb-2">
                            <div class="col-lg-6 product-card-title">
                                <h4 class="card-title mb-3" style="font-size: 18px; padding-top: 12px;">Add New Product</h4>
                            </div>
                            <div class="col-lg-6 text-right">
                                <a href="{{url('view/all/product')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Discard</a>
                                <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Save Product</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8 pr-lg-5">
                                <div class="form-group">
                                    <label for="name">Title <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Product Name Here" required>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="short_description">Short Description (Max 255 Characters)</label>
                                    <textarea id="short_description" name="short_description" class="form-control" rows="6" placeholder="Enter Short Description Here"></textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('short_description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>


                                <ul class="nav nav-tabs mt-4">
                                    <li class="nav-item">
                                        <a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Full Description</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Specification</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Warrenty Policy</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sizeChart" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Size Chart</span>
                                        </a>
                                    </li>                                    
                                </ul>

                                <div class="tab-content mb-4">
                                    <div class="tab-pane active" id="home">
                                        <textarea id="description" name="description" class="form-control"></textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('short_description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="profile">
                                        <textarea id="specification" name="specification" class="form-control"></textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('specification')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="settings">
                                        <textarea id="warrenty_policy" name="warrenty_policy" class="form-control"></textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('warrenty_policy')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="sizeChart">
                                        <textarea id="size_chart" name="size_chart" class="form-control"></textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('size_chart')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tags">Tags (for search result)</label>
                                    <input type="text" id="tags" data-role="tagsinput" name="tags" class="form-control" placeholder="e.g. Fashion, Dress, Shirts">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('tags')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group" id="product_image_gallery">
                                    <label for="tags">Product Image Gallery</label>
                                    <div class="input-images"></div>
                                </div>

                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">

                                    {{-- @include('backend.components.image_upload',[
                                        'selector_class' => 'product_image_upload',
                                        'input_name' => 'image',
                                        'width' => 200,
                                        'height' => 200,
                                        'folder' => 'uploads/products',
                                        'disk' => 'ftp', // ftp | s3 | public
                                        'accept' => 'image/*',
                                        'media_folder_id' => 2,
                                    ]) --}}

                                    <label for="image">Product Thumbnail Image<span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="dropify" data-height="205" data-max-file-size="2M" accept="image/*" required/>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group" id="product_price">
                                            <label for="price">Price (In BDT) <span class="text-danger">*</span></label>
                                            <input id="price" name="price" data-toggle="touchspin" type="text">

                                            <div class="invalid-feedback" style="display: block;">
                                                @error('price')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group" id="product_discounted_price">
                                            <label for="discount_price">Discount Price</label>
                                            <input type="text" id="discount_price" data-toggle="touchspin" name="discount_price" class="form-control">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('discount_price')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col" id="product_stock">
                                        <div class="form-group">
                                            <label for="stock">Stock</label>
                                            <input type="text" id="stock" data-toggle="touchspin" name="stock" class="form-control" placeholder="10">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('stock')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="code">Product Code</label>
                                            <input type="text" id="code" name="code" class="form-control" placeholder="YYWIW482">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="low_stock">Low Stock <span class="text-danger">*</span></label>
                                    <input type="text" id="low_stock" name="low_stock" class="form-control" placeholder="Enter Low Stock Value. e.g. 10" required>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('low_stock')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Category<span class="text-danger">*</span></label>
                                    <select name="category_id" data-toggle="select2" class="form-control" id="category_id" required>
                                        @php
                                            echo App\Models\Category::getDropDownList('name');
                                        @endphp
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('category_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="subcategory_id">Subcategory</label>
                                            <select name="subcategory_id" data-toggle="select2" class="form-control" id="subcategory_id">
                                                <option value="">Select One</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('subcategory_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="childcategory_id">Child Category</label>
                                            <select name="childcategory_id" data-toggle="select2" class="form-control" id="childcategory_id">
                                                <option value="">Select One</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('childcategory_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="brand_id">Brand</label>
                                            <select name="brand_id" data-toggle="select2" class="form-control" id="brand_id">
                                                @php
                                                    echo App\Models\Brand::getDropDownList('name');
                                                @endphp
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('brand_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="model_id">Model</label>
                                            <select name="model_id" data-toggle="select2" class="form-control" id="model_id">
                                                <option value="">Select One</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('model_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="flag_id">Flag</label>
                                            <select name="flag_id" data-toggle="select2" class="form-control" id="flag_id">
                                                @php
                                                    echo App\Models\Flag::getDropDownList('name');
                                                @endphp
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('flag')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col" id="product_warrenty">
                                        <div class="form-group">
                                            <label for="warrenty_id">Warrenty</label>
                                            <select name="warrenty_id" data-toggle="select2" class="form-control" id="warrenty_id">
                                                @php
                                                    echo App\Models\ProductWarrenty::getDropDownList('name');
                                                @endphp
                                            </select>

                                            <div class="invalid-feedback" style="display: block;">
                                                @error('warrenty')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    @if(DB::table('config_setups')->where('code', 'measurement_unit')->where('status', 0)->first())
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="unit_id">Unit</label>
                                            <select name="unit_id" data-toggle="select2" class="form-control" id="unit_id">
                                                @php
                                                    echo App\Models\Unit::getDropDownList('name');
                                                @endphp
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('unit_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                <div class="form-group">
                                    <label for="is_product_qty_multiply" class="font-weight-bold mb-1">Is Product Quantity Multiply</label>
                                    <span class="text-muted d-block mb-2" style="font-size: 13px;">
                                        If enabled, delivery charges will be calculated based on the total quantity of this product in the order.
                                    </span>
                                    <select name="is_product_qty_multiply" class="form-control" id="is_product_qty_multiply">
                                        <option value="">Select One</option>
                                        <option value="1" >Yes</option>
                                        <option value="0" >No</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('is_product_qty_multiply')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="video_url">Video URL</label>
                                    <input type="text" id="video_url" name="video_url" class="form-control" placeholder="https://youtube.com/YGUYUTYG">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('video_url')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="chest">Chest</label>
                                    <input type="text" id="chest" name="chest" class="form-control" placeholder="Enter Chest Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('chest')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="length">Length</label>
                                    <input type="text" id="length" name="length" class="form-control" placeholder="Enter Length Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('length')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sleeve">Sleeve</label>
                                    <input type="text" id="sleeve" name="sleeve" class="form-control" placeholder="Enter Sleeve Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('sleeve')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="waist">Waist</label>
                                    <input type="text" id="waist" name="waist" class="form-control" placeholder="Enter Waist Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('waist')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="weight">Weight</label>
                                    <input type="text" id="weight" name="weight" class="form-control" placeholder="Enter Weight Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('weight')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="size_ratio">Size Ratio</label>
                                    <input type="text" id="size_ratio" name="size_ratio" class="form-control" placeholder="Enter Size Ratio Size">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('size_ratio')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label for="fabrication">Fabrication</label>
                                    <textarea name="fabrication" id="fabrication" cols="12" class="form-control" rows="3"></textarea>
                                    <div class="invalid-feedback " style="display: block;">
                                        @error('fabrication')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="fabrication_gsm_ounce">Fabrication Gsm/Ounce</label>
                                    <input type="text" id="fabrication_gsm_ounce" name="fabrication_gsm_ounce" class="form-control" placeholder="Enter Fabrication GSM/Ounce">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('fabrication_gsm_ounce')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter Product Contact Number">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('contact_number')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col text-center pt-4">
                                <div class="card border-dark">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="has_variant">Product Has Variant ?</label><br>
                                            <input type="checkbox" id="has_variant" value="1" onchange="showVariantSection(this.value)" name="has_variant" data-toggle="switchery" data-color="#38b3d6" data-secondary-color="#df3554"/>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('has_variant')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                            <div id="common_setup" style="display: none;">
                                                <div class="d-flex flex-wrap align-items-end" style="gap:30px;">
                                                    <div>
                                                        <div> Stock </div>
                                                        <div>
                                                            <input type="number" id="common_stock" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div> Price </div>
                                                        <div>
                                                            <input type="number" id="common_price" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div> Discount Price </div>
                                                        <div>
                                                            <input type="number" id="common_discount" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <idv>
                                                        <button onclick="set_common_data();" type="button" class="btn btn-outline-info">
                                                            Set default
                                                        </button>
                                                    </idv>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3" id="product_variant" style="display: none">
                                            <div class="col-lg-12">

                                                <div class="table-responsive">
                                                    <h4 class="card-title mb-3">Product Variant <small class="text-danger font-weight-bolder">(Insert the Base Variant First)</small></h4>

                                                    <table class="table table-bordered rounded" id="product_variant_table">
                                                        <thead class="thead-light rounded">
                                                            <tr>
                                                                <th class="text-center">Image <span class="text-danger">*</span></th>

                                                                @if(DB::table('config_setups')->where('code', 'color')->where('status', 1)->first())
                                                                <th class="text-center">Color <span class="text-danger">*</span></th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'measurement_unit')->where('status', 1)->first())
                                                                <th class="text-center">Unit <span class="text-danger">*</span></th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'product_size')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 120px;">Size</th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'region')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 200px;">Region</th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'sim')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 140px;">SIM Type</th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'storage')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 140px;">Storage</th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'product_warranty')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 120px;">Warrenty</th>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'device_condition')->where('status', 1)->first())
                                                                <th class="text-center" style="min-width: 120px;">Condition</th>
                                                                @endif

                                                                <th class="text-center">Stock <span class="text-danger">*</span></th>
                                                                <th class="text-center">Price <span class="text-danger">*</span></th>
                                                                <th class="text-center">Disc. Price <span class="text-danger">*</span></th>
                                                                <th class="text-center" style="min-width: 50px;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="file" class="form-control" style="min-width: 100px;" name="product_variant_image[]">
                                                                </td>

                                                                @if(DB::table('config_setups')->where('code', 'color')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_color_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\Color::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'measurement_unit')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_unit_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\Unit::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'product_size')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_size_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\ProductSize::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'region')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_region_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\Region::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'sim')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_sim_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\Sim::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'storage')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_storage_type_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\StorageType::getDropDownList('ram');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'product_warranty')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_warrenty[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\ProductWarrenty::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                @if(DB::table('config_setups')->where('code', 'device_condition')->where('status', 1)->first())
                                                                <td class="text-center">
                                                                    <select name="product_variant_device_condition_id[]" data-toggle="select2" class="form-control">
                                                                        @php
                                                                            echo App\Models\DeviceCondition::getDropDownList('name');
                                                                        @endphp
                                                                    </select>
                                                                </td>
                                                                @endif

                                                                <td class="text-center">
                                                                    <input type="number" class="form-control" name="product_variant_stock[]" style="min-width: 100px;" value="0" style="height: 34px;" placeholder="0">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="number" class="form-control" name="product_variant_price[]" style="min-width: 100px;" value="0" style="height: 34px;" placeholder="0">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="number" class="form-control" name="product_variant_discounted_price[]" style="min-width: 100px;" value="0" style="height: 34px;" placeholder="0">
                                                                </td>

                                                                <td class="text-center">
                                                                    {{-- <a href="javascript:void(0)" onclick="removeRow(this)" class="btn btn-danger rounded btn-sm d-inline text-white"><i class="feather-trash-2" style="font-size: 14px; line-height: 2"></i></a> --}}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="row">
                                                        <div class="col-lg-12 text-center pb-3">
                                                            <button type="button" class="btn btn-success rounded addAnotherVariant" onclick="addAnotherVariant()">+ Add Another Variant</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="card-title mb-3">Product SEO Information <small class="text-danger font-weight-bolder">(Optional)</small></h4>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="meta_title">Meta Title</label>
                                                    <input type="text" id="meta_title" name="meta_title" class="form-control" placeholder="Meta Title">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('meta_title')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="meta_keywords">Meta Keywords</label>
                                                    <input type="text" id="meta_keywords" data-role="tagsinput" name="meta_keywords" class="form-control">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('meta_keywords')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="meta_description">Meta Description</label>
                                                    <textarea id="meta_description" name="meta_description" class="form-control" placeholder="Meta Description Here"></textarea>
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('meta_description')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/product')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Discard</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Save Product</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script src="{{url('assets')}}/plugins/switchery/switchery.min.js"></script>
    <script src="{{url('assets')}}/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <script src="{{url('multipleImgUploadPlugin')}}/image-uploader.min.js"></script>
    <script src="{{url('assets')}}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
    
        function set_common_data(){
            event.preventDefault();
            
            var stock = $('#common_stock').val();
            var price = $('#common_price').val();
            var discount = $('#common_discount').val();
            
            $(`[name="product_variant_stock[]"`).val(stock);
            $(`[name="product_variant_price[]"`).val(price);
            $(`[name="product_variant_discount[]"`).val(discount);
            
        }

        $('#has_variant').prop("checked", false);
        function showVariantSection(value){
            if ($('#has_variant').is(":checked")){
                $("#product_variant").fadeIn(500);
                $("#common_setup").fadeIn(500);

                $("#product_image_gallery").fadeOut(500);
                // $("#product_price").fadeOut(500);
                // $("#product_discounted_price").fadeOut(500);
                $("#product_stock").fadeOut(500);
                $("#product_warrenty").fadeOut(500);
            } else {
                $("#product_variant").fadeOut(500);
                $("#common_setup").fadeOut(500);

                $("#product_image_gallery").fadeIn(500);
                // $("#product_price").fadeIn(500);
                // $("#product_discounted_price").fadeIn(500);
                $("#product_stock").fadeIn(500);
                $("#product_warrenty").fadeIn(500);
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function addAnotherVariant(){
            $(".addAnotherVariant").html("Adding...");
            $.ajax({
                data: '',
                url: "{{ url('/add/another/variant') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $(".addAnotherVariant").html("Added");
                    $("#product_variant_table tbody").append(data.variant);
                    $(".addAnotherVariant").html("+ Add Another Variant");
                    $('[data-toggle="select2"]').select2(); // initiate select2 for newly added row
                    
                },
                error: function(data) {
                    console.log('Error:', data);
                    $(".addAnotherVariant").html("Something Went Wrong");
                }
            });
            $(".addAnotherVariant").blur();
        }

        function removeRow(btndel){
            if (typeof(btndel) == "object") {
                $(btndel).closest("tr").remove();
            } else {
                return false;
            }
        }


        var defaultOptions = {};
        $('[data-toggle="touchspin"]').each(function (idx, obj) {
            var objOptions = $.extend({}, defaultOptions, $(obj).data());
            $(obj).TouchSpin(objOptions);
        });


        $('[data-toggle="select2"]').select2();


        $('[data-toggle="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });

        $('.input-images').imageUploader({
            imagesInputName: 'photos',
            preloadedInputName: 'old'
        });
        $(".material-icons").html("<i class='fa fa-upload'></i>");


        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 300
        });
        $('#specification').summernote({
            placeholder: 'Write Specification Here',
            tabsize: 2,
            height: 300
        });
        $('#warrenty_policy').summernote({
            placeholder: 'Write Warrenty Policy Here',
            tabsize: 2,
            height: 300
        });
        $('#size_chart').summernote({
            placeholder: 'Write Size Chart Here',
            tabsize: 2,
            height: 300
        });
        // CKEDITOR.replace('description', {
        //     filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        //     filebrowserUploadMethod: 'form',
        //     height: 250,
        // });

        // CKEDITOR.replace('specification', {
        //     filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        //     filebrowserUploadMethod: 'form',
        //     height: 250,
        // });

        // CKEDITOR.replace('warrenty_policy', {
        //     filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        //     filebrowserUploadMethod: 'form',
        //     height: 250,
        // });

        $(document).ready(function () {
            $('#category_id').on('change', function () {
                var categoryId = this.value;
                $("#subcategory_id").html('');
                $("#childcategory_id").html('');
                $.ajax({
                    url: "{{url('/category/wise/subcategory')}}",
                    type: "POST",
                    data: {
                        category_id: categoryId,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#subcategory_id').html('<option value="">Select Subcategory</option>');
                        $('#childcategory_id').html('<option value="">Select Child Category</option>');
                        $.each(result, function (key, value) {
                            $("#subcategory_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#subcategory_id').on('change', function () {
                var subCategoryId = this.value;
                $("#childcategory_id").html('');
                $.ajax({
                    url: "{{url('/subcategory/wise/childcategory')}}",
                    type: "POST",
                    data: {
                        subcategory_id: subCategoryId,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#childcategory_id').html('<option value="">Select Child Category</option>');
                        $.each(result, function (key, value) {
                            $("#childcategory_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#brand_id').on('change', function () {

                var bandId = this.value;
                $("#model_id").html('');
                $.ajax({
                    url: "{{url('/brand/wise/model')}}",
                    type: "POST",
                    data: {
                        brand_id: bandId,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#model_id').html('<option value="">Select Model</option>');
                        $.each(result, function (key, value) {
                            $("#model_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endsection