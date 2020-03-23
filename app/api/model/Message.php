<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/31 21:50
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use app\api\service\Token;

class Message extends BaseModel
{
    public function getIdAttr($value){
        $user_id = Token::getCurrentTokenUserId();
        $result = MessageUser::where('user',$user_id)->where('message',$value)->find();
        if($result){

            return [
                'id'=>$value,
                'is_read'=>1,
                'is_del'=>$result['status']
            ];
        }

        if($result['status']===NULL){
            $result['status'] = 0;
        }
        return [
            'id'=>$value,
            'is_read'=>0,
            'is_del'=>$result['status']
        ];
    }

    public function getCreateTimeAttr($value){
        return timeToStr($value,'m-d H:i');
    }

    public static function getMessageList($page, $limit){
        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->where('status',1)
            ->field(['id','title','create_time'])
            ->order('create_time desc')
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function getMessage($id,$user_id){
        MessageUser::create([
            'user'=>$user_id,
            'message'=>$id
        ]);
        return self::field(['title','content','create_time'])->where('id',$id)->find();
    }

    public static function getMessageNotReadList(){
        $result = self::where('status',1)
            ->field(['id','title'])
            ->order('create_time desc')
            ->select();

        $num =0;
        foreach($result as $k=>$v){
            if($v['id']['is_read']==0){
                $num+=1;
            }
        }
        return $num;
    }
}