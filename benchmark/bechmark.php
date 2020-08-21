<?php

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use PTS\Events\EventEmitter;
use PTS\Events\Filter\FilterEmitter;

require_once __DIR__  .'/../vendor/autoload.php';

$iterations = $argv[1] ?? 1000; // 13ms - 10000
$blackfire = $argv[2] ?? false;
$iterations++;

if ($blackfire) {
    $client = new Client;
    $probe = $client->createProbe(new Configuration);
}

$startTime = microtime(true);
$events = new EventEmitter;
$filters = new FilterEmitter;

$events
    ->on('event-a', fn(int $a) => $a, 50, [1, 2])
    ->on('event-a', fn(int $b) => $b);

$filters
    ->on('filter-a', fn(int $a, int $b) => $a, 50, [1, 2])
    ->on('filter-a', fn(int $a, int $b) => $a);


while ($iterations--) {
    $events->emit('event-a', [1]);
    $filters->emit('filter-a', 1, [3]);
}

$diff = (microtime(true) - $startTime) * 1000;
echo sprintf('%2.3f ms', $diff);
echo "\n" . memory_get_peak_usage()/1024;

if ($blackfire) {
    $client->endProbe($probe);
}
