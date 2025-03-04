<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Interfaces\ReservationServiceInterface;
use App\Models\BookCopy;
use App\Models\Reservation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use AuthorizesRequests;
    protected $reservationService;
    
    public function __construct(ReservationServiceInterface $reservationService)
    {
        $this->reservationService = $reservationService;
    }
    
    public function index(Request $request)
    {
        $user = $request->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['bookCopy.book'])
            ->paginate(15);
            
        return ReservationResource::collection($reservations);
    }
    
    public function store(ReservationRequest $request)
    {
        $user = $request->user();

        $copy = BookCopy::findOrFail($request->book_copy_id);
        
        try {
            $reservation = $this->reservationService->reserveBook($user, $copy);
            
            return new ReservationResource($reservation);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);
        
        try {
            $cancelledReservation = $this->reservationService->cancelReservation($reservation);
            
            return new ReservationResource($cancelledReservation);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
    public function queue(BookCopy $copy)
    {
        $queue = $this->reservationService->getReservationQueue($copy);
        
        return ReservationResource::collection($queue);
    }
}
