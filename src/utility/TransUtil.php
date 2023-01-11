<?php
/**
 * @uthor xxyangyoulin
 */

namespace Swg\Composer\utility;

/**
 * 数据转换类工具
 */
class TransUtil
{
    const TIME_DAY = 1;
    const TIME_HOUR = 2;
    const TIME_MINUTE = 3;
    const TIME_SECONDS = 4;
    const DESC = 10;
    const ASC = 11;

    public static function getJsonPostHeader($json): array
    {
        if (is_array($json)) $json = json_encode($json);
        return array("Content-Type: application/json", "Content-Length: " . strlen($json));
    }

    /**
     * 保留小数位数
     * @param float $val
     * @param int $count 小数位数,默认保留2位
     * @return float|int
     */
    public static function retainDecimal(float $val, int $count = 2)
    {
        if ($count == 0) return intval($val);
        return floor($val * pow(10, $count)) / pow(10, $count);
    }

    /**
     * 金额：元转分
     * @param mixed $arr 数组或查询结果对象
     * @param string $indexes
     * @return mixed
     */
    public static function Yuan2FenArray($arr, string $indexes)
    {
        $indexes = explode(',', $indexes);
        foreach ($indexes as $index) {
            if (isset($arr[$index])) {
                $arr[$index] = self::Yuan2Fen($arr[$index]);
            }
        }
        return $arr;
    }

    /**
     * 金额：元转分
     * @param int name 参数注释
     */
    public static function Yuan2Fen($val): int
    {
        if (!is_numeric($val)) {
            // return null;
            throw new \Exception('传入金额单位为元的数据必须是大于等于0的数字，你传入的是：' . $val);
        }
        return intval(bcmul($val, 100));
    }

    /**
     * 金额：分转元
     * @param mixed $arr 数组或查询结果对象
     * @param string $indexes
     * @param bool $retain_decimal 保留两位小数，即使是 0.00，此时会返回字符串 "0.00"
     * @return mixed
     */
    public static function Fen2YuanArray($arr, string $indexes, bool $retain_decimal = false)
    {
        $indexes = explode(',', $indexes);
        foreach ($indexes as $index) {
            if (isset($arr[$index])) {
                $arr[$index] = self::Fen2Yuan($arr[$index], $retain_decimal);
            }
        }
        return $arr;
    }

    /**
     * 金额：分转元
     * @param $val
     * @param bool $retain_decimal 保留两位小数，即使是 0.00，此时会返回字符串 "0.00"
     * @return int|string|null
     */
    public static function Fen2Yuan($val, bool $retain_decimal = false)
    {
        if (!is_numeric($val)) {
            return $val;
        }
        if ($retain_decimal) {
            return bcdiv($val, 100, 2);
        }
        return bcdiv($val, 100, 2) + 0;
    }

    /**
     * 时间戳差值转换为天、小时、分钟、秒
     * @param int|string $large_time 较大时间【时间戳或可通过strtotime()转换为时间戳的时间字符串】
     * @param int|string $less_time 较小时间【时间戳或可通过strtotime()转换为时间戳的时间字符串】
     * @param int $type 转换类型
     * @return int
     */
    public static function timeDiff($large_time, $less_time, int $type = self::TIME_HOUR): int
    {
        if (!is_numeric($large_time)) $large_time = strtotime($large_time);
        if (!is_numeric($less_time)) $less_time = strtotime($less_time);
        $diff = $large_time - $less_time;
        switch ($type) {
            case self::TIME_DAY:
                return intval($diff / (3600 * 24));
            case self::TIME_HOUR:
                return intval($diff / (3600));
            case self::TIME_MINUTE:
                return intval($diff / (60));
            case self::TIME_SECONDS:
                return $diff;
            default:
                throw new \InvalidArgumentException('type 参数错误');
        }
    }

    /**
     * 手机号加  ****
     * Author: lvg
     * datetime 2022/11/8 11:48
     * @param mixed $mobile 手机号
     * @return array|string|string[]
     */
    public static function mobileEncryption($mobile)
    {
        return substr_replace($mobile, '****', 3, 4);
    }

    public static function nameEncryption($name): string
    {
        if (empty($name)) return "";
        return mb_substr($name, 0, 1) . (str_repeat('*', mb_strlen($name) - 1));
    }


    /**
     * 时间戳转换为日期格式
     * Author: lvg
     * datetime 2022/11/8 11:51
     * @param int $time 时间戳
     * @param string $format 格式
     * @return false|string
     */
    public static function timeToDate(?int $time, string $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) return $time;
        return date($format, $time);
    }

    /**
     * 键值对数组转换为字符串
     */
    public static function mapArrayToString(?array $array): string
    {
        if (empty($array)) {
            return '';
        }
        $return = '【';
        foreach ($array as $key => $value) {
            $return .= "$key($value) , ";
        }
        return $return .= '】';
    }

    /**
     * 过滤上传特殊表情符号的
     * @param mixed $str
     * @return mixed
     */
    static function filterEmoji($str)
    {
        preg_match_all('/[\x{4e00}-\x{9fff}\d\w\s[:punct:]]+/u', $str, $result);
        return join('', $result[0]);
    }


    /**
     * 二维数组根据某个数字字段排序
     */
    static function sort2dArray(&$sorted_array, $sort_index, int $sort_type = self::DESC)
    {
        usort($sorted_array, function ($a, $b) use ($sort_type, $sort_index) {
            if ($a[$sort_index] == $b[$sort_index]) {
                return 0;
            }
            return (($sort_type == self::DESC) ? 1 : -1) * (($a[$sort_index] < $b[$sort_index]) ? 1 : -1);
        });
    }

    /**
     * 去除字符串的空格
     * Author: lvg
     * datetime 2022/12/9 17:52
     * @param string|null $str
     * @return array|string|string[]|null
     */
    static function deleteTrim(string $str = null)
    {
        if (empty($str)) {
            return '';
        }
        $str = trim($str);
        $str = preg_replace('/\s+/', '', $str);// 接着去掉两个空格以上的
        $str = preg_replace('/\s(?=\s)/', '', $str);// 接着去掉两个空格以上的
        return preg_replace('/[\n\r\t]/', '', $str);// 最后将非空格替换为一个空格
    }

    /**
     * 转json，但是不转义中文
     */
    static function json_encode($data,$option=JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES){
        return self::json_encode($data, $option);
    }
}