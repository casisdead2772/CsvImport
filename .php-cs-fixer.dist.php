<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
    'blank_line_before_statement' => [ 'statements' => [
        'break',
        'continue',
        'declare',
        'return',
        'throw',
        'try',
        'if',
        'foreach'
    ]]
])
    ->setFinder($finder)
    ;