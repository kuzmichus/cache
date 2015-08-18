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

use Riak\Bucket;

class RiakCache extends CacheItemPool
{
    public function __construct(Bucket $bucket)
    {
        parent::__construct(new \Doctrine\Common\Cache\RiakCache($bucket));
    }
}
