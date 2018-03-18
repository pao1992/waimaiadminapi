<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Product extends BaseModel
{

    protected $hidden = ['delete_time', 'from', 'create_time', 'update_time'];

//修改器
    public function setSpecAttr($value)
    {
        return json_encode($value);
    }

    public function setCategoryPathAttr($value)
    {
        return implode('_', $value);
    }

    public function setNumAttr($value)
    {
        return implode('_', $value);
    }

    public function getNumAttr($value)
    {
        return explode('_', $value);
    }

    public function getCategoryPathAttr($value)
    {
        return explode('_', $value);
    }

    public function getSpecAttr($value)
    {
        return json_decode($value);
    }

    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
    public function category(){
        return $this->belongsTo('Category')->field('id,name as category_name')->bind('category_name');
    }

    public static function createOne($data)
    {
        $model = new self();
        return $model->allowField(true)->save($data);
    }

    public static function updateOne($id, $data)
    {
        //过滤字段
        $model = new self();
        return $model->allowField(true)->save($data, ['id' => $id]);
    }

    public static function batchUpdate($ids_arr, $data)
    {
        //过滤字段
        $model = new self();
        $model->allowField(true)->save($data,'`id` in ('.implode(',',$ids_arr).')');
    }

    public static function getAll($where)
    {
        $model = new self();
        //名字采用模糊查询
        if (isset($where['name'])) {
            $where['name'] = ['like', '%' . $where['name'] . '%'];
        }
        //keyword
        if (isset($where['keyword'])) {
            $where['name'] = ['like', '%' . $where['keyword'] . '%'];
        }
        unset($where['keyword']);
        //页码信息
        if (isset($where['page']) && isset($where['size'])) {
            $page = $where['page'] . ',' . $where['size'];
        }
        unset($where['page']);
        unset($where['size']);
        $total = $model->where($where)->count();
        if (isset($page)) {
            $model->page($page);
        }
        $res = $model->where($where)->with('category')->select();
        return array('data' => $res, 'total' => $total);


    }

    public static function getById($id)
    {
        return self::find($id);
    }

    public static function getProductsByCategoryID($categoryID)
    {
        $products = self::where('category_id', $categoryID)->select();
        return $products;
    }
}
