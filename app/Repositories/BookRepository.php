<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use App\Models\BookCopy;

class BookRepository implements BookRepositoryInterface {

    public function getAllBooks()
    {
        return Book::with('copies')->paginate(15);
    }
    
    public function getBookById($id)
    {
        return Book::with('copies')->findOrFail($id);
    }
    
    public function createBook(array $bookData)
    {
        return Book::create($bookData);
    }
    
    public function updateBook($id, array $bookData)
    {
        $book = Book::findOrFail($id);
        $book->update($bookData);
        
        return $book;
    }
    
    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        
        return true;
    }
    
    public function getAvailableCopies($bookId)
    {
        return BookCopy::where('book_id', $bookId)
            ->where('status', BookCopy::STATUS_AVAILABLE)
            ->get();
    }
}