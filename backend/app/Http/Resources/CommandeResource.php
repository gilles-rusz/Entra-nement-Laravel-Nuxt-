<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'statut' => $this->statut,
            'total_ht' => (float) $this->total_ht,
            'lignes' => LigneCommandeResource::collection($this->whenLoaded('lignes')),
            'cree_le' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
