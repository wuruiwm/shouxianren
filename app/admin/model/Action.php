<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use think\Loader;


Loader::import('qiniusdk.autoload', EXTEND_PATH, EXT);
class Action extends BaseModel
{
    public function getNewsTypeAttr($value)
    {
        return ['图文模式', '视频模式'][$value];
    }

    public function getActionCateAttr($value)
    {
        $result = ActionCate::find($value)['title'];
        return [
            'id'=>$value,
            'action_cate'=>$result
        ];
    }


    public function getMerchantAttr($value)
    {
        $result = Merchant::find($value)['title'];
        return [
            'id'=>$value,
            'title'=>$result
        ];
    }


    public function getHeadImgAttr($value)
    {
        $result = Attachment::where('id', 'in', $value)->field(['id', 'filepath'])->select();
        return $result;
    }

    public function getVideoIdAttr($value)
    {
        $video = VideoAttachment::find($value);
        return [
            'id'=>$value,
            'url'=>$video['filepath'],
            'fileext'=>$video['fileext']
        ];
    }




    public static function createAction($data)
    {
        $self = new Action();
        $self->create($data);
        $id = $self->getLastInsID();
        if ($id){
            self::updateAction($id,['tid'=>$id]);
            throw new SuccessMessage();
        }

    }

    public static function deleteAction($id)
    {

        /**
         * 以下操作 是把该活动里的 图文/视频/文字 等资源都删除
         */
        /*$result = self::where('id', 'in', $id)->select();
        foreach ($result as $k => $v) {
            $video_id = $v['video_id']['id'];
            $video_info = VideoAttachment::where('id',$video_id)->find();
            self::videoDelete($video_info['use_source'],$video_info['filepath']);
            VideoAttachment::where('id',$video_id)->delete();
            // 删除封面图
            $imgIds = $v['head_img'];
            foreach($imgIds as $n=>$m){
                $allPath = ROOT_PATH . 'public' . $m['filepath'];
                if (file_exists($allPath)) {
                    unlink($allPath);
                }
                Attachment::where('id', $m['id'])->delete();
            }
            self::where('id',$v['id'])->delete();
        }*/
//        return $id;
        $ids = explode(',',$id);
        self::destroy($ids);
        throw new SuccessMessage();
    }

    public static function updateAction($id, $data)
    {
        $self = new Action();
        if ($self->allowField(true)->save($data, ['id' => $id]))
            throw new SuccessMessage();
    }

    public static function readAction($page, $limit, $key)
    {
        if (isset($key['title']) and !empty($key['title'])) {
            $where['title'] = ['like', '%' . $key['title'] . '%'];
        }
        if (isset($key['action_cate']) and !empty($key['action_cate'])) {
            $where['action_cate'] = $key['action_cate'];
        }
        if (isset($key['status']) and !empty($key['status'])) {
            $where['status'] = $key['status'];
        }
        if (isset($key['top']) and !empty($key['top'])) {
            $where['top'] = $key['top'];
        }
        if (isset($key['merchant']) and !empty($key['merchant'])) {
            $where['merchant'] = $key['merchant'];
        }

        if (empty($where['action_cate']) and empty($where['status']) and empty($where['top']) and empty($where['title']) and empty($where['merchant'])) {
            $where = null;
        }

        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
//        $news['prefix_url'] = is_https();
        if (!$result) {
            return self::dataFormat(0);
        }
//        return json($where);
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function readActionById($id)
    {
        $result = self::get($id);
        $result['img_prefix_url'] = is_https();

        $video = VideoAttachment::find($result['video_id']);
        if ($video['use_source'] == 1) {
            $result['video_prefix_url'] = Qiniuconfig::readQiniuconfig(1)['prefix_url'];
        } else {
            $result['video_prefix_url'] = is_https();
        }

        if ($result)
            throw new SuccessMessage([
                'data' => $result
            ]);
        throw new ErrorMessage();
    }


    public static function videoDelete($use_source,$path)
    {

        if($use_source==1){
            $result = Qiniuconfig::readQiniuconfig(1);
            $qiniu = new Auth($result['accesskey'], $result['secretkey']);
            $bucket = $result['storage_name'];
            $config = new Config();
            $bucketManager = new BucketManager($qiniu, $config);
            $err = $bucketManager->delete($bucket, $path);
            return !$err;

        }else{
            $path = ROOT_PATH . 'public' .$path;
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        }

    }

    public static function readListByIdComment($page = '', $limit = '', $id = '')
    {
        $number = $page * $limit;
        $result = Comment::limit($number, $limit)
            ->order('create_time desc')
            ->where('article', $id)
            ->where('type', 2)
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', count($result));
    }

    public static function lessCommentNumber($id, $count)
    {
        $self = new News();
        return $self->where('id', $id)->setDec('comment_num', $count);
    }

    public static function deleteByIds($ids = null)
    {
        $ids = explode(',', $ids);
        return self::destroy($ids);
    }
}