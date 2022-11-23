<?php

namespace Swg\Composer;

use AdaPaySdk\Payment;
use AdaPaySdk\Member;
use AdaPaySdk\SettleAccount;
use AdaPaySdk\CorpMember;
use AdaPaySdk\PaymentConfirm;
use AdaPaySdk\Refund;
use AdaPaySdk\AdapayTools;
use AdaPaySdk\PaymentReverse;

/** 汇付天下支付模块 */
class Adapay
{
    /** @var string Adapay应用的app_id */
    private $app_id;

    /** @var array 请求参数 */
    private $ada_data;

    public function __construct()
    {
        $this->app_id = 'app_d7954d45-b9dd-4fad-8174-e4baa9276df7';
        # 加载SDK需要的文件
        include_once dirname(__FILE__) . "/../sdk/pay/adapay/AdapaySdk/init.php";
        # 加载商户的配置文件
        include_once dirname(__FILE__) . "/../sdk/pay/adapay/config.php";
        $this->ada_data['app_id'] = $this->app_id;
    }

    /**
     * 创建分账对象 Member对象 （针对个人分账）
     * Author: zhouhongcheng
     * datetime 2022/11/8 11:18
     * @method
     * @route
     * @param int $member_id B端用户编号
     * @param string $nickname 用户昵称
     * @return array
     */
    public function createAdaMember(int $member_id, string $nickname)
    {
        # 初始化用户对象类
        $member_create = new Member();
        $this->ada_data['member_id'] = 'P_' . date("Ymd") . '_' . str_pad($member_id, 7, 0, STR_PAD_LEFT);
        $this->ada_data['nickname'] = $nickname;
        $member_create->create($this->ada_data);
        if ($member_create->isError()) {
            return ['res' => false, 'msg' => "创建用户对象错误:" . $member_create->result['error_msg'] . '【' . $this->ada_data['member_id'] . '】'];
        }
        return ['res' => true, 'data' => ['member_id' => $this->ada_data['member_id']]];
    }

    /**
     * 创建分账对象 Company对象 （针对企业分账）
     * Author: zhouhongcheng
     * datetime 2022/11/8 18:59
     * @method
     * @route
     * @param int $member_id B端用户编号
     * @param string $company_name 企业名称
     * @param string $prov_code 省份编码
     * @param string $area_code 地区编码
     * @param string $social_credit_code 统一社会信用码
     * @param string $social_credit_code_expires 统一社会信用证有效期
     * @param string $business_scope 经营范围
     * @param string $legal_person 法人姓名
     * @param string $legal_cert_id 法人身份证号码
     * @param string $legal_cert_id_expires 法人身份证有效期
     * @param string $legal_mp 法人手机号
     * @param string $address 企业地址
     * @param string $bank_code 银行代码
     * @param string $card_no 银行卡号，如果需要自动开结算账户，本字段必填
     * @param string $card_name 银行卡对应的户名，如果需要自动开结算账户，本字段必填；若银行账户类型是对公，必须与企业名称一致
     * @param string $notify_url 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * @param string $file_real_path 上传附件，传入的中文文件名称为 UTF-8 字符集 URLEncode 编码后的字符串。内容须包含三证合一证件照、法人身份证正面照、法人身份证反面照、开户银行许可证照
     * @return array
     */
    public function createAdaCompany(int    $member_id, string $company_name, string $prov_code, string $area_code, string $social_credit_code,
                                     string $social_credit_code_expires, string $business_scope, string $legal_person, string $legal_cert_id,
                                     string $legal_cert_id_expires, string $legal_mp, string $address, string $bank_code, string $card_no, string $card_name,
                                     string $notify_url, string $file_real_path)
    {
        $corp_member = new CorpMember();
        $this->ada_data['member_id'] = 'C_' . date("Ymd") . '_' . str_pad($member_id, 7, 0, STR_PAD_LEFT);
        $this->ada_data['order_no'] = 'c' . date("Ymd") . $member_id . mt_rand(1000, 9999);//请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
        $this->ada_data['name'] = $company_name;//企业名称
        $this->ada_data['prov_code'] = $prov_code;//省份编码
        $this->ada_data['area_code'] = $area_code;//地区编码
        $this->ada_data['social_credit_code'] = $social_credit_code;//统一社会信用码
        $this->ada_data['social_credit_code_expires'] = $social_credit_code_expires;//统一社会信用证有效期
        $this->ada_data['business_scope'] = $business_scope;//经营范围
        $this->ada_data['legal_person'] = $legal_person;//法人姓名
        $this->ada_data['legal_cert_id'] = $legal_cert_id;//法人身份证号码
        $this->ada_data['legal_cert_id_expires'] = $legal_cert_id_expires;//法人身份证有效期
        $this->ada_data['legal_mp'] = $legal_mp;//法人手机号
        $this->ada_data['address'] = $address;//企业地址
        $this->ada_data['bank_code'] = $bank_code;//银行代码
        $this->ada_data['bank_acct_type'] = 1;//银行账户类型：1-对公；2-对私，如果需要自动开结算账户，本字段必填
        $this->ada_data['card_no'] = $card_no;//银行卡号，如果需要自动开结算账户，本字段必填
        $this->ada_data['card_name'] = $card_name;//银行卡对应的户名，如果需要自动开结算账户，本字段必填；若银行账户类型是对公，必须与企业名称一致
        $this->ada_data['notify_url'] = $notify_url;//异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
        $this->ada_data['attach_file'] = new \CURLFile($file_real_path);//附件
        $corp_member->create($this->ada_data);
        if ($corp_member->isError()) {
            return ['res' => false, 'msg' => "创建企业对象错误:" . $corp_member->result['error_msg'] . '【' . $this->ada_data['member_id'] . '】'];
        }
        return ['res' => true, 'data' => ['member_id' => $this->ada_data['member_id']]];
    }

    /**
     * 创建结算账户对象【个人】
     * Author: zhouhongcheng
     * datetime 2022/11/8 15:58
     * @method
     * @route
     * @param string $ada_member_id ada用户编号
     * @param string $card_id 银行卡号
     * @param string $card_name 姓名
     * @param string $cert_id 身份证号
     * @param string $tel_no 手机号
     * @return array
     */
    public function createMemberSettleAccount(string $ada_member_id, string $card_id, string $card_name, string $cert_id, string $tel_no)
    {
        # 初始化结算账户对象类
        $account = new SettleAccount();
        $this->ada_data['member_id'] = $ada_member_id;
        $this->ada_data['channel'] = 'bank_account';
        //对私
        $this->ada_data['account_info'] = [
            'card_id'        => $card_id,//银行卡号
            'card_name'      => $card_name,//银行卡对应的户名
            'cert_id'        => $cert_id,//身份证
            'cert_type'      => '00',
            'tel_no'         => $tel_no,//手机号
            'bank_acct_type' => 2,//银行账户类型：1-对公；2-对私
        ];
        # 创建结算账户
        $account->create($this->ada_data);
        if ($account->isError()) {
            return ['res' => false, 'msg' => "创建个人结算账户对象错误：" . $account->result['error_msg'] . '【' . $ada_member_id . '】'];
        }
        return ['res' => true, 'data' => []];
    }

    /**
     * 创建结算账户对象【企业】
     * Author: zhouhongcheng
     * datetime 2022/11/8 19:08
     * @method
     * @route
     * @param string $ada_member_id ada用户编号
     * @param string $card_id 银行卡号
     * @param string $card_name 银行卡对应的户名
     * @param string $tel_no 手机号
     * @param string $bank_code 银行编码
     * @param string $prov_code 银行账户开户银行所在省份编码
     * @param string $area_code 银行账户开户银行所在地区编码
     * @return array
     */
    public function createCompanySettleAccount(string $ada_member_id, string $card_id, string $card_name, string $tel_no,
                                               string $bank_code, string $prov_code, string $area_code)
    {
        # 初始化结算账户对象类
        $account = new SettleAccount();
        $this->ada_data['member_id'] = $ada_member_id;
        $this->ada_data['channel'] = 'bank_account';
        //对私
        $this->ada_data['account_info'] = [
            'card_id'        => $card_id,//银行卡号
            'card_name'      => $card_name,//银行卡对应的户名
            'tel_no'         => $tel_no,//手机号
            'bank_code'      => $bank_code,//银行编码
            'bank_acct_type' => 1,//银行账户类型：1-对公；2-对私
            'prov_code'      => $prov_code,//银行账户开户银行所在省份编码
            'area_code'      => $area_code,//银行账户开户银行所在地区编码
        ];
        # 创建结算账户
        $account->create($this->ada_data);
        if ($account->isError()) {
            return ['res' => false, 'msg' => "创建企业结算账户对象错误：" . $account->result['error_msg'] . "【" . $ada_member_id . "】"];
        }
        return ['res' => true, 'data' => []];
    }

    /**
     * 调用ada支付【延时分账】-订单支付接口
     * 注意：接口返回的【Adapay生成的支付对象id】 id字段一定要记录下来
     * Author: zhouhongcheng
     * datetime 2022/11/10 14:20
     * @method
     * @route
     * @param string $order_sn 订单号
     * @param string $pay_amt 支付金额 单位分
     * @param string $goods_title 支付标题
     * @param string $goods_desc 支付描述
     * @param string $description 其它描述
     * @param string $open_id 微信openid
     * @param string $notify_url 异步回调地址 URL 上请勿附带参数
     * @param int $time_expire 订单失效时间 单位s
     * @param int $device_type 设备类型，1 :手机， 2 :平板， 3:手表， 4:PC
     * @return array
     */
    public function createPayDelayed(string $order_sn, string $pay_amt, string $goods_title, string $goods_desc,
                                     string $open_id, string $notify_url, string $description = '', int $time_expire = 90,
                                     int    $device_type = 1)
    {
        if ($pay_amt <= 0) {
            return ['res' => false, 'msg' => "支付金额异常"];
        }

        # 初始化支付类
        $payment = new Payment();
        $this->ada_data['order_no'] = $order_sn;
        $this->ada_data['pay_channel'] = 'wx_lite';//微信小程序支付
        $this->ada_data['pay_amt'] = bcdiv($pay_amt, 100, 2);//交易金额，必须大于0，保留两位小数点，如0.10、100.05等
        $this->ada_data['pay_mode'] = 'delay';//支付模式，delay- 延时分账模式（值为 delay 时，div_members 字段必须为空）
        $this->ada_data['goods_title'] = $goods_title;//商品标题
        $this->ada_data['goods_desc'] = mb_substr($goods_desc, 0, 40, 'UTF-8');//商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
        $this->ada_data['description'] = $description;//订单附加说明
        $this->ada_data['time_expire'] = date("YmdHis", bcadd(time(), $time_expire));//订单失效时间
        $this->ada_data['device_info'] = [
            'device_type' => $device_type,//设备类型，1 :手机， 2 :平板， 3:手表， 4:PC
            'device_ip'   => request()->ip()//交易设备所在的公网 IP
        ];//前端设备信息
        $this->ada_data['expend'] = [
            'open_id' => $open_id
        ];//支付渠道额外参数，JSON格式
        $this->ada_data['notify_url'] = $notify_url;//异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
        $payment->create($this->ada_data);
        if ($payment->isError()) {
            return ['res' => false, 'msg' => "调用支付异常：" . $payment->result['error_msg'] . "【" . $order_sn . "】"];
        }
        return ['res' => true, 'data' => ['payment' => $payment->result]];
    }

    /**
     * 创建支付确认对象【确认清分】
     * Author: zhouhongcheng
     * datetime 2022/11/10 15:08
     * @method
     * @route
     * @param string $adapay_order_sn Adapay生成的支付对象id
     * @param string $order_sn 清分订单号
     * @param string $confirm_amt 确认清分总金额 单位分
     * @param array $payment_company 清分企业[[ 'member_id' => '企业编号','amount'=>'清分金额(单位分)']]
     * @param int $charge 手续费千分比:eg：6=6%%
     * @param string $company_id 手续费收款企业
     * @param string $description 清分备注
     * @return array|void
     */
    public function createPaymentConfirm(string $adapay_order_sn, string $order_sn, string $confirm_amt, array $payment_company,
                                         int    $charge = 6, string $company_id = '0', string $description = '')
    {
        if ($confirm_amt <= 0) {
            return ['res' => false, 'msg' => "清分金额异常"];
        }
        if (count($payment_company) == 0) {
            return ['res' => false, 'msg' => "清分企业信息丢失"];
        }
        if (count($payment_company) > 6) {
            return ['res' => false, 'msg' => "清分企业不能超过6个"];
        }

        foreach ($payment_company as $key => $value) {
            $payment_company[$key]['fee_flag'] = 'N';
            $payment_company[$key]['amount'] = bcmul($value['amount'], 0.001, 2);
        }
        $payment = new PaymentConfirm();
        unset($this->ada_data['app_id']);
        $charge_money = bcmul($confirm_amt, bcdiv($charge, 10000), 2);//转为百分比 同时元转分
        //山王果技术支持企业，用户收取手续费
        $charge_company = [
            'member_id' => $company_id,//收款企业编号
            'amount'    => $charge_money,//金额
            'fee_flag'  => 'Y',//承担交易所有手续费
        ];
        $this->ada_data['payment_id'] = $adapay_order_sn;//Adapay生成的支付对象id
        $this->ada_data['order_no'] = $order_sn;//请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
        $this->ada_data['confirm_amt'] = $confirm_amt;//确认金额，必须大于0，保留两位小数点，如0.10、100.05等。必须小于等于原支付金额-已确认金额-已撤销金额。
        $this->ada_data['description'] = $description;//附加说明
        $this->ada_data['div_members'] = [
            array_merge($charge_company, $payment_company)
        ];//分账对象信息列表，一次请求最多仅支持7个分账方
        $payment->create($this->ada_data);
        if ($payment->isError()) {
            return ['res' => false, 'msg' => "调用清分异常：" . $payment->result['error_msg'] . "【" . $adapay_order_sn . "】"];
        }
        return ['res' => true, 'data' => ['payment' => $payment->result]];
    }

    /**
     * 创建退款对象【退款】
     * Author: zhouhongcheng
     * datetime 2022/11/10 15:29
     * @method
     * @route
     * @param string $adapay_order_sn Adapay生成的支付对象id
     * @param string $refund_order_sn 退款订单号
     * @param string $refund_amt 退款金额单位分
     * @param string $reason 退款原因
     * @param int $device_type 设备类型
     * @return array
     */
    public function createOrderRefund(string $adapay_order_sn, string $refund_order_sn, string $refund_amt, string $reason, int $device_type = 4)
    {
        if ($refund_amt <= 0) {
            return ['res' => false, 'msg' => "退款金额异常"];
        }
        # 初始化退款对象
        $refund = new Refund();
        unset($this->ada_data['app_id']);
        $this->ada_data['id'] = $adapay_order_sn;//Adapay生成的支付对象id
        $this->ada_data['refund_order_no'] = $refund_order_sn;//退款订单号
        $this->ada_data['refund_amt'] = bcmul($refund_amt, 0.01, 2);//确认金额，必须大于0，保留两位小数点，如0.10、100.05等。必须小于等于原支付金额-已确认金额-已撤销金额。
        $this->ada_data['reason'] = $reason;//退款描述
        $this->ada_data['device_info'] = [
            'device_type' => $device_type,//设备类型，1 :手机， 2 :平板， 3:手表， 4:PC
            // 'device_ip'   => request()->ip()//交易设备所在的公网 IP
        ];//前端设备信息
        # 发起退款
        $refund->create($this->ada_data);
        if ($refund->isError()) {
            return ['res' => false, 'msg' => "调用退款异常：" . $refund->result['error_msg'] . "【" . $refund_order_sn . "】"];
        }
        return ['res' => true, 'data' => ['payment' => $refund->result]];
    }

    /**
     * 延时分账-订单撤销【延时分账退款】
     * 不用再调用退款接口
     * 需要记录返回的id
     * Author: zhouhongcheng
     * datetime 2022/11/17 11:37
     * @method
     * @route
     * @param string $adapay_order_sn Adapay生成的支付对象id
     * @param string $refund_order_sn 退款订单号
     * @param int $refund_amt 退款金额单位分
     * @param string $reason 退款原因
     * @param string $notify_url 异步通知地址，url为http /https路径，服务器POST回调，URL 上请勿附带参数。
     * @param int $device_type 设备类型
     * @return array
     */
    public function createReverse(string $adapay_order_sn, string $refund_order_sn, int $refund_amt, string $reason, string $notify_url, int $device_type = 1): array
    {
        # 初始化支付类
        $payment = new PaymentReverse();
        $this->ada_data['payment_id'] = $adapay_order_sn;
        $this->ada_data['order_no'] = $refund_order_sn;
        $this->ada_data['reverse_amt'] = bcmul($refund_amt, 0.01, 2);;
        $this->ada_data['notify_url'] = $notify_url;
        $this->ada_data['reason'] = $reason;
        $this->ada_data['device_info'] = [
            'device_type' => $device_type,//设备类型，1 :手机， 2 :平板， 3:手表， 4:PC
            // 'device_ip'   => request()->ip()//交易设备所在的公网 IP
        ];//前端设备信息
        # 发起撤销退款
        $payment->create($this->ada_data);
        # 对支付结果进行处理
        if ($payment->isError()) {
            return ['res' => false, 'msg' => "调用撤销订单异常：" . $payment->result['error_msg'] . "【" . $adapay_order_sn . "】"];
        }
        return ['res' => true, 'data' => ['payment' => $payment->result]];
    }

    /**
     * 延时分账-查询订单撤销【查询延时分账退款状态】
     * Author: zhouhongcheng
     * datetime 2022/11/17 13:48
     * @method
     * @route
     * @param $reverse_id
     * @return array
     */
    public function queryReverse($reverse_id): array
    {
        $payment = new PaymentReverse();
        unset($this->ada_data['app_id']);
        $this->ada_data['reverse_id'] = $reverse_id;
        $payment->query($this->ada_data);

        # 对支付结果进行处理
        if ($payment->isError()) {
            return ['res' => false, 'msg' => "调用撤销订单异常：" . $payment->result['error_msg'] . "【" . $reverse_id . "】"];
        }
        return ['res' => true, 'data' => ['payment' => $payment->result]];
    }

    /**
     * 签名验证
     * Author: zhouhongcheng
     * datetime 2022/11/17 11:13
     * @method
     * @route
     * @param string $post_data_str post返回的参数
     * @param string $post_sign_str 签名
     * @return array
     */
    public function callBack(string $post_data_str, string $post_sign_str): array
    {
        $adapay_tools = new AdapayTools();
        # 先校验签名和返回的数据的签名的数据是否一致
        $sign_flag = $adapay_tools->verifySign($post_data_str, $post_sign_str);
        if ($sign_flag) {
            return ['res' => true];
        } else {
            return ['res' => false, 'msg' => '签名验证失败'];
        }
    }

    /**
     * 主动查询支付结果
     * Author: zhouhongcheng
     * datetime 2022/11/10 16:25
     * @method
     * @route
     * @param string $adapay_order_sn Adapay生成的支付对象id
     * @return array|bool[]
     */
    public function queryPaymentStatus(string $adapay_order_sn)
    {
        # 初始化支付类
        $payment = new Payment();
        # 查询设置
        unset($this->ada_data['app_id']);
        $this->ada_data['payment_id'] = $adapay_order_sn;
        # 发起查询
        $payment->query($this->ada_data);

        if ($payment->isError()) {
            return ['res' => false, 'msg' => "查询支付信息异常：" . $payment->result['error_msg'] . "【" . $adapay_order_sn . "】"];
        }
        if (!empty($payment->result['status']) && $payment->result['status'] == 'succeeded') {
            return ['res' => true];
        }
        return ['res' => false, 'msg' => "查询支付信息失败：" . $payment->result['error_msg'] . "【" . $adapay_order_sn . "】"];
    }

    /**
     * 查询用户对象【用户】
     * Author: zhouhongcheng
     * datetime 2022/11/11 10:23
     * @method
     * @route
     * @param string $ada_member_id 汇付天下支付平台用户编号
     * @return array|bool[]
     */
    public function queryMemberStatus(string $ada_member_id)
    {
        # 初始化用户对象类
        $member = new Member();
        $this->ada_data['member_id'] = $ada_member_id;
        $member->query($this->ada_data);
        if ($member->isError()) {
            return ['res' => false, 'msg' => "查询用户信息异常：" . $member->result['error_msg'] . "【" . $ada_member_id . "】"];
        }
        return ['res' => true];
    }

    /**
     * 查询企业用户对象【企业用户】
     * Author: zhouhongcheng
     * datetime 2022/11/11 10:30
     * @method
     * @route
     * @param string $ada_member_id
     * @return array|bool[]
     */
    public function queryCompanyMemberStatus(string $ada_member_id)
    {
        # 初始化用户对象类
        $corp_member = new CorpMember();
        $this->ada_data['member_id'] = $ada_member_id;
        $corp_member->query($this->ada_data);
        if ($corp_member->isError()) {
            return ['res' => false, 'msg' => "查询企业用户信息异常：" . $corp_member->result['error_msg'] . "【" . $ada_member_id . "】"];
        }
        return ['res' => true];
    }

    /**
     * 查询结算信息
     * Author: zhouhongcheng
     * datetime 2022/11/11 10:37
     * @method
     * @route
     * @param string $ada_member_id 汇付天下支付平台用户编号
     * @param string $settle_account_id 由 Adapay 生成的结算账户对象 id
     * @return array|bool[]
     */
    public function querySettleAccountStatus(string $ada_member_id, string $settle_account_id)
    {
        # 初始化结算账户对象类
        $account = new SettleAccount();
        $this->ada_data['member_id'] = $ada_member_id;
        $this->ada_data['settle_account_id'] = $settle_account_id;
        $account->query($this->ada_data);
        if ($account->isError()) {
            return ['res' => false, 'msg' => "查询结算账户信息异常：" . $account->result['error_msg'] . "【" . $settle_account_id . "】"];
        }
        return ['res' => true];
    }
}