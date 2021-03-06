<?php

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['img_id', 'banner_id', 'delete_time'];

    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
    public function updateSort($id,$sort){
        self::save(['sort'=>$sort],['id'=>$id]);
    }
}
