<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/11
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;




use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Controller;
use think\Loader;

Loader::import('qiniusdk.autoload', EXTEND_PATH, EXT);



class Qiniu extends Controller
{

    public function videoUploadQiniu(){
        $qiniu = new Auth('R54lP88T8LgbNOC-wFgbZgXQfcMNzZccAKv0UTNn','PCvfV3aqkw_uEGuIyJ7HjARQK1aip7xWC4_o1fph');
        $bucket = 'biaodan';
        $token = $qiniu->uploadToken($bucket);

        $files = request()->file('file');




        $tmp_ = $_FILES['file']['tmp_name'];

        $filePath = $tmp_;
        $key = 'test.mp4';


        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }
    }

}