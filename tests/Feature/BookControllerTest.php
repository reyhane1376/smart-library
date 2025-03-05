<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $book;
    protected $bookCopy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');

        $this->book = Book::factory()->create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description'
        ]);

        $this->bookCopy = BookCopy::factory()->create([
            'book_id' => $this->book->id,
            'status' => BookCopy::STATUS_AVAILABLE
        ]);
    }

    #[Test]
    public function it_can_list_all_books()
    {
        Cache::flush();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'author',
                             'description'
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function it_can_show_a_single_book()
    {
        Cache::flush();

        $response = $this->getJson("/api/v1/books/{$this->book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $this->book->id,
                         'title' => 'Test Book',
                         'author' => 'Test Author',
                         'description' => 'Test Description'
                     ]
                 ]);
    }

    #[Test]
    public function it_can_create_a_new_book()
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'description' => 'New Description'
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'title' => 'New Book',
                         'author' => 'New Author',
                         'description' => 'New Description'
                     ]
                 ]);

        $this->assertDatabaseHas('books', $bookData);
    }

    #[Test]
    public function it_can_update_a_book()
    {
        $updateData = [
            'title' => 'Updated Book Title',
            'author' => 'Updated Author',
            'description' => 'Updated Description'
        ];

        $response = $this->putJson("/api/v1/books/{$this->book->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $this->book->id,
                         'title' => 'Updated Book Title',
                         'author' => 'Updated Author',
                         'description' => 'Updated Description'
                     ]
                 ]);

        $this->assertDatabaseHas('books', $updateData);
    }

    #[Test]
    public function it_can_delete_a_book()
    {
        $response = $this->deleteJson("/api/v1/books/{$this->book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'کتاب با موفقیت حذف شد.'
                 ]);

        $this->assertDatabaseMissing('books', ['id' => $this->book->id]);
    }

    #[Test]
    public function it_can_get_available_book_copies()
    {
        // Ensure we have available book copies
        BookCopy::factory()->count(3)->create([
            'book_id' => $this->book->id,
            'status' => BookCopy::STATUS_AVAILABLE
        ]);

        $response = $this->getJson("/api/v1/books/{$this->book->id}/available-copies");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'book_id',
                             'status'
                         ]
                     ]
                 ]);

        $this->assertCount(4, $response->json('data')); // 3 new + 1 from setUp
    }

    #[Test]
    public function it_fails_to_create_book_with_invalid_data()
    {
        $invalidBookData = [
            'title' => '', // Empty title should fail validation
            'author' => 'New Author',
            'description' => 'New Description'
        ];

        $response = $this->postJson('/api/v1/books', $invalidBookData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }
}
