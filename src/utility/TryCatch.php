<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility;

use Exception;
use think\facade\Db;

/**
 * Author: yyl
 * datetime 2022/11/4 13:07
 */
trait TryCatch
{
    /**
     * 事务自动 try catch 记录日志<br>
     * 服务层事务操作，<br>
     * 请在调用之前做好数据检查<br>
     * <br>
     * Author: yyl
     * datetime 2022/11/4 11:40
     * @param mixed $function 业务处理函数 返回值==false时候，将回滚事务
     * @param mixed|null $on_exception 错误回调函数
     * @return mixed|null
     */
    public static function startTrans($function, $on_exception = null,$trans_model=null)
    {
        $db = $trans_model??Db::class;
        
        $db::startTrans();
        try {
            $result = $function();

            if (!is_null($result) && $result == false) {
                $db::rollback();
            } else {
                $db::commit();
            }
            return $result;
        } catch (Exception $exception) {
            $db::rollback();
            if ($on_exception) {
                //TODO 记录日志
                return $on_exception($exception);
            } else {
                //抛到Handle处理
                throw $exception;
            }
        }
    }

    /**
     * 简单try
     * @throws Exception
     */
    public static function startTry($function, $on_exception = null)
    {
        try {
            return $function();
        } catch (Exception $exception) {
            if ($on_exception) {
                return $on_exception($exception);
            } else {
                throw $exception;
            }
        }
    }
}