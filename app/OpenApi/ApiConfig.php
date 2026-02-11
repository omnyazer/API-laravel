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
            url: "http://localhost:8000/api",
            description: "Serveur local"
        )
    ]
)]
class ApiConfig {}
