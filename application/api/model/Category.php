<?php

namespace app\api\model;

use think\Model;

class Category extends BaseModel
{
    public function productsSummary()
    {
        return $this->hasMany('Product', 'category_id', 'category_id')
            ->field('category_id,product_id,product_name');
    }
//
//    public function img()
//    {
//        return $this->belongsTo('Image', 'topic_img_id', 'id');
//    }

    public static function getCategories($ids)
    {
        $categories = self::select($ids);
        return $categories;
    }
    public static function getAll()
    {
        $categories = self::select();
        return $categories;
    }
    public static function getById($id)
    {
        return self::get($id);
    }
    public static function getCategoriesWithProds()
    {
        $category = self::with('productsSummary')->select();
        return $category;
    }
}
