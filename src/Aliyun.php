<?php
namespace Swg\Composer;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;

class Aliyun
{
    public function aliSendMsg($mobile, $tplCode, $tplParam, $signName, $accessKeyId, $accessKeySecret)
    {
        if (empty($mobile) || empty($tplCode)) return false;
        require_once 'sdk/aliyunsms/vendor/autoload.php';
        Config::load();
        if (empty($accessKeyId) || empty($accessKeySecret)) return false;
        $templateParam = $tplParam; //模板变量替换
        //短信模板ID
        $templateCode = $tplCode;
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        // 初始化AcsClient用于发起请求
        $acsClient = new DefaultAcsClient($profile);
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($mobile);
        // 必填，设置签名名称
        $request->setSignName($signName);
        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);
        // 可选，设置模板参数
        if ($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        //返回请求结果
        $result = json_decode(json_encode($acsResponse), true);
        if ($result['Message'] !== 'OK')  return false;
        return true;
    }
}