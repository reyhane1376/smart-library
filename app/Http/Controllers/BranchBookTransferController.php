<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\BookCopyTransfer;
use App\Models\Borrowing;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchBookTransferController extends Controller
{
    public function transferBetweenBranches(Request $request)
    {
        $request->validate([
            'book_copy_id'    => 'required|exists:book_copies,id',
            'from_branch_id'  => 'required|exists:branches,id',
            'to_branch_id'    => 'required|exists:branches,id',
            'transfer_reason' => 'nullable|string'
        ]);


        $borrowing = Borrowing::where('book_copy_id', $request->book_copy_id)
        ->whereNull('returned_at')->first();


        if ($borrowing)
        {
            throw new \Exception('کتاب مورد نظر امانت است.');
        }

        return DB::transaction(function () use ($request) {
            $bookCopy = BookCopy::findOrFail($request->book_copy_id);
            $fromBranch = Branch::findOrFail($request->from_branch_id);
            $toBranch = Branch::findOrFail($request->to_branch_id);

            if ($bookCopy->branch_id != $fromBranch->id)
            {
                throw new \Exception('کتاب در شعبه مورد نظر وجود ندارد.');
            }

            $transfer = BookCopyTransfer::create([
                'book_copy_id'    => $bookCopy->id,
                'from_branch_id'  => $fromBranch->id,
                'to_branch_id'    => $toBranch->id,
                'status'          => BookCopyTransfer::STATUS_REQUESTED,
                'transfer_reason' => $request->transfer_reason
            ]);

            $bookCopy->update([
                'status'    => BookCopy::STATUS_IN_TRANSFER,
                'branch_id' => $toBranch->id
            ]);

            return response()->json([
                'transfer' => $transfer,
                'message' => 'کتاب با موفقیت جابه جا شد.'
            ]);
        });
    }

    
    public function confirmTransfer(Request $request, BookCopyTransfer $transfer)
    {
        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'book_copy_id' => 'required|exists:book_copies,id',
        ]);

        $borrowing = Borrowing::where('book_copy_id', $request->book_copy_id)
        ->whereNull('returned_at')->first();


        if ($borrowing)
        {
            throw new \Exception('کتاب مورد نظر امانت است.');
        }

        return DB::transaction(function () use ($transfer) {
            $transfer->update([
                'status' => BookCopyTransfer::STATUS_COMPLETED,
                'completed_at' => now()
            ]);

            $transfer->bookCopy->update([
                'status' => BookCopy::STATUS_AVAILABLE,
                'branch_id' => $transfer->to_branch_id
            ]);

            return response()->json([
                'transfer' => $transfer,
                'message' => 'Book copy transfer completed'
            ]);
        });
    }
}
