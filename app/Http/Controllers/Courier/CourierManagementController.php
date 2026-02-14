<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\ProductOrderCourierMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierManagementController extends Controller
{
    /**
     * Show courier management page (Vue loads data via API).
     */
    public function index()
    {
        return view('backend.courier.index');
    }

    /**
     * Return all courier methods for the management UI.
     */
    public function getMethods(): JsonResponse
    {
        $methods = ProductOrderCourierMethod::orderBy('id')->get();
        return response()->json($methods);
    }

    /**
     * Update a courier method's config and status.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $method = ProductOrderCourierMethod::findOrFail($id);

        $validated = $request->validate([
            'config' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        if (isset($validated['config'])) {
            $method->config = $validated['config'];
        }
        if (isset($validated['status'])) {
            $method->status = $validated['status'];
        }
        $method->save();

        return response()->json($method);
    }
}
