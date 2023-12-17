<?php

class MemcacheCacheDriver extends BaseManager implements CacheDriver
{
    /**
     * Driver settings
     * @var array
     */
    private $settingsArr = [
        'dev' => [
            'default_ttl' => 2592000,
        ],
        'live' => [
            'default_ttl' => 2592000,
        ],
    ];

    /**
     * Servers by environment
     * @var array
     */
    private $connectionsArr = [
        'dev' => [
            [
                'host' => 'localhost',
                'port' => 11211,
            ],
        ],
        'live' => [
            [
                'host' => 'localhost',
                'port' => 11211,
            ],
        ],
    ];

    /**
     * Memcache instance
     * @var Memcache
     */
    private $client;

    /**
     * Current environment (dev/live)
     * @var string
     */
    private $env;

    public const ERRORS_TABLE = 'memcached_error';

    /**
     * MemcachedCacheDriver constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->env = siteFunctions::get_env();

        $this->client = new Memcache();

        foreach ($this->connectionsArr[$this->env] as $serverArr) {
            $this->client->addServer($serverArr['host'], $serverArr['port']);
        }
    }

    /**
     * MemcacheCacheDriver destructor
     */
    public function __destruct()
    {
        $this->client->close();
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null, $optionsArr = [])
    {
        try {
            $this->client->set($this->add_prefix($key), $value, 0, ($ttl ?: $this->settingsArr[$this->env]['default_ttl']));
        } catch (Exception $e) {
            $ts = time();
            $this->db->insert(static::ERRORS_TABLE, [
                'key' => $key,
                'value' => serialize($value),
                'time' => $ts,
                'info' => serialize($optionsArr),
                'last_update' => $ts,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        try {
            return $this->client->get($this->add_prefix($key));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Prefixes the key with the environment
     *
     * @param $key
     * @return string
     */
    private function add_prefix($key)
    {
        return $this->env . '.' . $key;
    }
}