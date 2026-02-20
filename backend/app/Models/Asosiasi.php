<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Asosiasi extends Model
{
    use HasUuids;

    protected $table = 'asosiasi';

    protected $fillable = ['sbu_type_id', 'name', 'sub_klasifikasi'];

    protected function casts(): array
    {
        return [
            'sub_klasifikasi' => 'array',
        ];
    }

    public function sbuType()
    {
        return $this->belongsTo(SbuType::class);
    }

    public function klasifikasi()
    {
        return $this->hasMany(Klasifikasi::class);
    }

    public function biayaItems()
    {
        return $this->hasMany(BiayaItem::class);
    }
}
