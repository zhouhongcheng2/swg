<?php
/**
 * @author yyl
 */

namespace Swg\Composer\redis;
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';
use Swg\Redis\Redis;

/**
 * Redis Token存储
 * Author: yyl
 * datetime 2022/11/22 12:03
 */
class RedisToken extends Redis
{
    protected $db = self::REDIS_TOKEN_DB;

    /** @var string token key前缀 */
    const TOKEN_PREF = 'token:';
    /** @var string 企业微信token前缀 */
    const TOKEN_WECHAT_QY = 'token:wx:qy:';

    /**
     * 存储token
     * @param $key
     * @param $token
     * @param float|int $ttl 有效时间 单位秒
     */
    public function setQyWechatToken($key, $token, $ttl = 3600 * 24)
    {
        $this->redis->set(self::TOKEN_WECHAT_QY . $key, $token, ['ex'=>$ttl]);
    }

    /**
     * 获取token
     */
    public function getQyWechatToken($key)
    {
        return $this->redis->get(self::TOKEN_WECHAT_QY . $key);
    }
}