<?php
/**
 * Created by 七月
 * User: 七月
 * Date: 2017/2/15
 * Time: 13:40
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\BannerItem as BannerItemModel;
use app\api\model\Image as ImageModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BaseException;
use app\lib\exception\MissException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SuccessReturn;
use app\api\controller\v2\Upload as UploadController;
use app\lib\exception\UpdateException;
use think\Db;

/**
 * Banner资源
 */
class Banner extends BaseController
{

    /**
     * 获取Banner信息
     * @url     /banner/:id
     * @http    get
     * @param   int $id banner id
     * @return  array of banner item , code 200
     * @throws  MissException
     */
    public function getAll()
    {
        $banner = BannerModel::getAll();
        if (!$banner) {
            throw new MissException([
                'msg' => '没有banner',
                'errorCode' => 40000
            ]);
        }
        return new SuccessReturn([
            'data' => $banner
        ]);
    }

    public function getOne($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $banner = BannerModel::getOne($id);
        if (!$banner) {
            throw new MissException([
                'msg' => '请求banner不存在',
                'errorCode' => 40000
            ]);
        }
        return new SuccessReturn([
            'data' => $banner
        ]);
    }

    public function createOne()
    {
        $data = input('post.');
        $res = BannerModel::createOne($data);
        if (!$res) {
            throw new BaseException(['msg' => '创建失败！']);
        }
        return new SuccessReturn([
            'data' => $res
        ]);
    }

    public function updateOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $model = new BannerModel();
        $data = input('put.');
        $res = $model->allowField(true)->isUpdate(true)->save($data, ['id' => $id]);
        if (!$res) {
            return new UpdateException();
        }
        return new SuccessMessage();
    }

    public function uploadImage()
    {
        $controller = new UploadController();
        $res = $controller->saveImg(config('common.banner_img_path'));
        $thumb_path = config('common.banner_img_path');
        $width = config('common.banner_img_width');
        $height = config('common.banner_img_height');
        $quality = config('common.banner_img_quality');
        $url = $controller->reduceImage($res, $thumb_path, $width, $height, $quality);
        return ['url' => $url, 'from' => 1];
    }

    public function createItem()
    {
        // 启动事务
        Db::startTrans();
        try {
            $data = input('post.');
            $img = $data['img'];
            $imgModel = new ImageModel();
            $img = $imgModel->save($img);
            $data['img_id'] = $imgModel->id;
            $bannerItemModel = new BannerItemModel;
            $bannerItemModel->allowField(true)->save($data);
            // 提交事务
            Db::commit();
            return new SuccessMessage();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw new BaseException(['msg' => '保存失败！']);
        }

    }

    public function updateItem($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $model = new BannerItemModel();
        $data = input('put.');
        $res = $model->allowField(true)->isUpdate(true)->save($data, ['id' => $id]);
        if (!$res) {
            return new UpdateException();
        }
        return new SuccessReturn();
    }

    public function deleteItem($id)
    {
        (new IDMustBePositiveInt())->check($id);
        // 启动事务
        Db::startTrans();
        try {
            $model = new BannerItemModel();
            $bannerItem = $model->with('img')->find($id);
            $bannerItem->together('img')->delete();
            // 提交事务
            Db::commit();
            return new SuccessMessage();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw new BaseException(['msg' => '删除失败！']);
        }

    }

    public function ItemReSort()
    {
        $data = input('post.');
        $model = new BannerItemModel();
        // 启动事务
        Db::startTrans();
        try {
            foreach ($data['sort'] as $k => $v) {
                $model->save(['sort' => $k], ['id' => $v]);
            }
            // 提交事务
            Db::commit();
            return new SuccessMessage();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw new BaseException(['msg' => '排序失败！']);
        }

    }
}