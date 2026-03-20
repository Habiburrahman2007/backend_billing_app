<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $products = $request->user()->products()->latest()->get();

        return response()->json(ProductResource::collection($products));
    }

    /**
     * POST /api/products
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Use client-provided UUID or generate one
        $data['id']      = $data['id'] ?? (string) Str::uuid();
        $data['user_id'] = $request->user()->id;

        $product = Product::create($data);

        return response()->json(new ProductResource($product), 201);
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('view', $product);

        return response()->json(new ProductResource($product));
    }

    /**
     * PUT /api/products/{product}
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $product->update($request->validated());

        return response()->json(new ProductResource($product));
    }

    /**
     * DELETE /api/products/{product}
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);

        try {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully.'], 200);
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint violation (Integrity constraint violation)
            // SQLSTATE 23000 is for integrity constraint violations
            if ($e->getCode() == "23000" || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return response()->json([
                    'message' => 'Gagal menghapus: Produk ini sudah pernah digunakan dalam transaksi dan tidak bisa dihapus demi integritas data.'
                ], 400);
            }
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * GET /api/products/barcode/{barcode}
     */
    public function findByBarcode(Request $request, string $barcode): JsonResponse
    {
        $product = $request->user()->products()
            ->where('barcode', $barcode)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json(new ProductResource($product));
    }
}
