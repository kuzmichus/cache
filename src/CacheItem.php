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

//include __DIR__ . '/Psr/Cache/CacheItemInterface.php';

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    protected $key;

    protected $value;

    protected $expiration;

    protected $hit;

    public function __construct($key, $ttl = null, $hit = false)
    {
        $this->key = $key;
        $this->setExpiration($ttl);

        $this->setHit($hit);
    }


    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this objects key.
     *
     * The value returned must be identical to the value original stored by set().
     *
     * if isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * Implementing Libraries MAY provide a default TTL if one is not specified.
     * If no TTL is specified and no default TTL has been set, the TTL MUST
     * be set to the maximum possible duration of the underlying storage
     * mechanism, or permanent if possible.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     * @param int|\DateTime $ttl
     *   - If an integer is passed, it is interpreted as the number of seconds
     *     after which the item MUST be considered expired.
     *   - If a DateTime object is passed, it is interpreted as the point in
     *     time after which the item MUST be considered expired.
     *   - If no value is passed, a default value MAY be used. If none is set,
     *     the value should be stored permanently or for as long as the
     *     implementation allows.
     * @return static
     *   The invoked object.
     */
    public function set($value, $ttl = null)
    {
        $this->value = $value;
        $this->setExpiration($ttl);
        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return boolean
     *   True if the request resulted in a cache hit.  False otherwise.
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * Confirms if the cache item exists in the cache.
     *
     * Note: This method MAY avoid retrieving the cached value for performance
     * reasons, which could result in a race condition between exists() and get().
     * To avoid that potential race condition use isHit() instead.
     *
     * @return boolean
     *  True if item exists in the cache, false otherwise.
     */
    public function exists()
    {
        return $this->isHit();
    }

    /**
     * Sets the expiration time this cache item.
     *
     * @param \DateTime|\DateTimeImmutable $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTime) {
            $this->expiration = $expiration;
        } elseif ($expiration instanceof \DateTimeImmutable) {
            $this->expiration = $this->expiration = $expiration;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Sets the expiration time this cache item.
     *
     * @param int|\DateInterval $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if (true === is_numeric($time)) {
            $this->expiration = new \DateTime('now +' . $time . ' seconds');
        } elseif ($time instanceof \DateInterval) {
            $this->expiration = new \DateTime('now +' . $time->format('U') . ' seconds');
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime
     *   The timestamp at which this cache item will expire.
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param null $ttl
     * @return $this
     */
    public function setExpiration($ttl = null)
    {
        if (true === is_numeric($ttl)) {
            $this->expiration = new \DateTime('now +' . $ttl . ' seconds');
        } elseif ($ttl instanceof \DateTime) {
            $this->expiration = $ttl;
        } elseif (null === $ttl) {
            $this->expiration = new \DateTime('now + 10 years');
        } else {
            throw new InvalidArgumentException();
        }
        return $this;
    }

    /**
     * @param boolean $hit
     *
     * @return CacheItem
     * @throws InvalidArgumentException not a boolean value given
     */
    public function setHit($hit)
    {
        /*        if (false === is_bool($hit)) {
            throw InvalidArgumentException::typeMismatch($hit, 'Boolean');
        }*/
        $this->hit = $hit;
        return $this;
    }
}
