<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@PHP8x2Migration:risky' => true,
        '@PHP8x4Migration' => true,
        '@PHPUnit10x0Migration:risky' => true,

        // PHPUnit
        'php_unit_attributes' => true,

        // Declare strict_types on the same line as <?php
        'declare_strict_types' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'fully_qualified_strict_types' => ['import_symbols' => true],
        'blank_line_before_statement' => ['statements' => ['case', 'continue', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']],

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
        'no_superfluous_phpdoc_tags' => false,

        // Misc
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'nullable_type_declaration_for_default_null_value' => true,
        'new_expression_parentheses' => ['use_parentheses' => true],
    ])
    ->setFinder($finder)
;
