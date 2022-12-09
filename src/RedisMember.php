<?php

namespace Swg\Composer;

use Swg\Composer\utility\SwgArray;
use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 用户库 */
class RedisMember extends Redis
{
    protected $db = self::REDIS_MEMBER_DB;

    /** @var string 用户等级名称 */
    const REDIS_MEMBER_LEVEL_NAME_KEY = 'member_level_name';
    const REDIS_C_MEMBER_INFO_PREF = 'c_member_';

    /**
     * 设置用户等级名称
     * Author: zhouhongcheng
     * datetime 2022/11/11 11:12
     * @method
     * @route
     * @param array $member_level_name ['1'=>'用户','2'=>'会员','3'=>'加盟','4'=>'旗舰','5'=>'联创']
     * @return bool
     */
    public function setMemberLevelName(array $member_level_name): bool
    {
        return $this->redis->set(self::REDIS_MEMBER_LEVEL_NAME_KEY, json_encode($member_level_name));
    }

    /**
     * 获取用户等级名称
     * Author: zhouhongcheng
     * datetime 2022/11/11 11:14
     * @method
     * @return mixed
     */
    public function getMemberLevelName()
    {
        return json_decode($this->redis->get(self::REDIS_MEMBER_LEVEL_NAME_KEY), true);
    }

    /**
     * 设置用户信息
     * Author: yyl
     * datetime 2022/11/16 09:57
     * @param int $member_id
     * @param array $data
     * @return bool
     */
    public function setMemberInfo(int $member_id, array $data): bool
    {
        return $this->setData(self::REDIS_C_MEMBER_INFO_PREF . $member_id, $data);
    }

    /**
     * 获取用户信息
     * Author: yyl
     * datetime 2022/11/16 09:58
     * @param $member_id
     * @return array|null
     */
    public function getMemberInfo($member_id,$field='*'): ?array
    {
        $info= $this->getData(self::REDIS_C_MEMBER_INFO_PREF . $member_id);
        return SwgArray::getArrayByField($info,$field,1);
    }
}