<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 快递公司信息 */
class RedisExpress extends Redis
{
    protected $db = self::REDIS_EXPRESS_COMPANY_DB;

    /** @var string 快递公司key */
    const EXPRESS_COMPANY_LIST_ID = 'express_list_id';
    const EXPRESS_COMPANY_LIST_QDB = 'express_list_qdb';

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
        $data_id = [];
        $data_qbd = [];
        foreach ($company as $value) {
            $data_id[$value['id']] = $this->encode($value);
            $data_qbd[$value['qdb_code']] = $this->encode($value);
        }
        return $this->updateHSet(self::EXPRESS_COMPANY_LIST_ID, $data_id) && $this->updateHSet(self::EXPRESS_COMPANY_LIST_QDB, $data_qbd);
    }

    /**
     * 获取快递公司信息
     * Author: zhouhongcheng
     * datetime 2022/11/20 16:05
     * @method
     * @route
     * @param string $company_id
     * @param string $key
     * @return mixed
     */
    public function getExpressById(string $company_id, string $key = self::EXPRESS_COMPANY_LIST_QDB)
    {
        return json_decode($this->redis->hGet($key, $company_id), true);
    }
}