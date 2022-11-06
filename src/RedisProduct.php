<?php
namespace Swg\Composer;

use Swg\Redis\Redis;
// require_once 'sdk/redis/Redis.php';
require_once root_path() .'vendor/swg/composer/sdk/redis/Redis.php';

/** redis商品信息库 */
class RedisProduct extends Redis
{
    /** @var string 商品列表key */
    CONST PRODUCT_LIST_KEY = 'product_list';

    /** @var string 加盟商产品包 */
    CONST PRODUCT_JM_PACKAGE_KEY = 'jm_package_key';


    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_PRODUCT_DB);
    }

    /**
     * 设置产品包
     * Author: zhouhongcheng
     * datetime 2022/11/5 14:34
     * @method
     * @route
     * @param string $package_key 产品包键
     * @param array $package_data 产品包data
     * @return bool
     */
    public function setProductPackage(string $package_key = self::PRODUCT_JM_PACKAGE_KEY,array $package_data = [])
    {
        return $this->redis->set($package_key,json_encode($package_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /**
     * 获取产品包
     * Author: zhouhongcheng
     * datetime 2022/11/5 14:36
     * @method
     * @route
     * @param string $package_key 产品包键
     * @return mixed
     */
    public function getProductPackage(string $package_key = self::PRODUCT_JM_PACKAGE_KEY)
    {
        return json_decode($this->redis->get($package_key),true);
    }

    /**
     * 设置商品列表信息
     * @todo 还未处理分页问题
     * 注：商品入库时把价格更改为元，按照sort排序 好入库
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:16
     * @method
     * @route
     * @param array $list 商品数据
     * @return bool
     */
    public function setProductList(array $list)
    {
        return $this->redis->set(self::PRODUCT_LIST_KEY,json_encode($list,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /**
     * 获取商品列表信息
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:17
     * @method
     * @route
     * @param
     * @return false|mixed|string
     */
    public function getProductList()
    {
        return json_decode($this->redis->get(self::PRODUCT_LIST_KEY),true);
    }

    /**
     * 获取商品详情
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:37
     * @method
     * @route
     * @param int $product_id 商品编号
     * @return false|mixed
     */
    public function getProductInfo(int $product_id)
    {
        $product_list = $this->getProductList();
        $temp_key = array_column($product_list,'id');//键值
        $product_list = array_combine($temp_key,$product_list);
        if (empty($product_list[$product_id])) return false;
        return $product_list[$product_id];
    }
}