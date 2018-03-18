<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/19
 * Time: 11:28
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use imgHandle\img;
class Upload extends BaseController
{
    public function productImgUpload(){
        $res = $this->saveImg(config('common.product_img_path'));
        $thumb_path = config('common.product_img_path');
        $width = config('common.product_main_img_width');
        $height = config('common.product_main_img_height');
        $quality = config('common.product_img_quality');
        $url = $this->reduceImage($res,$thumb_path,$width,$height,$quality);
        return ['url'=>$url,'from'=>1];
    }
    public function themeTopicImgUpload(){
        $res = $this->saveImg(config('common.theme_img_path'));
        $thumb_path = config('common.theme_img_path');
        $width = config('common.theme_topic_img_width');
        $height = config('common.theme_topic_img_height');
        $quality = config('common.theme_img_quality');
        $url = $this->reduceImage($res,$thumb_path,$width,$height,$quality);
        return ['url'=>$url,'from'=>1];
    }


    public function saveImg($path,$isWater = false,$filename = 'file'){
        //先上传
        $option = [
            'watermark_transparent'=>config('common.watermark_transparent'),//水印透明度
            'watermark_logo'=>config('common.watermark_logo'), //水印LOGO地址
        ];
        $img = new img($option);
        $res = $img->upload($_FILES[$filename],$path,true);//上传处理，顺便加水印
        return $res['message'];//文件储存的路径
    }
    public function reduceImage($file,$save_path,$width,$height,$quality = 100){
        $option = [
            'watermark_transparent'=>config('common.watermark_transparent'),//水印透明度
            'watermark_logo'=>config('common.watermark_logo'), //水印LOGO地址
            'thumb_maxwidth'=>config('common.thumb_maxwidth'),//缩略图最大宽度
            'thumb_maxheight'=>config('common.thumb_maxheight'), //缩略图最大高度
        ];
        $img = new img($option);
        $pic_thumb = $img->reduceImage($file,$save_path, $width,$height,$quality);
        return $pic_thumb;
    }





}