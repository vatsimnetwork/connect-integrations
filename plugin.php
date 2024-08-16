<?php

use Vatsim\Osticket\Auth\Plugin;

return [
    'id' => 'vatsim:oauth2',
    'version' => '1.2',
    'ost_version' => '1.16',
    'name' => 'VATSIM Authentication',
    'author' => 'VATSIM Tech Team <tech@vatsim.net>',
    'description' => 'Authenticates users via VATSIM Connect',
    'plugin' => 'bootstrap.php:'.Plugin::class,
];
