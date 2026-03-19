<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * GET /api/transactions
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $request->user()
            ->transactions()
            ->latest()
            ->paginate(20);

        return response()->json([
            'data'  => TransactionResource::collection($transactions->items()),
            'meta'  => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }

    /**
     * POST /api/transactions
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $transaction = DB::transaction(function () use ($request, $data) {
            // Create the transaction
            $transaction = Transaction::create([
                'user_id'      => $request->user()->id,
                'total_amount' => $data['total_amount'],
                'note'         => $data['note'] ?? null,
            ]);

            // Create each item and decrement stock
            foreach ($data['items'] as $item) {
                $transaction->items()->create([
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity'      => $item['quantity'],
                    'subtotal'      => $item['subtotal'],
                ]);

                // Decrement product stock
                Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            return $transaction->load('items');
        });

        return response()->json(new TransactionResource($transaction), 201);
    }

    /**
     * GET /api/transactions/{transaction}
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        return response()->json(new TransactionResource($transaction->load('items')));
    }
}
