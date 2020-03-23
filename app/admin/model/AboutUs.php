<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/22
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


use app\lib\exception\SuccessMessage;

class AboutUs extends BaseModel
{
    protected  $hidden = ['update_time','status','create_time'];
    public function getImgAttr($value)
    {

        $img_url = Attachment::where('id', $value)->column('filepath');
        $http = is_https();
        if ($img_url) {
            return ['id' => $value, 'url' => $http . $img_url[0]];
        }
    }

    public static function updateAboutUs($data,$id){
        $self = new AboutUs();
        return $self->allowField(true)->save($data,['id'=>$id]);

    }

    public static function readAboutUs($id)
    {
        return self::find($id);
    }
}