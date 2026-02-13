<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;



class UserController extends Controller
{
   #[OA\Post(
    path: "/api/register",
    summary: "Inscription utilisateur",
    tags: ["Auth"],
    requestBody: new OA\RequestBody(
        content: new OA\JsonContent(
            example: [
                "name" => "User",
                "email" => "user@example.com",
                "password" => "password123"
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Utilisateur créé"),
        new OA\Response(response: 422, description: "Erreur validation")
    ]
)]

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

        #[OA\Post(
        path: "/api/login",
        summary: "Connexion utilisateur",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(
                name: "Accept",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "application/json"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                example: [
                    "email" => "user@example.com",
                    "password" => "password123"
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Connexion réussie"),
            new OA\Response(response: 401, description: "Identifiants invalides")
        ]
    )]

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnexion utilisateur",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "application/json"
                )
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Déconnecté"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
