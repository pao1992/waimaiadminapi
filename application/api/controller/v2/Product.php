<?php
/**
 * Created by 七月.
 * User: 七月
 * Date: 2017/2/15
 * Time: 1:00
 */

namespace app\api\controller\v2;

use app\api\validate\BaseValidate;
use app\lib\exception\SuccessReturn;
use app\lib\exception\UpdateException;
use think\Controller;
use app\lib\exception\SuccessMessage;
use app\api\model\Product as ProductModel;
use app\lib\exception\BaseException;
use app\api\validate\IDMustBePositiveInt;

class Product extends Controller
{
    //{
//    protected $beforeActionList = [
//        'checkSuperScope' => ['only' => 'createOne,deleteOne']
//    ];
    function createOne()
    {
        //整理基本信息
        $data = input('post.');
        $res = ProductModel::createOne($data);
        if ($res) {
            return new SuccessMessage();
        }
    }

    public function getAll()
    {
        $filter = input('get.');
        //过滤空字段
        $data = BaseValidate::emptyFilter($filter);
        $res = ProductModel::getAll($data);
        if (!$res['total']) {
            return new BaseException([
                'errorCode' => '',
                'msg' => '没有商品！'
            ]);
        }
        return new SuccessReturn(
            array('data' => $res)
        );
    }

    public function getById($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $res = ProductModel::getById($id);
        if (!$res) {
            throw new BaseException(
                array('errorCode' => '20000', 'msg' => 'no such product')
            );
        }
        return new SuccessReturn(
            array('data' => $res)
        );
    }

    /**
     * @return boolean
     */
    public function updateOne($id)
    {
        //整理基本信息
        $data = input('put.');
        $res = ProductModel::updateOne($id, $data);
        if (!$res) {
            return new UpdateException();
        }
        return new SuccessMessage();
    }

    /**
     * @return boolean
     */
    public function batchUpdate($ids)
    {
        //整理基本信息
        $data = input('put.');
        $ids_arr = explode(',',$ids);
        ProductModel::batchUpdate($ids_arr, $data);
        return new SuccessMessage();
    }

    /**
     * 获取某分类下全部商品(不分页）
     * @url /product/all?id=:category_id
     * @param int $id 分类id号
     * @return \think\Paginator
     * @throws ThemeException
     */
    public function getByCategory($id = -1)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if ($products->isEmpty()) {
            return new BaseException([
                'errorCode' => '20001',
                'msg' => '该分类下没有商品'
            ]);
        }
        return new SuccessReturn(
            array('data' => $products)
        );;
    }

    public function deleteOne($id)
    {
        $res = ProductModel::destroy($id);
        if (!$res) {
            throw new BaseException();
        }
        return new SuccessMessage();
    }
}