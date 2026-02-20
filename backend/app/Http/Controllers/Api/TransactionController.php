<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * GET /api/transactions
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Transaction::query();

        if (!$user->canViewAll()) {
            $query->where('submitted_by_id', $user->id);
        }

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($query->get()),
        ]);
    }

    /**
     * POST /api/transactions
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transactionDate' => 'required|date',
            'transactionName' => 'required|string|max:255',
            'cost' => 'required|integer|min:0',
            'transactionType' => 'required|in:Keluar,Tabungan,Kas',
            'proof' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            'transaction_date' => $validated['transactionDate'],
            'transaction_name' => $validated['transactionName'],
            'cost' => $validated['cost'],
            'transaction_type' => $validated['transactionType'],
            'submitted_by_id' => $request->user()->id,
            'proof' => $validated['proof'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
        ], 201);
    }

    /**
     * PUT /api/transactions/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);

        if (!$request->user()->canViewAll() && $transaction->submitted_by_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'transactionDate' => 'sometimes|date',
            'transactionName' => 'sometimes|string|max:255',
            'cost' => 'sometimes|integer|min:0',
            'transactionType' => 'sometimes|in:Keluar,Tabungan,Kas',
            'proof' => 'nullable|string',
        ]);

        $updateData = [];
        if (isset($validated['transactionDate']))
            $updateData['transaction_date'] = $validated['transactionDate'];
        if (isset($validated['transactionName']))
            $updateData['transaction_name'] = $validated['transactionName'];
        if (isset($validated['cost']))
            $updateData['cost'] = $validated['cost'];
        if (isset($validated['transactionType']))
            $updateData['transaction_type'] = $validated['transactionType'];
        if (array_key_exists('proof', $validated))
            $updateData['proof'] = $validated['proof'];

        $transaction->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction->fresh()),
        ]);
    }

    /**
     * DELETE /api/transactions/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);

        if (!$request->user()->canViewAll() && $transaction->submitted_by_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $transaction->delete();

        return response()->json(['success' => true]);
    }
}
