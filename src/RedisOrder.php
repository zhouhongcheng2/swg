<?php

namespace Swg\Composer;

use RedisException;
use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 订单库 */
class RedisOrder extends Redis
{
    /** @var string 退款原因 */
    const REDIS_ORDER_REFUND_OPTION_KEY = 'refund_option';

    /** @var string C端订单 */
    const REDIS_CUSTOMER_ORDER_KEY = 'customer_order';

    /** @var string C端订单退款中 */
    const REDIS_CUSTOMER_ORDER_REFUND_KEY = 'customer_order_refund';
    /** @var string C端订单退款被驳回 */
    const REDIS_CUSTOMER_ORDER_REFUND_REJECT_KEY = 'customer_order_reject';
    /** @var string C端订单退款成功 */
    const REDIS_CUSTOMER_ORDER_REFUND_SUCCESS_KEY = 'customer_order_success';
    /** @var string 订单修改地址 */
    const REDIS_CUSTOMER_ORDER_UPDATE_ADDRESS_KEY = 'customer_order_update_address';


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

    /**
     * 追加C端订单
     * Author: zhouhongcheng
     * datetime 2022/11/11 13:49
     * @method
     * @route
     * @param string $order_sn 订单号
     * @return int
     */
    public function pushCustomerOrder(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_KEY, $order_sn);
    }

    /**
     * 取出并删除C端订单列表中最后一个元素
     * Author: zhouhongcheng
     * datetime 2022/11/11 14:21
     * @method
     * @route
     * @return bool|mixed
     */
    public function getCustomerOrder($key = self::REDIS_CUSTOMER_ORDER_KEY)
    {
        return $this->redis->rPop($key);
    }

    /**
     * C端订单退款申请成功写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param string $order_sn 订单编号
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefund(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_KEY, $order_sn);
    }

    /**
     * C端订单退款申请驳回写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param string $order_sn 订单编号
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefundReject(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_REJECT_KEY, $order_sn);
    }

    /**
     * C端订单退款成功写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param string $order_sn 订单编号
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefundSuccess(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_SUCCESS_KEY, $order_sn);
    }

    /**
     * C端修改地址成功写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param string $order_sn 订单编号
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderUpdateAddress(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_UPDATE_ADDRESS_KEY, $order_sn);
    }
}