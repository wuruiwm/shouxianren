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


use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;

class Banner extends BaseModel
{
    protected $field = true;

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

    public static function createBanner($data)
    {
        if(self::create($data))
            throw new SuccessMessage();
    }

    public static function deleteBanner($id)
    {
        // 删除物理资源操作
//        $result = self::where('id', 'in', $id)->select();
//        foreach ($result as $k => $v) {
//            $imgId = $v['img_id']['id'];
//            $path = Attachment::where('id', $imgId)->column('filepath')[0];
//            $allPath = ROOT_PATH . 'public' . $path;
//            if (file_exists($allPath)) {
//                if (unlink($allPath)) {
//                    self::where('id', $v['id'])->delete();
//                    Attachment::where('id', $imgId)->delete();
//                }
//            }
//        }
        $ids = explode(',',$id);
        self::destroy($ids);
        throw new SuccessMessage();
    }

    public static function updateBanner($id,$data)
    {
        $self = new Banner();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();
    }

    public static function readBanner($page, $limit,$key)
    {
        if (isset($key['type_id']) and !empty($key['type_id'])) {
            $where['type_id'] = $key['type_id'];
        }
        if (empty($where['type_id'])) {
            $where = null;
        }

        $number = $page * $limit;
        $banner = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
        if (!$banner) {
            return self::dataFormat(0);
        }
        return self::dataFormat($banner, 0, 'ok', self::count());
    }

    public static function readBannerById($id){
         $result = self::get($id);
        if($result)
            throw new SuccessMessage([
                'data'=>$result
            ]);
        throw new ErrorMessage();
    }
}