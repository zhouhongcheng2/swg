<?php

namespace Swg\Composer;

use Exception;
use Swg\wechat\Crypt\WXBizDataCrypt;
use function Webmozart\Assert\Tests\StaticAnalysis\false;

/** 微信类相关操作 */
class Wechat
{
    /** @var string 正式服 */
    const ENV_VERSION_RELEASE = 'release';

    /** @var string 体验版 */
    const ENV_VERSION_TRIAL = 'trial';

    /** @var string 开发版 */
    const ENV_VERSION_DEVELOP = 'develop';

    /**
     * 获取微信access_token
     * Author: zhouhongcheng
     * datetime 2022/11/2 16:40
     * @method
     * @route
     * @return false|bool
     */
    public function getAccessToken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env("WECHAT.DR_APP_ID") . '&secret=' . env("WECHAT.DR_APP_SECRET");
        $token = Common::curlGet($url);
        if (empty($token)) return false;
        return json_decode($token, true);
    }

    /**
     * 根据前端获得的code 置换openid
     * Author: zhouhongcheng
     * datetime 2022/11/2 16:59
     * @method
     * @route
     * @param string $code
     * @param string $app_id
     * @param string $app_secret
     * @return false|mixed
     */
    public function getOpenIdByCode(string $code)
    {
        if (!$code) return false;
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $post_data = [
            'appid'      => env("WECHAT.DR_APP_ID"),
            'secret'     => env("WECHAT.DR_APP_SECRET"),
            'grant_type' => 'authorization_code',
            'js_code'    => $code,
        ];
        $result = Common::curlPost($url, $post_data);
        $result = json_decode($result, true);
        if (isset($result['errcode'])) {
            return false;
        }
        return $result;
    }

    /**
     * 生成微信菊花二维码
     * Author: zhouhongcheng
     * datetime 2022/11/2 17:33
     * @method
     * @route
     * @param string $access_token
     * @param string $scene 自定义参数 eg:code=EB64 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符
     * @param string $invite_path 默认是主页，页面 page，例如 pages/index/index，根路径前不要填加 /，不能携带参数（参数请放在 scene 字段里），如果不填写这个字段，默认跳主页面。
     * @param string $env_version 要打开的小程序版本
     * @return false|string 返回七牛云地址
     * @throws Exception
     */
    public function getWechatQrCode(string $access_token, string $scene, string $invite_path = 'pages/store/index', string $env_version = self::ENV_VERSION_RELEASE)
    {
        if (!$access_token || !$invite_path) return false;
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';
        $url = $url . '?access_token=' . $access_token;
        $post_data['scene'] = $scene;
        $post_data['page'] = $invite_path;
        $post_data['is_hyaline'] = true;//背景色透明
        $post_data['env_version'] = $env_version;//背景色透明
        $img = Common::curlPost($url, json_encode($post_data));
        //上传到七牛云
        $qiniu = new Qiniu();
        return $qiniu->uploadQiniu($img, 1);
    }

    /**
     * 根据code、加密字符串、iv置换手机号
     * Author: zhouhongcheng
     * datetime 2022/11/8 10:24
     * @param string $code
     * @param string $encryptedData
     * @param string $iv
     * @return array|false
     */
    public function getWechatMobile(string $code, string $encryptedData, string $iv)
    {
        // 获取微信openid和session_key
        $data = $this->getOpenIdByCode($code);
        // 置换手机号
        $result = openssl_decrypt(base64_decode($encryptedData), "AES-128-CBC",
            base64_decode($data['session_key']), 1, base64_decode($iv));
        $dataObj = json_decode($result, true);
        if (!$dataObj || !is_array($dataObj)) {
            return false;
        }
        return array_merge($data, $dataObj);
    }

}