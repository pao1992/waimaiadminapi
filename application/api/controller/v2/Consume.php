<?php
namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\Consume as ConsumeModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessReturn;
use think\Db;

class Consume extends BaseController
{
    /**
     * 创建新的记录
     * @param $param
     * @return SuccessMessage
     * @throws BaseException
     */
    Public function createOne()
    {
        $res = ConsumeModel::createOne(input('post.'));
        return new SuccessMessage();
    }

    public function getAll()
    {
        $res = ConsumeModel::getAll(input('get.'),true);
        return new SuccessReturn(
            array('data'=>$res)
        );
    }
    /**
     * saleBar 销售额曲线图
     * @return SuccessReturn
     *
     */
    public function saleBar(){
        $data = input('get.');
        $res = ConsumeModel::saleBar($data);
        $return_data = array();
        foreach ($res as $k=>$v){
            $return_data['xAxis'][] = $v['date'] ;
            $return_data['series'][] = $v['total_price'];
        }
        return new SuccessReturn(['data'=>$return_data]);
    }
    /**
     * staffSaleBar 工作人员完成额
     * @return SuccessReturn
     *
     */
    public function staffSaleBar(){
        $data = input('get.');
        $res = ConsumeModel::staffSaleBar($data);
        $return_data = array();
        foreach ($res as $k=>$v){
            $return_data['xAxis'][] = $v['staff_name'] ;
            $return_data['series'][] = $v['total_price'];
        }
        return new SuccessReturn(['data'=>$return_data]);
    }

    /**
     * 产品销售额饼图
     * @return SuccessReturn
     */
    public function productSalePie(){
        $data = input('get.');
        $res = ConsumeModel::productSalePie($data);
        $return_data = array();
        foreach ($res as $k=>$v){
            $return_data['legend'][] = $v['product_name'] ;
            $return_data['series'][] = ['value'=>$v['total_price'],'name'=>$v['product_name']];
        }
        return new SuccessReturn(['data'=>$return_data]);
    }
    /**
     * 产品销售量饼图
     * @return SuccessReturn
     */
    public function productPie(){
        $data = input('get.');
        $res = ConsumeModel::productPie($data);
        $return_data = array();
        foreach ($res as $k=>$v){
            $return_data['legend'][] = $v['product_name'] ;
            $return_data['series'][] = ['value'=>$v['number'],'name'=>$v['product_name']];
        }
        return new SuccessReturn(['data'=>$return_data]);
    }
}