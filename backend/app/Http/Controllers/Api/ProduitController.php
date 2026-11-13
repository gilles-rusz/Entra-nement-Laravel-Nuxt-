<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProduitController extends Controller
{
    // GET /api/v1/produits?marque=Michelin&saison=hiver
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtres = $request->validate([
            'marque' => ['nullable', 'string', 'max:50'],
            'saison' => ['nullable', 'in:ete,hiver,4saisons'],
        ]);

        $produits = Produit::query()
            ->when($filtres['marque'] ?? null, fn ($q, $marque) => $q->where('marque', $marque))
            ->when($filtres['saison'] ?? null, fn ($q, $saison) => $q->where('saison', $saison))
            ->orderBy('marque')
            ->paginate(20);

        return ProduitResource::collection($produits);
    }

    // GET /api/v1/produits/{produit} : Laravel trouve le produit tout seul (route model binding),
    // et renvoie 404 s'il n'existe pas.
    public function show(Produit $produit): ProduitResource
    {
        return new ProduitResource($produit);
    }
}
