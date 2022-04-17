<?php

namespace Wappointment\Repositories;

use Wappointment\Services\Flag;
use Wappointment\Services\Settings;
abstract class AbstractRepository implements \Wappointment\Repositories\RepositoryInterface
{
    public $cache_key = '';
    public $expiration = 0;
    public function get()
    {
        if (empty(\Wappointment\Services\Settings::get('cache'))) {
            return $this->query();
        }
        $cached_result = get_transient($this->getCacheKey());
        return empty($cached_result) ? $this->init() : $cached_result;
    }
    /**
     * Initing the cache only supposed to run once
     *
     * @return void
     */
    protected function init()
    {
        $testFlag = 'cached_' . $this->getCacheKey();
        $cache = null;
        if (!\Wappointment\Services\Flag::get($testFlag)) {
            $cache = $this->cache();
            \Wappointment\Services\Flag::save($testFlag, true);
        }
        return $cache;
    }
    public function cache()
    {
        $data = $this->query();
        if (!empty($data)) {
            set_transient($this->getCacheKey(), $data, $this->expiration);
        }
        return $data;
    }
    public function clear()
    {
        delete_transient($this->getCacheKey());
    }
    public function refresh()
    {
        $this->clear();
        $this->cache();
    }
    private function getCacheKey()
    {
        return 'wappo_' . $this->cache_key;
    }
}
