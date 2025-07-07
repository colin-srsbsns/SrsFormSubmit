<?php

use App\Kernel;

$projectDir = realpath(__DIR__.'/..');   // ↔ /Users/Colin/project on your Mac
// ↔ /usr/home/xxxxx on Xneelo
echo $projectDir;
require_once $projectDir.'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
