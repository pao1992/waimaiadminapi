<?php

namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }
    public function getCreateTimeAttr($value)
    {
        if(!$value) return null;
        return date('H:i',$value);
    }
    public function getArriveTimeAttr($value)
    {
        if(!$value) return null;
        return date('H:i',$value);
    }
    public function getDeliverTimeAttr($value)
    {
        if(!$value) return null;
        return date('H:i',$value);
    }
    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode(($value));
    }
//    public function products()
//    {
//        return $this->hasMany('order_product', 'id', 'order_id');
//    }

    public static function getSummaryByUser($uid, $page=1, $size=15)
    {
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

    public static function getAll($where)
    {
        $model = new self();
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
        $res = $model->where($where)->order('id DESC')->select();
        return array('data' => $res, 'total' => $total);
    }

    public static function updateOne($id,$data){
        $model = new self();
        if(isset($data['delivered'])){
            if($data['delivered'] == '1'){
                $data['deliver_time'] = time();
            }else if($data['delivered'] == '2'){
                $data['arrive_time'] = time();
            }
        }
        $res = $model->allowField(true)->save($data,['id'=>$id]);
        return $res;
    }

    public function products()
    {
        return $this->belongsToMany('Product', 'order_product', 'product_id', 'order_id');
    }


}
