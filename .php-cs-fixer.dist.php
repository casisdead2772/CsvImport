<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
    ])
    ->setFinder($finder)
;
