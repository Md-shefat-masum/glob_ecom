<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PathaoController extends BaseController
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';

    private $accessToken;
    private $store;
    private $baseUrl;
    

    public function __construct()
    {
        $this->baseUrl = "https://api-hermes.pathao.com/aladdin/api/v1";
        
        // Try to get token from cache first (1 hour cache)
        $this->accessToken = Cache::get('pathao_access_token');
        $this->store = Cache::get('pathao_store_info');
        
        // If token doesn't exist in cache, generate new one
        if (!$this->accessToken) {
            $this->generateAndCacheToken();
        }
        
        // If store info doesn't exist, fetch it
        if (!$this->store && $this->accessToken) {
            $this->fetchAndCacheStoreInfo();
        }
    }
    
    /**
     * Generate new Pathao token and cache it for 1 hour
     */
    private function generateAndCacheToken()
    {
        try {
            $cred_data = [
                "client_id" => env('PATHAO_CLIENT_ID', ''),
                "client_secret" => env('PATHAO_CLIENT_SECRET', ''),
                "grant_type" => "password",
                "username" => env('PATHAO_USERNAME', ''),
                "password" => env('PATHAO_PASSWORD', '')
            ];

            $tokenResponse = Http::post("{$this->baseUrl}/issue-token", $cred_data);
            
            if ($tokenResponse->successful()) {
                $responseData = $tokenResponse->json();
                
                if (isset($responseData['access_token'])) {
                    $this->accessToken = $responseData['access_token'];
                    
                    // Cache token for 1 hour (3600 seconds)
                    Cache::put('pathao_access_token', $this->accessToken, 3600);
                    
                    Log::info('Pathao token generated and cached for 1 hour');
                } else {
                    Log::error('Pathao token generation failed - no access_token in response', [
                        'response' => $responseData
                    ]);
                    $this->accessToken = null;
                }
            } else {
                Log::error('Pathao token generation failed', [
                    'response' => $tokenResponse->json(),
                    'status' => $tokenResponse->status()
                ]);
                $this->accessToken = null;
            }
        } catch (\Exception $e) {
            Log::error('Pathao token generation exception', [
                'error' => $e->getMessage()
            ]);
            $this->accessToken = null;
        }
    }
    
    /**
     * Fetch store info and cache it for 1 hour
     */
    private function fetchAndCacheStoreInfo()
    {
        try {
            $storeResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$this->accessToken}",
                ])
                ->get("{$this->baseUrl}/stores");

            if ($storeResponse->successful()) {
                $this->store = $storeResponse->json()['data']['data'][0] ?? null;
                
                if ($this->store) {
                    // Cache store info for 1 hour (3600 seconds)
                    Cache::put('pathao_store_info', $this->store, 3600);
                    
                    Log::info('Pathao store info cached for 1 hour', [
                        'store_id' => $this->store['store_id'] ?? 'unknown'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Pathao store fetch failed', ['error' => $e->getMessage()]);
            $this->store = null;
        }
    }

    public function createOrder(Request $request)
    {
        if (!$this->accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Pathao authentication failed. Please check Pathao API credentials.'
            ], 500);
        }

        $request->validate([
            'recipient_name'    => 'required|string',
            'recipient_phone'   => 'required|string',
            'recipient_address' => 'required|string',
            'recipient_city'    => 'required|integer',
            'recipient_zone'    => 'required|integer',
            'recipient_area'    => 'required|integer',
            'delivery_type'     => 'required|integer',
            'item_type'         => 'required|integer',
            'item_quantity'     => 'required|numeric',
            'item_weight'       => 'required|numeric',
            'item_description'  => 'required|string',
            'amount_to_collect' => 'required|numeric',
        ]);

        $orderData = array_merge($request->all(), [
            'store_id' => $this->store['store_id'] ?? null,
            'merchant_order_id' => 'ORDER' . now()->timestamp,
            'special_instruction' => $request->special_instruction ?? '',
        ]);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json'
        ])->post("{$this->baseUrl}/orders", $orderData);

        return response()->json($response->json());
    }
    
    public function getOrderInfo($orderId)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json'
        ])->get("{$this->baseUrl}/orders/{$orderId}/info");
    
        return response()->json($response->json());
    }

    public function getPricePlan(Request $request)
    {
        if (!$this->accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Pathao authentication failed. Please check Pathao API credentials.'
            ], 500);
        }

        $request->validate([
            'item_type' => 'required|integer',
            'delivery_type' => 'required|integer',
            'item_weight' => 'required|numeric',
            'recipient_city' => 'required|integer',
            'recipient_zone' => 'required|integer',
        ]);

        $priceData = array_merge($request->all(), [
            'store_id' => $this->store['store_id'] ?? null
        ]);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json; charset=UTF-8'
        ])
        ->post("{$this->baseUrl}/merchant/price-plan", $priceData);

        return response()->json($response->json());
    }
    
    public function store_info(){
        return response()->json([
           'data' => $this->store,
           'status' => 'success',
           'code' => 200,
        ]);
    }
    
    public function cities()
    {
        try {
            if (!$this->accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pathao authentication failed. Please check Pathao API credentials.'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type'  => 'application/json; charset=UTF-8'
            ])
            ->withOptions([
                'verify' => false,
            ])
            ->get("{$this->baseUrl}/city-list");
    
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function zones($cityId)
    {
        try {
            if (!$this->accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pathao authentication failed. Please check Pathao API credentials.'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type'  => 'application/json; charset=UTF-8'
            ])
            ->withOptions([
                'verify' => false,
            ])
            ->get("{$this->baseUrl}/zone-list", [
                'city_id' => $cityId
            ]);
    
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function areas($zoneId)
    {
        try {
            if (!$this->accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pathao authentication failed. Please check Pathao API credentials.'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type'  => 'application/json; charset=UTF-8'
            ])
            ->withOptions([
                'verify' => false,
            ])
            ->get("{$this->baseUrl}/area-list", [
                'zone_id' => $zoneId
            ]);
    
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}