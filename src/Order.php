<?php

namespace Swg\Composer;

/** 订单类信息处理 */
class Order
{
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
    public function createOrderNumber(string $type = '', int $len = 6)
    {
        $str = '';
        switch ($type) {
            case 'order':
                $str .= 'O';//用户订单
                break;
            case 'warehouse':
                $str .= 'W';//仓库出入库
                break;
            case 'clear':
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
