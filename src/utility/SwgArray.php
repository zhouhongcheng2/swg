<?php

namespace Swg\Composer\utility;

/**
 * 数组处理
 * Author: zhouhongcheng
 * datetime 2022/11/5 13:18
 */
class SwgArray
{
    /**
     * 多维数组 取出指定字段
     * Author: zhouhongcheng
     * datetime 2022/11/5 15:58
     * @method
     * @route
     * @param array|null $data 数组
     * @param string $fields 字段
     * @return array
     */
    public static function getArrayByField(?array $data = [], string $fields = '*', $level = 2): ?array
    {
        if ($fields == '*') return $data;
        if (empty($data)) return [];
        $new_data = [];
        $fields = explode(',', $fields);

        if ($level == 1) {
            foreach ($fields as $val) {
                $val = trim($val);
                $new_data[$val] = !isset($data[$val]) || is_null($data[$val]) ? '' : $data[$val];
            }
            return $new_data;
        }
        foreach ($data as $key => $val) {
            foreach ($fields as $v) {
                $v = trim($v);
                $new_data[$key][$v] = !isset($val[$v]) || is_null($val[$v]) ? '' : $val[$v];
            }
        }
        return $new_data;
    }
}