<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "Library API",
        version: "1.0.0"
    ),
    servers: [
        new OA\Server(
        url: "http://localhost:8000",
            description: "Serveur local"
        )
    ]
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "token",
    description: "Entrer le token au format : Bearer {token}"
)]

class ApiConfig
{
}
