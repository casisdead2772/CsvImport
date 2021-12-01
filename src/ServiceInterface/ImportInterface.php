<?php

namespace App\ServiceInterface;

interface ImportInterface {
    public function importByRules($fileName, bool $isTest);
    public function getCsvRowsAsArrays($inputFile);
    // rules must be true
    public function getFileRules($productArray): bool;
    public function getProductValid($product): bool;
    public function getProductRules($product): bool;
}
