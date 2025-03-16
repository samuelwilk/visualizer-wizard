<?php
namespace App\DataProcessing\Domain;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheManager
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Store data in the cache.
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl Time-to-live in seconds
     * @throws InvalidArgumentException
     */
    public function cacheData(string $key, $data, int $ttl = 3600): void
    {
        $item = $this->cache->getItem($key);
        $item->set($data);
        $item->expiresAfter($ttl);
        $this->cache->save($item);
    }

    /**
     * Retrieve cached data.
     *
     * @param string $key
     * @return mixed|null Returns data if cache hit, or null otherwise.
     * @throws InvalidArgumentException
     */
    public function getCachedData(string $key): mixed
    {
        $item = $this->cache->getItem($key);
        return $item->isHit() ? $item->get() : null;
    }
}
