# php-simple-events

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8615ac6e31854b43b0a26a8dc558eb0e)](https://www.codacy.com/app/alexpts/php-simple-events?utm_source=github.com&utm_medium=referral&utm_content=alexpts/php-simple-events&utm_campaign=badger)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/590c6fdc-95ed-4ed6-b76d-169d99c5de58/big.png)](https://insight.sensiolabs.com/projects/590c6fdc-95ed-4ed6-b76d-169d99c5de58)

[![Build Status](https://travis-ci.org/alexpts/php-simple-events.svg?branch=master)](https://travis-ci.org/alexpts/php-simple-events)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-simple-events/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-events/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-simple-events/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-simple-events)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-simple-events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-events/?branch=master)

## Installation

```$ composer require alexpts/php-simple-events```

## События
Класс `Events` предостовляет диспетчер событий. Обработчики подписываются на событие и будут выполнены в момент срабатывания события.

### API Events
Класс предоставляет интерфейс `EventsInterface`, который содержит

```php
emit($name, array $arguments = []);

on($name, callable $handler, $priority = 50, array $extraArguments = []);

once($name, callable $handler, $priority = 50, array $extraArguments = []);

off($name, callable $handler = null, $priority = null);
 ```

#### Добавление обработчиков
Обработчики добавляются через метод on к событию. Обработчиком события может быть любой callable тип:

```php
use PTS\Events\Events;
$events = new Events;

$events->on('some:event', function(){ ... });
$events->on('some:event', 'trim');
$events->on('some', ['ClassName', 'method']);
$events->on('some', [$this, 'method']);
$events->once('some', $instanceWithInvokeMethod);
```

#### Порядок обработчиков
На одно событие может быть подписано множество обработчиков. В момент добавления обработчика к событию через метод `on` можно указать приоритет срабатывания обработчика 3 параметром в виде числа. Обработчики с наивысшем приоритетом выполняются первые. Обработчики с одинаковым приоритетом выполняются в порядке добавления их к событию.

```php
$events->on('post:title', 'trim', 10);
$events->on('post:title', 'prepareTitle', 70);
```

#### Отключение обработчиков
Любой обработчик может быть отписан от события с помощью метода `off`:
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

#### Прерывание распространения события

Чтобы прервать выполнение последующих обрабочтиков событий, необходимо кинуть исключение типа StopPropagation

```php
$events->on('eventName', function(){...});
$events->on('eventName', function(){ throw new StopPropagation; });
$events->on('eventName', function(){...}); // it does not work
```

## Фильтры
Фильтры очень похожи на события. В момент срабатывания события фильтрации нужно обязательно передать значение, которое будет пропущено через фильтры. 

### API Filters
Интерфейс фильтров полностью повторяет интерфейс событий в части подключения и отключения обработчиков.
А вот вызов фильтра происходит с помощью метода `filter` вместо `emit`.

```php
filter($name, $value, array $arguments = []);
on($name, callable $handler, $priority = 50, array $extraArguments = []);
once($name, callable $handler, $priority = 50, array $extraArguments = []);
off($name, callable $handler = null, $priority = null);
```

#### Пример
```php
use PTS\Events\Filters;
$filters = new Filters;

$filters->on('post:title', 'trim');
$title = $filters->filter('post:title', ' Raw title!!!');
```


#### Прерывание распространения фильтра

Чтобы прервать выполнение последующих обрабочтиков фильтра, необходимо кинуть исключение типа StopPropagation

```php
$filters->on('eventName', function($value){ return $value . ' _'; });
$filters->on('eventName', function(value){ throw (new StopPropagation)->setValue(value); });
$filters->on('eventName', function(value){ return $value . ' 2';}); // it does not work
$title = $filters->filter('pre_title', ' Raw title!!!'); //  'Raw title!!! _'
```
