<?php

class CacheManager
{
    /**
     * Static instances
     * @var static
     */
    protected static $instance = null;

    /**
     * Resolved cache drivers
     * @var CacheDriver[]
     */
    protected $driversArr;

    /**
     * CacheManager constructor
     *
     * @param string|array|null $drivers
     * @param array $paramsArr
     */
    public function __construct($drivers = null, $paramsArr = [])
    {
        if ($drivers === null || (is_array($drivers) && empty($drivers))) {
            $this->resolve_default_drivers();
        } else {
            $this->resolve_drivers($drivers, $paramsArr);
        }
    }

    /**
     * Returns default instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Resolves the manager with the default drivers
     */
    private function resolve_default_drivers()
    {
        $this->resolve_drivers(configManager::$default_cache_driversArr);
    }

    /**
     * Resolves the cache drivers
     *
     * @param string|array $drivers
     * @param array $paramsArr
     */
    private function resolve_drivers($drivers, array $paramsArr = [])
    {
        if (!is_array($drivers)) {
            $drivers = [
                $drivers => $paramsArr,
            ];
        }

        foreach ($drivers as $driver => $paramsArr) {
            $this->driversArr[$driver] = new $driver($paramsArr);
        }
    }

    /**
     * Stores a value to the cache
     *
     * @param $key
     * @param $value
     * @param int|null $ttl
     * @param array $optionsArr
     * @return bool[]
     */
    public function set($key, $value, $ttl = null, $optionsArr = [])
    {
        $resultsArr = [];

        foreach ($this->driversArr as $Driver) {
            $resultsArr[get_class($Driver)] = $Driver->set($key, $value, $ttl, $optionsArr);
        }

        return $resultsArr;
    }

    /**
     * Returns a cached value
     *
     * @param $key
     * @param null $driver
     * @return mixed
     */
    public function get($key, $driver = null)
    {
        $driversArr = $driver === null ? $this->driversArr : [$driver];

        foreach ($driversArr as $Driver) {
            if (($result = $Driver->get($key)) !== null) {
                return $result;
            }
        }

        return null;
    }
}