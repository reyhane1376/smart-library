<?php

namespace App\Interfaces;

interface BookRepositoryInterface
{

    public function getAllBooks();
    public function getBookById($id);
    public function createBook(array $bookData);
    public function updateBook($id, array $bookData);
    public function deleteBook($id);
    public function getAvailableCopies($bookId);
}