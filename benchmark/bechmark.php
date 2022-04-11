<?php

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use PTS\Events\EventEmitter;
use PTS\Events\EventEmitterExtraArgs;
use PTS\Events\Filter\FilterEmitter;
use PTS\Events\Filter\FilterExtraArgsEmitter;

require_once __DIR__  .'/../vendor/autoload.php';

$iterations = $argv[1] ?? 800000;
$blackfire = $argv[2] ?? false;
$iterations++;

if ($blackfire) {
    $client = new Client;
    $probe = $client->createProbe(new Configuration);
}

$startTime = microtime(true);

$events = new EventEmitter;
$events2 = new EventEmitterExtraArgs();

$filters = new FilterEmitter;
$filters2 = new FilterExtraArgsEmitter;

$events
    ->on('event-a', fn(int|null $b = null) => $b)
    ->on('event-a', fn(int|null $b = null) => $b);

$events2
    ->on('event-a', fn(int|null $a = null) => $a, 50, [1, 2])
    ->on('event-a', fn(int|null $b = null) => $b);

$filters
    ->on('filter-a', fn(int $a = 1, int $b = 2) => $a, 50, [1, 2])
    ->on('filter-a', fn(int $a = 1, int $b = 2) => $a);

$filters2
    ->on('filter-a', fn(int $a = 1, int $b = 2) => $a, 50, [1, 2])
    ->on('filter-a', fn(int $a = 1, int $b = 2) => $a);


function test(string $title, callable $func, int $iterations) {
    $startTime = microtime(true);

    while ($iterations--) {
        $func();
    }

    $diff = (microtime(true) - $startTime) * 1000;
    $duration = sprintf('%2.3f ms', $diff);
    echo $title . ': ' . $duration . PHP_EOL;
}

echo 'Events:' . PHP_EOL;

test('with args in emit', function() use ($events){
    $events->emit('event-a', [1]);
}, $iterations);

test('EA with args in emit', function() use ($events2){
    $events2->emit('event-a', [1]);
}, $iterations);


test('without args in emit', function() use ($events){
    $events->emit('event-a');
}, $iterations);

test('EA without args in emit', function() use ($events2){
    $events2->emit('event-a');
}, $iterations);



echo PHP_EOL . 'Filter:' . PHP_EOL;

test('with args in emit', function() use ($filters){
    $filters->emit('filter-a', 1, [3]);
}, $iterations);

test('EA with args in emit', function() use ($filters2){
    $filters2->emit('filter-a', 1, [3]);
}, $iterations);


test('without args in emit', function() use ($filters){
    $filters->emit('filter-a', 1);
}, $iterations);

test('EA without args in emit', function() use ($filters2){
    $filters2->emit('filter-a', 1);
}, $iterations);



echo "\n" . memory_get_peak_usage()/1024;

if ($blackfire) {
    $client->endProbe($probe);
}
