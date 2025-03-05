<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\CopyRepairHistory;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCopyReplacementController extends Controller
{
    public function replacedamages(Request $request)
    {
        $request->validate([
            'damaged_copy_id'    => 'required|exists:book_copies,id',
            'damage_description' => 'nullable|string'
        ]);

        $borrowing = Borrowing::where('book_copy_id', $request->damaged_copy_id)
        ->whereNull('returned_at')->first();


        if ($borrowing)
        {
            throw new \Exception('کتاب مورد نظر امانت است.');
        }

        return DB::transaction(function () use ($request) {
            $damagedCopy = BookCopy::findOrFail($request->damaged_copy_id);

            $newCopy = BookCopy::create([
                'book_id'            => $damagedCopy->book_id,
                'physical_condition' => BookCopy::CONDITION_EXCELLENT,
                'status'             => BookCopy::STATUS_AVAILABLE,
                'is_special'         => $damagedCopy->is_special,
                'version'            => $damagedCopy->version,
                'location'           => $damagedCopy->location
            ]);

            $damagedCopy->update([
                'status'             => BookCopy::STATUS_DAMAGE,
                'physical_condition' => BookCopy::CONDITION_NEEDS_REPAIR
            ]);

            return response()->json([
                'damaged_copy' => $damagedCopy,
                'new_copy' => $newCopy,
                'message' => 'جایگزینی با موفقیت انجام شد.'
            ]);
        });
    }



}
