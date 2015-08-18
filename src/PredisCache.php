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

use Predis\Client;

class PredisCache extends CacheItemPool
{
    public function __construct(Client $client)
    {
        return parent::__construct(new \Doctrine\Common\Cache\PredisCache($client));
    }
}
