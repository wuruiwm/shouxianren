<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/12
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Qiniuconfig;
use app\admin\model\VideoAttachment;
use app\lib\exception\SuccessMessage;
use think\Controller;
use think\Loader;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Session;

Loader::import('qiniusdk.autoload', EXTEND_PATH, EXT);

class Upload extends Controller
{
    public function uploadVideo()
    {

        $result = Qiniuconfig::readQiniuconfig(1);

        if ($result['type'] == 1) { //type为存储方式  1为'七牛云存储';


            $name = $_POST["name"];
            $size = $_POST["size"];
            $type = $_POST["type"];
            $lastModifiedDate = $_POST["lastModifiedDate"];
            if (array_key_exists('chunks', $_POST)) {
                $chunks = $_POST["chunks"];
            }
            if (array_key_exists('chunk', $_POST)) {
                $chunk = $_POST["chunk"];
            }
            $upload = $_SERVER["DOCUMENT_ROOT"] . "/uploads/admin/video";
            $tmp = $_SERVER["DOCUMENT_ROOT"] . "/uploads/tmp";
            if (!is_dir($tmp)) {
                mkdir($tmp, 0777, true);
            }


            if (!isset($chunks)) {

//                move_uploaded_file($_FILES["file"]["tmp_name"], $upload . "/" . $name);

                $qiniu = new Auth($result['accesskey'], $result['secretkey']);

                $bucket = $result['storage_name'];

                $token = $qiniu->uploadToken($bucket);

                $filePath = $_FILES['file']['tmp_name'];

                $key = '/uploads/admin/video/' . $name;
                $uploadMgr = new UploadManager();
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {

                    $data=[
                        'module'=>'admin',
                        'use_source'=>$result['type'],
                        'filename'=>$name,
                        'filepath'=>$key,
                        'filesize'=>$size,
                        'fileext'=>$type,
                        'uploadip'=>$this->request->ip(),
                        'local_name'=>$name,
                        'user_id'=>Session::has('admin') ? Session::get('admin') : 0
                    ];

                    $video = new VideoAttachment();
                    $result1 = $video->createVideo($data);
                    if($result1){
                        $id = $video->getLastInsID();
                        throw new SuccessMessage([
                            'data'=>[
                                'id'=>$id,
                                'url'=>$key,
                                'use_source'=>$result['type']
                            ]
                        ]);
                    }

                }


            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], $tmp . "/" . $name . ".tmp" . $chunk);
                $complete = true;
                for ($i = 0; $i < $chunks; $i++) {
                    if (!file_exists($tmp . "/" . $name . ".tmp" . $i)) {
                        $complete = false;
                        break;
                    }
                }

                if ($complete) {
                    $fp = fopen($upload . "/" . $name, "ab");
                    for ($i = 0; $i < $chunks; $i++) {
                        $tmp_file = $tmp . "/" . $name . ".tmp" . $i;
                        $handle = fopen($tmp_file, "rb");
                        fwrite($fp, fread($handle, filesize($tmp_file)));
                        fclose($handle);
                        unset($handle);
                        unlink($tmp_file);
                    }

                    $qiniu = new Auth($result['accesskey'], $result['secretkey']);

                    $bucket = $result['storage_name'];

                    $token = $qiniu->uploadToken($bucket);

                    $filePath = './uploads/admin/video/' . $name;

                    $key = '/uploads/admin/video/' . $name;
                    $uploadMgr = new UploadManager();
                    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                    if ($err !== null) {
                        var_dump($err);
                    } else {
                        unlink($filePath);

                        $data=[
                            'module'=>'admin',
                            'use_source'=>$result['type'],
                            'filename'=>$name,
                            'filepath'=>$key,
                            'filesize'=>$size,
                            'fileext'=>$type,
                            'uploadip'=>$this->request->ip(),
                            'local_name'=>$name,
                            'user_id'=>Session::has('admin') ? Session::get('admin') : 0
                        ];

                        $video = new VideoAttachment();
                        $result1 = $video->createVideo($data);
                        if($result1){
                            $id = $video->getLastInsID();
                            throw new SuccessMessage([
                                'data'=>[
                                    'id'=>$id,
                                    'url'=>$key,
                                    'use_source'=>$result['type']
                                ]
                            ]);
                        }

                    }
                }
            }


        } else {
            $name = $_POST["name"];
            $size = $_POST["size"];
            $type = $_POST["type"];
            $lastModifiedDate = $_POST["lastModifiedDate"];

            if (array_key_exists('chunks', $_POST)) {
                $chunks = $_POST["chunks"];
            }
            if (array_key_exists('chunk', $_POST)) {
                $chunk = $_POST["chunk"];
            }

            $upload = $_SERVER["DOCUMENT_ROOT"] . "/uploads/admin/video";
            $tmp = $_SERVER["DOCUMENT_ROOT"] . "/uploads/tmp";
            if (!is_dir($tmp)) {
                mkdir($tmp, 0777, true);
            }

            if (!isset($chunks)) {
                move_uploaded_file($_FILES["file"]["tmp_name"], $upload . "/" . $name);
                //输出信息
//                echo "--- 文件上传完毕 ---\n";
//                echo "文件名：" . $name . "\n";
//                echo "文件大小：" . $size . "\n";
//                echo "文件类型：" . $type . "\n";
//                echo "文件最后修改时间：" . $lastModifiedDate;

                $data=[
                    'module'=>'admin',
                    'use_source'=>$result['type'],
                    'filename'=>$name,
                    'filepath'=>'/uploads/admin/video/'.$name,
                    'filesize'=>$size,
                    'fileext'=>$type,
                    'uploadip'=>$this->request->ip(),
                    'local_name'=>$name,
                    'user_id'=>Session::has('admin') ? Session::get('admin') : 0
                ];

                $video = new VideoAttachment();
                $result1 = $video->createVideo($data);
                if($result1){
                    $id = $video->getLastInsID();
                    throw new SuccessMessage([
                        'data'=>[
                            'id'=>$id,
                            'url'=>'/uploads/admin/video/'.$name,
                            'use_source'=>$result['type']
                        ]
                    ]);
                }



            } else {
                // 如果分片的话先把分片存储到tmp文件夹下
                move_uploaded_file($_FILES["file"]["tmp_name"], $tmp . "/" . $name . ".tmp" . $chunk);
//                echo "--- 分片上传完毕 ---\n";

                $complete = true;
                for ($i = 0; $i < $chunks; $i++) {
                    if (!file_exists($tmp . "/" . $name . ".tmp" . $i)) {
                        $complete = false;
                        break;
                    }
                }

                if ($complete) {
                    $fp = fopen($upload . "/" . $name, "ab");
                    for ($i = 0; $i < $chunks; $i++) {
                        $tmp_file = $tmp . "/" . $name . ".tmp" . $i;
                        $handle = fopen($tmp_file, "rb");
                        fwrite($fp, fread($handle, filesize($tmp_file)));
                        fclose($handle);
                        unset($handle);
                        unlink($tmp_file);
                    }
//                    echo "--- 文件合并完毕 ---\n";
                    $data=[
                        'module'=>'admin',
                        'use_source'=>$result['type'],
                        'filename'=>$name,
                        'filepath'=>'/uploads/admin/video/'.$name,
                        'filesize'=>$size,
                        'fileext'=>$type,
                        'uploadip'=>$this->request->ip(),
                        'local_name'=>$name,
                        'user_id'=>Session::has('admin') ? Session::get('admin') : 0
                    ];

                    $video = new VideoAttachment();
                    $result1 = $video->createVideo($data);
                    if($result1){
                        $id = $video->getLastInsID();
                        throw new SuccessMessage([
                            'data'=>[
                                'id'=>$id,
                                'url'=>'/uploads/admin/video/'.$name,
                                'use_source'=>$result['type']
                            ]
                        ]);
                    }
                }
            }

        }
    }
  
  public function qiniuToken(){
        $result = Qiniuconfig::readQiniuconfig(1);
        $qiniu = new Auth($result['accesskey'], $result['secretkey']);
        $bucket = $result['storage_name'];
        $token = $qiniu->uploadToken($bucket);
        throw new SuccessMessage([
            'data'=>['token'=>$token]
        ]);
    }
}