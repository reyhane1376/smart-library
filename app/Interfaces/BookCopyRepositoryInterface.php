<?php

namespace App\Interfaces;

interface BookCopyRepositoryInterface
{
    public function getCopyById($id);
    public function createCopy(array $copyData);
    public function updateCopy($id, array $copyData);
    public function deleteCopy($id);
    public function getAvailableCopies();
    public function updateCopyStatus($id, $status);
    public function addRepairHistory($copyId, array $repairData);
    public function replaceCopy($oldCopyId, $newCopyId);
}