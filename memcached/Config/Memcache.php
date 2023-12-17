<?php
return array (
  'stats_api' => 'Memcache',
  'slabs_api' => 'Memcache',
  'items_api' => 'Memcache',
  'get_api' => 'Memcache',
  'set_api' => 'Memcache',
  'delete_api' => 'Memcache',
  'flush_all_api' => 'Memcache',
  'connection_timeout' => '1',
  'max_item_dump' => '100',
  'refresh_rate' => 5,
  'memory_alert' => '80',
  'hit_rate_alert' => '90',
  'eviction_alert' => '0',
  'file_path' => 'Temp/',
  'servers' => 
  array (
    'gal' => 
    array (
      '' => 
      array (
        'hostname' => 'hostname',
        'port' => 'port',
      ),
      'localhost:11211' => 
      array (
        'hostname' => 'localhost',
        'port' => '11211',
      ),
    ),
  ),
);