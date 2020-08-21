# php-simple-events

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8615ac6e31854b43b0a26a8dc558eb0e)](https://www.codacy.com/app/alexpts/php-simple-events?utm_source=github.com&utm_medium=referral&utm_content=alexpts/php-simple-events&utm_campaign=badger)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/590c6fdc-95ed-4ed6-b76d-169d99c5de58/big.png)](https://insight.sensiolabs.com/projects/590c6fdc-95ed-4ed6-b76d-169d99c5de58)

[![Build Status](https://travis-ci.org/alexpts/php-simple-events.svg?branch=master)](https://travis-ci.org/alexpts/php-simple-events)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-simple-events/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-events/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-simple-events/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-simple-events)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-simple-events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-events/?branch=master)

## EventEmitter
It is very fast event emitter and filters.

## Installation

```$ composer require alexpts/php-simple-events```

## Usage

### Creating an Emitter
```php
$emitter = new PTS\Events\EventEmitter;
```

### Adding Listeners

Callable handler
```php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

With priority
```php
$emitter->on('user.created', $handler, 100);
```

With extra arguments (last)
```php
$extra1 = 1;
$extra2 = 'some';
$emitter->on('log', function(string $log, int $extra1, string $extra2) {
   // ...
}, 50, [$extra1, $extra2]);
$emitter->emit('log', ['some log']);
```


### Removing Listeners

Remove concrete handler
```php
$handler = fn() => 'log';
$emitter->on('log', $handler)
$emitter->off('log', $handler);
```

Remove all handlers
```php
$emitter->off('log');
```

Remove concrete handler with priority
```php
$emitter->off('log', $handler, 100);
```


## Emitting Events

Simple emit
```php
$emitter->emit('log');
```

Pass arguments on emit
```php
$emitter->on('log', function(string $log, int $a2, bool $a3) {
    // ...
});
$emitter->emit('log', ['arg1', 2, true]);
```




## Interface


```php
emit(string $name, array $args = []): self;
on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
off(string $event, callable $handler = null, int $priority = null): self;

listeners(string $event = null): array;
eventNames(): array;
 ```

#### EventHandler
EventHandler must be `callable`

```php
use PTS\Events\Events;
$eventsBus = new EventEmitter;

$eventsBus->on('some:event', function(){ ... });
$eventsBus->on('some:event', 'trim');
$eventsBus->on('some', ['ClassName', 'method']);
$eventsBus->on('some', [$this, 'method']);
$eventsBus->once('some', $instanceWithInvokeMethod);
```

#### Order handlers
Listeners has priority. All listeners invoke by priority

```php
$events->on('post:title', 'trim', 10); // second
$events->on('post:title', 'prepareTitle', 70); // first
```

#### Remove handler
```php
// remove handler 'trim' with priority = 10
$events->on('post:title', 'trim', 10);
$events->off('post:title', 'trim', 10);

// remove all handler 'trim' with any priority
$events->on('post:title', 'trim', 10);
$events->off('post:title', 'trim');

// remove all handlers
$events->on('post:title', 'trim', 10);
$events->off('post:title');
```

#### StopPropagation

```php
$events->on('eventName', function(){ ... });
$events->on('eventName', function(){ throw new StopPropagation; });
$events->on('eventName', function(){ ... }); // it does not call
```

## Filters

### API Filters
Filters is very similar to EventEmitter. Filter passes first value through all listeners and returns modified value.

```php
emit(string $name, $value, array $args = []);
on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
off(string $event, callable $handler = null, int $priority = null): self;

listeners(string $event = null): array;
eventNames(): array;
```

Example
```php
use PTS\Events\Filters;
$filters = new Filters;

$filters->on('post:title', 'trim');
$title = $filters->filter('post:title', '   Raw title   '); // `Raw title`
```



### Inject EventEmitter / FilterEmitter

1. Event/Filter Bus.
```php
use PTS\Events\EventBusTrait;

class Service {
    use EventBusTrait;

    public function getPost()
    {
        $post = ...;
        // you can to modify $post via filter/event
        $post = $this->filter('getPost', $post); // from EventBusTrait
        return $post;
    }
}
```

2. Event/Filter for any object.

Extend from EventEmitter:
```php
use PTS\Events\Filter\FilterEmitter;
use PTS\Events\EventEmitter;

class Request extend FilterEmitter { // extend EventEmitter

    public function parseHeader()
    {
        $rawHttpRequest = '...';
        $headers = $this->filter('parseHeader', $rawHttpRequest);
        return $headers;
    }
}

$request = new Request;
$parseHeader = new ParserHeader;
$request->on('parseHeader', [$parseHeader, 'parse']);
$headers = $request->parseHeader();
```

Use trait:

```php
use PTS\Events\Filter\FilterEmitterTrait;
use PTS\Events\EventEmitterTrait;

class Request

    use FilterEmitterTrait;

    public function parseHeader()
    {
        $rawHttpRequest = '...';
        $headers = $this->filter('parseHeader', $rawHttpRequest);
        return $headers;
    }
}

$request = new Request;
$parseHeader = new ParserHeader;
$request->on('parseHeader', [$parseHeader, 'parse']);
$headers = $request->parseHeader();
```