<?php
namespace Swg\Composer;

use Swg\Redis\Redis;
// require_once 'sdk/redis/Redis.php';
require_once root_path() .'vendor/swg/composer/sdk/redis/Redis.php';

/** redis配置库 */
class RedisConfig extends Redis
{
    /** @var string B端配置 redis前缀 */
    CONST REDIS_BUSINESS_CONFIG_KEY = 'business_config';

    /** @var string 服务端配置 redis前缀 */
    CONST REDIS_SERVICE_CONFIG_KEY = 'service_config';


    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_CONFIG_DB);
    }

}