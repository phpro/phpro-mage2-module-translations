<?php
/**
 * PHP Coding Standards fixer configuration
 */

$finder = PhpCsFixer\Finder::create()
    ->in('./')
    ->name(['*.php']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setRules([
        '@PSR2' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'phpdoc_summary' => false,
        'blank_line_after_opening_tag' => false,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        // Removes @param and @return tags that don't provide any useful information;
        // Set to false to ensure custom Magento API implementations do not fail. Magento uses reflection based on its docblocks to process request/responses
        'no_superfluous_phpdoc_tags' => false,
        // add declare strict type to every file
        'declare_strict_types' => true,
        // use native phpunit methods
        'php_unit_construct' => true,
        // Enforce camel case for PHPUnit test methods
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'yoda_style' => ['equal' => true, 'identical' => true, 'less_and_greater' => true],
        'php_unit_test_case_static_method_calls' => true,
        // comparisons should always be strict
        'strict_comparison' => true,
        // functions should be used with $strict param set to true
        'strict_param' => true,
        'array_indentation' => true,
        'compact_nullable_typehint' => true,
        'fully_qualified_strict_types' => false,
    ])
    ->setFinder($finder);

