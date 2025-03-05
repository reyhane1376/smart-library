<?php

namespace App\Interfaces;

interface BookCopyReplacementRepositoryInterface
{
    public function replaceDamagedCopy(int $damagedCopyId, ?string $damageDescription = null): array;
}
