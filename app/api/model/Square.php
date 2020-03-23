<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/30 21:21
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use app\admin\model\Attachment;
use app\admin\model\Qiniuconfig;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;

class Square extends BaseModel
{
    public function getUserAttr($value)
    {
        $result = User::exist('id', $value, ['avatar', 'nickname'], false);
        return $result;
    }

    public function getImgAttr($value)
    {
        $result = Attachment::where('id', 'in', $value)->field(['id', 'filepath','source'])->select();
        $prefix_url = Qiniuconfig::where('id',1)->value('prefix_url');
        foreach ($result as $k => $v) {
            if($v['source']!='qiniu'){
                $result[$k]['filepath'] = is_https() . '/' . $result[$k]['filepath'];
            }else{
                $result[$k]['filepath'] = $prefix_url . '/' . $result[$k]['filepath'];
            }
        }

        return $result;
    }

    public function getTidAttr($value)
    {
        $user_id = Token::getCurrentTokenUserIdAlone();
        if ($user_id) {
            $result = Like::where('article', $value)
                ->where('user', $user_id)
                ->where('type', 3)
                ->find();
            if ($result) {
                return ['id' => $value, 'is_awesome' => 'yes'];
            } else {
                return ['id' => $value, 'is_awesome' => 'no'];
            }
        } else {
            return ['id' => $value, 'is_awesome' => 'no'];
        }
    }

    public function commentList()
    {
        return $this->hasMany('Comment', 'article', 'id')->where('type', '3')->order('create_time asc');
    }

    public function likeList()
    {
        return $this->hasMany('Like', 'article', 'id')->where('type', '3')->order('create_time desc');
    }

    public function getCreateTimeAttr($value)
    {
        return timeToStr(GetMkTime($value), 'm-d H:i');
    }

    public static function publish($post)
    {
      $bool = self::isCheckCount($post['user']);
        if ($bool) {
            $self = new Square();
            $self->create($post);
            $id = $self->getLastInsID();
            if ($id) {
                self::update(['tid' => $id],['id'=>$id]);
                return true;
            }
        }
        throw new ErrorMessage(['msg' => '今天已达到发布上限，请明天再来。']);            
      
    }

    private static function isCheckCount($user_id)
    {
        $stint = User::where('id', $user_id)->field('stint')->find()['stint']; // 3
        $now_time = GetMkTime(timeToStr(time(), 'Y-m-d'));
        $count = self::where([
            'user' => $user_id
        ])->where('create_time', '>', $now_time)
            ->count();

        if ($count >= $stint)
            return false;
        return true;
    }

    public static function readList($page, $limit)
    {
        $number = $page * $limit;
        $self = new Square();
        $result = $self::limit($number, $limit)
            ->order('create_time desc')
            ->with('commentList')
            ->with('likeList')
            ->where('is_show', 1)
            ->select();

        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }
}