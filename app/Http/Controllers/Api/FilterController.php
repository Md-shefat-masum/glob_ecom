<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use App\Models\DeviceCondition;
use App\Models\ProductWarrenty;
use App\Models\Sim;
use App\Models\StorageType;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';
    public function filterSearchResults(Request $request){
        if ($request->header('Authorization') == FilterController::AUTHORIZATION_TOKEN) {

            // search criteria
            $searchKeyword = $request->search_keyword;
            $sortBy = $request->sort_by; // 1=>Low to High; 2=>High To Low
            $priceRangeMin = $request->minimum_price;
            $priceRangeMax = $request->maximum_price;
            $warrentyId = $request->warrenty_id;
            // $storageId = $request->storage_type_id;
            // $deviceConditionId = $request->product_type_id;
            // $simTypeId = $request->sim_type_id;
            // $regionId = $request->region_id;


            $data = DB::table('products')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                        ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                        ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                        ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                        ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                        ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                        ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                        ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                        ->where('products.status', 1)
                        ->where('products.name', 'LIKE', '%'.$searchKeyword.'%')

                        ->when($sortBy, function($query) use ($sortBy){
                            if($sortBy == 1)
                                return $query->orderBy('products.discount_price', 'asc');
                            elseif($sortBy == 2)
                                return $query->orderBy('products.discount_price', 'desc');
                            else
                                return $query->orderBy('products.discount_price', 'asc');
                        })
                        ->when($priceRangeMin, function($query) use ($priceRangeMin){
                            return $query->where('products.discount_price', '>=', $priceRangeMin);
                        })
                        ->when($priceRangeMax, function($query) use ($priceRangeMax){
                            return $query->where('products.discount_price', '<=', $priceRangeMax);
                        })

                        // ->when($storageId, function($query) use ($storageId){
                        //     return $query->leftJoin('storage_types', 'product_variants.storage_type_id', '=', 'storage_types.id')
                        //             ->where('storage_types.id', $storageId);
                        // })
                        // ->when($deviceConditionId, function($query) use ($deviceConditionId){
                        //     return $query->leftJoin('device_conditions', 'product_variants.device_condition_id', '=', 'device_conditions.id')
                        //             ->where('device_conditions.id', $deviceConditionId);
                        // })
                        // ->when($simTypeId, function($query) use ($simTypeId){
                        //     return $query->leftJoin('sims', 'product_variants.sim_id', '=', 'sims.id')
                        //             ->where('sims.id', $simTypeId);
                        // })
                        // ->when($regionId, function($query) use ($regionId){
                        //     return $query->leftJoin('country', 'product_variants.region_id', '=', 'country.id')
                        //             ->where('country.id', $regionId);
                        // })
                        ->when($warrentyId, function($query) use ($warrentyId){
                            return $query->leftJoin('product_warrenties as variant_warrenty', 'product_variants.warrenty_id', '=', 'variant_warrenty.id')
                                    ->where('variant_warrenty.id', $warrentyId);
                        })

                        // ->orWhere(function ($query) use ($searchKeyword) {
                        //     $query->where('products.name', 'LIKE', '%'.$searchKeyword.'%')
                        //         ->where('categories.name', 'LIKE', '%'.$searchKeyword.'%')
                        //         ->where('subcategories.name', 'LIKE', '%'.$searchKeyword.'%')
                        //         ->where('products.tags', 'LIKE', '%'.$searchKeyword.'%')
                        //         ->where('brands.name', 'LIKE', '%'.$searchKeyword.'%');
                        // })

                        ->groupBy('products.id')
                        ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function filterProducts(Request $request){


        if ($request->header('Authorization') == FilterController::AUTHORIZATION_TOKEN) {

            // search criteria
            $categoryId = $request->category_id;
            $subcategoryId = $request->subcategory_id;
            $childcategoryId = $request->childcategory_id;
            $flagId = $request->flag_id;
            $brandId = $request->brand_id;
            $searchKeyword = $request->search_keyword;

            $sortBy = $request->sort_by; // 1=>Low to High; 2=>High To Low
            $priceRangeMin = $request->minimum_price;
            $priceRangeMax = $request->maximum_price;
            $warrentyId = $request->warrenty_id;
            // $storageId = $request->storage_type_id;
            // $deviceConditionId = $request->product_type_id;
            // $simTypeId = $request->sim_type_id;
            // $regionId = $request->region_id;


            if($categoryId || $subcategoryId || $childcategoryId || $flagId || $brandId || $sortBy || $priceRangeMin || $priceRangeMax || $warrentyId || $searchKeyword){ //$storageId || $deviceConditionId || $simTypeId || $regionId ||
                $data = DB::table('products')
                            ->join('categories', 'products.category_id', '=', 'categories.id')
                            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                            ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                            ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                            ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                            ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                            ->where('products.status', 1)

                            ->when($searchKeyword, function($query) use ($searchKeyword){
                                return $query->where('products.name', 'LIKE', '%'.$searchKeyword.'%');
                            })
                            ->when($categoryId, function($query) use ($categoryId){
                                return $query->where('products.category_id', $categoryId);
                            })
                            ->when($subcategoryId, function($query) use ($subcategoryId){
                                return $query->where('products.subcategory_id', $subcategoryId);
                            })
                            ->when($childcategoryId, function($query) use ($childcategoryId){
                                return $query->where('products.childcategory_id', $childcategoryId);
                            })
                            ->when($flagId, function($query) use ($flagId){
                                return $query->where('products.flag_id', $flagId);
                            })
                            ->when($brandId, function($query) use ($brandId){
                                return $query->where('products.brand_id', $brandId);
                            })

                            ->when($sortBy, function($query) use ($sortBy){
                                if($sortBy == 1)
                                    return $query->orderBy('products.discount_price', 'asc')->orderBy('products.price', 'asc');
                                elseif($sortBy == 2)
                                    return $query->orderBy('products.discount_price', 'desc')->orderBy('products.price', 'desc');
                                else
                                    return $query->orderBy('products.discount_price', 'asc')->orderBy('products.price', 'asc');
                            })
                            ->when($priceRangeMin, function($query) use ($priceRangeMin){
                                return $query->where('products.discount_price', '>=', $priceRangeMin)->where('products.price', '>=', $priceRangeMin);
                            })
                            ->when($priceRangeMax, function($query) use ($priceRangeMax){
                                return $query->where('products.discount_price', '<=', $priceRangeMax)->where('products.price', '<=', $priceRangeMax);
                            })

                            // ->when($storageId, function($query) use ($storageId){
                            //     return $query->leftJoin('storage_types', 'product_variants.storage_type_id', '=', 'storage_types.id')
                            //             ->where('storage_types.id', $storageId);
                            // })
                            // ->when($deviceConditionId, function($query) use ($deviceConditionId){
                            //     return $query->leftJoin('device_conditions', 'product_variants.device_condition_id', '=', 'device_conditions.id')
                            //             ->where('device_conditions.id', $deviceConditionId);
                            // })
                            // ->when($simTypeId, function($query) use ($simTypeId){
                            //     return $query->leftJoin('sims', 'product_variants.sim_id', '=', 'sims.id')
                            //             ->where('sims.id', $simTypeId);
                            // })
                            // ->when($regionId, function($query) use ($regionId){
                            //     return $query->leftJoin('country', 'product_variants.region_id', '=', 'country.id')
                            //             ->where('country.id', $regionId);
                            // })
                            ->when($warrentyId, function($query) use ($warrentyId){
                                return $query->leftJoin('product_warrenties as variant_warrenty', 'product_variants.warrenty_id', '=', 'variant_warrenty.id')
                                        ->where('variant_warrenty.id', $warrentyId);
                            })

                            ->groupBy('products.id')
                            ->paginate(16);

                return response()->json([
                    'success' => true,
                    'data' => ProductResource::collection($data)->resource
                ], 200);
            } else {

                // will send no products at all
                $data = DB::table('products')
                            ->join('categories', 'products.category_id', '=', 'categories.id')
                            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                            ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                            ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                            ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                            ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                            ->where('products.id', '<', 0)
                            ->groupBy('products.id')
                            ->paginate(20);

                return response()->json([
                    'success' => true,
                    'data' => ProductResource::collection($data)->resource
                ], 200);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }

    }


    public function getAllStorages(Request $request){
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $storages = StorageType::orderBy('serial', 'asc')->where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $storages
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllSims(Request $request){
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $sims = Sim::orderBy('id', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $sims
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllDeviceConditions(Request $request){
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DeviceCondition::orderBy('serial', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllWarrentyTypes(Request $request){
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = ProductWarrenty::orderBy('serial', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllRegions(Request $request){
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('country')->orderBy('name', 'asc')->whereIn('id', [38,44,99,226,107,224])->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }
}