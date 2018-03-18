<?php

namespace app\api\model;

use think\Model;

class Banner extends BaseModel
{
    public function items()
    {
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }

    protected $autoWriteTimestamp = true;
    protected $hidden = ['create_time','delete_time', 'update_time'];
    public static function getAll(){
        return self::select();
    }
    /**
     * @param $id int banner所在位置
     * @return Banner
     */
    public static function getOne($id)
    {
        $banner = self::with(['items'=>function($query){$query->with('img')->order('sort');}])->find($id);
        return $banner;
    }
    public static function getDataAttr($value)
    {
        return json_decode($value);
    }
    public static function createOne($data){
        $model = new self();
        return $model->allowField(true)->save($data);
    }

}
