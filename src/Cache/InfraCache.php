<?php

namespace App\Cache;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class InfraCache
{
    public function __construct(
        private TagAwareCacheInterface $reportCache
    ) {}

    /**
     * cached
     *
     * @param string $prefix
     * @param array $key
     * @param callable $callback
     * @return mixed
     */
    public function cached(string $prefix, array $key, callable $callback): mixed
    {
        $cacheKey = $prefix . '_' . md5(serialize($key));

        return $this->reportCache->get($cacheKey, function (ItemInterface $item,) use ($callback) {
            $item->tag(['report']);

            return $callback();
        });
    }
}
