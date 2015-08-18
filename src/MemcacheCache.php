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

use \Memcache;

class MemcacheCache extends CacheItemPool
{
    public function __construct(Memcache $memcached)
    {
        parent::__construct(new \Doctrine\Common\Cache\MemcacheCache());
        $this->provider->setMemcache($memcached);
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param Memcache $memcache
     *
     * @return void
     */
    public function setMemcache(Memcache $memcache)
    {
        $this->provider->setMemcache($memcache);
    }
    /**
     * Gets the memcached instance used by the cache.
     *
     * @return Memcache|null
     */
    public function getMemcache()
    {
        return $this->provider->getMemcache();
    }
}
