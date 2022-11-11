<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis中国地址库 */
class RedisArea extends Redis
{
    /** @var string 省份 */
    const REDIS_PROVINCE_KEY = 'address_province';
    /** @var string 市前缀 下划线后拼接 省份id */
    const REDIS_CITY_KEY_PREF = 'address_city_';
    /** @var string 县前缀 下划线后拼接 市id */
    const REDIS_COUNTY_KEY_PREF = 'address_county_';
    /** @var string 省市县树形 */
    const REDIS_ALL_PARENT_KEY = 'address_all_parent';
    /** @var string 街道键 下划线后拼接 县id */
    const REDIS_STREET_KEY_PREF = 'area_street_';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_AREA_DB);
    }


    #=======【 省市县树形 】=====================================

    /**
     * 添加省市县树形
     * @param array $data 树形数组
     */
    public function addAllParentAddress(array $data)
    {
        $this->setAddressData(self::REDIS_ALL_PARENT_KEY, $data);
    }

    /**
     * 获取省市县树结构
     * @return array|null 省市县树形数组
     */
    public function getAllParentAddress(): ?array
    {
        return $this->getAddressData(self::REDIS_ALL_PARENT_KEY);
    }

    #=======【 省 】=====================================

    /**
     * 添加省份信息
     */
    public function addProvince(array $data)
    {
        $this->setAddressData(self::REDIS_PROVINCE_KEY, $data);
    }

    /**
     * 获取所有省份信息
     * @return array
     */
    public function getProvince(): ?array
    {
        return $this->getAddressData(self::REDIS_PROVINCE_KEY);
    }

    #=======【 市 】=====================================

    /**
     * 添加某个省份的城市信息
     * @param int $province_id 省份id
     * @param $data
     */
    public function addCityOfProvince(int $province_id, $data)
    {
        $this->setAddressData(self::REDIS_CITY_KEY_PREF . $province_id, $data);
    }

    /**
     * 获取某个省份的城市信息
     * @param int $province_id 省份id
     * @return array|null 城市列表
     */
    public function getCityOfProvince(int $province_id): ?array
    {
        return $this->getAddressData(self::REDIS_CITY_KEY_PREF . $province_id);
    }

    #=======【 县 】=====================================

    /**
     * 添加某个市的县信息
     * @param int $city_id 市id
     * @param $data
     */
    public function addCountyOfCity(int $city_id, $data)
    {
        $this->setAddressData(self::REDIS_COUNTY_KEY_PREF . $city_id, $data);
    }

    /**
     * 获取某个市的县信息
     * @param int $city_id 市id
     * @return array|null 县区列表
     */
    public function getCountyOfCity(int $city_id): ?array
    {
        return $this->getAddressData(self::REDIS_COUNTY_KEY_PREF . $city_id);
    }


    #=======【 街 】=====================================

    /**
     * 添加某个县的街道
     * @param int $county_id 县id
     * @param array $data
     */
    public function addTownOfCounty(int $county_id, array $data)
    {
        $this->setAddressData(self::REDIS_STREET_KEY_PREF . $county_id, $data);
    }

    /**
     * 获取某个县的街道
     * @param int $county_id 县区id
     * @return array|null 街道列表
     */
    public function getTownOfCounty(int $county_id): ?array
    {
        return $this->getAddressData(self::REDIS_STREET_KEY_PREF . $county_id);
    }

    #=======【 set/get 】=====================================

    public function setAddressData(string $area_key, array $data): bool
    {
        return $this->redis->set($area_key, json_encode($data));
    }

    public function getAddressData(string $area_key)
    {
        return json_decode($this->redis->get($area_key));
    }
}