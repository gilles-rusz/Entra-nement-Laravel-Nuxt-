<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit>
 */
class ProduitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference' => strtoupper(fake()->unique()->bothify('PN-#####')),
            'marque' => fake()->randomElement(['Michelin', 'Continental', 'Bridgestone', 'Pirelli', 'Goodyear']),
            'modele' => fake()->word(),
            'dimension' => fake()->randomElement(['205/55 R16', '225/45 R17', '195/65 R15']),
            'saison' => fake()->randomElement(['ete', 'hiver', '4saisons']),
            'prix_ht' => fake()->randomFloat(2, 50, 250),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
