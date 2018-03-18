<?php

namespace app\api\model;

use think\Model;
use think\Db;

class DispatchRange extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    public function setPointsAttr($value)
    {
        return json_encode($value);
    }
    public function getPointsAttr($value)
    {
        return json_decode($value);
    }
    public function getStatusAttr($value)
    {
        return $value?true:false;
    }
    public static function createOne($data)
    {
        $model = new self();
        return $model->allowField(true)->save($data);
    }

    public static function updateOne($id, $data)
    {
        $model = new self();
        return $model->allowField(true)->save($data,['id'=>$id]);
    }

    public static function getById($id)
    {
        return self::get($id);
    }

    public static function getAll($where)
    {
        $model = new self();
        $res = $model->where($where)->select();
        return $res;
    }

    public static function deleteOne($id)
    {
        return self::where(['id'=>$id])->delete();
    }
}
