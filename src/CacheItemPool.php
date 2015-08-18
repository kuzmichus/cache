<?php
/**
 *
 * PHP version 5.5
 *
 * @package Cache
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPool implements CacheItemPoolInterface
{
    /** @var \Doctrine\Common\Cache\CacheProvider  */
    protected $provider;

    /** @var array|CacheItemInterface[] */
    private $deferred = [];

    public function __construct(\Doctrine\Common\Cache\CacheProvider $provider)
    {
        $this->provider = $provider;
    }

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
    public function getItem($key)
    {
        if (isset($this->deferred[$key])) {
            return $this->deferred[$key];
        }

        if (false === $this->provider->contains($key)) {
            return new CacheItem($key);
        }

        $item = $this->provider->fetch($key);
        $item->setHit(true);

        return $item;
    }

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
    public function getItems(array $keys = array())
    {
        if (true === empty($keys)) {
            return [];
        }

        /*$results = $this->provider->fetchMultiple($keys);
        $items= [];
        foreach($results as $key => $result) {
            $item = new CacheItem($key);
            $item->set($result);
            $item->setProvider($this->provider);
            $items[] = $item;
        }*/
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * Deletes all items in the pool.
     *
     * @return boolean
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return $this->provider->flushAll();
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     * An array of keys that should be removed from the pool.
     * @return static
     * The invoked object.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            if (true === $this->provider->contains($key)) {
                $this->provider->delete($key);
            }
        }
        return $this;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return static
     *   The invoked object.
     */
    public function save(CacheItemInterface $item)
    {
        $this->doSave($item);

        return $this;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     * @return static
     *   The invoked object.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;

        return $this;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {
        $result = true;
        foreach ($this->deferred as $key => $deferred) {
            $saveResult = $this->doSave($deferred);
            if (true === $saveResult) {
                unset($this->deferred[$key]);
            }
            $result = $result && $saveResult;
        }
        return $result;
    }

    private function doSave(CacheItemInterface $item)
    {
        $now = new \DateTime();
        $ttl = $item->getExpiration()->format('U') - $now->format('U');
        if ($ttl < 0) {
            return false;
        }
        return $this->provider->save($item->getKey(), $item, $ttl);
    }
}
