# Entraînement Laravel + Nuxt : API B2B de pneus

Petit projet pour se remettre à PHP, Laravel et Vue/Nuxt, sur un cas proche d'un vrai site B2B :
un catalogue de pneus, des commandes clients et un import de fichiers CSV comme ceux reçus par FTP.

- `backend/` : API REST en **Laravel 12** (PHP 8.2 ou plus), base SQLite, tokens **Sanctum**
- `frontend/` : interface en **Nuxt 4 / Vue 3** qui appelle l'API
- `exemples/` : fichiers CSV pour tester l'import (un propre, un plein d'erreurs)
- `EXERCICES.md` : les exercices à coder soi-même

## Installation

Il faut PHP 8.2+, Composer et Node.js 20+. Sur Windows, le plus simple est
[Laravel Herd](https://herd.laravel.com/windows) (installe PHP et Composer) et [Node.js](https://nodejs.org).

```bash
# 1. L'API
cd backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # sous Windows : créer le fichier vide à la main
php artisan migrate --seed
php artisan serve                      # http://localhost:8000

# 2. Le front (dans un deuxième terminal)
cd frontend
npm install
npm run dev                            # http://localhost:3000
```

Compte de démo : `garage@exemple.fr` / `motdepasse` (et un deuxième client : `metz@exemple.fr`).

Lancer les tests de l'API : `cd backend && php artisan test`

## Les routes de l'API

Toutes les routes commencent par `/api/v1`. Sauf `/login`, elles demandent l'en-tête
`Authorization: Bearer <token>`, sinon la réponse est `401`.

| Méthode | Route | Rôle | Codes possibles |
|---|---|---|---|
| POST | `/login` | Reçoit email + mot de passe, renvoie un token | 200, 401, 422, 429 |
| POST | `/logout` | Supprime le token utilisé | 200 |
| GET | `/moi` | Infos du client connecté | 200 |
| GET | `/produits?marque=&saison=` | Catalogue filtré et paginé | 200, 422 |
| GET | `/produits/{id}` | Un produit | 200, 404 |
| GET | `/commandes` | Les commandes du client connecté | 200 |
| POST | `/commandes` | Crée une commande et décrémente le stock | 201, 422 |
| GET | `/commandes/{id}` | Une commande (seulement la sienne) | 200, 403, 404 |
| POST | `/imports/produits` | Import d'un CSV (champ `fichier`) | 200, 422 |

Essayer avec curl :

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"garage@exemple.fr","password":"motdepasse"}'

curl http://localhost:8000/api/v1/produits?saison=hiver \
  -H "Accept: application/json" -H "Authorization: Bearer LE_TOKEN"

curl -X POST http://localhost:8000/api/v1/imports/produits \
  -H "Accept: application/json" -H "Authorization: Bearer LE_TOKEN" \
  -F fichier=@exemples/produits-avec-erreurs.csv
```

Ou avec [Bruno](https://www.usebruno.com/) ou Postman, plus confortable.

## Le chemin d'une requête dans Laravel

Exemple : `POST /api/v1/commandes`

1. **`routes/api.php`** : Laravel trouve la route et applique les middlewares
   (`throttle:60,1` limite à 60 requêtes par minute, `auth:sanctum` vérifie le token).
2. **`app/Http/Controllers/Api/CommandeController.php`**, méthode `store` :
   - `$request->validate(...)` vérifie les données. Si elles sont fausses, Laravel répond tout seul `422` avec les erreurs.
   - `DB::transaction(...)` : la commande, ses lignes et la baisse du stock sont enregistrées ensemble, ou pas du tout.
3. **`app/Models/`** : les modèles Eloquent (`Produit`, `Commande`, `LigneCommande`, `User`) font le lien avec les tables.
4. **`app/Http/Resources/`** : on choisit les champs renvoyés en JSON.
5. Réponse `201 Created` avec la commande.

Les tables sont décrites dans `database/migrations/`, les données de démo dans `database/seeders/DatabaseSeeder.php`.

## L'import CSV

`app/Services/ImportProduitsCsv.php` reprend les pièges classiques des fichiers clients :

- encodage Windows-1252 (accents cassés) converti en UTF-8 ;
- BOM ajouté par Excel supprimé ;
- séparateur `;` ou `,` détecté automatiquement ;
- prix au format français `89,90` ;
- chaque ligne est validée : les bonnes sont importées (créées ou mises à jour par référence),
  les mauvaises sont listées avec leur numéro de ligne, au lieu de tout bloquer.

Envoyer `exemples/produits-avec-erreurs.csv` depuis la page Import pour voir le rapport.

## Côté Nuxt

- `frontend/app/composables/useApi.ts` : toutes les requêtes passent par là, le token est ajouté automatiquement.
- `frontend/nuxt.config.ts` : les appels `/api/...` sont transmis par Nuxt à Laravel (port 8000), il faut donc lancer les deux serveurs.
- `frontend/app/pages/` : une page = une route (`index.vue` = connexion, `produits.vue`, `import.vue`).
- `ref()`, `reactive()`, `computed()`, `watch()` : la réactivité de Vue 3, l'équivalent de `useState`, `useMemo` et `useEffect` en React.
