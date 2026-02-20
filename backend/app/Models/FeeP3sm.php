<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FeeP3sm extends Model
{
    use HasUuids;

    protected $table = 'fee_p3sm';

    protected $fillable = ['cost', 'month', 'year'];

    protected function casts(): array
    {
        return [
            'cost' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }
}
