<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(['align_double_arrow', 'concat_with_spaces', '-concat_without_spaces', 'short_array_syntax'])

;
