<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility\log;

use Exception;
use think\facade\Log;

/**
 * 日志工具扩展
 */
class LogUtil  extends Log
{
    /**
     * 打印异常到日志[用在命令行程序，因为tp的命令行的异常不会被ExceptionHandler捕获]
     * Author: yyl
     * datetime 2022/12/22 11:31
     * @param Exception|null $exception
     * @param string|null $title 异常标题
     * @return void
     */
    public static function exception(?Exception $exception, ?string $title = null)
    {
        if (empty($exception))return;

        self::error("⚠️--------------------------".get_class($exception));
        if ($title) {
            self::error($title."[".$exception->getMessage()."]");
        }else{
            self::error($exception->getMessage());
        }

        self::error($exception->getFile());
        self::error($exception->getLine());
        self::error('----------------------------------------------------------');
    }
    
    public static function __callStatic($method, $params)
    {
        $static = static::createFacade();
        $static = $static->channel('command');
        return call_user_func_array([$static, $method], $params);
    }

    /**
     * echo 日志
     */
    public static function echoLog($log)
    {
        echo '['.date('H:m:s m-d-Y',time()).'] ' ,$log, PHP_EOL;
    }

    /**
     * echo 日志并exit
     */
    public static function echoAbortLog($log)
    {
        self::echoLog($log);
        exit();
    }
}