<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

require_once 'sdk/redis/Redis.php';
// require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 订单库 */
class RedisOrder extends Redis
{
    /** @var string 退款原因 */
    const REDIS_ORDER_REFUND_OPTION_KEY = 'refund_option';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_ORDER_DB);
    }

    /**
     * 设置退款原因选项
     * Author: zhouhongcheng
     * datetime 2022/11/11 11:39
     * @method
     * @route
     * @param $refund_option ['1'=>'不想要了','2'=>'买错了','3'=>'太过了','4'=>'未按约定时间发货','5'=>'其它']
     * @return bool
     */
    public function setRefundOption($refund_option)
    {
        return $this->redis->set(self::REDIS_ORDER_REFUND_OPTION_KEY, json_encode($refund_option));
    }

    /**
     * 获取退款原因选项
     * Author: zhouhongcheng
     * datetime 2022/11/11 13:12
     * @method
     * @route
     * @return mixed
     */
    public function getRefundOption()
    {
        return json_decode($this->redis->get(self::REDIS_ORDER_REFUND_OPTION_KEY), true);
    }
}