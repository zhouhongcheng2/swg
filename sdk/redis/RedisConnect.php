<?php
namespace Swg\Redis;

/** Redis链接 */
final class RedisConnect
{
    private static $instance;
    private function __construct()
    {

    }

    /**
     * 链接redis
     * Author: zhouhongcheng
     * datetime 2022/11/4 16:35
     * @method
     * @route
     * @return false|\Redis
     */
    public static function connectRedis()
    {
        try {
            if (!self::$instance instanceof \Redis) {
                $host = 'r-bp1sro7jzd202d40onpd.redis.rds.aliyuncs.com';
                $port = 6379;
                $timeout = 20;
                $redis = new \Redis();
                $redis->connect($host, $port, $timeout);
                $redis->auth('csNE5u4k8XYX2');
                self::$instance = $redis;
            }
        } catch (\RedisException $e) {
            return false;
        }
        return self::$instance;
    }
}