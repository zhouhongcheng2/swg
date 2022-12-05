<?php

namespace Swg\Composer;

/** 订单类信息处理 */
class Order
{
    /** @var string 用户订单 */
    const TYPE_ORDER = 'O';
    /** @var string 库存日志 */
    const TYPE_WAREHOUSE = 'W';
    /** @var string 清分 */
    const TYPE_CLEAR = 'C';
    /** @var string 其他 */
    const TYPE_OTHER = 'T';


    /**
     * 随机生成订单号
     * 根据不同业务生成不同的订单号，默认随机6位数字，理论上可兼容1s内 10的6次方 个订单号
     * Author: zhouhongcheng
     * datetime 2022/11/2 20:11
     * @method
     * @route
     * @param string $type 订单类型
     * @param int $len 随机字符长度
     * @return string
     */
    public function createOrderNumber(string $type = '', int $len = 6): string
    {
        $str = '';
        switch ($type) {
            case self::TYPE_ORDER:
                $str .= 'O';//用户订单
                break;
            case self::TYPE_WAREHOUSE:
                $str .= 'W';//仓库出入库
                break;
            case self::TYPE_CLEAR:
                $str .= 'C';//清分
                break;
            default:
                $str .= 'T';//other
                break;
        }
        $pool = '0123456789';
        return $str . substr(date("Y"), 2) . date("mdHis") . substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }
}
