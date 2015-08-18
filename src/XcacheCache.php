<?php
/**
 *
 * PHP version 5.5
 *
 * @package Cache\Psr
 * @author  Sergey V.Kuzin <sergey@kuzin.name>
 * @license MIT
 */

namespace Cache;

class XcacheCache extends CacheItemPool
{
    public function __construct()
    {
        return parent::__construct(new \Doctrine\Common\Cache\XcacheCache());
    }
}
