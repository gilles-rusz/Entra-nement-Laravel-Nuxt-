<?php

namespace Tests\Feature;

use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_renvoie_un_token(): void
    {
        User::factory()->create(['email' => 'garage@exemple.fr', 'password' => 'motdepasse']);

        $this->postJson('/api/v1/login', ['email' => 'garage@exemple.fr', 'password' => 'motdepasse'])
            ->assertOk()
            ->assertJsonStructure(['token', 'client' => ['id', 'nom', 'email']]);
    }

    public function test_login_refuse_un_mauvais_mot_de_passe(): void
    {
        User::factory()->create(['email' => 'garage@exemple.fr', 'password' => 'motdepasse']);

        $this->postJson('/api/v1/login', ['email' => 'garage@exemple.fr', 'password' => 'faux'])
            ->assertStatus(401);
    }

    public function test_les_produits_demandent_un_token(): void
    {
        $this->getJson('/api/v1/produits')->assertStatus(401);
    }

    public function test_liste_des_produits_filtree_par_saison(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Produit::factory()->create(['saison' => 'hiver']);
        Produit::factory()->create(['saison' => 'ete']);

        $this->getJson('/api/v1/produits?saison=hiver')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.saison', 'hiver');
    }

    public function test_une_commande_decremente_le_stock(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $produit = Produit::factory()->create(['stock' => 10, 'prix_ht' => 100]);

        $this->postJson('/api/v1/commandes', ['lignes' => [['produit_id' => $produit->id, 'quantite' => 4]]])
            ->assertCreated()
            ->assertJsonPath('data.total_ht', 400);

        $this->assertSame(6, $produit->fresh()->stock);
    }

    public function test_une_commande_est_refusee_si_stock_insuffisant(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $produit = Produit::factory()->create(['stock' => 2]);

        $this->postJson('/api/v1/commandes', ['lignes' => [['produit_id' => $produit->id, 'quantite' => 4]]])
            ->assertStatus(422)
            ->assertJsonValidationErrors('lignes.0.quantite');

        $this->assertSame(2, $produit->fresh()->stock);
        $this->assertSame(0, Commande::count());
    }

    public function test_un_client_ne_voit_pas_la_commande_d_un_autre(): void
    {
        $autre = User::factory()->create();
        $commande = $autre->commandes()->create(['statut' => 'en_attente']);

        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/v1/commandes/{$commande->id}")->assertStatus(403);
    }

    public function test_import_csv_importe_les_bonnes_lignes_et_liste_les_erreurs(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $csv = "reference;marque;modele;dimension;saison;prix_ht;stock\n"
            ."MI-TEST-1;Michelin;Primacy 4+;205/55 R16;ete;98,50;40\n"
            ."MI-TEST-2;Michelin;Alpin 6;205/55R16;hiver;112,00;25\n"
            ."MI-TEST-3;Michelin;CrossClimate 2;225/45 R17;printemps;139,90;18\n";

        $fichier = UploadedFile::fake()->createWithContent('produits.csv', $csv);

        $this->postJson('/api/v1/imports/produits', ['fichier' => $fichier])
            ->assertOk()
            ->assertJsonPath('statut', 'partiel')
            ->assertJsonPath('crees', 1)
            ->assertJsonCount(2, 'erreurs')
            ->assertJsonPath('erreurs.0.ligne', 3);

        $this->assertDatabaseHas('produits', ['reference' => 'MI-TEST-1', 'prix_ht' => 98.50]);
    }

    public function test_import_csv_refuse_si_colonnes_manquantes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $fichier = UploadedFile::fake()->createWithContent('produits.csv', "reference;marque\nX;Y\n");

        $this->postJson('/api/v1/imports/produits', ['fichier' => $fichier])
            ->assertStatus(422)
            ->assertJsonPath('statut', 'refuse');
    }
}
