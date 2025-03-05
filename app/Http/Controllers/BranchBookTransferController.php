<?php

namespace App\Http\Controllers;

use App\Interfaces\BranchBookTransferRepositoryInterface;
use App\Models\BookCopy;
use App\Models\BookCopyTransfer;
use App\Models\Borrowing;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchBookTransferController extends Controller
{
    protected $branchBookTransferRepository;

    public function __construct(BranchBookTransferRepositoryInterface $branchBookTransferRepository)
    {
        $this->branchBookTransferRepository = $branchBookTransferRepository;
    }

    public function transferBetweenBranches(Request $request)
    {
        $validate = $request->validate([
            'book_copy_id'    => 'required|exists:book_copies,id',
            'from_branch_id'  => 'required|exists:branches,id',
            'to_branch_id'    => 'required|exists:branches,id',
            'transfer_reason' => 'nullable|string'
        ]);

        try {
            $transfer = $this->branchBookTransferRepository->transferBetweenBranches($validate);

            return response()->json([
                'transfer' => $transfer,
                'message' => 'کتاب با موفقیت جابه جا شد.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    public function confirmTransfer(Request $request, BookCopyTransfer $transfer)
    {
        $validate = $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'book_copy_id' => 'required|exists:book_copies,id',
        ]);

        try {
            $updatedTransfer = $this->branchBookTransferRepository->confirmTransfer($transfer, $validate);

            return response()->json([
                'transfer' => $updatedTransfer,
                'message' => 'انتقال کتاب با موفقیت تکمیل شد.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
