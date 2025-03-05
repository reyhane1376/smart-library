<?php

namespace App\Repositories;

use App\Interfaces\BranchBookTransferRepositoryInterface;
use App\Models\BookCopy;
use App\Models\BookCopyTransfer;
use App\Models\Borrowing;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class BranchBookTransferRepository implements BranchBookTransferRepositoryInterface
{
    public function transferBetweenBranches(array $data): BookCopyTransfer
    {
        $borrowing = Borrowing::where('book_copy_id', $data['book_copy_id'])
            ->whereNull('returned_at')
            ->first();

        if ($borrowing) {
            throw new \Exception('کتاب مورد نظر امانت است.');
        }

        return DB::transaction(function () use ($data) {
            $bookCopy = BookCopy::findOrFail($data['book_copy_id']);
            $fromBranch = Branch::findOrFail($data['from_branch_id']);
            $toBranch = Branch::findOrFail($data['to_branch_id']);

            if ($bookCopy->branch_id != $fromBranch->id) {
                throw new \Exception('کتاب در شعبه مورد نظر وجود ندارد.');
            }

            $transfer = BookCopyTransfer::create([
                'book_copy_id'    => $bookCopy->id,
                'from_branch_id'  => $fromBranch->id,
                'to_branch_id'    => $toBranch->id,
                'status'          => BookCopyTransfer::STATUS_REQUESTED,
                'transfer_reason' => $data['transfer_reason'] ?? null
            ]);

            $bookCopy->update([
                'status'    => BookCopy::STATUS_IN_TRANSFER,
                'branch_id' => $toBranch->id
            ]);

            return $transfer;
        });
    }

    public function confirmTransfer(BookCopyTransfer $transfer, array $data): BookCopyTransfer
    {
        $borrowing = Borrowing::where('book_copy_id', $data['book_copy_id'])
            ->whereNull('returned_at')
            ->first();

        if ($borrowing) {
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

            return $transfer;
        });
    }
}
