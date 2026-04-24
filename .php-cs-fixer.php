<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,

        // Declare strict_types on the same line as <?php
        'declare_strict_types' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,

        // Short array syntax
        'array_syntax' => ['syntax' => 'short'],

        // No Yoda style
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],

        // Explicit boolean comparisons (=== true / === false)
        'no_unneeded_control_parentheses' => true,

        // PHPDoc
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_summary' => false,

        // Misc
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline_array' => true,
    ])
    ->setFinder($finder)
;
