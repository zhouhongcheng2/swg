<?php

namespace Swg\Composer\redis;
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

use Swg\Redis\Redis;

/**
 * 接口数据缓存
 */
class RedisRequest extends Redis
{
    protected $db = self::REDIS_REQUEST_CACHE_DB;

    const REQUEST_CACHE_KEY = 'request:';

    /**
     * 设置截止今日缓存
     * @param int $member_id 当前登录用户ID
     */
    public function setCacheAsOfToday($request_key, $params, int $member_id, ?array $data)
    {
        $ttl = $this->getTodayEndTtl();
        $this->setCache($request_key, $params, $member_id, $data, $ttl);
    }

    /**
     * 获取或设置截止今日缓存
     * @param $request_key
     * @param int $member_id 当前登录用户ID
     * @param callable|null $callable
     * @return mixed|null
     */
    public function getCacheOfToday($request_key, $params, int $member_id, ?callable $callable = null)
    {
        $ttl = $this->getTodayEndTtl();
        return $this->getCache($request_key, $params, $member_id, $callable, $ttl);
    }

    /**
     * 获取缓存
     * @param $request_key
     * @param int $member_id 当前登录用户ID
     * @param callable|null $callable 数据击穿回调，该回调查询数据并返回
     * @param int $ttl
     * @return mixed|null
     */
    public function getCache($request_key, $params, int $member_id, ?callable $callable = null, int $ttl = 60)
    {
        if ($this->isEnabled()) {
            $cache = $this->redis->get($this->getKey($request_key, $params, $member_id));
            if ($cache) {
                return json_decode($cache, true);
            }
        }

        if ($callable) {
            $data = $callable();
            if ($this->isEnabled()) {
                $this->setCache($request_key, $params, $member_id, $data, $ttl);
            }
            return $data;
        }
        return null;
    }

    /**
     * 设置缓存
     * @param $request_key
     * @param int $member_id 当前登录用户ID
     * @param array|null $data
     * @param int $ttl 生命周期
     */
    public function setCache($request_key, $params, int $member_id, ?array $data, int $ttl = 60)
    {
        $this->selectDb();
        $this->redis->set($this->getKey($request_key, $params, $member_id), $this->encode($data), ['ex' => $ttl]);
    }

    /**
     * @return false|int
     */
    private function getTodayEndTtl()
    {
        $now = time();
        return strtotime(date('Y-m-d 23:59:59'), $now) - $now;
    }

    /**
     * key格式统一
     */
    private function getKey($request_key, $params, int $member_id): string
    {
        $key = self::REQUEST_CACHE_KEY . $request_key . ':' . 'member_' . $member_id;
        if (!empty($params)) {
            $key .= ':' . md5(json_encode($params));
        }
        return $key;
    }

    /**
     * 是否启用请求缓存
     * @return void
     */
    public function isEnabled()
    {
        return env('REDIS.ENABLE_REQUEST_CACHE') == true;
    }
}