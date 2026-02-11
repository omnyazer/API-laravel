<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

use OpenApi\Attributes as OA;




class BookController extends Controller
{

    #[OA\Get(
        path: "/api/books",
        summary: "Liste des livres",
        responses: [
            new OA\Response(response: 200, description: "Succès")
        ]
    )]

    public function index()
    {
        $books = Book::paginate(2);
        return BookResource::collection($books);
    }

    #[OA\Post(
        path: "/api/books",
        summary: "Créer un livre",
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                example: [
                    "title" => "Livre test",
                    "author" => "Auteur test",
                    "summary" => "Résumé test",
                    "isbn" => "1234567890123"
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Créé"),
            new OA\Response(response: 422, description: "Erreur validation")
        ]
    )]

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'author' => ['required', 'string', 'min:3', 'max:100'],
            'summary' => ['required', 'string', 'min:10', 'max:500'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
        ]);

        $book = Book::create($validated);

        return new BookResource($book);
    }
    #[OA\Get(
        path: "/api/books/{id}",
        summary: "Afficher un livre",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Succès"),
            new OA\Response(response: 404, description: "Introuvable")
        ]
    )]

    public function show(Book $book)
    {
        $cacheKey = 'book_' . $book->id;

        $book = Cache::remember($cacheKey, 60 * 60, function () use ($book) {
            return $book;
        });

        return new BookResource($book);
    }

    #[OA\Put(
        path: "/api/books/{id}",
        summary: "Modifier un livre",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Modifié"),
            new OA\Response(response: 422, description: "Erreur validation")
        ]
    )]

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'author' => ['required', 'string', 'min:3', 'max:100'],
            'summary' => ['required', 'string', 'min:10', 'max:500'],
            'isbn' => [
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($book->id),
            ],
        ]);

        $book->update($validated);

        return new BookResource($book);
    }

    #[OA\Delete(
        path: "/api/books/{id}",
        summary: "Supprimer un livre",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 204, description: "Supprimé")
        ]
    )]

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }

}
