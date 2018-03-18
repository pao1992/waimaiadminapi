<?php

namespace app\api\controller\v2;

use app\api\controller\BaseController;
use app\api\model\Theme as ThemeModel;
use app\api\validate\BaseValidate;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\ThemeNew;
use app\api\validate\ThemeProduct;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SuccessReturn;
use app\lib\exception\ThemeException;
use app\lib\exception\UpdateException;
use think\exception\ValidateException;

/**
 * 主题推荐,主题指首页里多个聚合在一起的商品
 * 注意同专题区分
 * 常规的REST服务在创建成功后，需要在Response的
 * header里附加成功创建资源的URL，但这通常在内部开发中
 * 并不常用，所以本项目不采用这种方式
 */
class Theme extends BaseController
{
    public function getAll()
    {
        $data = input('*.');
        $result = ThemeModel::getAll($data);
        return new SuccessReturn([
            'data' => $result->toArray()
        ]);
    }

    public function getOne($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $result = ThemeModel::getOne($id);
        if (!$result) {
            throw new ThemeException();
        }
        return new SuccessReturn([
            'data' => $result
        ]);
    }

    public function createOne()
    {
        $post = input('post.');
        (new ThemeNew())->check($post);
//        isset($put['topic_img']) && ($put['topic_img']['url'] = $put['topic_img']['url']);
//        isset($put['head_img']) && ($put['head_img']['url'] = $put['head_img']['url']);
        ThemeModel::createOne($post);
        return new SuccessMessage();
    }

    public function updateOne($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $put = input('put.');
//        isset($put['topic_img']) && ($put['topic_img']['url'] = $put['topic_img']['url']);
//        isset($put['head_img']) && ($put['head_img']['url'] = $put['head_img']['url']);
        $res = ThemeModel::updateOne($id, $put);
        if(!$res){
            throw new UpdateException();
        }
        return new SuccessMessage();
    }

    public static function deleteOne($id)
    {
        $result = ThemeModel::deleteOne($id);
        if (!$result) {
            throw new BaseException();
        }
        return new SuccessMessage();
    }

    /**
     * @url /theme/:t_id/product/:p_id
     * @Http POST
     * @return SuccessMessage or Exception
     */
    public function addThemeProduct($t_id, $p_ids)
    {
        $validate = new ThemeProduct();
        $validate->goCheck();
        ThemeModel::addThemeProduct($t_id, $p_ids);
        return new SuccessMessage();
    }

    /**
     * @url /theme/:t_id/product/:p_id
     * @Http DELETE
     * @return SuccessMessage or Exception
     */
    public function deleteThemeProduct($t_id, $p_ids)
    {
        $validate = new ThemeProduct();
        $validate->goCheck();
        $themeID = (int)$t_id;
        ThemeModel::deleteThemeProduct($themeID, $p_ids);
        return new SuccessMessage([
            'code' => 204
        ]);
    }

    // 去除部分属性，尽量对客户端保持精简
//    private function cutThemes($themes)
//    {
//        foreach ($themes as &$theme) {
//            foreach ($theme['products'] as &$product) {
//                unset($product['stock']);
//                unset($product['summary']);
//            }
//        }
//        return $themes;
//    }
}
