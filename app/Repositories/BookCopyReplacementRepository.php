<?php

namespace App\Repositories;

use App\Interfaces\BookCopyReplacementRepositoryInterface;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\CopyRepairHistory;
use Illuminate\Support\Facades\DB;

class BookCopyReplacementRepository implements BookCopyReplacementRepositoryInterface
{
    public function replaceDamagedCopy(int $damagedCopyId, ?string $damageDescription = null): array
    {
        $borrowing = Borrowing::where('book_copy_id', $damagedCopyId)
            ->whereNull('returned_at')
            ->first();

        if ($borrowing) {
            throw new \Exception('کتاب مورد نظر امانت است.');
        }

        return DB::transaction(function () use ($damagedCopyId, $damageDescription) {
            $damagedCopy = BookCopy::findOrFail($damagedCopyId);

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
            
            CopyRepairHistory::create([
                'book_copy_id'   => $damagedCopy->id,
                'repair_details' => $damageDescription ?? 'نسخه تعویض شده',
                'repair_date'    => now()
            ]);

            return [
                'damaged_copy' => $damagedCopy,
                'new_copy'     => $newCopy
            ];
        });
    }
}
