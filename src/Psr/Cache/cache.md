## Введение

Кэширование - распространенный способ улучшить производительность любого проекта, делая
кэширование библиотек одна из наиболее распространенных функций многих платформ и
библиотеки. Это имеет, приводят к ситуации, где много библиотек прокручивают свое собственное
кэширование библиотек, с различными уровнями функциональности. Эти различия
порождение разработчиков должными быть изучить многократные системы, которые могут или не могут
обеспечьте функциональность, в которой они нуждаются. Кроме того, разработчики кэширования
сами библиотеки обращенным к выбору между только поддержкой ограниченного количества
из платформ или создания большого количества классов адаптера.

Единый интерфейс для кэширования систем решит эти проблемы. Библиотека и
разработчики платформы могут рассчитывать на кэширующиеся системы, прокладывающие себе путь, они
ожидание, в то время как разработчики кэширующихся систем должны будут только реализовать
единственный набор интерфейсов, а не целый выбор адаптеров.

Ключевые слова "ДОЛЖНЫ", "не ДОЛЖЕН", "ТРЕБУЕМЫЙ", "БЫТЬ", "НЕ БУДУ", "ДОЛЖЕН", "не ДОЛЖЕН", "РЕКОМЕНДУЕМЫЙ",
"МОЖЕТ", и "ДОПОЛНИТЕЛЬНЫЙ" в этом документе должны интерпретироваться, как описано в [RFC 2119][].

[RFC 2119]: http://tools.ietf.org/html/rfc2119

## Цель

Цель этого PSR состоит в том, чтобы позволить разработчикам создавать осведомленные о кэше библиотеки это
может быть интегрирован в существующие платформы и системы без потребности в
заказные разработки.

## Определения

*    **Вызов Библиотеки** - библиотека или код, которому фактически нужен кэш
службы. Эта библиотека использует службы кэширования, которые реализуют это
интерфейсы стандарта, но будет иначе не знать о
реализация тех, которые кэшируют службы.

*    **Реализация Библиотеки** - Эта библиотека ответственна за реализацию
этот стандарт, чтобы предоставить услуги кэширования любой Библиотеке Вызова.
Реализация Библиотеки ДОЛЖНА обеспечить классы, которые реализуют Cache\PoolInterface
и интерфейсы Cache\ItemInterface. Реализация Библиотек ДОЛЖНА поддерживать в
минимальная функциональность TTL, как описано ниже с целой второй гранулярностью.

*    **TTL** - Время жизни (TTL) элемента является количеством времени между
когда тот элемент сохранен, и это считают устарелым. TTL обычно определяется
к целочисленному времени представления в секундах или объекту DateInterval.

*    **Истечение** - фактическое время, когда элемент собирается пойти устарелый. Это это
обычно вычисленный, добавляя TTL ко времени, когда объект хранится, но
май также быть явно установленным с объектом DateTime.

    У элемента с 300-секундным TTL, сохраненным в 1:30:00, будет истечение в
    1:35:00.

    Перед своим требуемым Временем истечения срока реализация Библиотек МОЖЕТ истечь элемент,
но ДОЛЖЕН рассматривать элемент, как истекли, как только его Время истечения срока достигнуто.

*    **Ключ** - последовательность по крайней мере одного символа, который однозначно определяет a
кэшируемый элемент. Реализация библиотек MUST поддерживает ключи, состоящие из
символы 'A-Z', 'a-z', '0-9', '_', и'.' в любом порядке в кодировании UTF-8 и a
длина до 64 символов. Реализация дополнительной поддержки библиотек MAY
символы и кодировки или более длительные длины, но должен поддерживать, по крайней мере, это
минимум. Библиотеки ответственны за свой собственный выход ключевых последовательностей
как надлежащий, но ДОЛЖЕН быть в состоянии возвратить исходную неизмененную ключевую последовательность.
Следующие символы зарезервированы для будущих расширений и не ДОЛЖНЫ быть
поддерживаемый, реализовывая библиотеки: '{} () / \':

*    **Хит** - удачное обращение в кэш происходит, когда Библиотека Вызова запрашивает Элемент ключом
и совпадающее значение найдено для того ключа, и то значение не истекло, и
значение не инвалид по некоторой другой причине.

*    **Существует** - Когда элемент существует в кэше во время этого вызова.
Поскольку это отдельное от isHit() есть потенциальное состояние состязания между
время exists(), вызван, и get() вызываемый, настолько Вызывающие Библиотеки ДОЛЖНЫ
удостоверьтесь, что проверили isHit() на всем том, чтобы get() вызовы.

* **Мисс** - неудачное обращение в кэш является противоположностью удачного обращения в кэш. Удачное обращение в кэш происходит
когда Библиотека Вызова запрашивает элемент ключом и тем значением, не найденным для этого
ключ или значение был найден, но истек, или значение - инвалид для некоторых
другая причина. Значение с истекшим сроком НУЖНО всегда считать неудачным обращением в кэш.

*    **Задержанный** - задержанный кэш сохраняет, указывает, что элемент кэша может не быть
сохраненный сразу пулом. Объект Пула МОЖЕТ задержать сохранение задержанного
элемент кэша, чтобы использовать в своих интересах объемные операции присвоения, поддерживаемые некоторыми
механизмы хранения. Пул ДОЛЖЕН гарантировать, что любые задержанные элементы кэша в конечном счете
сохраненный и данные не потерян и МОЖЕТ сохранить их перед Библиотекой Вызова
запросы, что они быть сохраненным. Когда Библиотека Вызова вызывает фиксацию ()
метод все выдающиеся задержанные элементы ДОЛЖЕН быть сохранен. Библиотека Реализации
МОЖЕТ использовать любую логику, надлежащее, чтобы определить, когда сохраниться задержанный
элементы, такие как объектный деструктор, сохраняя все на save(), тайм-аут или
проверка макс. элементов или любая другая надлежащая логика. Запросы на элемент кэша это
был задержан ДОЛЖЕН возвратить задержанный, но еще сохраненный элемент.

## Данные

Осуществление библиотек ДОЛЖНО поддержать все сериализуемые типы данных PHP, включая:

* **Strings** - Строки символов произвольного размера в любом PHP-совместимом кодировании.
* **Integers** - Все целые числа любого размера, поддержанного PHP, до 64 битов подписались.
* **Floats** - Все подписанные значения с плавающей запятой.
* **Boolean** - верный и ложный.
* **Null** - фактическая пустая стоимость.
* **Arrays** - Индексируемые, ассоциативные и многомерные множества произвольной глубины.
* **Object** - Любой объект, который поддерживает преобразование в последовательную форму без потерь и
десериализация, таким образом, что $o == не преобразовывают в последовательную форму (преобразовывают в последовательную форму ($o)). Объекты МОГУТ
усильте сериализуемый интерфейс PHP, '__ сон ()' или '__ пробуждение ()' волшебные методы,
или подобная языковая функциональность в подходящих случаях.

Все данные, переданные в Библиотеку Осуществления, ДОЛЖНЫ быть возвращены точно как
переданный. Это включает переменный тип. Таким образом, это - ошибка возвратиться
(последовательность) 5, если (интервал) 5 была спасенная стоимость. Осуществление Библиотек МОЖЕТ использовать PHP's
преобразуйте в последовательную форму ()/, не преобразовывают в последовательную форму () функции внутренне, но не требуются, чтобы делать так.
Совместимость с ними просто используется в качестве основания для приемлемых ценностей объекта.

Если не возможно возвратить точную спасенную стоимость по какой-либо причине, осуществляя
библиотеки ДОЛЖНЫ ответить тайником мисс, а не поврежденные данные.

## Основные понятия

### Pool

Pool представляет коллекцию пунктов в системе кэширования. Бассейн
логическое Хранилище всех пунктов это содержит. Восстановлены все cacheable пункты
из Бассейна как объект Изделия и все взаимодействие с целой вселенной
припрятавшие про запас объекты происходят через Бассейн.

### Пункты

Пункт представляет единственную пару ключа/стоимости в Бассейне. Ключ - предварительные выборы
уникальный идентификатор для Пункта и ДОЛЖЕН быть неизменным. Стоимость МОЖЕТ быть изменена
в любое время.

## Интерфейсы

### CacheItemInterface

CacheItemInterface определяет пункт в системе тайника. Каждый объект Изделия
ДОЛЖЕН быть связан с определенным ключем, который может быть установлен согласно
осуществление системы и как правило передается объектом Cache\PoolInterface.

Объект Cache\CacheItemInterface заключает в капсулу хранение и поиск
пункты тайника. Каждый Cache\ItemInterface произведен Cache\PoolInterface
объект, который ответственен за любую необходимую установку, а также соединение
объект с уникальным Ключом. Объекты Cache\ItemInterface ДОЛЖНЫ быть в состоянии
сохраните и восстановите любой тип стоимости PHP, определенной в разделе Данных этого
документ.

Запрос Библиотек не ДОЛЖЕН иллюстрировать примерами сами объекты Изделия. Они могут только
требуйте от объекта Бассейна через getItem () метод. Запрос Библиотек
НЕ ДОЛЖЕН предполагать, что Пункт, созданный одной Библиотекой Осуществления,
совместимый с Бассейном из другой Библиотеки Осуществления.

```php
namespace Psr\Cache;

/**
 * CacheItemInterface определяет интерфейс для взаимодействия с объектами в кэше.
 */
interface CacheItemInterface
{
    /**
     * Возвращает ключ для текущего элемента кэша.
     *
     * Ключ загружен Библиотекой Реализации, но должен быть доступен высокоуровневым 
     * вызывающим сторонам при необходимости.
     *
     * @return string
     *   Ключевая последовательность для этого элемента кэша.
     */
    public function getKey();

    /**
     * Получает значение элемента от кэша, связанного с этим, возражает ключу.
     *
     * Значение возвратилось, должно быть идентично значению, исходному сохраненный set().
     *
     * Если isHit () возвращает false, этот метод ДОЛЖЕН возвратить null. 
     * Обратите внимание на то, что null - законное кэшируемое значение, таким образом,
     * isHit () метод ДОЛЖЕН использоваться, чтобы дифференцироваться между "null значением, был найден", 
     * и "никакое значение не было найдено".
     *
     * @return mixed
     *   Значение, соответствующее этому ключу элемента кэша или нулю, если не найденный.
     */
    public function get();

    /**
     * Устанавливает значение, представленное этим элементом кэша.
     *
     * Параметр $value может быть любым элементом, который может быть сериализирован PHP,
     * несмотря на то, что метод сериализации оставляют до Библиотеки Реализации.
     *
     * Реализация Библиотек МОЖЕТ обеспечить TTL по умолчанию, если Вы не определены. 
     * Если никакой TTL не определен, и никакой TTL по умолчанию не был установлен, 
     * TTL ДОЛЖЕН быть установлен в максимальную возможную продолжительность базового
     * механизма хранения или постоянный, если это возможно.
     *
     * @param mixed $value
     *   Сериализуемое значение, которое будет сохранено.
     * @param int|\DateTime $ttl
     *   - Если целое число передано, оно интерпретируется как число секунд, 
     *     после которых элемент НУЖНО считать с истекшим сроком.
     *   - Если объект DateTime передан, он интерпретируется как момент времени,
     *     после которого элемент НУЖНО считать с истекшим сроком.
     *   - Если никакое значение не передано, значение по умолчанию МОЖЕТ использоваться.
     *     Если ни один не установлен, значение должно быть сохранено постоянно или столько,
     *     сколько реализация позволяет.
     * @return static
     *   Вызванный объект.
     */
    public function set($value, $ttl = null);

    /**
     * Подтверждает, привел ли поиск элемента кэша к удачному обращению в кэш.
     *
     * Примечание: у Этого метода не ДОЛЖНО быть состояния состязания между вызовом isHit (),
     * и вызов get() 
     *
     * @return boolean
     *   True, если запрос привел к удачному обращению в кэш. Иначе False.
     */
    public function isHit();

    /**
     * Подтверждает, существует ли элемент кэша в кэше.
     *
     * Примечание: Этот метод МОЖЕТ избежать получать кэшируемое значение по причинам производительности,
     * которые могли привести к состоянию состязания между, exists(), и get().
     * Избегать, чтобы потенциальное состояние состязания использовало isHit() вместо этого.
     *
     * @return boolean
     *  True, если элемент существует в кэше, иначе false.
     */
    public function exists();

    /**
     * Sets the expiration for this cache item.
     *
     * @param int|\DateTime $ttl
     *   - If an integer is passed, it is interpreted as the number of seconds
     *     after which the item MUST be considered expired.
     *   - If a DateTime object is passed, it is interpreted as the point in
     *     time after which the item MUST be considered expired.
     *   - If null is passed, a default value MAY be used. If none is set,
     *     the value should be stored permanently or for as long as the
     *     implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function setExpiration($ttl = null);

    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime
     *   The timestamp at which this cache item will expire.
     */
    public function getExpiration();
}
```
### CacheItemPoolInterface

Основная цель Cache\CacheItemPoolInterface состоит в том, чтобы принять ключ от
Вызов Библиотеки и возврата связанный объект Cache\CacheItemInterface.
Это - также основная точка взаимодействия со всем набором кэша.
Всю конфигурацию и инициализацию Пула оставляют до Реализации
Библиотека.

```php
namespace Psr\Cache;

/**
 * \Psr\Cache\CacheItemPoolInterface generates Cache\CacheItem objects.
 */
interface CacheItemPoolInterface
{

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return an ItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     * @return \Psr\Cache\CacheItemInterface
     *   The corresponding Cache Item.
     * @throws \Psr\Cache\InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     */
    public function getItem($key);

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     * An indexed array of keys of items to retrieve.
     * @return array|\Traversable
     * A traversable collection of Cache Items keyed by the cache keys of
     * each item. A Cache item will be returned for each key, even if that
     * key is not found. However, if no keys are specified then an empty
     * traversable MUST be returned instead.
     */
    public function getItems(array $keys = array());

    /**
     * Deletes all items in the pool.
     *
     * @return boolean
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear();

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     * An array of keys that should be removed from the pool.
     * @return static
     * The invoked object.
     */
    public function deleteItems(array $keys);

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return static
     *   The invoked object.
     */
    public function save(CacheItemInterface $item);

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     * @return static
     *   The invoked object.
     */
    public function saveDeferred(CacheItemInterface $item);

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit();

}
```

### InvalidArgumentException

```php
namespace Psr\Cache;

/**
 * Exception interface for invalid cache arguements.
 *
 * Any time an invalid argument is passed into a method it must throw an
 * exception class which implements Psr\Cache\InvalidArgumentException.
 */
interface InvalidArgumentException { }
```

### CacheException

Этот интерфейс исключения предназначен для использования, когда критические ошибки происходят,
включая, но не ограничиваясь, *установка кэша*, такая как соединение с сервером кэширования
или недопустимые учетные данные предоставлены.

Любое исключение, выданное Библиотекой Реализации, ДОЛЖНО реализовать этот интерфейс.

```php
namespace Psr\Cache;

/**
 * Exception interface for all exceptions thrown by an Implementing Library.
 */
interface CacheException {}
```