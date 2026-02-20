<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_name',
        'marketing_name',
        'input_date',
        'submitted_by_id',
        'certificate_type',
        'sbu_type',
        'selected_sub',
        'selected_klasifikasi',
        'selected_sub_klasifikasi',
        'selected_kualifikasi',
        'selected_biaya_lainnya',
        'biaya_setor_kantor',
        'keuntungan',
    ];

    protected function casts(): array
    {
        return [
            'input_date' => 'date:Y-m-d',
            'selected_sub' => 'array',
            'selected_klasifikasi' => 'array',
            'selected_kualifikasi' => 'array',
            'selected_biaya_lainnya' => 'array',
            'biaya_setor_kantor' => 'integer',
            'keuntungan' => 'integer',
        ];
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }
}
