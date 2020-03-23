<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/9
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

class News extends BaseModel
{

    public function getNewsTypeAttr($value)
    {
        return ['图文模式', '视频模式'][$value];
    }

    public function getNewsCateAttr($value)
    {
        $result = NewsCate::find($value)['title'];
        return [
            'id' => $value,
            'title' => $result
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
            'id' => $value,
            'url' => $video['filepath'],
            'fileext' => $video['fileext']
        ];
    }


    public static function createNews($data)
    {
        $self = new News();
        $self->create($data);
        $id = $self->getLastInsID();
        if ($id){
            self::updateNews($id,['tid'=>$id]);
            throw new SuccessMessage();
        }
    }

    public static function deleteNews($id)
    {
        /**
         * 以下屏蔽的是删除物理资源
         */
        /*$result = self::where('id', 'in', $id)->select();
//        return json($result);
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

        $ids = explode(',', $id);
        self::destroy($ids);
        throw new SuccessMessage();
    }

    public static function updateNews($id, $data)
    {
        $self = new News();
        if ($self->allowField(true)->save($data, ['id' => $id]))
            throw new SuccessMessage();
    }

    public static function readNews($page, $limit, $key)
    {
        if (isset($key['title']) and !empty($key['title'])) {
            $where['title'] = ['like', '%' . $key['title'] . '%'];
        }
        if (isset($key['news_cate']) and !empty($key['news_cate'])) {
            $where['news_cate'] = $key['news_cate'];
        }
        if (isset($key['status']) and !empty($key['status'])) {
            $where['status'] = $key['status'];
        }
        if (isset($key['top']) and !empty($key['top'])) {
            $where['top'] = $key['top'];
        }
        if (empty($where['news_cate']) and empty($where['status']) and empty($where['top']) and empty($where['title'])) {
            $where = null;
        }

        $number = $page * $limit;
        $news = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
//        $news['prefix_url'] = is_https();
        if (!$news) {
            return self::dataFormat(0);
        }
//        return json($where);
        return self::dataFormat($news, 0, 'ok', self::count());
    }

    public static function readNewsById($id)
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


    public static function videoDelete($use_source, $path)
    {
        if ($use_source == 1) {
            $result = Qiniuconfig::readQiniuconfig(1);
            $qiniu = new Auth($result['accesskey'], $result['secretkey']);
            $bucket = $result['storage_name'];
            $config = new Config();
            $bucketManager = new BucketManager($qiniu, $config);
            $err = $bucketManager->delete($bucket, $path);
            return !$err;

        } else {
            $path = ROOT_PATH . 'public' . $path;
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
            ->where('type', 1)
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