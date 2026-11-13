# Exercices

À faire dans l'ordre, chacun prend entre 15 minutes et 1 heure. Après chaque exercice côté API,
lancer `php artisan test`, et si possible ajouter un test dans `backend/tests/Feature/ApiTest.php`.

## Niveau 1 : lire et modifier

1. **Filtre par dimension.** Ajouter `?dimension=205/55 R16` sur `GET /produits`.
   Indice : `ProduitController@index`, même principe que `marque`.
2. **Recherche libre.** Ajouter `?q=primacy` qui cherche dans la marque, le modèle et la référence.
   Indice : `where(fn ($q) => $q->where('modele', 'like', "%$texte%")->orWhere(...))`.
3. **Prix TTC.** Ajouter un champ `prix_ttc` (TVA 20 %) dans `ProduitResource`, puis l'afficher dans `produits.vue`.

## Niveau 2 : nouvelles routes

4. **Annuler une commande.** Route `POST /commandes/{commande}/annuler` :
   - 403 si la commande n'appartient pas au client ;
   - 422 si le statut n'est pas `en_attente` ;
   - remettre les pneus en stock, passer le statut à `annulee`, le tout dans une transaction.
5. **Page « Mes commandes ».** Créer `frontend/app/pages/commandes.vue` qui appelle `GET /commandes`
   et affiche chaque commande avec ses lignes. Ajouter le lien dans le menu de `app.vue`.
6. **Policy.** Remplacer le `abort_if` de `CommandeController@show` par une Policy Laravel
   (`php artisan make:policy CommandePolicy --model=Commande`), et l'utiliser aussi pour l'annulation.

## Niveau 3 : comme en entreprise

7. **Réservé aux admins.** Ajouter une colonne `est_admin` aux clients (nouvelle migration),
   et limiter `/imports/produits` aux admins (403 sinon).
8. **Import XML.** Accepter aussi un fichier `.xml` :
   ```xml
   <produits>
     <produit><reference>MI-X</reference><marque>Michelin</marque>...</produit>
   </produits>
   ```
   Indice : `simplexml_load_string()`, puis réutiliser la même validation que pour le CSV
   (sortir la validation dans une méthode commune).
9. **Import en tâche de fond.** Pour les gros fichiers : enregistrer le fichier, lancer un Job
   (`php artisan make:job ImporterProduits`), répondre tout de suite `202 Accepted`,
   et stocker le rapport en base pour le consulter ensuite. Lancer le worker avec `php artisan queue:work`.
10. **Historique des imports.** Table `imports` (client, nom du fichier, date, nombre de lignes, erreurs)
    et route `GET /imports` pour les consulter. Utile pour le support : « votre fichier de mardi avait 3 erreurs ».

## Bonus

- Remplacer le parsing CSV fait main par la librairie `league/csv` (`composer require league/csv`).
- Documenter l'API avec OpenAPI / Swagger (par exemple avec le package `dedoc/scramble`).
- Ajouter une route `GET /produits/{produit}/disponibilite` qui renvoie juste le stock, très demandée par les clients B2B.
