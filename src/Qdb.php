<?php

namespace Swg\Composer;

/**
 * 企德宝处理
 */
class Qdb
{

    /** @var int 未审核 */
    const ORDER_STATUS_AUDIT_NO = 0;
    /** @var int 已审核 */
    const ORDER_STATUS_AUDIT_YES = 1;
    /** @var int 待财审 */
    const ORDER_STATUS_AUDIT_FINANCE_WAIT = 2;
    /** @var int 作废 */
    const ORDER_STATUS_AUDIT_ERROR = 9;

    /** @var int 为退款 */
    const REFUND_STATUS_NO = 0;
    /** @var int 全部退款 */
    const REFUND_STATUS_ALL = 1;
    /** @var int 部分退款 */
    const REFUND_STATUS_PARTIAL = 2;
    /** @var int 退款审核中 */
    const REFUND_STATUS_AUDITING = 4;

    /** @var int 未发货 */
    const DELIVERY_STATUS_WAIT_SEND = 0;
    /** @var int 已发货 */
    const DELIVERY_STATUS_WAIT_GET = 1;
    /** @var int 部分发货 */
    const DELIVERY_STATUS_PARTIAL_SEND = 2;
    /** @var int 空 */
    const DELIVERY_STATUS_NONE = 3;
    /** @var int 发货失败 */
    const DELIVERY_STATUS_ERROR = 4;

    /**
     *  查询企德宝订单信息
     * Author: lvg
     * datetime 2022/11/17 14:34
     * @param array $order_list 订单列表，索引数组，内容就是订单号 ["2170166864113745869","1110166864062301347"]
     * @param string $start_time 开始时间  日期格式 2022-11-12 00:00:00
     * @param string $end_time 结束时间 日期格式 2022-11-17 23:59:59
     * @return array|mixed
     */
    public function getOrderInfoByOrderSns(array $order_list, string $start_time, string $end_time)
    {
        $data = [
            'beginDate'   => $start_time,
            'endDate'     => $end_time,
            'tradeNoList' => $order_list,
            'timeType'    => 0,
        ];
        $api = env('PYTHON.API_URL');
        $path = '/qdb/appointed_query_order/';
        $res = json_decode(Common::curlPost($api . $path, json_encode($data)), true);
        if (!$res || !is_array($res)) {
            return ['code' => false, 'msg' => '获取数据失败'];
        }
        if (!$res['is_success']) {
            return ['code' => false, 'msg' => '数据为空'];
        }
        return $res['data'];
    }


}