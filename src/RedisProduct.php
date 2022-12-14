<?php

namespace Swg\Composer;

use Exception;
use Swg\Redis\Redis;

// require_once 'sdk/redis/Redis.php';
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

/** redis商品信息库 */
class RedisProduct extends Redis
{
    protected $db = self::REDIS_PRODUCT_DB;

    /** @var string 商品列表key */
    const PRODUCT_LIST_KEY = 'product_list';
    const PRODUCT_ORIGIN = 'product_origin';
    const GIFT_LIST_KEY = 'gift_list';

    /** @var string 加盟商产品包 */
    const PRODUCT_JM_PACKAGE_KEY = 'jm_package_key';

    /**
     * 设置产品包
     * Author: zhouhongcheng
     * datetime 2022/11/5 14:34
     * @method
     * @route
     * @param array $package_data 产品包data
     * @return bool
     */
    public function setProductPackage(array $package_data): bool
    {
        $this->redis->del(self::PRODUCT_JM_PACKAGE_KEY);
        $save = [];
        foreach ($package_data as &$item) {
            $save[$item['id']] = $this->encode($item);
        }
        return $this->updateHSet(self::PRODUCT_JM_PACKAGE_KEY, $save);
    }

    /**
     * 获取产品包
     * Author: zhouhongcheng
     * datetime 2022/11/5 14:36
     * @method
     * @route
     * @return array|null
     * @deprecated
     */
    public function getProductPackage(): ?array
    {
        return $this->getProductPackageList();
    }

    /**
     * 获取商品包列表
     */
    public function getProductPackageList(): ?array
    {
        $data = $this->getProductPackageListIdAsKey();
        if (empty($data)) return null;
        return array_values($data);
    }

    /**
     * 获取商品包列表id作为key
     */
    public function getProductPackageListIdAsKey(): ?array
    {
        return $this->getHSetData(self::PRODUCT_JM_PACKAGE_KEY);
    }

    /**
     * 根据id列表获取商品包
     */
    public function getProductPackageByIds($ids): ?array
    {
        if (is_null($ids)) return null;
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        return $this->getHSetData(self::PRODUCT_JM_PACKAGE_KEY, $ids);
    }

    /**
     * 根据id获取商品包
     */
    public function getProductPackageById($id): ?array
    {
        return $this->getHSetData(self::PRODUCT_JM_PACKAGE_KEY, $id);
    }

    /**
     * 设置商品列表信息
     * @param array $products 商品数据
     * @return bool
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:16
     * @method
     * @route
     */
    public function setProductList(array $products): bool
    {
        //存储所有商品
        $product_origin = [];

        //删除历史数据
        $this->redis->del(self::GIFT_LIST_KEY);
        $this->redis->del(self::PRODUCT_LIST_KEY);
        $this->redis->del(self::PRODUCT_ORIGIN);

        foreach ($products as $product) {
            $product_origin[$product['id']] = $this->encode($product);
            $this->redis->zAdd(self::PRODUCT_LIST_KEY, $product['sort'], $product['id']);
            if ($product['is_reward']) {
                $this->redis->zAdd(self::GIFT_LIST_KEY, $product['sort'], $product['id']);
            }
        }

        return $this->updateHSet(self::PRODUCT_ORIGIN, $product_origin);
    }

    /**
     * 更新/添加单个商品信息
     */
    public function updateProduct(array $product)
    {
        //更新商品真实信息
        $id = $product['id'];
        $this->redis->hMSet(self::PRODUCT_ORIGIN, [$id => $this->encode($product)]);

        //更新商品赠品列表
        if ($product['is_reward']) {
            //更新赠品商品列表
            $this->redis->zRem(self::GIFT_LIST_KEY, $id);
            $this->redis->zAdd(self::GIFT_LIST_KEY, $product['sort'], $id);
        }
        
        //更新普通商品列表
        $this->redis->zRem(self::PRODUCT_LIST_KEY, $id);
        $this->redis->zAdd(self::PRODUCT_LIST_KEY, $product['sort'], $id);
    }

    /**
     * 退款时，更新库存和销售量
     * 修改该方法需要注意 updateStockOnSale()方法的调用关系
     * Author: yyl
     * datetime 2023/1/9 14:14
     * @return void
     * @throws Exception
     */
    public function updateStockOnRefund(int $product_id, int $num)
    {
        $product_info = self::getInstance()->getProductById($product_id);
        if (empty($product_info)) {
            throw new Exception("更新库存和销售量, 找不到商品[product_id=$product_id]");
        }
        $product_info['product_stock'] += $num;
        $product_info['real_sales'] -= $num;
        self::getInstance()->updateProduct($product_info);
    }

    /**
     * 销售时，更新库存和销量
     * @throws Exception
     */
    public function updateStockOnSale(int $product_id,int $num)
    {
        $this->updateStockOnRefund($product_id,-$num);
    }

    /**
     * 获取商品列表信息 按sort从大到小
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:17
     * @param int|null $page
     * @param int|null $limit
     * @return array[]|null
     */
    public function getProductList(?int $page = null, ?int $limit = 10): ?array
    {
        //返回全部
        if (is_null($page)) {
            $products = $this->getProductListIdAsKey();
            if (empty($products)) return null;
            return array_values($products);
        }
        //分页
        list($start, $end) = $this->getStartAndEnd($page, $limit);
        $ids = $this->redis->zRevRange(self::PRODUCT_LIST_KEY, $start, $end);
        if (empty($ids)) return null;
        $products = $this->getProductByIds($ids);
        if (empty($products)) return null;
        return array_values($products);
    }

    /**
     * 获取 以商品编号为健的数组
     * datetime 2022/11/15 17:52
     * @method post
     * @return array|null
     */
    public function getProductListIdAsKey(): ?array
    {
        $ids = $this->redis->zRevRange(self::PRODUCT_LIST_KEY, 0, -1);
        if (empty($ids)) return null;
        return $this->getHSetData(self::PRODUCT_ORIGIN, $ids);
    }

    /**
     * 根据ids列表获取商品
     * @param string|array $ids
     * @return array[]|null [[id=>product]]
     */
    public function getProductByIds($ids): ?array
    {
        if (is_null($ids)) return null;
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        return $this->getHSetData(self::PRODUCT_ORIGIN, $ids);
    }

    /**
     * 根据id返回商品
     * @param int|string $id
     * @return null|array
     */
    public function getProductById(int $id): ?array
    {
        if (is_null($id)) return null;
        return $this->getHSetData(self::PRODUCT_ORIGIN, $id);
    }

    /**
     * 获取赠品列表
     */
    public function getGiftList(): ?array
    {
        $gifts = $this->getGiftListIdAsKey();
        if (empty($gifts)) return null;
        return array_values($gifts);
    }

    /**
     * Author: yyl
     * datetime 2022/11/13 15:05
     * @return array[]|null [[id=>product]]
     */
    public function getGiftListIdAsKey(): ?array
    {
        $ids = $this->redis->zRevRange(self::GIFT_LIST_KEY, 0, -1);
        if (is_null($ids)) return null;
        return $this->getProductByIds($ids);
    }

    /**
     * @param int|string|array $ids
     * @return array[]|null [[id=>product]]
     */
    public function getGiftByIds($ids): ?array
    {
        if (is_null($ids)) return null;
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        $return = [];
        foreach ($ids as $id) {
            $return[$id] = $this->getGiftById($id);
        }
        return $return;
    }

    /**
     * Author: yyl
     * datetime 2022/11/13 15:04
     * @param int $id
     * @return array|null
     */
    public function getGiftById(int $id): ?array
    {
        $ids = $this->redis->zRevRange(self::GIFT_LIST_KEY, 0, -1);
        if (empty($ids)) return null;
        if (!in_array($id, $ids)) return null;
        return $this->getProductById($id);
    }

    /**
     * 获取商品详情
     * Author: zhouhongcheng
     * datetime 2022/11/4 17:37
     * @method
     * @route
     * @param int $product_id 商品编号
     * @return false|mixed
     * @deprecated 将删除，不要调用
     */
    public function getProductInfo(int $product_id)
    {
        return $this->getProductById($product_id);
    }
}