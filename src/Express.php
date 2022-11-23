<?php

namespace Swg\Composer;

use app\model\order\ShopOrderProduct;

/**
 * 快递信息
 */
class Express
{
    /** @var int 在途中 */
    const STATUS_ON_THE_WAY = 0;
    /** @var int 揽收中 */
    const STATUS_COLLECT = 1;
    /** @var int 疑难件 */
    const STATUS_DIFFICULT = 2;
    /** @var int 已签收 */
    const STATUS_MEMBER_GET = 3;
    /** @var int 用户退签 */
    const STATUS_WITHDRAWAL = 4;
    /** @var int 派件中 */
    const STATUS_DISPATCH = 5;
    /** @var int 清关 */
    const STATUS_CUSTOMS = 8;
    /** @var int 用户拒签 */
    const STATUS_REFUSE_GET = 14;

    protected $app_key = null;
    protected $app_sign = null;
    protected $request_url = null;

    public function __construct()
    {
        $this->app_key = env('EXPRESS.APP_KEY');
        $this->app_sign = env('EXPRESS.APP_SIGN');
        $this->request_url = env('EXPRESS.APP_URL');
    }

    /**
     * 快递订阅
     * Author: zhouhongcheng
     * datetime 2022/11/22 15:09
     * @param string $express_company 物流公司名称
     * @param string $express_number 物流编号
     * @param string $callbackurl 异步请求地址
     * @return bool
     */
    public function subscribeExpress(string $express_company, string $express_number, string $callbackurl): bool
    {
        $param = [
            'company'    => $express_company,
            'number'     => $express_number,
            'key'        => $this->app_key,// 客户授权key
            'parameters' => [
                'callbackurl' => $callbackurl,//回调接口的地址，默认仅支持http，如需兼容https请联系快递100技术人员处理
            ]
        ];
        $post_data = array();
        $post_data["schema"] = 'json';
        $post_data["param"] = json_encode($param);

        foreach ($post_data as $k => $v) {
            $params .= "$k=" . urlencode($v) . "&";     //默认UTF-8编码格式
        }
        $post_data = substr($params, 0, -1);

        $header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $res = json_decode(Common::curlPost($this->request_url, json_encode($post_data), $header), true);
        if (!$res['result']) {
            $exception_data = [
                ['title' => '快递单号', 'remark' => $express_number],
                ['title' => '异常信息', 'remark' => $res['message']]
            ];
            (new WechatRobot())->sendWechatRobotMsg($exception_data, '快递订阅异常');
        }
        return true;
    }

    /**
     * 查询物流信息
     * Author: lvg
     * datetime 2022/11/18 11:03
     * @param string $express_company 物流公司名称
     * @param string $express_number 物流编号
     * @param mixed $mobile 收货人手机号
     * @return array
     */
    public function getExpress(string $express_company, string $express_number, $mobile): array
    {
        $data = [];
        //参数设置
        $data['key'] = $this->app_key;// 客户授权key
        $data['customer'] = $this->app_sign;// 查询公司编号
        $param = [
            'com'   => $express_company,// 快递公司编码
            'num'   => $express_number,// 快递单号
            'phone' => $mobile,// 手机号
        ];
        $data['param'] = json_encode($param);
        $data['sign'] = strtoupper(md5($data['param'] . $data['key'] . $data['customer']));
        $params = '';
        foreach ($data as $k => $v) {
            $params .= "$k=" . urlencode($v) . "&";// 默认UTF-8编码格式
        }
        $data = substr($params, 0, -1);
        $header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $res = json_decode(Common::curlPost($this->request_url . '/query.do', $data, $header), true);
        if (empty($res['data']) || empty($res['state'])) {
            return ['res' => false, 'msg' => '物流信息查询失败', 'data' => []];
        }
        list($order_status, $receive_time) = self::getOrderStatusByExpressState($res['state'], $res['data'][0]);
        $return_data = [
            'list'         => $res['data'],
            'order_status' => $order_status,// 订单状态，已经匹配了订单产品表的发货状态
            'receive_time' => $receive_time,// 收货时间，未收货是0
        ];
        return ['res' => true, 'msg' => 'Success', 'data' => $return_data];
    }

    /**
     * 通过物流状态匹配订单状态
     * Author: lvg
     * datetime 2022/11/18 10:55
     * @param mixed $state 物流状态
     * @param mixed $last_data 物流的最后一条数据
     * @return array
     */
    static function getOrderStatusByExpressState($state, $last_data): array
    {
        $order_status = null;
        $receive_time = 0;
        switch ($state) {
            case self::STATUS_ON_THE_WAY:
                //在途中
            case self::STATUS_COLLECT:
                //揽收中
            case self::STATUS_DIFFICULT:
                //疑难
            case self::STATUS_WITHDRAWAL:
                //已退签
            case self::STATUS_DISPATCH:
                //派件中
            case self::STATUS_CUSTOMS:
                //清关
            case self::STATUS_REFUSE_GET:
                //已拒签
                $order_status = ShopOrderProduct::ORDER_STATUS_TO_BE_RECEIVED;

                break;
            case self::STATUS_MEMBER_GET:
                //已签收
                $order_status = ShopOrderProduct::ORDER_STATUS_FINISHED;
                $receive_time = empty($last_data['time']) ? 0 : $last_data['time'];
                break;
            default:
                break;
        }
        return [$order_status, $receive_time];
    }
}