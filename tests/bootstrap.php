<?php

passthru(sprintf(
    'php bin/console doctrine:schema:drop --force --full-database'
));

passthru(sprintf(
    'php bin/console doctrine:schema:update -f'
));

require __DIR__.'/../vendor/autoload.php';