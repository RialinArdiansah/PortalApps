<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'companyName' => $this->company_name,
            'marketingName' => $this->marketing_name,
            'inputDate' => $this->input_date?->format('Y-m-d'),
            'submittedById' => $this->submitted_by_id,
            'certificateType' => $this->certificate_type,
            'sbuType' => $this->sbu_type,
            'selectedSub' => $this->selected_sub,
            'selectedKlasifikasi' => $this->selected_klasifikasi,
            'selectedSubKlasifikasi' => $this->selected_sub_klasifikasi,
            'selectedKualifikasi' => $this->selected_kualifikasi,
            'selectedBiayaLainnya' => $this->selected_biaya_lainnya,
            'biayaSetorKantor' => $this->biaya_setor_kantor,
            'keuntungan' => $this->keuntungan,
        ];
    }
}
