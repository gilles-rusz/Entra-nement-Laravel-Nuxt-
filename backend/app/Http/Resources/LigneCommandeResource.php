<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LigneCommandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'produit' => new ProduitResource($this->whenLoaded('produit')),
            'quantite' => $this->quantite,
            'prix_unitaire_ht' => (float) $this->prix_unitaire_ht,
            'total_ht' => round($this->quantite * $this->prix_unitaire_ht, 2),
        ];
    }
}
