<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\ProductOrderCourierMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoController extends Controller
{
    private const BASE_URL = 'https://api-hermes.pathao.com/aladdin/api/v1';

    /**
     * Get Pathao order short info by consignment_id.
     * API: GET /aladdin/api/v1/orders/{{consignment_id}}/info
     * Public route (no auth).
     */
    public function getOrderStatus(string $pathaoId): JsonResponse
    {
        $courierMethod = ProductOrderCourierMethod::where('title', 'like', '%Pathao%')
            ->where('status', 'active')
            ->first();

        if (!$courierMethod) {
            return response()->json(['success' => false, 'message' => 'Pathao courier method not configured.'], 404);
        }

        $config = $courierMethod->config ?? [];
        $accessToken = $this->getAccessToken((int) $courierMethod->id, $config);
        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Failed to get Pathao access token.'], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->get(self::BASE_URL . '/orders/' . $pathaoId . '/info');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(
                $response->json() ?? ['message' => $response->body()],
                $response->status()
            );
        } catch (\Throwable $e) {
            Log::error('Pathao getOrderStatus exception', ['pathao_id' => $pathaoId, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Pathao order (consignment) for the given ProductOrder.
     * Uses courier method config: client_id, client_secret, grant_type, username, password.
     * Optional delivery_info: pathao_city_id, pathao_zone_id, pathao_area_id (Pathao will auto-fill from address if omitted).
     * API: POST {{base_url}}/aladdin/api/v1/orders
     */
    public function createOrder(ProductOrder $order, $courierMethodId)
    {
        $courierMethod = ProductOrderCourierMethod::find($courierMethodId);
        if (!$courierMethod) {
            Log::warning('Pathao: courier method not found', ['id' => $courierMethodId]);
            return null;
        }

        $config = $courierMethod->config ?? [];
        $clientId = $config['client_id'] ?? '';
        $clientSecret = $config['client_secret'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        if (empty($clientId) || empty($clientSecret) || empty($username) || empty($password)) {
            Log::warning('Pathao: missing credentials in config');
            return null;
        }

        $accessToken = $this->getAccessToken($courierMethodId, $config);
        if (!$accessToken) {
            Log::warning('Pathao: failed to get access token', ['order_id' => $order->id]);
            return null;
        }

        $storeId = $this->getStoreId($courierMethodId, $accessToken);
        if (!$storeId) {
            Log::warning('Pathao: failed to get store id', ['order_id' => $order->id]);
            return null;
        }

        $order->load(['customer', 'order_products.product']);
        $customer = $order->customer;
        $deliveryInfo = $order->delivery_info ?? [];

        $recipientName = trim($customer ? ($customer->name ?? '') : '');
        $recipientPhone = $this->normalizePhone($customer ? ($customer->phone ?? '') : '');
        $recipientAddress = ($customer && !empty($customer->address))
            ? trim($customer->address)
            : trim($deliveryInfo['recipient_address'] ?? $deliveryInfo['address'] ?? '');

        if (strlen($recipientName) < 3 || strlen($recipientPhone) !== 11 || strlen($recipientAddress) < 10) {
            Log::warning('Pathao: recipient_name (3-100), recipient_phone (11 digits), recipient_address (10-220) required', ['order_id' => $order->id]);
            return null;
        }

        $itemNames = $order->order_products->map(function ($op) {
            return $op->product ? $op->product->name : 'Item';
        })->toArray();
        $itemDescription = implode(', ', array_slice($itemNames, 0, 10));
        if (count($itemNames) > 10) {
            $itemDescription .= '...';
        }
        $itemQuantity = (int) max(1, $order->order_products->sum('qty'));
        $itemWeight = (float) ($deliveryInfo['pathao_item_weight'] ?? $deliveryInfo['item_weight'] ?? 1);
        $itemWeight = max(0.5, min(10, $itemWeight)); // Pathao: min 0.5 kg, max 10 kg

        // delivery_type: 48 = Normal Delivery, 12 = On Demand
        $deliveryType = (int) ($deliveryInfo['pathao_delivery_type'] ?? $deliveryInfo['delivery_type'] ?? 48);
        // item_type: 1 = Document, 2 = Parcel
        $itemType = (int) ($deliveryInfo['pathao_item_type'] ?? $deliveryInfo['item_type'] ?? 2);

        $orderPayload = [
            'store_id' => $storeId,
            'merchant_order_id' => $order->order_code ?? ('ORD' . $order->id . '-' . time()),
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            // 'recipient_address' => substr($recipientAddress, 0, 220),
            'recipient_address' => substr($order->address, 0, 220),
            'delivery_type' => $deliveryType,
            'item_type' => $itemType,
            'special_instruction' => substr((string) ($order->note ?? ''), 0, 500),
            'item_quantity' => $itemQuantity,
            'item_weight' => (string) $itemWeight,
            'item_description' => $itemDescription,
            'amount_to_collect' => (int) round($order->total),
        ];

        // Optional: only include if set (do not send null per Pathao docs)
        $recipientCity = (int) ($deliveryInfo['pathao_city_id'] ?? $deliveryInfo['recipient_city'] ?? 0);
        $recipientZone = (int) ($deliveryInfo['pathao_zone_id'] ?? $deliveryInfo['recipient_zone'] ?? 0);
        $recipientArea = (int) ($deliveryInfo['pathao_area_id'] ?? $deliveryInfo['recipient_area'] ?? 0);
        if ($recipientCity > 0) {
            $orderPayload['recipient_city'] = $recipientCity;
        }
        if ($recipientZone > 0) {
            $orderPayload['recipient_zone'] = $recipientZone;
        }
        if ($recipientArea > 0) {
            $orderPayload['recipient_area'] = $recipientArea;
        }
        $secondaryPhone = $this->normalizePhone($deliveryInfo['recipient_secondary_phone'] ?? $deliveryInfo['alternative_phone'] ?? '');
        if (strlen($secondaryPhone) === 11) {
            $orderPayload['recipient_secondary_phone'] = $secondaryPhone;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(self::BASE_URL . '/orders', $orderPayload);

            if ($response->successful()) {
                $body = $response->json();
                $order->is_couriered = 1;
                $order->courier_info = array_merge($body['data'] ?? $body, [
                    'courier' => 'pathao',
                    'merchant_order_id' => $orderPayload['merchant_order_id'],
                    'order_status_url' => route('pathao.order-status', $body['data']['consignment_id']),
                    'pathao_status_url' => "https://merchant.pathao.com/tracking?consignment_id=" . $body['data']['consignment_id'] . "&phone=" . $recipientPhone,
                ]);
                $order->save();
                return $body;
            }

            Log::warning('Pathao create order failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Pathao create order exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Issue access token. API: POST {{base_url}}/aladdin/api/v1/issue-token
     * Body: client_id, client_secret, grant_type (password), username, password
     */
    private function getAccessToken(int $courierMethodId, array $config): ?string
    {
        $cacheKey = 'pathao_access_token_courier_' . $courierMethodId;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $credData = [
            'client_id' => $config['client_id'] ?? '',
            'client_secret' => $config['client_secret'] ?? '',
            'grant_type' => $config['grant_type'] ?? 'password',
            'username' => $config['username'] ?? '',
            'password' => $config['password'] ?? '',
        ];

        $tokenResponse = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(30)
            ->post(self::BASE_URL . '/issue-token', $credData);

        if (!$tokenResponse->successful()) {
            Log::error('Pathao issue-token failed', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->body(),
            ]);
            return null;
        }

        $data = $tokenResponse->json();
        $accessToken = $data['access_token'] ?? null;
        if ($accessToken) {
            $expiresIn = (int) ($data['expires_in'] ?? 432000); // default 5 days in seconds
            Cache::put($cacheKey, $accessToken, max(60, $expiresIn - 60));
        }
        return $accessToken;
    }

    private function getStoreId(int $courierMethodId, string $accessToken): ?int
    {
        $cacheKey = 'pathao_store_id_courier_' . $courierMethodId;
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return (int) $cached;
        }

        $storeResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->get(self::BASE_URL . '/stores');

        if (!$storeResponse->successful()) {
            return null;
        }

        $data = $storeResponse->json();
        $stores = $data['data']['data'] ?? $data['data'] ?? [];
        $firstStore = is_array($stores) ? ($stores[0] ?? null) : null;
        $storeId = $firstStore['store_id'] ?? null;
        if ($storeId !== null) {
            Cache::put($cacheKey, $storeId, 86400); // 24h
        }
        return $storeId !== null ? (int) $storeId : null;
    }

    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if (strlen($digits) > 11) {
            $digits = substr($digits, -11);
        }
        return $digits;
    }
}
