<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImportProduitsCsv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    // POST /api/v1/imports/produits (multipart/form-data, champ "fichier")
    // C'est le même CSV que celui déposé aujourd'hui sur le FTP, mais envoyé en HTTPS avec un token.
    public function produits(Request $request, ImportProduitsCsv $import): JsonResponse
    {
        $request->validate([
            'fichier' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $rapport = $import->importer($request->file('fichier')->getRealPath());

        return response()->json($rapport, $rapport['statut'] === 'refuse' ? 422 : 200);
    }
}
