<?php
namespace Swg\Composer;

use think\Exception;

/** 文件日志类 */
class Log
{
    /**
     * 写入日志
     * @param $data
     * @param string $fileName
     * @return array
     */
    static function addLogs($data, string $fileName = ''): array
    {
        try {
            $path = root_path() . 'public/logs/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (!$fileName) {
                $fileName = date('d') . '.txt';
            }
            $file = $path . $fileName;
            // 增加创建时间
            $data['create_time'] = date("H:i:s");
            if (!file_put_contents($file, json_encode($data) . PHP_EOL, FILE_APPEND)) {
                throw new Exception('日志记录失败');
            }
            return ['code' => 1, 'msg' => 'Success'];
        } catch (Exception $exception) {
            return ['code' => 0, 'msg' => $exception->getMessage()];
        }
    }
}