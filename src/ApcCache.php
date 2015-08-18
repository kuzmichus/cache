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

class ApcCache extends CacheItemPool
{
    public function __construct()
    {
        parent::__construct(new \Doctrine\Common\Cache\ApcCache());
    }
}
