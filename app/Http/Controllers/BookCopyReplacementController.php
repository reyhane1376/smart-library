<?php

namespace App\Http\Controllers;

use App\Interfaces\BookCopyReplacementRepositoryInterface;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\CopyRepairHistory;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCopyReplacementController extends Controller
{
    protected $bookCopyReplacementRepository;

    public function __construct(BookCopyReplacementRepositoryInterface $bookCopyReplacementRepository)
    {
        $this->bookCopyReplacementRepository = $bookCopyReplacementRepository;
    }

    public function replacedamages(Request $request)
    {
        $request->validate([
            'damaged_copy_id'    => 'required|exists:book_copies,id',
            'damage_description' => 'nullable|string'
        ]);

        try {
            $result = $this->bookCopyReplacementRepository->replaceDamagedCopy(
                $request->damaged_copy_id, 
                $request->damage_description
            );

            return response()->json([
                'damaged_copy' => $result['damaged_copy'],
                'new_copy'     => $result['new_copy'],
                'message'      => 'جایگزینی با موفقیت انجام شد.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }



}
