<?php

namespace App\Service\ImportService;

interface ImportInterface {
    public function importByRules($fileName, bool $isTest);
    public function getCsvRowsAsArrays($inputFile);
    // rules must be true
    public function getFileRules($itemArray): bool;
    public function getItemValid($item): bool;
    public function getItemRules($item): bool;
}
