<?php

namespace Database\Seeders;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Garage Dupont',
            'email' => 'garage@exemple.fr',
            'password' => 'motdepasse',
        ]);

        User::create([
            'name' => 'Pneus Service Metz',
            'email' => 'metz@exemple.fr',
            'password' => 'motdepasse',
        ]);

        $produits = [
            ['MI-PR4-20555', 'Michelin', 'Primacy 4+', '205/55 R16', 'ete', 98.50, 40],
            ['MI-AL6-20555', 'Michelin', 'Alpin 6', '205/55 R16', 'hiver', 112.00, 25],
            ['MI-CC2-22545', 'Michelin', 'CrossClimate 2', '225/45 R17', '4saisons', 139.90, 18],
            ['CO-PC6-22545', 'Continental', 'PremiumContact 6', '225/45 R17', 'ete', 121.30, 32],
            ['CO-WC8-19565', 'Continental', 'WinterContact TS 870', '195/65 R15', 'hiver', 84.70, 12],
            ['BR-T005-20555', 'Bridgestone', 'Turanza T005', '205/55 R16', 'ete', 89.90, 50],
            ['BR-BL6-22545', 'Bridgestone', 'Blizzak LM005', '225/45 R17', 'hiver', 128.40, 8],
            ['PI-CI-19565', 'Pirelli', 'Cinturato P7', '195/65 R15', 'ete', 76.20, 60],
            ['PI-SZ-22545', 'Pirelli', 'Scorpion Winter 2', '225/45 R17', 'hiver', 145.00, 0],
            ['GY-VE3-20555', 'Goodyear', 'Vector 4Seasons Gen-3', '205/55 R16', '4saisons', 104.60, 22],
        ];

        foreach ($produits as [$reference, $marque, $modele, $dimension, $saison, $prix, $stock]) {
            Produit::create([
                'reference' => $reference,
                'marque' => $marque,
                'modele' => $modele,
                'dimension' => $dimension,
                'saison' => $saison,
                'prix_ht' => $prix,
                'stock' => $stock,
            ]);
        }
    }
}
