<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/8
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


use think\Model;
use think\Request;
use traits\model\SoftDelete;

class BaseModel extends Model
{
    use SoftDelete;
    protected $field = true;



    public static function dataFormat($data=[],$code=0,$msg='',$count=0){

        $request = Request::instance();
        $url = $request->url();
        if($data==null){
            return json([
                'code'=>0,
                'msg'=>'暂无相关数据',
                'count'=>0,
                'data'=>null,
                'requestUrl'=>$url,
            ],200);
        }
        return json([
            'code'=>0,
            'msg'=>'总共有'. $count . '条数据',
            'count'=>$count,
            'data'=>$data,
            'requestUrl'=>$url,
        ]);

    }
}