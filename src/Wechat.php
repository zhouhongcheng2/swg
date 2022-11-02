<?php
namespace Swg\Composer;

use Exception;

/** 微信类相关操作 */
class Wechat
{
    /**
     * 获取微信access_token
     * Author: zhouhongcheng
     * datetime 2022/11/2 16:40
     * @method
     * @route
     * @param string $app_id
     * @param string $app_secret
     * @return false|mixed
     */
    public function getAccessToken(string $app_id,string $app_secret)
    {
        if (!$app_id || !$app_secret) return false;
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .$app_id . '&secret=' . $app_secret;
        $token = Common::curlGet($url);
        if (empty($token)) return false;
        return json_decode($token, true)['access_token'];
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
    public function getOpenIdByCode(string $code,string $app_id,string $app_secret)
    {
        if (!$code) return false;
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $post_data = [
            'appid'     =>  $app_id,
            'secret'    =>  $app_secret,
            'grant_type'    =>  'authorization_code',
            'js_code'    =>  $code,
        ];
        $result = Common::curlPost($url,$post_data);
        $result = json_decode($result,true);
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
     * @param string $invite_code 邀请码
     * @param string $invite_path 默认是主页，页面 page，例如 pages/index/index，根路径前不要填加 /，不能携带参数（参数请放在 scene 字段里），如果不填写这个字段，默认跳主页面。
     * @return false
     * @throws Exception
     */
    public function getWechatQrCode(string $access_token,string $invite_code,string $invite_path = 'pages/store/index')
    {
        if (!$access_token || !$invite_path || !$invite_code) return false;
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';
        $url = $url . '?access_token=' . $access_token;
        $post_data['scene'] = "code=" . $invite_code;
        $post_data['page'] = $invite_path;
        $post_data['is_hyaline'] = true;//背景色透明
        $img = Common::curlPost($url, json_encode($post_data));
        //上传到七牛云
        $qiniu = new \Swg\Composer\Qiniu();
        return $qiniu->uploadQiniu($img,1);
    }
}