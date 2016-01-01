# php-simple-events

[![Build Status](https://travis-ci.org/alexpts/php-simple-events.svg?branch=master)](https://travis-ci.org/alexpts/php-simple-events)
[![Test Coverage](https://codeclimate.com/github/alexpts/php-simple-events/badges/coverage.svg)](https://codeclimate.com/github/alexpts/php-simple-events/coverage)
[![Code Climate](https://codeclimate.com/github/alexpts/php-simple-events/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-simple-events)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-simple-events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-events/?branch=master)


## События
Класс `Events` предостовляет диспетчер событий. Обработчики подписываются на событие и будут выполнены в момент срабатывания события.

### API Events
Класс предоставляет интерфейс `EventsInterface`, который содержит 3 метода

```php
emit($name, array $arguments = []);

on($name, callable $handler, $priority = 50, array $extraArguments = []);

off($eventName, callable $handler = null, $priority = null);
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
$events->on('some', $instanceWithInvokeMethod);
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

## Фильтры
Фильтры очень похожи на события. В момент срабатывания события фильтрации нужно обязательно передать значение, которое будет пропущено через фильтры. 

### API Filters
Интерфейс фильтров полностью повторяет интерфейс событий в части подключения и отключения обработчиков.
А вот вызов фильтра происходит с помощью метода `filter` вместо `emit`.

```php
filter($name, $value, array $arguments = []);
on($name, callable $handler, $priority = 50, array $extraArguments = []);
off($eventName, callable $handler = null, $priority = null);
```

#### Пример
```php
use PTS\Events\Filters;
$filters = new Filters;

$filters->on('post:title', 'trim');
$title = $filter->filter('post:title', ' Raw title!!!');
```