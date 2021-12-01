<?php

namespace App\ServiceInterface;

interface ImportInterface {
    public function importByRules($fileName, bool $isTest);
    public function getCsvRowsAsArrays($inputFile);
}
