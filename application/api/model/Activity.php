<?php

namespace app\api\model;

use think\Model;
use think\Db;
use app\api\model\User as userModel;
use app\api\model\UserActivity as userActivityModel;
class Activity extends BaseModel
{

    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    public function user(){
        return $this->belongsToMany('user','user_activity','user_id','id');
    }
    public function setStartTimeAttr($value)
    {
        return strtotime($value);
    }

    public function setEndTimeAttr($value)
    {
        return strtotime($value);
    }

    public function getStartTimeAttr($value)
    {
        return date('Y/m/d', $value);
    }

    public function getEndTimeAttr($value)
    {
        return date('Y/m/d', $value);
    }

    public static function createOne($data)
    {
        $res = self::create($data);
        return $res;
    }

    public static function updateOne($id,$data)
    {
        $model = new Activity();
        $model->where('id',$id);
        $res = $model->isUpdate(true)->save($data);
        return $res;
    }

    public static function getActivityById($id)
    {
        return self::get($id);
    }

    public static function getAllActivities($filter)
    {
        return self::where($filter)->select();
    }

    public static function deleteOne($id)
    {
        return self::destroy($id);
    }
}
