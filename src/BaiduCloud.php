<?php

namespace Swg\Composer;

use app\exception\CodeResponse;
use app\exception\FatalErrorException;
use Symfony\Component\VarDumper\VarDumper;

/**
 * 百度云第三方api
 */
class BaiduCloud
{
    protected $access_token = null;

    public function __construct()
    {
        $this->getAccessToken();
    }

    /**
     * 获取token
     * Author: lvg
     * datetime 2022/11/10 14:27
     * @return void
     */
    public function getAccessToken()
    {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $data = [
            'grant_type'    => 'client_credentials',
            'client_id'     => env('BAIDU.API_KEY'),
            'client_secret' => env('BAIDU.SECRET_KEY'),
        ];
        $res = json_decode(Common::curlPost($url, $data), true);
        if ($res && is_array($res)) {
            $this->access_token = $res['access_token'];
        }
    }


    /**
     * 自动识别地址
     * Author: lvg
     * datetime 2022/11/10 14:34
     * @param string $text 识别内容
     * @return array|false
     * @throws FatalErrorException
     */
    public function getAddress(string $text = '')
    {
        $url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/address';
        if (!$this->access_token) {
            throw new FatalErrorException('地址识别授权失败');
        }
        $url .= '?access_token=' . $this->access_token;
        $data = [
            'text' => self::deleteKeyword($text),
        ];
        $header = [
            'Content-Type:application/json',
        ];
        $res = Common::curlPost($url, json_encode($data), $header, 5, true);
        if ($res['http_code'] != 200) {
            throw new FatalErrorException("地址识别返回状态码(${$res['http_code']})");
        }

        $data_obj = json_decode($res['data'], true);
        if (!$data_obj || !is_array($data_obj)) {
            return false;
        }
        // 对比省市区地址信息
        $return_data = $this->getProvince($data_obj);
        $return_data['mobile'] = $data_obj['phonenum'];
        $return_data['realname'] = $data_obj['person'];
        return $return_data;
    }

    /**
     * 对比省的名字和id
     * Author: lvg
     * datetime 2022/11/10 14:48
     * @param array $address_data 地址数据
     * @return array|false
     */
    public function getProvince(array $address_data)
    {
        $data = [
            'province_id' => null,
            'province'    => null,
            'city_id'     => null,
            'city'        => null,
            'county_id'   => null,
            'county'      => null,
            'town_id'     => null,
            'town'        => null,
        ];
        $province_code = $address_data['province_code'] ? str_pad($address_data['province_code'], 12, 0) : null;
        $city_code = $address_data['city_code'] ? str_pad($address_data['city_code'], 12, 0) : null;
        $county_code = $address_data['county_code'] ? str_pad($address_data['county_code'], 12, 0) : null;
        $town_code = $address_data['town_code'] ? str_pad($address_data['town_code'], 12, 0) : null;
        $province = empty($province_code) ? null : RedisArea::getInstance()->getProvince($province_code);
        if (!$province) {
            return $data;
        }
        $data['province_id'] = $province['id'];
        $data['province'] = $province['name'];

        $city = empty($city_code) ? null : RedisArea::getInstance()->getCityOfProvince($province['id'], $city_code);
        if (!$city) {
            return $data;
        }
        $data['city_id'] = $city['id'];
        $data['city'] = $city['name'];

        $county = empty($county_code) ? null : RedisArea::getInstance()->getCountyOfCity($city['id'], $county_code);
        if (!$county) {
            return $data;
        }
        $data['county_id'] = $county['id'];
        $data['county'] = $county['name'];

        $town = empty($town_code) ? null : RedisArea::getInstance()->getTownOfCounty($county['id'], $town_code);
        if (!$town) {
            return $data;
        }
        $data['town_id'] = $town['id'];
        $data['town'] = $town['name'];

        return $data;
    }


    /**
     * 删除地址匹配的关键字
     * @param $address
     * @return array|string|string[]
     */
    static function deleteKeyword($address)
    {
        $address = str_replace('地址：', '', $address);
        $address = str_replace('地址:', '', $address);
        $address = str_replace('姓名:', '', $address);
        $address = str_replace('姓名：', '', $address);
        $address = str_replace('手机号：', '', $address);
        $address = str_replace('手机号:', '', $address);
        $address = str_replace('收件人：', '', $address);
        $address = str_replace('收件人:', '', $address);
        $address = str_replace('收件地址：', '', $address);
        $address = str_replace('收件地址:', '', $address);
        $address = str_replace('联系人：', '', $address);
        $address = str_replace('联系人:', '', $address);
        $address = str_replace('联系地址：', '', $address);
        return str_replace('联系地址:', '', $address);
    }
}