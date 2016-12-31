<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

$header = <<<EOF
This file is part of the Active Collab DatabaseStructure project.

(c) A51 doo <info@activecollab.com>. All rights reserved.
EOF;

//PhpCsFixer\Fixer\Comment\HeaderCommentFixer::setHeader($header);

return (new PhpCsFixer\Config('psr2'))->setRules([
    'header_comment' => [
        'header' => $header,
    ],
    'no_whitespace_before_comma_in_array',
    'whitespace_after_comma_in_array',
    'no_multiline_whitespace_around_double_arrow',
    'hash_to_slash_comment',
    'include',
    'no_alias_functions',
    'trailing_comma_in_multiline_array',
    'no_leading_namespace_whitespace',
    'no_blank_lines_after_class_opening ',
    'no_blank_lines_after_phpdoc',
    'phpdoc_scalar',
    'phpdoc_summary',
    'self_accessor',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'space_after_semicolon',
    'no_singleline_whitespace_before_semicolons',
    'cast_spaces',
    'standardize_not_equals',
    'ternary_operator_spaces',
    'trim_array_spaces',
    'no_unused_imports',
    'no_whitespace_in_blank_line',
    'ordered_imports',
    'array_syntax',
    'phpdoc_align',
    '-phpdoc_separation',
    '-phpdoc_no_package',
    '-no_mixed_echo_print',
    '-concat_space',
    '-simplified_null_return',
])->setFinder((new PhpCsFixer\Finder())->in([__DIR__ . '/src', __DIR__ . '/test']));
