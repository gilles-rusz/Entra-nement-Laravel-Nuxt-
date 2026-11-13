<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'marque', 'modele', 'dimension', 'saison', 'prix_ht', 'stock'];

    protected function casts(): array
    {
        return [
            'prix_ht' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
}
