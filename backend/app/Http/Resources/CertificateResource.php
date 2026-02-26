<?php

namespace App\Http\Resources;

use App\Models\SbuType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Load menuConfig from the related SbuType
        $menuConfig = null;
        if ($this->sbu_type_slug) {
            $sbuType = SbuType::where('slug', $this->sbu_type_slug)->first();
            $menuConfig = $sbuType?->menu_config;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'subMenus' => $this->sub_menus ?? [],
            'sbuTypeSlug' => $this->sbu_type_slug,
            'menuConfig' => $menuConfig,
        ];
    }
}
