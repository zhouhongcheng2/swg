<?php
namespace Swg\Composer;

use Swg\Redis\Redis;
require_once 'sdk/redis/Redis.php';

class RedisConfig extends Redis
{
    CONST CONFIG_DB = 3;
    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::CONFIG_DB);
    }
}