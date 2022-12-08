<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis配置库 */
class RedisConfig extends Redis
{
    protected $db = self::REDIS_CONFIG_DB;
    /** @var string 服务端配置 redis前缀 */
    const REDIS_SERVICE_CONFIG_KEY = 'service';

    /**
     * 设置配置
     * Author: lvg
     * datetime 2022/11/30 13:20
     * @param string $module
     * @param string $name
     * @param array $data
     * @return bool
     */
    public function setConfig(string $module, string $name, array $data): bool
    {
        return $this->setData($module . '_' . $name, $data);
    }

    /**
     * 删除配置
     * Author: lvg
     * datetime 2022/11/30 13:20
     * @param string $module
     * @param string $name
     * @return int
     */
    public function delConfig(string $module, string $name): int
    {
        return $this->delete($module . '_' . $name);
    }

    /**
     * 获取配置
     * Author: lvg
     * datetime 2022/11/30 13:21
     * @param string $module
     * @param string $name
     * @return array|mixed|null
     */
    public function getConfig(string $module, string $name)
    {
        $info = $this->getData($module . '_' . $name);
        switch ($info['type']) {
            case 'json':
            case 'array':
                $data = json_decode($info['value'], true);
                break;
            default:
                $data = $info['value'];
                break;
        }
        return $data;
    }
}