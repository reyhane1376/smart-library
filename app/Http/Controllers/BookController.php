<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    protected $bookRepository;
    
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    
    public function index()
    {
        $books = Cache::remember('books.all', now()->addMinutes(10), function () {
            return $this->bookRepository->getAllBooks();
        });
        
        return BookResource::collection($books);
    }
    
    public function show($id)
    {
        $book = Cache::remember("books.{$id}", now()->addMinutes(10), function () use ($id) {
            return $this->bookRepository->getBookById($id);
        });
        
        return new BookResource($book);
    }
    
    public function store(BookRequest $request)
    {
        $book = $this->bookRepository->createBook($request->validated());
        Cache::tags(['books'])->flush();
        
        return new BookResource($book);
    }
    
    public function update(BookRequest $request, $id)
    {
        $book = $this->bookRepository->updateBook($id, $request->validated());
        Cache::forget("books.{$id}");
        Cache::tags(['books'])->flush();
        
        return new BookResource($book);
    }
    
    public function destroy($id)
    {
        $this->bookRepository->deleteBook($id);
        Cache::forget("books.{$id}");
        Cache::tags(['books'])->flush();
        
        return response()->json(['message' => 'کتاب با موفقیت حذف شد.']);
    }
    
    public function availableCopies($bookId)
    {
        $copies = $this->bookRepository->getAvailableCopies($bookId);
        
        return response()->json(['data' => $copies]);
    }
}
