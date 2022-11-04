<?php
namespace Swg\Composer;

use Swg\Redis\Redis;
require_once 'sdk/redis/Redis.php';

class RedisProduct extends Redis
{
    CONST PRODUCT_DB = 1;
    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::PRODUCT_DB);
    }

    public function getProductList()
    {
        var_dump($this->redis->get('product_list'));
    }
}