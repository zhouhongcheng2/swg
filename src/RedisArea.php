<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis中国地址库 */
class RedisArea extends Redis
{
    /** @var string 省、市、区 键名 */
    const REDIS_AREA_PROVINCE_KEY = 'area_province';
    const REDIS_PROVINCE_KEY = 'address_province';
    const REDIS_CITY_KEY_PREF = 'address_city_';
    const REDIS_COUNTY_KEY_PREF = 'address_county_';
    const REDIS_TOWN_KEY_PREF = 'address_town_';

    /** @var string 街道键 下划线后拼接 区级编号 */
    const REDIS_AREA_STREET_KEY = 'area_street_';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_AREA_DB);
    }

    /**
     * 添加省份信息
     */
    public function addProvince(array $data)
    {
        $this->setAddressData(self::REDIS_PROVINCE_KEY, $data);
    }

    /**
     * 获取省份信息
     * @return array
     */
    public function getProvince(): ?array
    {
        return $this->getAddressData(self::REDIS_PROVINCE_KEY);
    }

    /**
     * 添加某个省份的城市信息
     */
    public function addCityOfProvince($province_id, $data)
    {
        $this->setAddressData(self::REDIS_CITY_KEY_PREF . $province_id, $data);
    }

    /**
     * 获取某个省份的城市信息
     */
    public function getCityOfProvince($province_id)
    {
        $this->getAddressData(self::REDIS_CITY_KEY_PREF . $province_id);
    }

    /**
     * 添加某个市的县信息
     */
    public function addCountyOfCity($city_id, $data)
    {
        $this->setAddressData(self::REDIS_COUNTY_KEY_PREF . $city_id, $data);
    }

    /**
     * 获取某个市的县信息
     */
    public function getCountyOfCity($city_id)
    {
        $this->getAddressData(self::REDIS_COUNTY_KEY_PREF . $city_id);
    }

    /**
     * 添加某个县的街道
     */
    public function addTownOfCounty($county_id, $data)
    {
        $this->setAddressData(self::REDIS_TOWN_KEY_PREF . $county_id, $data);
    }

    /**
     * 获取某个县的街道
     */
    public function getTownOfCounty($county_id)
    {
        $this->getAddressData(self::REDIS_TOWN_KEY_PREF . $county_id);
    }

    /**
     * 设置区域信息【省、市、区 存一个字段】
     * 数据格式需和前端沟通，前端怎么展示方便怎么存储
     * Author: zhouhongcheng
     * datetime 2022/11/4 18:29
     * @param string $area_key 键
     * @param array $data 地址数据
     * @return bool
     */
    public function setAddressData(string $area_key, array $data)
    {
        return $this->redis->set($area_key, json_encode($data));
    }

    /**
     * 获取区域信息
     * Author: zhouhongcheng
     * datetime 2022/11/4 18:30
     * @param string $area_key 键
     * @return mixed
     */
    public function getAddressData(string $area_key)
    {
        return json_decode($this->redis->get($area_key));
    }
}