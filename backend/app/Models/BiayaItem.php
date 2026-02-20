<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BiayaItem extends Model
{
    use HasUuids;

    protected $fillable = ['sbu_type_id', 'asosiasi_id', 'category', 'name', 'biaya'];

    public function sbuType()
    {
        return $this->belongsTo(SbuType::class);
    }

    public function asosiasi()
    {
        return $this->belongsTo(Asosiasi::class);
    }
}
