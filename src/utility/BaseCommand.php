<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility;

use Exception;
use Swg\Composer\utility\log\LogUtil;
use think\console\Command;

/**
 * 命令行基类
 * 方便打印日志，输出日期和异常
 */
abstract class BaseCommand extends Command
{
    /**
     * 打印日志，带时间
     */
    public function info($string = '', $type = 'info')
    {
        $this->output->writeln("[" . date('H:m:s m-d-Y') . "][$type] " . $string);
    }

    /**
     * Author: yyl
     * datetime 2022/12/30 15:33
     * @param string|Exception $info
     * @return void
     */
    public function error($info)
    {
        if ($info instanceof Exception) {
            LogUtil::exception($info);//详情日志写到runtime/command/xxx.log统一管理，避免日志文件过大
            $this->error($info->getMessage());
        } else {
            $this->info($info . " 具体信息请查看runtime/command/xxx.log", 'error❎');
        }
    }
}