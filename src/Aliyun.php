<?php

namespace Swg\Composer;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Exception;

/** 阿里云相关操作 */
class Aliyun
{
    /** @var string 人头面 */
    const ID_CARD_FACE = 'face';

    /** @var string 国徽面 */
    const ID_CARD_BACK = 'back';

    /**
     * 阿里云短信发送
     * Author: zhouhongcheng
     * datetime 2022/11/3 15:16
     * @method
     * @route
     * @param string $mobile 手机号
     * @param string $tplCode 短信模板
     * @param array $templateParam 参数
     * @return bool
     */
    public function aliSendMsg(string $mobile, string $tplCode, array $templateParam = []): bool
    {
        $signName = env("ALIYUN.JK_SMS_SIGN_NAME");
        $accessKeyId = env("ALIYUN.JK_SMS_ACCESS_KEY_ID");
        $accessKeySecret = env("ALIYUN.JK_SMS_ACCESS_KEY_SECRET");

        if (empty($mobile) || empty($tplCode)) return false;
        // require_once 'sdk/aliyunsms/vendor/autoload.php';
        require_once root_path() . 'vendor/swg/composer/sdk/aliyunsms/vendor/autoload.php';
        Config::load();
        if (empty($accessKeyId) || empty($accessKeySecret)) return false;
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
        if ($result['Message'] !== 'OK') return false;
        return true;
    }

    /**
     * 身份证识别
     * Author: lvg
     * datetime 2022/11/3 15:44
     * @method
     * @route
     * @param string $img_path 图片地址
     * @param string $side 身份证正反
     * @param bool $is_local 是否本地图片
     * @return array
     */
    public function ocrIdCard(string $img_path, string $side = self::ID_CARD_FACE, bool $is_local = true): array
    {
        try {
            $url = "https://cardnumber.market.alicloudapi.com/rest/160601/ocr/ocr_idcard.json";
            $appcode = env("ALIYUN.DR_OCR_ID_CARD_CODE");
            // 请求头
            $header = [
                'Authorization:APPCODE ' . $appcode,
                'Content-Type:application/json; charset=UTF-8',
            ];
            // 获取图片的base64
            if ($is_local) {
                $image_data = Common::imageToBase64($img_path, $is_local);
                if (!$image_data) throw new Exception('图片转换失败');
            } else {
                $image_data = $img_path;
            }
            // 请求数据
            $data = [
                'image'     => $image_data,
                'configure' => json_encode(['side' => $side])
            ];
            // 执行请求
            $res = json_decode(Common::curlPost($url, json_encode($data), $header), true);
            if ($res && is_array($res) && !empty($res['success'])) {
                return [
                    'code' => 1,
                    'msg'  => 'OK',
                    'data' => $res
                ];
            } else {
                throw new Exception('识别失败');
            }
        } catch (Exception $exception) {
            return [
                'code' => 0,
                'msg'  => $exception->getMessage(),
                'data' => []
            ];
        }

    }

    /**
     * 营业执照识别
     * Author: lvg
     * datetime 2022/11/3 15:50
     * @method
     * @route
     * @param string $img_path 图片地址
     * @param bool $is_local 是否本地图片
     * @return array
     */
    public function ocrBusinessLicense(string $img_path, bool $is_local = true): array
    {
        try {
            $url = 'https://bizlicense.market.alicloudapi.com/rest/160601/ocr/ocr_business_license.json';
            $appcode = env("ALIYUN.DR_OCR_ID_CARD_CODE");
            // 请求头
            $header = [
                'Authorization:APPCODE ' . $appcode,
                'Content-Type:application/json; charset=UTF-8',
            ];
            // 获取图片的base64
            if ($is_local) {
                $image_data = Common::imageToBase64($img_path, $is_local);
                if (!$image_data) throw new Exception('图片转换失败');
            } else {
                $image_data = $img_path;
            }
            $res = json_decode(Common::curlPost($url, json_encode(['image' => $image_data]), $header), true);
            if ($res && is_array($res) && !empty($res['success'])) {
                return [
                    'code' => 1,
                    'msg'  => 'Success',
                    'data' => $res
                ];
            } else {
                throw new Exception('识别失败');
            }
        } catch (Exception $exception) {
            return [
                'code' => 0,
                'msg'  => $exception->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * 银行开户照识别
     * Author: zhouhongcheng
     * datetime 2022/11/9 14:58
     * @method
     * @route
     * @param string $img_path 网络图片链接/暂未支持本地图片
     * @return array
     */
    public function ocrBankAccount(string $img_path)
    {
        //API产品路径
        $url = "http://blisence.market.alicloudapi.com/ai_market/ai_ocr_universal/internet_pub/v1";
        //阿里云APPCODE
        $appcode = env("ALIYUN.DR_OCR_ID_CARD_CODE");
        $headers[] = "Authorization:APPCODE " . $appcode;
        //根据API的要求，定义相对应的Content-Type
        $headers[] = "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8";
        #IMAGE_TYPE内容数据类型，如：0，则表示BASE64编码；1，则表示图像文件URL链接

        #启用URL方式进行识别
        #内容数据类型是图像文件URL链接
        $IMAGE = $img_path;
        $IMAGE = urlencode($IMAGE);
        $IMAGE_TYPE = "1";

        $bodys = "IMAGE=" . $IMAGE . "&IMAGE_TYPE=" . $IMAGE_TYPE;
        $res = Common::curlPost($url, $bodys, $headers);
        $res = json_Decode($res, true);
        return [
            'code' => 1,
            'msg'  => 'Success',
            'data' => $res
        ];
    }
}