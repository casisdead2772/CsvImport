<?php

namespace App\ServiceInterface;

interface ImportInterface {
    public function importByRules($itemsArray, bool $isTest);
    public function getCsvRowsAsArrays($inputFile);
}
