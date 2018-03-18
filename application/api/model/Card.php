<?php

namespace app\api\model;

use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use think\Model;
use think\Db;

class Card extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    //多对多关联用户
    public function userCard(){
        return $this->hasMany('UserCard','card_id','card_id');
    }
    public function user()
    {
        return $this->belongsToMany('User','user_card','user_id','card_id');
    }
    public function setStartAttr($value)
    {
        return strtotime($value);
    }
    public function setEndAttr($value)
    {
        return strtotime($value);
    }
    public function getStartAttr($value)
    {
        return date('Y/m/d',$value);
    }
    public function getEndAttr($value)
    {
        return date('Y/m/d',$value);
    }
    public static function getCouponsByDate($date){
        $res = self::where('start','<',$date)
            ->where('end','>',$date)->select();
        return $res;
    }
    public static function getDiffUsers($card_id,$filter){
        $res = self::where('id = '.$card_id)
            ->select();
        return $res;
    }

    /**
     * 创建用户卡/绑定用户
     * @param $card_id
     * @param $user_id
     * @return BaseException|SuccessMessage
     */
    public static function bindUser($card_id,$user_id){
        $card = self::get($card_id);
        //增加关联数据
        $data = array(
            'card_id'=>$card_id,
            'user_id'=>$user_id,
            'price'=>$card['price'],
            'title'=>$card['title'],
            'times'=>$card['times'],
            'remaining_times'=>$card['times']
        );
        $res = UserCard::createOne($data);
        if(empty($res)){
            return new BaseException();
        }
        return new SuccessMessage(
            array(
                'msg'=>'create success!',
            )
        );

    }

//
//    public function img()
//    {
//        return $this->belongsTo('Image', 'topic_img_id', 'id');
//    }
}
