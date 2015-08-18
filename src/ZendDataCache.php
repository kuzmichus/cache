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

class ZendDataCache extends CacheItemPool
{
    public function __construct()
    {
        return parent::__construct(new \Doctrine\Common\Cache\ZendDataCache());
    }
}