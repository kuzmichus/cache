<?php
/**
 *
 * PHP version 5.5
 *
 * @package Cache\Psr
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace Cache\Psr;

use Cache\CacheItemPool;
use MongoCollection;

class MongoDBCache extends CacheItemPool
{
    public function __construct(MongoCollection $collection)
    {
        parent::__construct(new \Doctrine\Common\Cache\MongoDBCache($collection));
    }
}
