<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/17
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

// 商户
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use think\Loader;

Loader::import('phpqrcode.phpqrcode', EXTEND_PATH, EXT);
class Merchant extends BaseModel
{
    public function getQrcodeUrlAttr($value)
    {
        if(empty($value)){
            return "/static/admin/images/goods-3.jpg";
        }
        return is_https().DS.$value;
    }

    public function getImgAttr($value)
    {

        $img_url = Attachment::where('id', $value)->column('filepath');
        $http = is_https();
        if ($img_url) {
            return ['id' => $value, 'url' => $http . $img_url[0]];
        }
    }

    public function getUserAttr($value)
    {
        return User::get($value);
    }


    public function getTypeAttr($value)
    {

        $title = MerchantCate::where('id', $value)->column('title');
        return [
            'id'=>$value,
            'title'=>$title
        ];
    }


    public static function updateMerchant($id,$data)
    {
        $self = new Merchant();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();
    }

    public static function readMerchant($page, $limit,$key)
    {
        if (isset($key['title']) and !empty($key['title'])) {
            $where['title'] = ['like', '%' . $key['title'] . '%'];
        }

        if (empty($where['title'])) {
            $where = null;
        }

        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function readMerchantById($id){
        $result = self::get($id);
        if($result)
            throw new SuccessMessage([
                'data'=>$result
            ]);
        throw new ErrorMessage();
    }

    public static function qrcode($id)
    {
        $id_ = 'aoeiuü'.$id;
        $value = encryptDecrypt(config('qrcode.qrcode_salt'),$id_,1);
        $qrcode = new \QRcode();
        $errorCorrectionLevel = "M";
        $matrixPointSize = "10";
        ob_clean();
        $filepath = 'uploads/admin/qrcode/'.$value.'.png';
        $qrcode::png($value,$filepath, $errorCorrectionLevel, $matrixPointSize, 2);

        $self = new Merchant();
        $self->allowField(true)->save(['qrcode_url'=>$filepath],['id'=>$id]);
        return is_https().DS.$filepath;
    }
    public static function getMerchantById($id){
        return self::get($id);
    }
}