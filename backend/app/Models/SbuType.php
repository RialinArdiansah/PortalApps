<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SbuType extends Model
{
    use HasUuids;

    protected $fillable = ['slug', 'name'];

    public function asosiasi()
    {
        return $this->hasMany(Asosiasi::class);
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
