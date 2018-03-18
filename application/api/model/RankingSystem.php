<?php

namespace app\api\model;

use app\api\model\UserInfo as UserInfoModel;

class RankingSystem extends BaseModel
{

    protected $autoWriteTimestamp = false;
    public function getConsumeLimitAttr($value){
        return json_decode($value);
    }
    public function getRateLimitAttr($value){
        return json_decode($value);
    }
    public static function saveConfig($data){
        $res = self::update($data);
        if($res){
            return array('code'=>'success');
        }else{
            return array('code'=>'error');
        }
    }
    public static function getConfig(){
       return self::withTrashed()->find(1);
    }
    public static function getAll(){
        $res = self::select();
        return $res->toArray();

    }
    public static function getById($id){
        $res = self::get($id);
        if($res){
            return array('code'=>'success','data'=>$res);
        }else{
            return array('code'=>'error',',msg'=>'');
        }
    }
    public static function createOne($data){
        $model = new self();
        $res = $model->allowField(true)->save($data);
        if($res){
            return array('code'=>'success');
        }else{
            return array('code'=>'error');
        }
    }
    public static function updateOne($id,$data){
        $model = new self();
        $res = $model->allowField(true)->where(array('id'=>$id))->update($data);
        if($res){
            return array('code'=>'success');
        }else{
            return array('code'=>'error');
        }
    }
    public static function deleteOne($id){
        $tmp = UserInfoModel::where('last_ranking|ranking','=',$id)->find();
        if($tmp){
            return array('code'=>'error','msg'=>'该等级下有用户！');
        }
        $res = self::destroy($id);
        if($res){
            return array('code'=>'success');
        }else{
            return array('code'=>'error','msg'=>'删除失败！');
        }
    }
}
