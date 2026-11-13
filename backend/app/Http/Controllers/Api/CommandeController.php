<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommandeResource;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommandeController extends Controller
{
    // GET /api/v1/commandes : uniquement les commandes du client connecté.
    public function index(Request $request): AnonymousResourceCollection
    {
        $commandes = $request->user()->commandes()
            ->with('lignes.produit')
            ->latest()
            ->paginate(20);

        return CommandeResource::collection($commandes);
    }

    // POST /api/v1/commandes
    // Corps attendu : { "lignes": [ { "produit_id": 1, "quantite": 4 } ] }
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.produit_id' => ['required', 'integer', 'exists:produits,id'],
            'lignes.*.quantite' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        // Transaction : soit tout est enregistré (commande + lignes + stock), soit rien.
        // Sans ça, une erreur au milieu laisserait une commande à moitié créée.
        $commande = DB::transaction(function () use ($donnees, $request) {
            $commande = $request->user()->commandes()->create(['statut' => 'en_attente']);
            $total = 0;

            foreach ($donnees['lignes'] as $index => $ligne) {
                // lockForUpdate : empêche deux commandes simultanées de vendre le même dernier pneu.
                $produit = Produit::lockForUpdate()->findOrFail($ligne['produit_id']);

                if ($produit->stock < $ligne['quantite']) {
                    throw ValidationException::withMessages([
                        "lignes.$index.quantite" => "Stock insuffisant pour {$produit->reference} (disponible : {$produit->stock}).",
                    ]);
                }

                $produit->decrement('stock', $ligne['quantite']);

                $commande->lignes()->create([
                    'produit_id' => $produit->id,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire_ht' => $produit->prix_ht,
                ]);

                $total += $ligne['quantite'] * $produit->prix_ht;
            }

            $commande->update(['total_ht' => round($total, 2)]);

            return $commande;
        });

        return (new CommandeResource($commande->load('lignes.produit')))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/v1/commandes/{commande}
    public function show(Request $request, Commande $commande): CommandeResource
    {
        // Un client ne doit jamais voir la commande d'un autre client.
        abort_if($commande->user_id !== $request->user()->id, 403, 'Cette commande ne vous appartient pas.');

        return new CommandeResource($commande->load('lignes.produit'));
    }
}
