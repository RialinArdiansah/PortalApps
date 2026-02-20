<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Klasifikasi extends Model
{
    use HasUuids;

    protected $table = 'klasifikasi';

    protected $fillable = ['sbu_type_id', 'asosiasi_id', 'name', 'sub_klasifikasi', 'kualifikasi', 'sub_bidang'];

    protected function casts(): array
    {
        return [
            'sub_klasifikasi' => 'array',
            'kualifikasi' => 'array',
            'sub_bidang' => 'array',
        ];
    }

    public function sbuType()
    {
        return $this->belongsTo(SbuType::class);
    }

    public function asosiasi()
    {
        return $this->belongsTo(Asosiasi::class);
    }
}
