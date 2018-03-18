<?php

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\Category as CategoryModel;
use app\api\model\Product as ProductModel;
use app\api\validate\CategoryNew;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use app\lib\exception\SuccessReturn;
use app\lib\exception\UpdateException;
use think\Controller;

class Category extends BaseController
{
    /**
     * 获取全部类目列表，但不包含类目下的商品
     * Request 演示依赖注入Request对象
     * @url /category/all
     * @return array of Categories
     * @throws MissException
     */
    public function getAll()
    {
        $categories = CategoryModel::getAll();
        if(empty($categories)){
           throw new MissException([
               'msg' => '还没有任何类目',
               'errorCode' => 50000
           ]);
        }
        return new SuccessReturn(
            array(
                'data'=>$categories
            )
        );
    }
    public function getCategoryTreeWithProds()
    {
        $categories = CategoryModel::getCategoriesWithProds();
        if(empty($categories)){
            throw new MissException([
                'msg' => '还没有任何类目',
                'errorCode' => 50000
            ]);
        }
        $tree = CategoryModel::get(1)->toArray();
        $tree = $this->getSub($tree,$categories);
        $tree = json([$tree]);
        return $tree;
    }

    public function getCategoryTree()
    {
        //此处可用原生写法，没有必要使用模型，有待优化
        $categories = CategoryModel::all([])->toArray();
        if(empty($categories)){
            throw new MissException([
                'msg' => '还没有任何类目',
                'errorCode' => 50000
            ]);
        }
        $tree = CategoryModel::get(1)->toArray();
        $tree = $this->getSub($tree,$categories);
        $tree = json([$tree]);
        return $tree;
    }
    protected function getSub($tree,$categories){
        foreach ($categories as $k=>$v){
            if($v['parent_id'] == $tree['category_id']){
                $v = $this->getSub($v,$categories);
                $tree['children'][] = $v;
            }
        }
        return $tree;
    }
    Public function createOne(){
        $data = input('param.');
        $validate = new CategoryNew();
        $validate->goCheck();
        // 根据规则取字段是很有必要的，防止恶意更新非客户端字段
        $data = input('post.');
        CategoryModel::insert($data);
        return new SuccessMessage();
    }
    Public function updateOne($id){
        (new IDMustBePositiveInt())->check($id);
        $model = new CategoryModel();
        $data = input('put.');
        $res = $model->save($data,['id'=>$id]);
        if(!$res){
            throw new UpdateException();
        }
        return new SuccessMessage();
    }


    /**
     * 这里没有返回类目的关联属性比如类目图片
     * 只返回了类目基本属性和类目下的所有商品
     * 返回什么，返回多少应该根据团队情况来考虑
     * 为了接口通用性可以返回大量的无用数据
     * 也可以只返回客户端需要的数据，但这会造成有大量重复接口
     * 接口应当和业务绑定还是和实体绑定需要团队自己抉择
     * 此接口主要是为了返回分类下面的products，请对比products中的
     * 接口，这是一种不好的接口设计
     * @url /category/:id/products
     * @return Category single
     * @throws MissException
     */
    public function getById($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $category = CategoryModel::getById($id);
        if(empty($category)){
            throw new MissException([
                'msg' => 'category not found'
            ]);
        }
        return new SuccessReturn(
            array('data'=>$category)
        );
    }

    public function deleteOne($id)
    {
        //判断下面是不是还有商品，有商品就不能删
        $res = ProductModel::where(['category_id'=>$id])->count();
        if($res){
            //还有商品不能删
            throw new BaseException([
                'errorCode'=>'50004',
                'msg'=>'类目下有商品，不能删除！'
            ]);
        }else{
            CategoryModel::destroy($id);
            return new SuccessMessage();
        }
    }
}