<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\ProductOrderCourierMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastController extends Controller
{
    /**
     * Get Steadfast API config (url + headers). Returns null if not configured.
     * @return array{api_url: string, headers: array}|null
     */
    private function getSteadfastConfig(): ?array
    {
        $courierMethod = ProductOrderCourierMethod::where('title', 'like', '%Steadfast%')
            ->where('status', 'active')
            ->first();

        if (!$courierMethod) {
            return null;
        }

        $config = $courierMethod->config ?? [];
        $apiUrl = rtrim($config['api_url'] ?? '', '/');
        $apiKey = $config['api_key'] ?? '';
        $apiSecret = $config['api_secret'] ?? '';

        if (empty($apiUrl) || empty($apiKey) || (string) $apiSecret === '') {
            return null;
        }

        return [
            'api_url' => $apiUrl,
            'headers' => [
                'Api-Key' => $apiKey,
                'Secret-Key' => $apiSecret,
                'Content-Type' => 'application/json',
            ],
        ];
    }

    /**
     * Get order status from Steadfast by consignment ID.
     * API: GET /status_by_cid/{id}
     */
    public function getOrderStatus(string $steadFastId): JsonResponse
    {
        $config = $this->getSteadfastConfig();
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Steadfast courier method not configured.'], 404);
        }

        $endpoint = $config['api_url'] . '/status_by_cid/' . $steadFastId;

        try {
            $response = Http::withHeaders($config['headers'])->timeout(30)->get($endpoint);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            Log::error('Steadfast getOrderStatus exception', ['stead_fast_id' => $steadFastId, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get current balance. API: GET /get_balance
     */
    public function getBalance(): JsonResponse
    {
        return $this->steadfastGet('/get_balance');
    }

    /**
     * Create return request. API: POST /create_return_request
     * Body: consignment_id or invoice or tracking_code (one required), reason (optional)
     */
    public function createReturnRequest(Request $request): JsonResponse
    {
        $payload = array_filter([
            'consignment_id' => $request->input('consignment_id'),
            'invoice' => $request->input('invoice'),
            'tracking_code' => $request->input('tracking_code'),
            'reason' => $request->input('reason'),
        ], fn ($v) => $v !== null && $v !== '');
        $hasIdentifier = isset($payload['consignment_id']) || isset($payload['invoice']) || isset($payload['tracking_code']);
        if (!$hasIdentifier) {
            return response()->json(['success' => false, 'message' => 'One of consignment_id, invoice or tracking_code is required.'], 422);
        }
        return $this->steadfastPost('/create_return_request', $payload);
    }

    /**
     * Single return request view. API: GET /get_return_request/{id}
     */
    public function getReturnRequest(string $id): JsonResponse
    {
        return $this->steadfastGet('/get_return_request/' . $id);
    }

    /**
     * Get return requests list. API: GET /get_return_requests
     */
    public function getReturnRequests(): JsonResponse
    {
        return $this->steadfastGet('/get_return_requests');
    }

    /**
     * Get payments. API: GET /payments
     */
    public function getPayments(): JsonResponse
    {
        return $this->steadfastGet('/payments');
    }

    /**
     * Get single payment with consignments. API: GET /payments/{payment_id}
     */
    public function getPayment(string $paymentId): JsonResponse
    {
        return $this->steadfastGet('/payments/' . $paymentId);
    }

    /**
     * Get police stations. API: GET /police_stations
     */
    public function getPoliceStations(): JsonResponse
    {
        return $this->steadfastGet('/police_stations');
    }

    private function steadfastGet(string $path): JsonResponse
    {
        $config = $this->getSteadfastConfig();
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Steadfast courier method not configured.'], 404);
        }
        $endpoint = $config['api_url'] . $path;
        try {
            $response = Http::withHeaders($config['headers'])->timeout(30)->get($endpoint);
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json($response->json() ?? ['message' => $response->body()], $response->status());
        } catch (\Throwable $e) {
            Log::error('Steadfast API GET exception', ['path' => $path, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function steadfastPost(string $path, array $payload): JsonResponse
    {
        $config = $this->getSteadfastConfig();
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Steadfast courier method not configured.'], 404);
        }
        $endpoint = $config['api_url'] . $path;
        try {
            $response = Http::withHeaders($config['headers'])->timeout(30)->post($endpoint, $payload);
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json($response->json() ?? ['message' => $response->body()], $response->status());
        } catch (\Throwable $e) {
            Log::error('Steadfast API POST exception', ['path' => $path, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a consignment at Steadfast (Bangladesh) for the given order.
     * API: POST /create_order
     */
    public function createOrder(ProductOrder $order, $courierMethodId)
    {
        $courierMethod = ProductOrderCourierMethod::find($courierMethodId);
        if (!$courierMethod) {
            Log::warning('Steadfast: courier method not found', ['id' => $courierMethodId]);
            return null;
        }

        $config = $courierMethod->config ?? [];
        $apiUrl = rtrim($config['api_url'] ?? '', '/');
        $apiKey = $config['api_key'] ?? '';
        $apiSecret = $config['api_secret'] ?? '';

        if (empty($apiUrl) || empty($apiKey) || (string) $apiSecret === '') {
            Log::warning('Steadfast: missing api_url, api_key or api_secret in config');
            return null;
        }

        $order->load(['customer', 'order_products.product']);
        $customer = $order->customer;
        $deliveryInfo = $order->delivery_info ?? [];

        $recipientName = $customer ? ($customer->name ?? '') : '';
        $recipientPhone = $this->normalizePhone($customer ? ($customer->phone ?? '') : '');
        $recipientAddress = ($customer && !empty($customer->address))
            ? $customer->address
            : ($deliveryInfo['recipient_address'] ?? '');
        $recipientEmail = $customer ? ($customer->email ?? '') : '';

        if (strlen($recipientPhone) !== 11) {
            Log::warning('Steadfast: recipient_phone must be 11 digits', ['order_id' => $order->id, 'phone' => $recipientPhone]);
        }

        $invoice = $this->sanitizeInvoice($order->order_code ?? (string) $order->id);
        $itemNames = $order->order_products->map(function ($op) {
            return $op->product ? $op->product->name : 'Item';
        })->toArray();
        $itemDescription = implode(', ', array_slice($itemNames, 0, 10));
        if (count($itemNames) > 10) {
            $itemDescription .= '...';
        }
        $totalLot = $order->order_products->sum('qty');

        $payload = [
            'invoice' => $invoice,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'recipient_address' => substr($order->address, 0, 220),
            'cod_amount' => (float) $order->total,
            'note' => $order->note ?? '',
            'item_description' => $itemDescription,
            'total_lot' => (int) $totalLot,
        ];

        if (!empty($recipientEmail)) {
            $payload['recipient_email'] = $recipientEmail;
        }
        if (!empty($deliveryInfo['alternative_phone'])) {
            $payload['alternative_phone'] = $this->normalizePhone($deliveryInfo['alternative_phone']);
        }
        if (isset($deliveryInfo['delivery_type'])) {
            $payload['delivery_type'] = (int) $deliveryInfo['delivery_type'];
        }

        $endpoint = $apiUrl . '/create_order';
        $headers = [
            'Api-Key' => $apiKey,
            'Secret-Key' => $apiSecret,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)->timeout(30)->post($endpoint, $payload);
            if ($response->successful()) {
                $body = $response->json();
                $order->is_couriered = 1;
                $order->courier_info = [
                    ...$body['consignment'],
                    'order_status_url' => route('steadfast.order-status', $body['consignment']['consignment_id']),
                ];
                $order->save();
                return $body;
            }

            Log::warning('Steadfast create_order failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Steadfast create_order exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Invoice must be unique, alpha-numeric including hyphens and underscores.
     */
    private function sanitizeInvoice(string $code): string
    {
        $code = preg_replace('/[^a-zA-Z0-9\-_]/', '', $code);
        return $code !== '' ? $code : 'inv-' . uniqid();
    }

    /**
     * Normalize to 11-digit Bangladesh phone (digits only; take last 11 if longer).
     */
    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if (strlen($digits) > 11) {
            $digits = substr($digits, -11);
        }
        return $digits;
    }
}
