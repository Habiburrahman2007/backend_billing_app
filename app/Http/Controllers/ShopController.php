<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadLogoRequest;
use App\Http\Requests\UpsertShopRequest;
use App\Http\Resources\ShopResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * GET /api/shop
     */
    public function show(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return response()->json(['message' => 'Shop not found.'], 404);
        }

        return response()->json(new ShopResource($shop));
    }

    /**
     * POST /api/shop — Create or update (upsert) shop info
     */
    public function upsert(UpsertShopRequest $request): JsonResponse
    {
        $shop = $request->user()->shop()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        $statusCode = $shop->wasRecentlyCreated ? 201 : 200;

        return response()->json(new ShopResource($shop), $statusCode);
    }

    /**
     * POST /api/shop/logo — Upload shop logo (base64 JSON)
     */
    public function uploadLogo(UploadLogoRequest $request): JsonResponse
    {
        $base64 = $request->logo;

        // Strip data URI prefix if present: "data:image/png;base64,..."
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        // Decode base64 string
        $imageData = base64_decode($base64, strict: true);

        if ($imageData === false) {
            return response()->json([
                'message' => 'Invalid base64 image data.',
            ], 422);
        }

        // Delete old logo if it exists
        $shop = $request->user()->shop;
        if ($shop && $shop->logo_path) {
            Storage::disk('public')->delete($shop->logo_path);
        }

        // Store new logo
        $filename  = 'logos/' . Str::uuid() . '.png';
        Storage::disk('public')->put($filename, $imageData);

        // Upsert shop with new logo path (ensure shop exists)
        $shop = $request->user()->shop()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['logo_path' => $filename]
        );

        return response()->json([
            'message'  => 'Logo uploaded successfully.',
            'logo_url' => Storage::disk('public')->url($filename),
        ]);
    }
}
