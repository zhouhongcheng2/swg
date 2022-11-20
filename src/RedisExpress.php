<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

require_once 'sdk/redis/Redis.php';
// require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 快递公司信息 */
class RedisExpress extends Redis
{
    /** @var string 快递公司key */
    const EXPRESS_COMPANY_LIST = 'express_list';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_EXPRESS_COMPANY_DB);
    }

    /**
     * 快递公司信息设置
     * Author: zhouhongcheng
     * datetime 2022/11/20 15:46
     * @method
     * @route
     * @param array $company [['id' => 1, 'company' => '韵达速递', 'code' => 'YD', 'qdb_code' => '07', 'kd100_code' => 'yunda']];
     * @return bool
     */
    public function setExpressList(array $company)
    {
        $data = [];
        foreach ($company as $value) {
            $data[$value['id']] = $this->encode($value);
        }
        return $this->updateHSet(self::EXPRESS_COMPANY_LIST, $data);
    }

    /**
     * 获取快递公司信息
     * Author: zhouhongcheng
     * datetime 2022/11/20 16:05
     * @method
     * @route
     * @param int $company_id
     * @return mixed
     */
    public function getExpressById(int $company_id)
    {
        return json_decode($this->redis->hGet(self::EXPRESS_COMPANY_LIST, $company_id), true);
    }
}