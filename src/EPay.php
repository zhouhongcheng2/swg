<?php

namespace Swg\Composer;

class EPay
{
    /**
     * 订单退款
     * Author: lvg
     * datetime 2022/11/29 9:43
     * @param string $order_sn 订单号
     * @param int $refund_money 退款金额 分
     * @param string $refund_sn 退款交易单号
     * @param int $order_pay_money 订单支付金额 分
     * @return array|int[]
     */
    public function orderRefund(string $order_sn, int $refund_money, string $refund_sn, int $order_pay_money): array
    {
        $to_member_res = self::ICBCToMember($order_sn, $refund_sn, $order_pay_money, $refund_money);
        if ($to_member_res['code'] != 1) {
            $file_path = root_path() . '/public/logs/ghpay/';
            if (!file_exists($file_path)) {
                mkdir($file_path, 0777, true);
            }
            file_put_contents($file_path . '/ghpay_refund_member_error_' . date("Ym") . '.txt', PHP_EOL . date("Y-m-d H:i:s") . PHP_EOL . $order_sn . '-' . $to_member_res['msg']);
            return ['code' => false, 'msg' => $to_member_res['msg']];
        }
        return ['code' => true];
    }

    /**
     * 执行退款操作
     * Author: lvg
     * datetime 2022/11/29 9:44
     * @param string $order_sn 订单号
     * @param string $refund_sn 退款交易单号
     * @param int $total_amount 退款金额 分
     * @param int $refund_amount 订单支付金额 分
     * @return array|int[]
     */
    static function ICBCToMember(string $order_sn, string $refund_sn, int $total_amount, int $refund_amount): array
    {
        require_once root_path() . 'vendor/swg/composer/sdk/ghzf/ESTApi.php';
        $ESTApi = new \ESTApi("prod"); //正式环境

        $bizRequestContent = [
            "clientIp"     => request()->ip(),
            "mchOrderNo"   => $order_sn,//商户原订单号
            "orderNo"      => "",//E商通原交易订单号
            "mchRefundNo"  => $refund_sn,//商户退款订单号，需保证商户系统唯⼀
            "totalAmount"  => $total_amount,//订单总⾦额（单位：分）
            "refundAmount" => $refund_amount,//退款总⾦额（单位：分）
            "refundDesc"   => "不想要了，退货退钱",//退款描述
        ];
        $data = [
            "appId"             => "10000000000003160144",
            "timestamp"         => date("YmdHis000"),
            "msgId"             => uniqid("", true),
            "signType"          => "RSA2",
            "encryptType"       => "AES",
            "bizRequestContent" => $ESTApi->jsonEncode($bizRequestContent)
        ];

        $res = $ESTApi->refundCreate($data);
        if ($res === false) return ['code' => -1, 'msg' => $ESTApi->getError()];
        $res = json_decode($res, true);
        if ($res['bizResponseContent']['code'] != 'OK') return ['code' => -1, 'msg' => $res['bizResponseContent']['summary']];
        return ['code' => 1];
    }
}