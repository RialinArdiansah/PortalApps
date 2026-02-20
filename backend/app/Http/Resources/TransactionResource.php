<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transactionDate' => $this->transaction_date?->format('Y-m-d'),
            'transactionName' => $this->transaction_name,
            'cost' => $this->cost,
            'transactionType' => $this->transaction_type,
            'submittedById' => $this->submitted_by_id,
            'proof' => $this->proof,
        ];
    }
}
