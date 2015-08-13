PSR-Cache Meta Document
===================

1. Summary
----------

Кэширование - распространенный способ улучшить производительность любого проекта, делая
кэширование библиотек одна из наиболее распространенных функций многих платформ и
библиотеки. Это имеет, приводят к ситуации, где много библиотек прокручивают свое собственное
кэширование библиотек, с различными уровнями функциональности. Эти различия
порождение разработчиков должными быть изучить многократные системы, которые могут или не могут
обеспечьте функциональность, в которой они нуждаются. Кроме того, разработчики кэширования
сами библиотеки обращенным к выбору между только поддержкой ограниченного количества
из платформ или создания большого количества классов адаптера.


2. Why Bother?
--------------

Единый интерфейс для кэширования систем решит эти проблемы. Библиотека и
разработчики платформы могут рассчитывать на кэширующиеся системы, прокладывающие себе путь, они
ожидание, в то время как разработчики кэширующихся систем должны будут только реализовать
единственный набор интерфейсов, а не целый выбор адаптеров.

Кроме того, реализация, представленная здесь, разработана для будущей расширяемости.
Это позволяет множество внутренне различных но совместимых с API реализаций
и предлагает четкий путь для будущего расширения позже PSRs или определенным
разработчики.

Достоинства:
* стандартный интерфейс для кэширования позволяет автономным библиотекам поддерживать
кэширование посреднических данных без усилия; они могут просто (дополнительно) зависеть
в этом стандартном интерфейсе и рычагах это, не касаясь
детали реализации.
* Обычно разрабатываемые кэширующиеся библиотеки, совместно использованные многократными проектами, даже если
они расширяют этот интерфейс, вероятно, будут более устойчивыми, чем дюжина отдельно
разработанные реализации.

Недостатки:
* Любая интерфейсная стандартизация рискует душить будущие инновации как
будучи "не Путем Это Сделало (TM)". Однако мы полагаем, что кэширование достаточно
коммодитизированное пространство задач, которое здесь смягчает дополнительная возможность, предлагаемая
любой потенциальный риск застоя.

3. Scope
--------

## 3.1 Goals

* A common interface for basic and intermediate-level caching needs.
* A clear mechanism for extending the specification to support advanced features,
both by future PSRs or by individual implementations. This mechanism must allow
for multiple independent extensions without collision.

## 3.2 Non-Goals

* Architectural compatibility with all existing cache implementations.
* Advanced caching features such as namespacing or tagging that are used by a
minority of users.

4. Approaches
-------------

### 4.1 Chosen Approach

This specification adopts a "repository model" or "data mapper" model for caching
rather than the more traditional "expire-able key-value" model.  The primary
reason is flexibility.  A simple key/value model is much more difficult to extend.

The model here mandates the use of a CacheItem object, which represents a cache
entry, and a Pool object, which is a given store of cached data.  Items are
retrieved from the pool, interacted with, and returned to it.  While a bit more
verbose at times it offers a good, robust, flexible approach to caching,
especially in cases where caching is more involved than simply saving and
retrieving a string.

Most method names were chosen based on common practice and method names in a
survey of member projects and other popular non-member systems.

Pros:

* Flexible and extensible
* Allows a great deal of variation in implementation without violating the interface
* Does not implicitly expose object constructors as a pseudo-interface.

Cons:

* A bit more verbose than the naive approach

Examples:

Some common usage patterns are shown below.  These are non-normative but should
demonstrate the application of some design decisions.

```php
/**
 * Gets a list of available widgets.
 *
 * In this case, we assume the widget list changes so rarely that we want
 * the list cached forever until an explicit clear.
 */
function get_widget_list()
{
    $pool = get_cache_pool('widgets');
    $item = $pool->getItem('widget_list');
    if (!$item->isHit()) {
        $value = compute_expensive_widget_list();
        $item->set($value);
        $pool->save($item);
    }
    return $item->get();
}
```

```php
/**
 * Caches a list of available widgets.
 *
 * In this case, we assume a list of widgets has been computed and we want
 * to cache it, regardless of what may already be cached.
 */
function save_widget_list($list)
{
    $pool = get_cache_pool('widgets');
    $item = $pool->getItem('widget_list');
    $item->set($list);
    $pool->save($item);
}
```

```php
/**
 * Clears the list of available widgets.
 *
 * In this case, we simply want to remove the widget list from the cache. We
 * don't care if it was set or not; the post condition is simply "no longer set".
 */
function clear_widget_list()
{
    $pool = get_cache_pool('widgets');
    $pool->deleteItems(['widget_list']);
}
```

```php
/**
 * Clears all widget information.
 *
 * In this case, we want to empty the entire widget pool. There may be other
 * pools in the application that will be unaffected.
 */
function clear_widget_cache()
{
    $pool = get_cache_pool('widgets');
    $pool->clear();
}
```

```php
/**
 * Load widgets.
 *
 * We want to get back a list of widgets, of which some are cached and some
 * are not. This of course assumes that loading from the cache is faster than
 * whatever the non-cached loading mechanism is.
 *
 * In this case, we assume widgets may change frequently so we only allow them
 * to be cached for an hour (3600 seconds). We also cache newly-loaded objects
 * back to the pool en masse.
 *
 * Note that a real implementation would probably also want a multi-load
 * operation for widgets, but that's irrelevant for this demonstration.
 */
function load_widgets(array $ids)
{
    $pool = get_cache_pool('widgets');
    $keys = array_map(function($id) { return 'widget.' . $id; }, $ids);
    $items = $pool->getItems($keys);

    $widgets = array();
    foreach ($items as $key => $item) {
        if ($item->isHit()) {
            $value = $item->get();
        }
        else {
            $value = expensive_widget_load($id);
            $item->set($value, 3600);
            $pool->saveDeferred($item, true);
        }
        $widget[$value->id()] = $value;
    }
    $pool->commit(); // If no items were deferred this is a no-op.

    return $widgets;
}
```

```php
/**
 * This examples reflects functionality that is NOT included in this
 * specification, but is shown as an example of how such functionality MIGHT
 * be added by extending implementations.
 */


interface TaggablePoolInterface extends Psr\Cache\PoolInterface
{
    /**
     * Clears only those items from the pool that have the specified tag.
     */
    clearByTag($tag);
}

interface TaggableItemInterface extends Psr\Cache\ItemInterface
{
    public function setTags(array $tags);
}

/**
 * Caches a widget with tags.
 */
function set_widget(TaggablePoolInterface $pool, Widget $widget)
{
    $key = 'widget.' . $widget->id();
    $item = $pool->getItem($key);

    $item->setTags($widget->tags());
    $item->set($widget);
    $pool->save($item);
}
```

### 4.2 Alternative: "Weak item" approach

A variety of earlier drafts took a simpler "key value with expiration" approach,
also known as a "weak item" approach.  In this model, the "Cache Item" object
was really just a dumb array-with-methods object.  Users would instantiate it
directly, then pass it to a cache pool.  While more familiar, that approach
effectively prevented any meaningful extension of the Cache Item.  It effectively
made the Cache Item's constructor part of the implicit interface, and thus
severely curtailed extensibility or the ability to have the cache item be where
the intelligence lives.

In a poll conducted in June 2013, most participants showed a clear preference for
the more robust if less conventional "Strong item" / repository approach, which
was adopted as the way forward.

Pros:
* More traditional approach.

Cons:
* Less extensible or flexible.

### 4.3 Alternative: "Naked value" approach

Some of the earliest discussions of the Cache spec suggested skipping the Cache
Item concept all together and just reading/writing raw values to be cached.
While simpler, it was pointed out that made it impossible to tell the difference
between a cache miss and whatever raw value was selected to represent a cache
miss.  That is, if a cache lookup returned NULL it's impossible to tell if there
was no cached value or if NULL was the value that had been cached.  (NULL is a
legitimate value to cache in many cases.)

Most more robust caching implementations we reviewed -- in particular the Stash
caching library and the home-grown cache system used by Drupal -- use some sort
of structured object on `get` at least to avoid confusion between a miss and a
sentinel value.  Based on that prior experience FIG decided that a naked value
on `get` was impossible.

### 4.4 Alternative: ArrayAccess Pool

There was a suggestion to make a Pool implement ArrayAccess, which would allow
for cache get/set operations to use array syntax.  That was rejected due to
limited interest, limited flexibility of that approach (trivial get and set with
default control information is all that's possible), and because it's trivial
for a particular implementation to include as an add-on should it desire to
do so.

5. People
---------

### 5.1 Editor

* Larry Garfield

### 5.2 Sponsors

* Pádraic Brady (Coordinator)
* John Mertic

### 5.3 Contributors

* Paul Dragoonis
* Robert Hafner

6. Votes
--------


7. Relevant Links
-----------------

_**Note:** Order descending chronologically._

* [Survey of existing cache implementations][1], by @dragoonis
* [Strong vs. Weak informal poll][2], by @Crell
* [Implementation details informal poll][3], by @Crell

[1]: https://docs.google.com/spreadsheet/ccc?key=0Ak2JdGialLildEM2UjlOdnA4ekg3R1Bfeng5eGlZc1E#gid=0
[2]: https://docs.google.com/spreadsheet/ccc?key=0AsMrMKNHL1uGdDdVd2llN1kxczZQejZaa3JHcXA3b0E#gid=0
[3]: https://docs.google.com/spreadsheet/ccc?key=0AsMrMKNHL1uGdEE3SU8zclNtdTNobWxpZnFyR0llSXc#gid=1
