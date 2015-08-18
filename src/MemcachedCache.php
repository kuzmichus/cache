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

use \Memcached;

class MemcachedCache extends CacheItemPool
{
    public function __construct(Memcached $memcached)
    {
        parent::__construct(new \Doctrine\Common\Cache\MemcachedCache());
        $this->provider->setMemcached($memcached);
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param Memcached $memcached
     *
     * @return void
     */
    public function setMemcached(Memcached $memcached)
    {
        $this->provider->setMemcached($memcached);
    }
    /**
     * Gets the memcached instance used by the cache.
     *
     * @return Memcached|null
     */
    public function getMemcached()
    {
        return $this->provider->getMemcached();
    }
}
