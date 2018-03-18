<?php

namespace app\api\controller\v2;

use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct as OrderProductModel;
use app\lib\exception\SuccessReturn;
use think\Db;
use think\Request;

class Statistic extends BaseController
{
    /**
     * 产品销量统计
     * @return SuccessReturn
     */
    public static function productsPie()
    {
        //必须设置content-type:application/json
        $request = Request::instance();
        $params = $request->param();
        $where = [];
        $where['o.delete_time'] = ['exp',' is NULL'];
        if (!empty($params['s_time']) && !empty($params['e_time'])) {
            $where['o.create_time'] = [['>=', strtotime($params['s_time'] . ' 0:0:0')], ['<=', strtotime($params['e_time'] . ' 23:59:59')], 'and'];
        }
        $res = Db::table('order_product op')
            ->join([['product p','op.product_id = p.id'],['order o','op.order_id = o.id']])
            ->field('p.price,SUM(op.count) AS count,p.name,p.id')->group('p.id')->where($where)->select();
        $res = $res->toArray();
        $return_data = $res;
        foreach ($res as $k=>$v){
            $return_data[$k]['total_price'] = $v['price']*$v['count'];
        }
        return new SuccessReturn([
            'data'=>$return_data
        ]);
    }
    /**
     * 销量统计
     * @return SuccessReturn
     */
    public static function salesVolumeBar()
    {
        //必须设置contetn-type:application/json
        $request = Request::instance();
        $data = $request->param();
        $where = array();
        if (!empty($data['s_time']) && !empty($data['e_time'])) {
            $where['create_time'] = [['>=', strtotime($data['s_time'] . ' 0:0:0')], ['<=', strtotime($data['e_time'] . ' 23:59:59')], 'and'];
        } else {
            $data['s_time'] = date('Y-n-j', strtotime('last month'));
            $data['e_time'] = date('Y-n-j');
        }
        $model = new OrderModel();
        $res = $model->where($where)->field(['date', 'SUM(`total_price`) as total_price'])->group('date')->select();
        $res_data = array();
        foreach ($res->toArray() as $k => $v) {
            $res_data[$v['date']] = $v;
        }
        $date_arr = array();
        $current_date = strtotime($data['s_time']);
        while ($current_date <= strtotime($data['e_time'])) {
            $temp = date('Y-n-j', $current_date);
            $date_arr[] = isset($res_data[$temp]) ? $res_data[$temp] : ['date' => $temp, 'total_price' => 0];
            $current_date += 3600 * 24;
        }
        return New SuccessReturn(['data' => $date_arr]);
    }
}
