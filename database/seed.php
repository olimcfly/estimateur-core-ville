#!/usr/bin/env php
<?php

declare(strict_types=1);

$city = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--city=')) {
        $city = strtolower(trim(substr($arg, 7)));
    }
}

if ($city !== null && $city !== '') {
    $_ENV['SITE_CITY_SLUG'] = $city;
    $_SERVER['SITE_CITY_SLUG'] = $city;
}

require __DIR__ . '/seed-articles.php';
