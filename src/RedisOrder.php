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
    /** @var string 订单收货 */
    const REDIS_CUSTOMER_ORDER_GET_PRODUCT_KEY = 'customer_order_get_product';

    /** @var string 自动勾单 队列 */
    const REDIS_AUTO_TICK_ORDER_LIST_KEY = 'auto_tick_order_list';
    /** @var string 自动勾单加入队列的 最大快递完成时间 */
    const REDIS_AUTO_TICK_ORDER_MAX_TIME = 'auto_tick_order_max_time';

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
     * 追加自动勾单子订单
     * Author: zhouhongcheng
     * datetime 2022/11/24 13:51
     * @param string $son_order_sn 子订单号
     * @return false|int
     */
    public function pushAutoTickOrder(string $son_order_sn)
    {
        return $this->redis->lpush(self::REDIS_AUTO_TICK_ORDER_LIST_KEY, $son_order_sn);
    }

    /**
     * 设置 自动勾单 入队列的最后时间
     * Author: zhouhongcheng
     * datetime 2022/11/24 13:55
     * @param array $data ['date'=>'当日时间戳','last_time'=>'当前时间戳']
     * @return bool
     */
    public function setAutoTickLastTime(array $data): bool
    {
        return $this->redis->set(self::REDIS_AUTO_TICK_ORDER_MAX_TIME, json_encode($data));
    }

    /**
     * 获取 自动勾单 入队列的最后时间
     * Author: zhouhongcheng
     * datetime 2022/11/24 14:01
     * @return mixed
     */
    public function getAutoTickLastTime()
    {
        return json_decode($this->redis->get(self::REDIS_AUTO_TICK_ORDER_MAX_TIME), true);
    }

    /**
     * 追加C端订单
     * Author: zhouhongcheng
     * datetime 2022/11/11 13:49
     * @param string $order_sn 订单号
     * @return false|int
     */
    public function pushCustomerOrder(string $order_sn)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_KEY, $order_sn);
    }

    /**
     * 取出并删除C端订单列表中最后一个元素
     * Author: zhouhongcheng
     * datetime 2022/11/11 14:21
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
     * @param array $data
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefund(array $data)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_KEY, json_encode($data));
    }

    /**
     * C端订单退款申请驳回写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param array $data
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefundReject(array $data)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_REJECT_KEY, json_encode($data));
    }

    /**
     * C端订单退款成功写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param array $data
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderRefundSuccess(array $data)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_REFUND_SUCCESS_KEY, json_encode($data));
    }

    /**
     * C端修改地址成功写入订单
     * Author: lvg
     * datetime 2022/11/17 10:25
     * @param array $data 写入数据
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderUpdateAddress(array $data)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_UPDATE_ADDRESS_KEY, json_encode($data));
    }


    /**
     * 订单收货同步B端   Redis
     * Author: lvg
     * datetime 2022/11/22 18:57
     * @param array $data
     * @return false|int|\Redis
     * @throws RedisException
     */
    public function pushCustomerOrderGetProduct(array $data)
    {
        return $this->redis->lpush(self::REDIS_CUSTOMER_ORDER_GET_PRODUCT_KEY, json_encode($data));
    }
}