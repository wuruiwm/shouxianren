<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/24
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use app\admin\model\Attachment;

class Merchant extends BaseModel
{
    protected $hidden = ['delete_time','create_time','update_time'];
    public function getImgAttr($value)
    {

        $img_url = Attachment::where('id', $value)->column('filepath');
        $http = is_https();
        if ($img_url) {
            return ['id' => $value, 'url' => $http . $img_url[0]];
        }
    }

    public static function readMerchant($id){
        return self::get($id);
    }
}