<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-21 14:08:32
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-11-21 15:18:42
 */
namespace app\api\model;


use app\admin\model\Attachment;
use app\lib\exception\SuccessMessage;

class Banner extends BaseModel
{
    protected $hidden = ['sort','delete_time','create_time','update_time','status'];

    public function getImgIdAttr($value)
    {

        $img_url = Attachment::where('id', $value)->column('filepath');
        $http = is_https();
        if ($img_url) {
            return ['id' => $value, 'url' => $http . $img_url[0]];
        }
    }

    public function getTypeIdAttr($value)
    {
        $val = ['', '首页轮播图', '广场轮播图', '关于我们轮播图'][$value];
        return ['id'=>$value,'title'=>$val];
    }

    public static function readBanner($type_id)
    {
        if(!empty($type_id)){
            $where = ['type_id'=>$type_id];
        }else{
            $where=[];
        }

        $banner = self::order('sort desc')
            ->where($where)
            ->where(['status'=>0])
            ->select();
        //echo json_encode($banner);exit();
        //echo is_https();exit();
        // foreach ($banner as $k => $v) {
        //     echo $v['img_id']['url'];
        // }
        // exit();
        throw new SuccessMessage([
            'data'=>$banner
        ]);
    }
}