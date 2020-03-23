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

namespace app\api\model;


use app\admin\model\Attachment;
use app\admin\model\Qiniuconfig;
use app\admin\model\VideoAttachment;
use app\api\service\Token;

class News extends BaseModel
{
    public function getNewsCateAttr($value)
    {
        $title = NewsCate::find($value)['title'];
        return ['id' => $value, 'title' => $title];
    }

    public function getCreateTimeAttr($value)
    {
        return timeToStr(GetMkTime($value), 'm-d H:i');
    }

    public function getTidAttr($value)
    {
        $user_id = Token::getCurrentTokenUserIdAlone();
        if($user_id){
            $result = Like::where('article', $value)
                ->where('user', $user_id)
                ->where('type', 1)
                ->find();
            if ($result) {
                return ['id' => $value, 'is_awesome' => 'yes'];
            } else {
                return ['id' => $value, 'is_awesome' => 'no'];
            }
        }else{
            return ['id' => $value, 'is_awesome' => 'no'];
        }
    }

    public function commentList()
    {
        return $this->hasMany('Comment', 'article', 'id')->where('type', '1')->order('create_time desc');
    }


    public function getHeadImgAttr($value)
    {
        //$result = Attachment::where('id', 'in', $value)->field(['id', 'filepath'])->select();
        //foreach ($result as $k => $v) {
        //    $result[$k]['filepath'] = is_https() . '/' . $result[$k]['filepath'];
       // }
      //  return $result;
      $result = Attachment::where('id', 'in', $value)->field(['id', 'filepath','original_image'])->select();
        foreach ($result as $k => $v) {

            if(empty($v['original_image'])){
                $img_path = is_https() . $v['filepath'];
            }else{
                $img_path = is_https() . '/' . $v['original_image'];
            }
            $result[$k]['original_image'] =  is_https() . '/' . $v['filepath'];;
            $result[$k]['filepath'] = $img_path;
        }
        return $result;
    }

    public function getAwesomeNumAttr($value)
    {
        return ($value < 0) ? '0' : $value;
    }

    public function getCommentNumAttr($value)
    {
        return ($value < 0) ? '0' : $value;
    }


    public function getVideoIdAttr($value)
    {
        $video = VideoAttachment::find($value);
        if ($video['use_source'] == 1) {
            $video_prefix_url = Qiniuconfig::readQiniuconfig(1)['prefix_url'];
        } else {
            $video_prefix_url = is_https();
        }
        return [
            'id' => $value,
            'url' => $video_prefix_url . '/' . $video['filepath'],
            'fileext' => $video['fileext']
        ];
    }

    public static function readNews($page, $limit, $type)
    {

        if (isset($type) and !empty($type)) {
            $where['news_cate'] = $type;
        }
        if (empty($where['news_cate'])) {
            $where = null;
        }

        $number = $page * $limit;
        $self = new News();
        $news = $self::limit($number, $limit)
            ->order('top desc')
            ->order('sort desc')
            ->order('create_time desc')
            ->field(['content', 'update_time', 'delete_time', 'status'], true)
            ->where($where)
            ->where('status', 1)
            ->select();

        if (!$news) {
            return self::dataFormat(0);
        }
        if(isset($type) and !empty($type)){
            return self::dataFormat($news, 0, 'ok', count($news));
        }else{
            return self::dataFormat($news, 0, 'ok', self::count());
        }
    }

    public static function readNewsById($id)
    {
        $result = self::field(['update_time', 'delete_time', 'status', 'sort', 'top', 'news_cate'], true)
            ->with('commentList')
            ->where('status', 1)
            ->find($id);
        $self = new News();
        $self->where('id', $id)->setInc('read_num');
        return $result;
    }
  
  public static function readIndependentNewsById($id){
        $result = self::field(['update_time', 'delete_time', 'status', 'sort', 'top', 'news_cate','tid'], true)
            ->where('status', 1)
            ->find($id);
        $self = new News();
        $self->where('id', $id)->setInc('read_num');
        return $result;
    }

    public static function newSearch($page,$limit,$key){
        $number = $page * $limit;
        $self = new News();
        $news = $self::limit($number, $limit)
            ->where('title|content','like',"%$key%")
            ->order('create_time')
            ->select();
        if (!$news) {
            return self::dataFormat(0);
        }
        return self::dataFormat($news, 0, 'ok', count($news));
    }
}