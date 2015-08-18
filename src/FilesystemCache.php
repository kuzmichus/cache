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

class FilesystemCache extends CacheItemPool
{
    public function __construct($directory, $extension = \Doctrine\Common\Cache\FilesystemCache::EXTENSION)
    {
        parent::__construct(new \Doctrine\Common\Cache\FilesystemCache($directory, $extension));
    }
}
