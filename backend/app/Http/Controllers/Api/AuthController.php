<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/v1/login : le client envoie email + mot de passe, il reçoit un token.
    // Il enverra ensuite ce token dans chaque requête : "Authorization: Bearer <token>".
    public function login(Request $request): JsonResponse
    {
        $donnees = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $client = User::where('email', $donnees['email'])->first();

        if (! $client || ! Hash::check($donnees['password'], $client->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        $token = $client->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'client' => ['id' => $client->id, 'nom' => $client->name, 'email' => $client->email],
        ]);
    }

    // POST /api/v1/logout : supprime le token utilisé pour cette requête.
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    public function moi(Request $request): JsonResponse
    {
        $client = $request->user();

        return response()->json(['id' => $client->id, 'nom' => $client->name, 'email' => $client->email]);
    }
}
