<?php

namespace app\api\model;

use think\Model;

class OrderProduct extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected function getSpecAttr($value){
        return json_decode($value);
    }
    public function order()
    {
        return $this->belongsTo('order');
    }
    public function item()
    {
        return $this->belongsTo('Product', 'product_id', 'id')->bind('price,name');
    }
}
