<?php

namespace Swg\Composer;

use app\exception\BusinessException;
use app\exception\CodeResponse;
use app\model\order\ShopOrder;
use app\model\order\ShopOrderProduct;
use app\service\ProductService;
use app\utility\TransUtil;
use think\Exception;


/**
 * 企德宝处理
 */
class Qdb
{
    const SHOP_ID = 1000350000000337;

    /** @var int 未审核 */
    const ORDER_STATUS_AUDIT_NO = 0;
    /** @var int 已审核 */
    const ORDER_STATUS_AUDIT_YES = 1;
    /** @var int 待财审 */
    const ORDER_STATUS_AUDIT_FINANCE_WAIT = 2;
    /** @var int 作废 */
    const ORDER_STATUS_AUDIT_ERROR = 9;

    /** @var string 待付款 */
    const TRADE_STATUS_WAIT_PAY = "01";
    /** @var string 待发货 */
    const TRADE_STATUS_WAIT_SEND = "02";
    /** @var string 已发货待签收 */
    const TRADE_STATUS_WAIT_GET = "03";
    /** @var string 已签收 */
    const TRADE_STATUS_RECEIVED = "10";
    /** @var string 交易关闭 */
    const TRADE_STATUS_OFF = "11";

    /** @var string 在线交易 */
    const TYPE_CODE_ONLINE = "2";
    /** @var string 货到付款 */
    const TYPE_CODE_ON_DELIVERY = "1";

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

    /** @var int 支付宝 */
    const PAY_TYPE_ALIPAY = 1;
    /** @var int 微信 */
    const PAY_TYPE_WECHAT = 2;
    /** @var int 线下交易 */
    const PAY_TYPE_OFFLINE = 3;
    /** @var int 线上交易 */
    const PAY_TYPE_ONLINE = 4;
    /** @var int 线上交易 */
    const PAY_TYPE_COLLECTION = 5;

    /** 不推送的产品编号 */
    const NO_PUSH_PRODUCT_IDS = [1, 32];

    protected $app_url = null;
    protected $no_push_product = null;
    protected $no_push_mobile = null;
    protected $push_host = null;
    protected $no_push_message = null;

    public function __construct()
    {
        $this->app_url = env('PYTHON.API_URL');
        $this->no_push_product = explode(',', env('QDB.NO_PUSH_PRODUCT_IDS'));
        $this->no_push_mobile = explode(',', env('QDB.NO_PUSH_MOBILE'));
        $this->push_host = explode(',', env('QDB.PUSH_HOST'));
        $this->no_push_message = explode(',', env('QDB.NO_PUSH_MESSAGE'));
    }

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
        $path = $this->app_url . '/qdb/appointed_query_order/';
        $res = json_decode(Common::curlPost($path, json_encode($data)), true);
        if (!$res || !is_array($res)) {
            return ['code' => false, 'msg' => '获取数据失败'];
        }
        if (!$res['is_success']) {
            return ['code' => false, 'msg' => '数据为空'];
        }
        return $res['data'];
    }

    /**
     * 推送企得宝
     * Author: lvg
     * datetime 2022/11/18 15:08
     * @param mixed $order_sn 订单编号
     * @return bool
     * @throws BusinessException
     */
    public function pushOrder($order_sn): bool
    {
        try {
            // 判断当前域名
            if (!in_array(request()->domain(), $this->push_host)) {
                return true;
            }
            $url = $this->app_url . '/qdb/order_push/';
            $qdb_data = [];
            // 获取订单信息
            $order_product_info = ShopOrderProduct::getInfoByOrderSn($order_sn);
            if (!$order_product_info) {
                throw new BusinessException(CodeResponse::FAILURE, '订单信息不存在');
            }
            $order = $order_product_info['shopOrder'];
            // 未支付订单不推送
            if (!$order['pay_time']) {
                return true;
            }
            // 某些手机号不推送
            if (in_array($order['receiver_mobile'], $this->no_push_mobile)) {
                return true;
            }
            //部分用户备注不推erp
            if (in_array($order['message'], $this->no_push_message)) {
                return true;
            }
            // 收货人 地址 留言一定要过滤表情
            $qdb_data['buyerMessage'] = TransUtil::filterEmoji($order['message']);
            $qdb_data['receiverName'] = TransUtil::filterEmoji($order['receiver_name']);// 收货人姓名
            $qdb_data['tradeNo'] = $order_product_info['son_order_sn'];// 订单编号
            $qdb_data['platformRefundStatus'] = self::REFUND_STATUS_NO;
            switch ($order_product_info['order_status']) {
                case 0:
                    $qdb_data['platformTradeStatus'] = self::TRADE_STATUS_WAIT_PAY;
                    break;
                case 1:
                    $qdb_data['platformTradeStatus'] = self::TRADE_STATUS_WAIT_SEND;
                    break;
                case 2:
                    $qdb_data['platformTradeStatus'] = self::TRADE_STATUS_WAIT_GET;
                    break;
                case 3:
                    $qdb_data['platformTradeStatus'] = self::TRADE_STATUS_RECEIVED;
                    break;
                default:
                    return true;
            }
            $qdb_data['orderTypeCode'] = self::TYPE_CODE_ONLINE;
            //$qdb_data['postAmount'] = bcadd($order['shipping_money'] / 100, 0, 2);// 运费
            $qdb_data['postAmount'] = 0;// 运费
            $qdb_data['payAmount'] = bcadd($order['order_money'] / 100, 0, 2);// 支付金额
            $qdb_data['receiverMobile'] = $order['receiver_mobile'];// 收货人手机
            $qdb_data['shopId'] = self::SHOP_ID;
            $qdb_data['receiverProvince'] = $order['receiver_province'];
            $qdb_data['receiverCity'] = $order['receiver_city'];
            $qdb_data['receiverDistrict'] = $order['receiver_county'];
            $qdb_data['receiverAddress'] = TransUtil::filterEmoji($order['receiver_address_details']);// 收人地址-小
            if ($order['receiver_town_id']) {
                $qdb_data['receiverAddress'] = $order['receiver_town'] . TransUtil::filterEmoji($order['receiver_address_details']);// 收人地址-小
            }
            // 这里要取出商品的所有数据
            $product = ProductService::new()->getProductById($order_product_info['product_id']);
            $goods_code = $product['goods_code'];// 条形码
            $goods_num = $product['goods_num'];//erp最小单位
            // 购买数量等于erp最小单位*商品总数量
            $quantity = floatval(bcadd($order_product_info['product_num'] * $goods_num, 0, 2));
            // 商品金额除以erp最小单位
            $price = floatval(bcdiv(bcdiv($order_product_info['order_money'], $goods_num), 100));
            // 商品总支付价格减去总运费除以(购买数量乘以erp最小单位)
            $realPrice = floatval(bcadd(($qdb_data['payAmount'] - $qdb_data['postAmount']) / $quantity, 0, 2));
            $qdb_data['ordersGoods'] = [
                [
                    "platformGoodsCode" => $goods_code,//商品条形码
                    "platformGoodsName" => $product['product_name'],// 商品名字
                    "quantity"          => $order_product_info['product_num'],// 购买数量
                    "price"             => $price,// 产品价格
                    "realPrice"         => $realPrice, // 商品总支付价格减去总运费除以(购买数量乘以erp最小单位)
                ]
            ];
            $qdb_data['ordersPay'] = [
                [
                    'payTypeCode' => self::PAY_TYPE_ONLINE,
                    'payNo'       => $order_product_info['son_order_sn'],
                ]
            ];
            $header = [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen(json_encode($qdb_data))
            ];
            $res = Common::curlPost($url, json_encode($qdb_data), $header);
            if (empty($res['is_success']) || $res['is_success'] != 'true') {
                throw new Exception('与企得宝建立连接失败');
            }
            return true;
        } catch (Exception $exception) {
            $robot = [
                ['title' => '企得宝推送异常', 'remark' => $exception->getMessage()],
                ['title' => '订单号', 'remark' => $order_sn],
            ];
            (new WechatRobot())->sendWechatRobotMsg($robot, '企得宝推送异常测试');
            return true;
        }
    }
}