<?php

namespace app\api\model;

use think\Model;

class System extends BaseModel
{
    protected function setBusinessHoursAttr($value)
    {
        return json_encode($value);
    }
    protected function getBusinessHoursAttr($value)
    {
        $res = json_decode($value,true);
        return $res?$res:[];
    }
    protected function getisOpenAttr($value)
    {
        return $value?true:false;
    }
    protected function getlimitCloseAttr($value)
    {
        return $value?true:false;
    }
    public static function getSummary()
    {
        $system = self::withTrashed()->find();
        return $system;
    }
}
