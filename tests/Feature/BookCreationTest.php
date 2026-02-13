<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_book_is_created_with_valid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/books', [
            'title' => 'Test Book',
            'author' => 'Auteur Test',
            'summary' => 'Le résumé du livre',
            'isbn' => '9789999999999',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
        ]);
    }

    public function test_book_is_not_created_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/books', [
            'title' => 'ab',
            'author' => 'Auteur Test',
            'summary' => 'Le résumé du livre',
            'isbn' => '9788888888888',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('books', [
            'isbn' => '9788888888888',
        ]);
    }

    public function test_book_is_not_created_when_user_not_authenticated(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => 'Test Book',
            'author' => 'Auteur Test',
            'summary' => 'Le résumé du livre',
            'isbn' => '9787777777777',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('books', [
            'isbn' => '9787777777777',
        ]);
    }
}
