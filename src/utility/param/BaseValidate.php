<?php
namespace Swg\Composer\utility\param;
use think\helper\Str;
use think\Validate;
use Swg\Composer\utility\param\facade\Param;

/**
 * 验证器基类
 * 写这个原因？1.为了方便调用 2.可以把自定义且公用的验证方法放入本类
 * Author: yyl
 * datetime 2022/10/29 20:34
 */
class BaseValidate extends Validate
{
    /**
     * 验证数据
     * 写这个的原因的避免使用的时候手动new
     * @param string|null $scene 验证场景
     * @param bool $failException 默认验证失败抛出异常
     * @return bool 验证成功返回 true
     */
    public static function doCheck(string $scene = null, bool $failException = true): bool
    {
        return (new static())->goCheck($scene, $failException);
    }

    /**
     * 执行验证
     * @param string|null $scene 验证场景
     * @param bool $failException 默认验证失败抛出异常
     * @return bool 验证成功返回 true
     */
    public function goCheck(string $scene = null, bool $failException = true): bool
    {
        $validate = $this->failException($failException)->batch(false);
        if (!empty($scene)) {
            $validate->scene($scene);
        }
        return $validate->check($this->allInput());
    }

    /**
     * 判断是否是正整数
     * @param $value
     * @return bool
     * @noinspection PhpUnused
     */
    protected function isPositiveInt($value): bool
    {
        //表单或者json传进来的参数可能是int类型，也可能是string类型的数字
        //1.所以先判断是否是数字或者字符串类型的数字
        //2.判断是整数。+0是为了将字符串类型的数字转为int类型
        //3.判断是正整数
        if (is_numeric($value) && is_int($value + 0) && $value + 0 > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 必须是正数金额
     * Author: yyl
     * datetime 2022/11/1 13:41
     * @param $value
     * @return bool
     * @noinspection PhpUnused
     */
    protected function isPositiveAmount($value): bool
    {
        // 必须是整数或浮点数，且允许为负
        if (!$this->isAmount($value)) {
            return false;
        }
        // 不为 0
        if (empty((int)($value * 100))) {
            return false;
        }
        // 不为负数
        if ((int)($value * 100) < 0) {
            return false;
        }
        return true;
    }

    /**
     * 必须是0或者正数金额
     * Author: yyl
     * datetime 2022/11/1 13:42
     * @param $value
     * @return bool
     * @noinspection PhpUnused
     */
    protected function isPositiveOrZeroAmount($value): bool
    {
        // 必须是整数或浮点数，且允许为负
        if (!$this->isAmount($value)) {
            return false;
        }
        // 不为负数
        if ((int)($value * 100) < 0) {
            return false;
        }
        return true;
    }


    /**
     * 金额校验：可以是负、0、正金额
     * @access private
     * Author: yyl
     * datetime 2022/11/1 13:38
     * @param $value
     * @return bool
     */
    protected function isAmount($value): bool
    {
        // 必须是整数或浮点数，且允许为负
        if (!preg_match("/^[-]?\d+(.\d{1,2})?$/", $value)) {
            return false;
        }
        return true;
    }

    /**
     * 任意值
     * @param $value
     * @return bool
     */
    protected function any($value): bool
    {
        return true;
    }

    /**
     * 获取规则中有定义的数据
     * 调用本函数前，你应该先调用 goCheck()检查数据
     * @param mixed $source_data 原数据；默认为Param::allInput
     * @param bool $include_empty 返回包括空数据 empty()函数判断
     * @return array
     */
    public function getDataByRule($source_data = null, bool $include_empty = true): array
    {
        if ($source_data === null) $source_data = $this->allInput();

        $return = [];
        foreach ($this->rule as $key => $value) {
            //截取真实key
            if (Str::contains($key, '|')) $key = trim(explode('|', $key)[0]);
            if (Str::contains($key, '.')) $key = trim(explode('.', $key)[0]);
            if (!isset($source_data[$key])) continue;
            if (!$include_empty && empty($source_data[$key])) continue;
            $return[$key] = $source_data[$key];
        }
        return $return;
    }

    public function allInput()
    {
        return Param::allInput();
    }
}