<?php

use App\Kernel;

$projectDir = dirname(__DIR__);   // ↔ /Users/Colin/project on your Mac
// ↔ /usr/home/xxxxx on Xneelo
//echo $projectDir;
$isXneelo = str_starts_with($projectDir, '/usr/www/');

if ($isXneelo) {
    require_once '/usr/home/formsdpmfc/vendor/autoload_runtime.php';// server-specific logging, ini_set, etc.
}
else{
    require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
}


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
