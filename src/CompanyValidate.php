<?php

namespace Swg\Composer;

class CompanyValidate
{
    protected $APP_ID = null;
    protected $APP_SECURITY = null;

    protected $sign;

    public function __construct()
    {
        $this->APP_ID = env('SHUMAI.APP_ID');
        $this->APP_SECURITY = env('SHUMAI.APP_SECURITY');
    }

    /**
     * 组装签名信息
     * @param $time
     * @return string
     */
    public function getSign($time): string
    {
        return md5($this->APP_ID . '&' . $time . '&' . $this->APP_SECURITY);
    }

    /**
     * 获取毫秒
     * @return string
     */
    static function getMicrometer(): string
    {
        $time_list = explode(' ', microtime());
        return bcmul(bcadd($time_list[1], bcadd($time_list[0], 0, 3), 3), 1000, 0);
    }

    /**
     * 模糊搜索公司列表
     * Author: lvg
     * datetime 2022/11/22 14:17
     * 文档地址：https://www.tianyandata.cn/productDetail/83
     * @param string|null $company_name 公司名称
     * @param int $limit 没页数量 最高10 官方规定
     * @param int $page 默认1
     * @return false|mixed
     */
    public function getCompanyInfoByVagueName(string $company_name = null, int $limit = 10, int $page = 1)
    {
        if ($limit>10){
            $limit=10;
        }
        if (!$company_name) {
            return false;
        }
        $time = self::getMicrometer();
        $parma = [
            'appid'     => $this->APP_ID,
            'timestamp' => $time,
            'sign'      => $this->getSign($time),
            'keyword'   => $company_name,
            'pageNo'    => $page,
            'pageSize'  => $limit,
        ];
        $url = 'https://api.shumaidata.com/v4/business3/get' . '?' . http_build_query($parma);
        $data = json_decode(Common::curlGet($url), true);
        if ($data && is_array($data) && $data['success']) {
            return $data['data']['data'];
        }
        return false;
    }


    /**
     * 获取公司的详细信息
     * Author: lvg
     * datetime 2022/11/22 14:17
     * @param string|null $company_name
     * @return false|mixed
     */
    public function getCompanyInfo(string $company_name = null)
    {
        if (!$company_name) {
            return false;
        }
        $time = self::getMicrometer();
        $parma = [
            'appid'     => $this->APP_ID,
            'timestamp' => $time,
            'sign'      => $this->getSign($time),
            'keyword'   => $company_name,
        ];
        $url = 'https://api.shumaidata.com/v4/business2/get' . '?' . http_build_query($parma);
        $data = json_decode(Common::curlGet($url), true);
        if ($data && is_array($data) && $data['success']) {
            return $data['data']['data'];
        }
        return false;
    }
}