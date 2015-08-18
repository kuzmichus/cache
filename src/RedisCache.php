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

use Redis;

class RedisCache extends CacheItemPool
{
    public function __construct()
    {
        return parent::__construct(new \Doctrine\Common\Cache\PredisCache());
    }

    /**
     * Sets the redis instance to use.
     *
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis)
    {
        $this->provider->setRedis($redis);
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return Redis|null
     */
    public function getRedis()
    {
        return $this->provider->getRedis();
    }
}
