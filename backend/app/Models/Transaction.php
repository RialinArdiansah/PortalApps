<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'transaction_date',
        'transaction_name',
        'cost',
        'transaction_type',
        'submitted_by_id',
        'proof',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date:Y-m-d',
            'cost' => 'integer',
        ];
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }
}
