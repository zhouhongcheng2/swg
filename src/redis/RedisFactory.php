<?php
/**
 * @author yyl
 */

namespace Swg\Composer\redis;
use Swg\Composer\bean\OpeareLog;
use Swg\Redis\Redis;

require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

class RedisFactory extends Redis
{
    protected $db = self::REDIS_FACTORY;
    /** @var string 开关控制log */
    const CONTROL_LOG_KEY = 'factory:control:log';
    
    /** 添加控制日志 */
    public function addControlLog(OpeareLog $log)
    {
        return $this->redis->zAdd(self::CONTROL_LOG_KEY.":".$log->identity.":".date('Y-m-d',$log->time),
            $log->score, $this->encode((array)$log));
    }

    /**
     * 获取控制日志
     * @param mixed $identity 设置唯一标示
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getControlLogList($identity,$date,int $page,int $limit)
    {
        $date = is_numeric($date)? date('Y-m-d') : $date;
        list($start, $end) = $this->getStartAndEnd($page, $limit);
        $data = $this->redis->zRevRange(self::CONTROL_LOG_KEY.":".$identity.":".$date, $start, $end);
        if (empty($data)) {
            return [];
        }
        foreach ($data as &$datum) {
            $datum = json_decode($datum, true);
        }
        return $data;
    }
    
    /** 
     * 清除过期日志 
     * 默认清除三个月以前的日志
     */
    public function clearExpiredLogs($identity,?int $expired_date)
    {
        $all_keys = $this->redis->keys(self::CONTROL_LOG_KEY.':'.$identity.':*');
        $del_keys = [];
        foreach ($all_keys as $key) {
            $date = $this->splitStringByString($key, ':')[1];
            if (strtotime($date)<$expired_date){//过期
                $del_keys[] = self::CONTROL_LOG_KEY.":".$identity.':'.$date;
            }
        }
        return $this->redis->del($del_keys);
    }

    /** 按最后一个指定字符分割字符串 */
    function splitStringByString($string,$separtion = '.')
    {
        $resultArray = [];
        $resultArray[0] = substr($string,0,strrpos($string,$separtion,0));
        $resultArray[1] = substr($string,strrpos($string,$separtion,0)+1,strlen($string));
        return $resultArray;
    }
}