<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2017/5/15
 * Time: 22:17
 */
namespace imgHandle;
class img
{
    //类开始
    public $originimage = ""; //源图片文件地址
    public $imageext = ""; //源图片格式
    public $thumbimage = ""; //缩略图文件存放地址
    public $thumb_maxwidth = 1440; //缩略图最大宽度
    public $thumb_maxheight = 900; //缩略图最大高度
    public $watermark_text = ""; //水印文字内容
    public $watermark_minwidth = 300; //源图片最小宽度：大于此值时加水印
    public $watermark_minheight = 200; //源图片最小高度：大于此值时加水印
    public $watermark_fontfile = ""; //字体文件
    public $watermark_fontsize = 14; //字体大小
    public $watermark_logo = "./static/logo/logo.png"; //水印LOGO地址
    public $watermark_transparent = 20; //水印LOGO不透明度
    private $origin_width = 0; //源图片宽度
    private $origin_height = 0; //源图片高度
    private $imageQuilty = 100; //图片质量
    private $tmp_originimage = ""; //临时图片(源图片)
    private $tmp_thumbimage = ""; //临时图片(缩略图)
    private $tmp_waterimage = ""; //临时图片(水印LOGO)
    private $_waterPosition = 1; //1正中间 2右下角
    function __construct($option)
    {
        foreach ($option as $k=>$v){
            $this->$k = $v;
        }

        isset($option['watermark_logo']) && ($this->watermark_logo = getcwd().$option['watermark_logo']); //水印LOGO地址
    }
    //生成缩略图
    public function gen_thumbimage()
    {
        if ($this->originimage == "" || $this->thumbimage == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->thumb_maxwidth && $this->origin_height < $this->thumb_maxheight) {
            $this->thumb_maxwidth = $this->origin_width;
            $this->thumb_maxheight = $this->origin_height;
        } else {
            if ($this->origin_width < $this->origin_height) {
                $this->thumb_maxwidth = ($this->thumb_maxheight / $this->origin_height) * $this->origin_width;
            } else {
                $this->thumb_maxheight = ($this->thumb_maxwidth / $this->origin_width) * $this->origin_height;
            }
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        $this->gen_tmpimage_thumb();
        if ($this->tmp_originimage == "" || $this->tmp_thumbimage == "") {
            return -1;
        }
        imagecopyresampled($this->tmp_thumbimage, $this->tmp_originimage, 0, 0, 0, 0, $this->thumb_maxwidth, $this->thumb_maxheight, $this->origin_width, $this->origin_height);
        switch ($this->imageext) {
            case "gif" :
                imagegif($this->tmp_thumbimage, $this->thumbimage);
                return 1;
                break;
            case "jpg" :
                imagejpeg($this->tmp_thumbimage, $this->thumbimage, $this->imageQuilty);
                return 2;
                break;
            case "png" :
                imagepng($this->tmp_thumbimage, $this->thumbimage);
                return 3;
                break;
            default :
                return -2;
                break;
        }
    }

    //添加文字水印
    public function add_watermark1()
    {
        if ($this->originimage == "" || $this->watermark_text == "" || $this->watermark_fontfile == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->watermark_minwidth || $this->origin_height < $this->watermark_minheight) {
            return 0;
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        if ($this->tmp_originimage == "") {
            return -1;
        }
        $textcolor = imagecolorallocate($this->tmp_originimage, 255, 0, 0);
        $angle = 0;
        $px = $this->origin_width / 2 - 200;
        $py = $this->origin_height / 2 - 10;
        imagettftext($this->tmp_originimage, $this->watermark_fontsize, $angle, $px, $py, $textcolor, $this->watermark_fontfile, $this->watermark_text);
        switch ($this->imageext) {
            case "gif" :
                imagegif($this->tmp_originimage, $this->originimage);
                return 1;
                break;
            case "jpg" :
                imagejpeg($this->tmp_originimage, $this->originimage, $this->imageQuilty);
                return 2;
                break;
            case "png" :
                imagepng($this->tmp_originimage, $this->originimage);
                return 3;
                break;
            default :
                return -2;
                break;
        }
    }

    //添加LOGO水印
    public function add_watermark2()
    {
        if ($this->originimage == "" || $this->watermark_logo == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->watermark_minwidth || $this->origin_height < $this->watermark_minheight) {
            return 0;
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        $this->gen_tmpimage_waterlogo();
        if ($this->tmp_originimage == "" || $this->tmp_waterimage == "") {
            return -1;
        }
        list ($logo_width, $logo_height) = getimagesize($this->watermark_logo);
        //正中间
        $waterZb = $this->waterPosition($logo_width, $logo_height);
        $px = $waterZb ['x'];
        $py = $waterZb ['y'];
        imagecopymerge($this->tmp_originimage, $this->tmp_waterimage, $px, $py, 0, 0, $logo_width, $logo_height, $this->watermark_transparent);
        switch ($this->imageext) {
            case "gif" :
                imagegif($this->tmp_originimage, $this->originimage);
                return 1;
                break;
            case "jpg" :
                imagejpeg($this->tmp_originimage, $this->originimage, $this->imageQuilty);
                return 2;
                break;
            case "png" :
                imagepng($this->tmp_originimage, $this->originimage);
                return 3;
                break;
            default :
                return -2;
                break;
        }
    }

//递归生成文件夹
    function Directory($dir)
    {
        return is_dir($dir) or $this->Directory(dirname($dir)) and mkdir($dir, 0777);
    }

    /**
     * 上传缩略图
     * 注意上传文件大小限制
     * @param String $files $_FILES['upload'] 类型
     * @param String $path 存储的目录 默认在/static/attached/
     * @param boolean $isWater 是否添加水印
     * @return string $filePath 网页url图片路径
     */
    public function upload($files, $path, $isWater,$max_size = 2)
    {
        if (is_uploaded_file($files ['tmp_name'])) {
            $upfile = $files;
            $name = $upfile ['name'];
            $type = $upfile ['type'];
            $size = $upfile ['size'];
            $tmp_name = $upfile ['tmp_name'];
            $error = $upfile ['error'];
            //限制上传图片的大小
            if ($size > 1048576*2) {
                return array('status' => false, 'message' => "$name图片太大超过".$max_size.'MB');
            }
            //获取图片尺寸
            $rs = $this->getImageSize($tmp_name);
            if (!$rs ['status']) {
                $rs ['message'] = $name . $rs ['message'];
                return $rs;
            }
            // 创建文件夹
            $save_path = getcwd() . $path;
            if (!file_exists($save_path)) {
                $this->Directory($save_path);
            }

            if ($error == '0') {
                $fileType = substr($name, strpos($name, ".") + 1);
                $prefix = $this->getRandPrefix();
                $newName = date("YmdHi") . $prefix . "." . $fileType;
                $filepath = $save_path . '/' . $newName;
                move_uploaded_file($tmp_name, $filepath);

            }

            if ($isWater) {
                $this->water($filepath);
            }

            return array('status' => true, 'message' => $path . '/' . $newName);
        }
    }

    /**
     * 图片增加水印处理
     * @param unknown_type $image
     */
    public function water($image)
    {
        $this->originimage = $image;
        //LOGO水印
        $this->add_watermark2();
    }

    /**
     *
     * 获取随机前缀
     */
    private function getRandPrefix()
    {
        $string = "abcdefghijklmnopqrstuvwxyz0123456789";
        $prefix = '';
        for ($i = 0; $i < 4; $i++) {
            $rand = rand(0, 33);
            $prefix .= $string{$rand};
        }
        return $prefix;
    }

    //检测图片大小
    private function getImageSize($image)
    {
        list ($width, $height, $type, $attr) = getimagesize($image);

        if ($type != 2 && $type != 3) {
            return array('status' => false, 'message' => "图片格式不正确,请上传JPG或者PNG图片" . $type);
        }
        //检测图片大小
//        if ($width > 1440) {
//            return array ('status' => false, 'message' => "图片宽度请小于1440px,当前为" . $width . "px" );
//        }
//
//        if ($height > 900) {
//            return array ('status' => false, 'message' => "图片高度请小于900px,当前为" . $height . "px" );
//
//        }
        return array('status' => true);
    }

    /**
     * 生成缩略图
     *
     * @param String $imagefile 原始文件,绝对路径
     * @param String $path 保存路径
     * @param String $thumbWidth 缩略图宽度
     * @param String $thumbHeight 缩略图高度
     * @return String 缩略图url
     */
    public function reduceImage($imagefile, $save_path = "thumb",$thumbWidth, $thumbHeight,$imageQuilty = 100)
    {
        $imagefile =  getcwd().$imagefile;
        $imagefile_s = $save_path . "/s_" . basename($imagefile);//缩略图保存位置
        $imagefile_s_root = getcwd().$imagefile_s;
        //生成文件夹
        $this->Directory($save_path);
        $imagetrans = new img ([]);
        $imagetrans->imageQuilty = $imageQuilty;
        $imagetrans->originimage = $imagefile;
        $imagetrans->thumbimage = $imagefile_s_root;
        $imagetrans->thumb_maxwidth = $thumbWidth;
        $imagetrans->thumb_maxheight = $thumbHeight;
        $isokid = $imagetrans->gen_thumbimage();

        return $imagefile_s;

    }

    /**
     * 水印位置
     * @param int $logo_width
     * @param int $logo_height
     * @return 水印坐标
     */
    private function waterPosition($logo_width, $logo_height)
    {
        switch ($this->_waterPosition) {
            case 1 :
                $px = $this->origin_width / 2 - $logo_width / 2;
                $py = $this->origin_height / 2 - $logo_height / 2;
                break;
            case 2 :
                $px = $this->origin_width - $logo_width - 10;
                $py = $this->origin_height - $logo_height - 10;
                break;
            default :
                $px = $this->origin_width / 2 - $logo_width / 2;
                $py = $this->origin_height / 2 - $logo_height / 2;
                break;
        }

        return array('x' => $px, 'y' => $py);
    }

    //获取图片尺寸
    private function get_oriwidthheight()
    {
        list ($this->origin_width, $this->origin_height) = getimagesize($this->originimage);
        return 1;
    }

    /*
     * 检测图片格式
     * 原方法需要开启exif 扩展
     */
    private function get_imagetype()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1 :
                $this->imageext = "gif";
                break;
            case 2 :
                $this->imageext = "jpg";
                break;
            case 3 :
                $this->imageext = "png";
                break;
            default :
                $this->imageext = "unknown";
                break;
        }
    }

    //创建临时图片(源图片)
    private function gen_tmpimage_origin()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1 :
                $this->tmp_originimage = imagecreatefromgif($this->originimage);
                $bgcolor = imagecolorallocate($this->tmp_originimage, 0, 0, 0);
                $bgcolortrans = imagecolortransparent($this->tmp_originimage, $bgcolor);
                break;
            case 2 :
                $this->tmp_originimage = imagecreatefromjpeg($this->originimage);
                break;
            case 3 :
                $this->tmp_originimage = imagecreatefrompng($this->originimage);
                imagesavealpha($this->tmp_originimage, true);
                break;
            default :
                $this->tmp_originimage = "";
                break;
        }
    }

    //创建临时图片(缩略图)
    private function gen_tmpimage_thumb()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1 :
                $this->tmp_thumbimage = imagecreatetruecolor($this->thumb_maxwidth, $this->thumb_maxheight);
                $bgcolor = imagecolorallocate($this->tmp_thumbimage, 255, 255, 255);
                imagefill($this->tmp_thumbimage, 0, 0, $bgcolor);
                break;
            case 2 :
                $this->tmp_thumbimage = imagecreatetruecolor($this->thumb_maxwidth, $this->thumb_maxheight);
                break;
            case 3 :
                $this->tmp_thumbimage = imagecreatetruecolor($this->thumb_maxwidth, $this->thumb_maxheight);
                $bgcolor = imagecolorallocate($this->tmp_thumbimage, 255, 255, 255);
                imagefill($this->tmp_thumbimage, 0, 0, $bgcolor);
                imagealphablending($this->tmp_thumbimage, false);
                imagesavealpha($this->tmp_thumbimage, true);
                break;
            default :
                $this->tmp_thumbimage = "";
                break;
        }
    }

    //创建临时图片(LOGO水印)
    private function gen_tmpimage_waterlogo()
    {
        $ext = $this->getImgext($this->watermark_logo);
        switch ($ext) {
            case 1 :
                $this->tmp_waterimage = imagecreatefromgif($this->watermark_logo);
                $bgcolor = imagecolorallocate($this->tmp_waterimage, 0, 0, 0);
                $bgcolortrans = imagecolortransparent($this->tmp_waterimage, $bgcolor);
                break;
            case 2 :
                $this->tmp_waterimage = imagecreatefromjpeg($this->watermark_logo);
                break;
            case 3 :
                $this->tmp_waterimage = imagecreatefrompng($this->watermark_logo);
                imagesavealpha($this->tmp_waterimage, true);
                break;
            default :
                $this->tmp_waterimage = "";
                break;
        }
    }

    /*
     * 获取后缀名
     */
    public function getImgext($filename)
    {
        return exif_imagetype($filename);
    }

    //释放资源
    public function __destruct()
    {
        if (is_object($this->tmp_originimage) == true) {
            imagedestroy($this->tmp_originimage);
        }
        if (is_object($this->tmp_thumbimage) == true) {
            imagedestroy($this->tmp_thumbimage);
        }
        if (is_object($this->tmp_waterimage) == true) {
            imagedestroy($this->tmp_waterimage);
        }
    }
}