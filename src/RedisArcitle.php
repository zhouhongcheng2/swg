<?php

namespace Swg\Composer;
require_once root_path() . 'vendor/swg/composer/sdk/redis/Redis.php';

use app\common\model\service\system\ArticleList;
use Swg\Redis\Redis;

/** redis中国地址库 */
class RedisArcitle extends Redis
{

    const REDIS_KEY = 'article';

    public function __construct()
    {
        parent::__construct();
        $this->redis->select(self::REDIS_CONFIG_DB);
    }

    /**
     * 设置文章redis
     * Author: lvg
     * datetime 2022/11/14 17:47
     * @param string $title
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setArticleByTitle(string $title){
        $list= ArticleList::getListByTitleForRedis($title);
        $arr=[];
        foreach ($list as $value){
            $arr[$value['articleCate']['module'].'_id_'.$value]=$this->encode($value->toArray());
        }
        return $this->redis->hMSet(self::REDIS_KEY, $set_list);
    }

    /**
     * 获取文章内容
     * Author: lvg
     * datetime 2022/11/14 17:47
     * @param mixed $id
     * @param mixed $module
     * @return array|null
     */
    public function getArticle($id,$module){
        return$this->getHSetData('article',$module.'_id_'.$id);
    }
}
