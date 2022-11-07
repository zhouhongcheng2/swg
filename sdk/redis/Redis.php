<?php
namespace Swg\Redis;

use Swg\Composer\WechatRobot;

class Redis
{
    public $redis;
    /** @var int 商品库 */
    CONST REDIS_PRODUCT_DB = 1;

    /** @var int 配置库 */
    CONST REDIS_CONFIG_DB = 3;

    /** @var int 配置库 */
    CONST REDIS_AREA_DB = 5;

    public function __construct()
    {
        require_once 'RedisConnect.php';
        $this->redis = RedisConnect::connectRedis();
        if ($this->redis == false){
            $robot = new WechatRobot();
            $robot->sendWechatRobotMsg([["title"=>"redis异常","remark"=>"redis链接异常请管理员及时核查"]],'Redis链接异常');
            throw new Exception("Redis链接失败");
        }
    }
}