<?php
/**
 * @author yyl
 */

namespace Swg\Composer\wx;

use Swg\Composer\exception\WxException;
use Swg\Composer\redis\RedisToken;

/**
 * 企业微信通用操作
 */
class WechatQy
{
    /**
     * 获取token
     * @throws WxException
     */
    public static function getToken()
    {
        $token = RedisToken::getInstance()->getQyWechatToken(env('QY_WECHAT.token_name'));
        if ($token) return $token;

        $data = self::getQyWxAccessToken();
        $token = $data['access_token'];
        $ttl = bcsub($data['expires_in'], 200);//保证每一次的token都在有效期内 将有效期减 200 秒
        RedisToken::getInstance()->setQyWechatToken(env('QY_WECHAT.token_name'), $token, $ttl);
        return $token;
    }

    /**
     * 获取实时企业微信token
     * @return array
     * @throws WxException
     */
    public static function getQyWxAccessToken(): array
    {
        $query = [
            'corpid'     => env('QY_WECHAT.corp_id'),
            'corpsecret' => env('QY_WECHAT.corpsecret'),
        ];
        $url = env('QY_WECHAT.token_url') . '?' . http_build_query($query);
        $contents = file_get_contents($url);
        $token = json_decode($contents, true);
        if (empty($token) || !isset($token['access_token'])) {
            throw new WxException("获取微信企业 token 失败:$contents");
        }
        return $token;
    }
}