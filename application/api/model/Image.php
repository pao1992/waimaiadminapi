<?php

namespace app\api\model;

use think\Model;

class Image extends BaseModel
{
    protected $hidden = ['update_time','delete_time', 'id', 'from'];

    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}

