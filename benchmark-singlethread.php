<?php

use SbWereWolf\Scripting\FileSystem\Path;


$benchStartAt = time();
$message = "Benchmark start at {$benchStartAt}";
echo $message;

$pathParts = [__DIR__, 'vendor', 'autoload.php'];
$autoloaderPath = join(DIRECTORY_SEPARATOR, $pathParts);
require_once($autoloaderPath);

$pathComposer = new Path(__DIR__);

$scriptPath = $pathComposer->make(['data-import.php']);
$metricsPath = $pathComposer->make(
    [
        'metrics',
        pathinfo(__FILE__, PATHINFO_FILENAME) . '-' . $benchStartAt . '.csv',
    ]
);
$metricsDescriptor = fopen($metricsPath, "w");

for ($i = 0; $i < 3; $i++) {
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
echo $message;
