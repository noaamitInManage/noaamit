<?php

class FileCacheDriver implements CacheDriver
{
    private const PATH = '_static/';

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null, $optionsArr = [])
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $key;
    }

    private function delete_file()
    {

    }

    private function get_file_path($key)
    {

    }
}