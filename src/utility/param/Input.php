<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility\param;

/**
 * 参数定义类
 */
abstract class Input extends BaseValidate
{
    /**
     * @return static
     */
    public static function fetch(string $scene = null): Input
    {
        $static = new static();
        $static->goCheck($scene);
        $static->fill();
        return $static;
    }

    /**
     * 填充对象属性
     * @param array|null $input 填充Input字段
     * @return Input
     */
    public function fill(?array $input = null): Input
    {
        if (is_null($input)) {
            $input = $this->allInput();
        }

        $map = get_object_vars($this);
        $keys = array_keys($map);

        foreach ($input as $key => $value) {
            if (in_array($key, $keys)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

}