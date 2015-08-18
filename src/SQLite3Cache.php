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

use SQLite3;

class SQLite3Cache extends CacheItemPool
{
    public function __construct(SQLite3 $sqlite, $table)
    {
        parent::__construct(new \Doctrine\Common\Cache\SQLite3Cache($sqlite, $table));
    }
}
