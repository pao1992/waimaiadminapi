<?php
/**
 * Created by 七月.
 * User: 七月
 * Date: 2017/2/16
 * Time: 1:59
 */

namespace app\api\model;


use app\lib\exception\BaseException;
use app\lib\exception\ProductException;
use app\lib\exception\ThemeException;
use think\Db;
use think\Exception;
use think\Model;

class Theme extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time', 'topic_img_id', 'head_img_id'];

    /**
     * 关联Image
     * 要注意belongsTo和hasOne的区别
     * 带外键的表一般定义belongsTo，另外一方定义hasOne
     */
    public function topicImg()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    /**
     * 关联product，多对多关系
     */
    public function products()
    {
        return $this->belongsToMany(
            'Product', 'theme_product', 'product_id', 'theme_id');
    }

    public static function getOne($id)
    {
        return self::with('products,topicImg,headImg')->find($id);
    }

    public static function getAll($where)
    {
        $model = new self();
        return $model->allowField(true)->where($where)->with('topicImg,headImg')->select();
    }
    public static function createOne($data)
    {
        Db::startTrans();
        try {
            $model = new self();

            if (isset($data['topic_img'])) {
                $img_model = new Image();
                $img_model->save(['url' => $data['topic_img']['url'], 'from' => $data['topic_img']['from']]);
            }
//            if (isset($data['head_img'])) {
//                $res = $theme->topicImg()->save(['url' => $data['head_img']['url'], 'form' => $data['head_img']['form']]);
//            }
            $data['topic_img_id'] = $img_model->id;
            $model->allowField(true)->save($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }
    public static function updateOne($id, $data)
    {
        Db::startTrans();
        try {
            $model = new self();
            $theme = self::get($id);
            if (isset($data['topic_img'])) {
                $res = $theme->topicImg->save(['url' => $data['topic_img']['url'], 'from' => $data['topic_img']['from']]);
            }
//            if (isset($data['head_img'])) {
//                $res = $theme->topicImg()->save(['url' => $data['head_img']['url'], 'form' => $data['head_img']['form']]);
//            }
            $res = $theme->allowField(true)->save($data, ['id' => $id]);
            // 提交事务
            Db::commit();
            return $res;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }

    public static function deleteOne($id)
    {
        $theme = self::with('products')->find($id);
        if(!$theme->products->isEmpty()){
            throw new BaseException(
                ['msg'=>'该主题下仍有关联的商品',
                'errorCode'=>'30001']
            );
        }
        return self::destroy($id);
    }

    public static function addThemeProduct($themeID, $p_ids)
    {
        $p_ids = explode(',',$p_ids);
        foreach ($p_ids as $v) {
            $models = self::checkRelationExist($themeID, $v);
            // 写入中间表，这里要注意，即使中间表已存在相同themeID和itemID的
            // 数据，写入不成功，但TP并不会报错
            // 最好是在插入前先做一边查询检查
            $res = $models['theme']->products()
                ->attach($v);
        }
        return true;
    }

    public static function deleteThemeProduct($themeID, $p_ids)
    {
        $p_ids = explode(',',$p_ids);
        foreach ($p_ids as $v) {
            $models = self::checkRelationExist($themeID, $v);
            Db::table('theme_product')->where(['product_id'=>$v,'theme_id'=>$themeID])->delete(true);
        }
        return true;
    }
    private static function checkRelationExist($themeID, $productID)
    {
        $theme = self::get($themeID);
        if (!$theme) {
            throw new ThemeException();
        }
        $product = Product::get($productID);
        if (!$product) {
            throw new ProductException();
        }
        return [
            'theme' => $theme,
            'product' => $product
        ];

    }
}