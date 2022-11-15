<?php

namespace Swg\Redis;

use Exception;
use Swg\Composer\WechatRobot;

class Redis
{
    public $redis;
    /** @var int 商品库 */
    const REDIS_PRODUCT_DB = 1;

    /** @var int 用户库 */
    const REDIS_MEMBER_DB = 2;

    /** @var int 配置库 */
    const REDIS_CONFIG_DB = 3;

    /** @var int 订单库 */
    const REDIS_ORDER_DB = 4;

    /** @var int 地址库 */
    const REDIS_AREA_DB = 5;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        require_once 'RedisConnect.php';
        $this->redis = RedisConnect::connectRedis();
        if ($this->redis == false) {
            $robot = new WechatRobot();
            $robot->sendWechatRobotMsg([["title" => "redis异常", "remark" => "redis链接异常请管理员及时核查"]], 'Redis链接异常');
            throw new Exception("Redis链接失败");
        }
    }

    /**
     * 该接口是删除老数据重新添加
     */
    protected function updateHSet($key, array $set_list): bool
    {
        $this->redis->del($key);
        return $this->redis->hMSet($key, $set_list);
    }

    /**
     * Author: yyl
     * datetime 2022/11/12 23:56
     * @param $key
     * @param null|array|mixed $item_keys 为null时候返回所有数据
     * @return array|null item_key为null时：[data,data] or 不为null时：[[item_key=>data]]
     * ！！！注意，从本函数取出的数据需要判断是否为空
     */
    protected function getHSetData($key, $item_keys = null): ?array
    {
        if (is_null($item_keys)) {
            $json_array = $this->redis->hVals($key);
            if (empty($json_array)) return null;
            return array_map(function ($val) {
                return json_decode($val, true);
            }, $json_array);
        }

        if (!is_array($item_keys)) {
            $json = $this->redis->hGet($key, $item_keys);
            if (empty($json)) {
                return null;
            }
            return json_decode($json, true);
        }

        $kv_arr = $this->redis->hMGet($key, $item_keys);
        foreach ($kv_arr as $key => &$val) {
            $val = json_decode($val, true);
        }
        return $kv_arr;
    }

    /**
     * json 但不转义 unicode 和斜杠
     */
    protected function encode($data, $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    {
        return json_encode($data, $options);
    }

    /**
     * Author: yyl
     * datetime 2022/11/13 15:41
     * @param int $page
     * @param int $limit
     * @return array
     */
    protected function getStartAndEnd(int $page, int $limit): array
    {
        if ($page <= 0) $page = 1;
        $start = max(($page - 1) * $limit, 0);
        $end = $start + $limit - 1;
        return array($start, $end);
    }
}