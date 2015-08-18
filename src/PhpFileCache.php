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

class PhpFileCache extends CacheItemPool
{
    public function __construct($directory, $extension = \Doctrine\Common\Cache\PhpFileCache::EXTENSION)
    {
        parent::__construct(new \Doctrine\Common\Cache\PhpFileCache($directory, $extension));
    }
}
