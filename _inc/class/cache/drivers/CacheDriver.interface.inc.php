<?php

interface CacheDriver
{
    /**
     * Stores a value to the cache driver
     *
     * @param $key
     * @param $value
     * @param int|null $ttl
     * @param array $optionsArr
     * @return bool
     */
    public function set($key, $value, $ttl = null, $optionsArr = []);

    /**
     * Returns a cached value from the driver
     *
     * @param $key
     * @return mixed
     */
    public function get($key);
}