<?php
namespace Swg\Redis;

class Redis
{
    public $redis;
    public function __construct()
    {
        try {
            $host = 'r-bp1sro7jzd202d40onpd.redis.rds.aliyuncs.com';
            $port = 6379;
            $timeout = 20;
            $this->redis = new \Redis();
            $this->redis->connect($host,$port,$timeout);
            $this->redis->auth('csNE5u4k8XYX2');
        } catch (\RedisException $e) {
            return false;
        }
    }

}