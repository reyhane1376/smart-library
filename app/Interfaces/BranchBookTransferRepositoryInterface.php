<?php

namespace App\Interfaces;

use App\Models\BookCopyTransfer;

interface BranchBookTransferRepositoryInterface
{
    public function transferBetweenBranches(array $data): BookCopyTransfer;
    public function confirmTransfer(BookCopyTransfer $transfer, array $data): BookCopyTransfer;
}
