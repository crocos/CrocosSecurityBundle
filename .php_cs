<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'align_double_arrow',
        'no_blank_lines_before_namespace',
        'ordered_use',
        'short_array_syntax',
    ])

    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude('tests/Fixtures')
            ->in(__DIR__)
    )
;
