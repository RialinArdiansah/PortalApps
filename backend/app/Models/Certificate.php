<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'sub_menus', 'sbu_type_slug'];

    protected function casts(): array
    {
        return [
            'sub_menus' => 'array',
        ];
    }
}
