<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Une Resource choisit les champs envoyés en JSON au client.
// On ne renvoie jamais directement le modèle : on garde le contrôle sur ce qui sort.
class ProduitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'marque' => $this->marque,
            'modele' => $this->modele,
            'dimension' => $this->dimension,
            'saison' => $this->saison,
            'prix_ht' => (float) $this->prix_ht,
            'stock' => $this->stock,
            'disponible' => $this->stock > 0,
        ];
    }
}
