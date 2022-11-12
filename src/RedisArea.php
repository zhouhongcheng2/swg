<?php

namespace Swg\Composer;

require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

use Swg\Redis\Redis;


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
    public function setPCCTree(array $data)
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
    public function setProvince(array $data)
    {
        $set_list = [];
        foreach ($data as $province) {
            $set_list[$province['province_id']] = json_encode($province);
        }
        $this->updateHSet(self::REDIS_PROVINCE_KEY, $set_list);
    }

    /**
     * 获取省份信息
     * @param string|null $code 省份编码；null 获取所有省份
     * @return array
     */
    public function getProvince(string $code = null): ?array
    {
        return $this->getHSetData(self::REDIS_PROVINCE_KEY, $code);
    }



    #=======【 市 】=====================================

    /**
     * 添加某个省份的城市信息
     * @param int $province_id 省份id
     * @param $data
     */
    public function setCityOfProvince(int $province_id, $data)
    {
        $set_list = [];
        foreach ($data as $item) {
            $set_list[$item['city_id']] = json_encode($item);
        }
        $this->updateHSet(self::REDIS_CITY_KEY_PREF . $province_id, $set_list);

    }

    /**
     * 获取某个省份的城市信息
     * @param int $province_id 省份id
     * @return array|null 城市列表
     */
    public function getCityOfProvince(int $province_id, $city_code = null): ?array
    {
        return $this->getHSetData(self::REDIS_CITY_KEY_PREF . $province_id, $city_code);
    }

    #=======【 县 】=====================================

    /**
     * 添加某个市的县信息
     * @param int $city_id 市id
     * @param $data
     */
    public function setCountyOfCity(int $city_id, $data)
    {
        $set_list = [];
        foreach ($data as $item) {
            $set_list[$item['county_id']] = json_encode($item);
        }
        $this->updateHSet(self::REDIS_COUNTY_KEY_PREF . $city_id, $set_list);
    }

    /**
     * 获取某个市的县信息
     * @param int $city_id 市id
     * @return array|null 县区列表
     */
    public function getCountyOfCity(int $city_id, $county_code = null): ?array
    {
        return $this->getHSetData(self::REDIS_COUNTY_KEY_PREF . $city_id, $county_code);
    }

    #=======【 街 】=====================================

    /**
     * 添加某个县的街道
     * @param int $county_id 县id
     * @param array $data
     */
    public function setTownOfCounty(int $county_id, array $data)
    {
        $set_list = [];
        foreach ($data as $item) {
            $set_list[$item['town_id']] = json_encode($item);
        }
        $this->updateHSet(self::REDIS_STREET_KEY_PREF . $county_id, $set_list);
    }

    /**
     * 获取某个县的街道
     * @param int $county_id 县区id
     * @return array|null 街道获取街道列表
     */
    public function getTownOfCounty(int $county_id, $town_code = null): ?array
    {
        return $this->getHSetData(self::REDIS_STREET_KEY_PREF . $county_id, $town_code);
    }

    #=======【 set/get 】=====================================

    private function setAddressData(string $area_key, array $data): bool
    {
        $this->redis->del($area_key);//需要先删除，否则hash set无法覆盖
        return $this->redis->set($area_key, json_encode($data));
    }

    private function getAddressData(string $area_key)
    {
        return json_decode($this->redis->get($area_key));
    }

    private function updateHSet($key, array $set_list): void
    {
        $this->redis->del($key);
        $this->redis->hMSet($key, $set_list);
    }

    private function getHSetData($key, string $code = null): ?array
    {
        if (is_null($code)) {
            $json_array = $this->redis->hVals($key);
            if (empty($json_array)) return null;
            return array_map(function ($val) {
                return json_decode($val, true);
            }, $json_array);
        }

        $json = $this->redis->hGet($key, $code);
        if (empty($json)) {
            return null;
        }
        return json_decode($json, true);
    }
}
