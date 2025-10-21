<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    ItemController,
    TagController,
    FavoriteController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ces routes sont chargées par le RouteServiceProvider et appartiennent
| au groupe middleware "api". Ici on expose les endpoints publics et privés.
|
*/

// --- Authentification ---
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// --- Endpoints publics ---
Route::get('items/search', [ItemController::class, 'search']); // alias pratique
Route::apiResource('items', ItemController::class)->only(['index','show']);
Route::get('tags', [TagController::class, 'index']);

// --- Routes protégées (auth Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // CRUD complet sur items (ajout, modif, suppression)
    Route::apiResource('items', ItemController::class)->only(['store','update','destroy']);

    // Création de tags
    Route::post('tags', [TagController::class, 'store']);

    // Favoris utilisateur
    Route::get('me/favorites', [FavoriteController::class, 'index']);
    Route::post('items/{item}/favorite', [FavoriteController::class, 'store']);
    Route::delete('items/{item}/favorite', [FavoriteController::class, 'destroy']);

    // Déconnexion
    Route::post('logout', [AuthController::class, 'logout']);
});
