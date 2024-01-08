<?php
declare(strict_types=1);
use Horde\Test\Bootstrap;

$candidates = [
    dirname(__FILE__, 3) . '/vendor/autoload.php',
    dirname(__FILE__, 5) . '/autoload.php',
];
// Cover root case and library case
foreach ($candidates as $candidate) {
    if (file_exists($candidate)) {
        require_once $candidate;
        break;
    }
}
if (class_exists(Bootstrap::class)) {
    Bootstrap::bootstrap(dirname(__FILE__));
}
