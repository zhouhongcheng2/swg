<?php

namespace Swg\Composer;

use AdaPaySdk\Payment;
use AdaPaySdk\Member;
use AdaPaySdk\SettleAccount;
use AdaPaySdk\CorpMember;

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
        $this->ada_data['order_no'] = 'c' . date("Ymd") . $member_id . mt_rand(1000, 999);//请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
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
            return ['res' => false, 'msg' => "创建企业结算账户对象错误：" . $account->result['error_msg'] . '【' . $ada_member_id . '】'];
        }
        return ['res' => true, 'data' => []];
    }

    
}