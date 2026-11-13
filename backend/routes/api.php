<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\ProduitController;
use Illuminate\Support\Facades\Route;

// Toutes les routes commencent par /api/v1. Le "v1" permet de sortir un jour une v2
// sans casser les clients qui utilisent encore la v1.
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Tout ce qui suit demande un token valide, sinon réponse 401.
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/moi', [AuthController::class, 'moi']);

        Route::apiResource('produits', ProduitController::class)->only(['index', 'show']);
        Route::apiResource('commandes', CommandeController::class)->only(['index', 'store', 'show']);

        Route::post('/imports/produits', [ImportController::class, 'produits']);
    });
});
