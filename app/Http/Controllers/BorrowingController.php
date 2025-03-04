<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Interfaces\BorrowingServiceInterface;
use App\Models\BookCopy;
use App\Models\Borrowing;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    use AuthorizesRequests;
    protected $borrowingService;
    
    public function __construct(BorrowingServiceInterface $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }
    
    public function index(Request $request)
    {
        $user = $request->user();
        $borrowings = Borrowing::where('user_id', $user->id)
            ->with(['bookCopy.book'])
            ->paginate(15);
            
        return BorrowingResource::collection($borrowings);
    }
    
    public function store(BorrowingRequest $request)
    {
        $user = $request->user();
        $copy = BookCopy::findOrFail($request->book_copy_id);
        
        try {
            $borrowing = $this->borrowingService->borrowBook($user, $copy);
            
            return new BorrowingResource($borrowing);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
    public function return(Request $request, Borrowing $borrowing)
    {
        $this->authorize('return', $borrowing);
        
        $condition = $request->input('condition', Borrowing::GOOD);
        
        try {
            $returnedBorrowing = $this->borrowingService->returnBook($borrowing, $condition);
            
            return new BorrowingResource($returnedBorrowing);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
    public function extend(Request $request, Borrowing $borrowing)
    {
        $this->authorize('extend', $borrowing);
        
        $days = $request->input('days', 7);
        
        try {
            $extendedBorrowing = $this->borrowingService->extendBorrowingPeriod($borrowing, $days);
            
            return new BorrowingResource($extendedBorrowing);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
