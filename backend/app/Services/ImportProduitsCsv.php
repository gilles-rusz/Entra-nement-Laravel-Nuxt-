<?php

namespace App\Services;

use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Importe un fichier CSV de produits, en gérant les pièges classiques des fichiers clients :
// encodage Windows (accents cassés), séparateur ";" ou ",", BOM Excel, prix "89,90".
// Les lignes valides sont importées, les lignes fausses sont listées dans le rapport.
class ImportProduitsCsv
{
    private const COLONNES = ['reference', 'marque', 'modele', 'dimension', 'saison', 'prix_ht', 'stock'];

    public function importer(string $chemin): array
    {
        $contenu = file_get_contents($chemin);

        if (! mb_check_encoding($contenu, 'UTF-8')) {
            $contenu = mb_convert_encoding($contenu, 'UTF-8', 'Windows-1252');
        }

        $contenu = preg_replace('/^\xEF\xBB\xBF/', '', $contenu);
        $lignes = preg_split('/\r\n|\n|\r/', trim($contenu));

        $separateur = substr_count($lignes[0], ';') >= substr_count($lignes[0], ',') ? ';' : ',';
        $entete = array_map(fn ($c) => strtolower(trim($c)), str_getcsv($lignes[0], $separateur));

        $manquantes = array_diff(self::COLONNES, $entete);
        if ($manquantes) {
            return [
                'statut' => 'refuse',
                'message' => 'Colonnes manquantes : '.implode(', ', $manquantes),
                'crees' => 0,
                'mis_a_jour' => 0,
                'erreurs' => [],
            ];
        }

        $valides = [];
        $erreurs = [];

        foreach (array_slice($lignes, 1) as $i => $texte) {
            $numero = $i + 2;

            if (trim($texte) === '') {
                continue;
            }

            $valeurs = str_getcsv($texte, $separateur);
            if (count($valeurs) !== count($entete)) {
                $erreurs[] = ['ligne' => $numero, 'message' => 'Nombre de colonnes incorrect.'];
                continue;
            }

            $produit = array_map('trim', array_combine($entete, $valeurs));
            $produit['prix_ht'] = str_replace(',', '.', $produit['prix_ht']);
            $produit['saison'] = strtolower($produit['saison']);

            $validation = Validator::make($produit, [
                'reference' => ['required', 'string', 'max:50'],
                'marque' => ['required', 'string', 'max:50'],
                'modele' => ['required', 'string', 'max:100'],
                'dimension' => ['required', 'regex:/^\d{3}\/\d{2} R\d{2}$/'],
                'saison' => ['required', 'in:ete,hiver,4saisons'],
                'prix_ht' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
            ]);

            if ($validation->fails()) {
                $erreurs[] = ['ligne' => $numero, 'message' => implode(' ', $validation->errors()->all())];
                continue;
            }

            $valides[] = $validation->validated();
        }

        $crees = 0;
        $misAJour = 0;

        DB::transaction(function () use ($valides, &$crees, &$misAJour) {
            foreach ($valides as $donnees) {
                $produit = Produit::updateOrCreate(['reference' => $donnees['reference']], $donnees);
                $produit->wasRecentlyCreated ? $crees++ : $misAJour++;
            }
        });

        return [
            'statut' => $erreurs ? 'partiel' : 'ok',
            'message' => count($valides).' ligne(s) importée(s), '.count($erreurs).' ligne(s) en erreur.',
            'crees' => $crees,
            'mis_a_jour' => $misAJour,
            'erreurs' => $erreurs,
        ];
    }
}
