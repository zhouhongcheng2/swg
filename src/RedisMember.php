<?php

namespace Swg\Composer;

use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis 用户库 */
class RedisMember extends Redis
{
    /** @var string 用户等级名称 */
    const REDIS_MEMBER_LEVEL_NAME_KEY = 'member_level_name';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_MEMBER_DB);
    }

    /**
     * 设置用户等级名称
     * Author: zhouhongcheng
     * datetime 2022/11/11 11:12
     * @method
     * @route
     * @param array $member_level_name ['1'=>'用户','2'=>'会员','3'=>'加盟','4'=>'旗舰','5'=>'联创']
     * @return bool
     */
    public function setMemberLevelName(array $member_level_name)
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
}