<?php

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use PTS\Events\Events;
use PTS\Events\Filters;

require_once __DIR__  .'/../vendor/autoload.php';

$iterations = $argv[1] ?? 1000;
$blackfire = $argv[2] ?? false;
$iterations++;

if ($blackfire) {
    $client = new Client;
    $probe = $client->createProbe(new Configuration);
}

$startTime = microtime(true);
$events = new Events;
$filters = new Filters;


$events->on('event-a', function (int $a) {
    return $a;
}, 50, [1, 2])->on('event-a', function (int $b) {
    return $b;
});

$filters->on('filter-a', function(int $a, int $b) {
    return $a;
}, 50, [1, 2])->on('filter-a', function (int $a, int $b) {
    return $a;
});


while ($iterations--) {
    $events->emit('event-a', [1]);
    $filters->filter('filter-a', 1, [3]);
}

$diff = (microtime(true) - $startTime) * 1000;
echo sprintf('%2.3f ms', $diff);
echo "\n" . memory_get_peak_usage()/1024;

if ($blackfire) {
    $client->endProbe($probe);
}
