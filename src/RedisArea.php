<?php
namespace Swg\Composer;

use Swg\Redis\Redis;
// require_once 'sdk/redis/Redis.php';
require_once root_path() .'vendor/swg/composer/sdk/redis/Redis.php';

/** redis中国地址库 */
class RedisArea extends Redis
{
    /** @var string 省、市、区 键名 */
    CONST REDIS_AREA_PROVINCE_KEY = 'area_province';

    /** @var string 街道键 下划线后拼接 区级编号 */
    CONST REDIS_AREA_STREET_KEY = 'area_street_';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_AREA_DB);
    }

    /**
     * 设置区域信息【省、市、区 存一个字段】
     * 数据格式需和前端沟通，前端怎么展示方便怎么存储
     * Author: zhouhongcheng
     * datetime 2022/11/4 18:29
     * @method
     * @route
     * @param array $data 地址数据
     * @param string $area_key 键
     * @return bool
     */
    public function setAreaData(array $data,string $area_key = self::REDIS_AREA_PROVINCE_KEY)
    {
        return $this->redis->set($area_key,json_encode($data));
    }

    /**
     * 获取区域信息
     * Author: zhouhongcheng
     * datetime 2022/11/4 18:30
     * @method
     * @route
     * @param string $area_key 键
     * @return mixed
     */
    public function getStreetData(string $area_key = self::REDIS_AREA_PROVINCE_KEY)
    {
        return json_decode($this->redis->get($area_key));
    }
}