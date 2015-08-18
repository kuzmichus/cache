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

use \Couchbase;

class CouchbaseCache extends CacheItemPool
{
    public function __construct()
    {
        parent::__construct(new \Doctrine\Common\Cache\CouchbaseCache());
    }

    /**
     * Sets the Couchbase instance to use.
     *
     * @param Couchbase $couchbase
     *
     * @return void
     */
    public function setCouchbase(Couchbase $couchbase)
    {
        $this->provider->setCouchbase($couchbase);
    }

    /**
     * Gets the Couchbase instance used by the cache.
     *
     * @return Couchbase|null
     */
    public function getCouchbase()
    {
        return $this->provider->getCouchbase();
    }
}
