<?php

use SbWereWolf\Scripting\Config\EnvReader;
use SbWereWolf\Scripting\FileSystem\Path;


$benchStartAt = time();
$message = "Benchmark start at {$benchStartAt}";
echo $message . PHP_EOL;

$pathParts = [__DIR__, 'vendor', 'autoload.php'];
$autoloaderPath = join(DIRECTORY_SEPARATOR, $pathParts);
require_once($autoloaderPath);

$pathComposer = new Path(__DIR__);
$metricsPath = $pathComposer->make(
    [
        'metrics',
        pathinfo(__FILE__, PATHINFO_FILENAME) . '-' . $benchStartAt . '.csv',
    ]
);
$metricsDescriptor = fopen($metricsPath, "w");

$configPath = $pathComposer->make(['pgsql-config.env']);
(new EnvReader($configPath))->defineConstants();

$scriptPath = $pathComposer->make(['data-import-pgsql.php']);

for ($i = 0; $i < (int)constant('SCRIPT_RUN_NUMBERS'); $i++) {
    $scriptStartAt = time();

    require($scriptPath);

    $scriptFinishAt = time();
    $scriptDuration = $scriptFinishAt - $scriptStartAt;

    fputcsv($metricsDescriptor, [$i,$scriptDuration]);
}

fclose($metricsDescriptor);

$benchFinishAt = time();

$benchDuration = $benchFinishAt - $benchStartAt;
$message = "Benchmark duration is $benchDuration";
echo $message . PHP_EOL;
