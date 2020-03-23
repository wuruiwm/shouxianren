<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/19
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

// 图片 统一上传接口

use app\admin\model\Qiniuconfig;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use think\Db;
use think\Session;

class Upload extends BaseController
{
    /**
     * @apiDefine  upload 图片上传
     */

    /**
     * @api {post} upload/image  1、单文件上传[普通图片]
     * @apiGroup upload
     * @apiVersion 0.1.0
     * @apiDescription  普通图片上传
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/upload/image
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} file 图片文件名
     * @apiName 15
     */

    public function userUpload()
    {
        // ROOT_PATH: ==> /www/wwwroot/sxr.ijiandian.com
        // DS: ==> /
        $files = request()->file('file');
        $local_name = $files->getInfo()['name'];
        $is_exist = Db::name('attachment')->where(['local_name' => $local_name])->find();
        if ($is_exist) {
            throw new SuccessMessage([
                'data' => [
                    'id' => $is_exist['id'],
                    'src' => is_https() . DS . $is_exist['filepath']
                ]
            ]);
        }
        $info = $files->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'user');
        if ($info) {
            //写入到附件表
            $data = [];
            $data['module'] = 'user';
            $data['filename'] = $info->getFilename();//文件名
            $data['filepath'] = DS . 'uploads' . DS . 'user' . DS . $info->getSaveName();//文件路径
            $data['fileext'] = $info->getExtension();//文件后缀
            $data['filesize'] = $info->getSize();//文件大小
            $data['create_time'] = time();//时间
            $data['uploadip'] = $this->request->ip();//IP
            $data['user_id'] = Token::getCurrentTokenUserId();
            $data['status'] = 1;//通过后台上传的文件直接审核通过
            $data['admin_id'] = $data['user_id'];
            $data['audit_time'] = time();
            $data['use'] = 'user';//用处
            $data['local_name'] = $local_name;
            $res['id'] = Db::name('attachment')->insertGetId($data);
            $res['src'] = is_https() . DS . 'uploads' . DS . 'user' . DS . $info->getSaveName();
            throw new SuccessMessage([
                'data' => $res
            ]);
        } else {
            throw new ErrorMessage();
        }
    }


    public function uploads()
    {
        if ($this->request->file('files')) {
            $files = $this->request->file('files');
            $web_config = Db::name('webconfig')->where('web', 'web')->find();
            $imgs = [];
            $img_str = '';
            foreach ($files as $key => $file) {
                $info = $file->validate(['size' => $web_config['file_size'] * 1024, 'ext' => $web_config['file_type']])->rule('date')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'user');
                if ($info) {
                    //写入到附件表
                    $data = [];
                    $data['module'] = 'user';
                    $data['filename'] = $info->getFilename();//文件名
                    $data['filepath'] = DS . 'uploads' . DS . 'user' . DS . $info->getSaveName();//文件路径
                    $data['fileext'] = $info->getExtension();//文件后缀
                    $data['filesize'] = $info->getSize();//文件大小
                    $data['create_time'] = time();//时间
                    $data['uploadip'] = $this->request->ip();//IP
                    $data['user_id'] = Session::has('admin') ? Session::get('admin') : 0;
                    //通过后台上传的文件直接审核通过
                    $data['status'] = 1;
                    $data['admin_id'] = $data['user_id'];
                    $data['audit_time'] = time();
                    $data['use'] = 'user';//用处
                    $res['id'] = Db::name('attachment')->insertGetId($data);
                    $res['src'] = http_type() . DS . 'uploads' . DS . 'user' . DS . $info->getSaveName();
                    $res['code'] = 2;
                    $img_str .= $res['id'] . ',';
                    array_push($imgs, $res);
                } else {
                    // 上传失败获取错误信息
                    return $this->error('上传失败：' . $files->getError());
                }
            }
            $r_data['room_map'] = substr($img_str, 0, strlen($img_str) - 1);
            $r_data['img_data'] = $imgs;
            return $r_data;

        } else {
            throw new ErrorMessage([
                'msg' => '上传失败'
            ]);
        }
    }

    /**
     * @api {post} upload/image/base64  2、单文件上传[base64]
     * @apiGroup upload
     * @apiVersion 0.1.0
     * @apiDescription  base64图片上传
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/upload/image/base64
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} base64 图片文件名
     * @apiName 40
     */

    public function userUploadBase64()
    {
        $user_id = Token::getCurrentTokenUserId();
        //传输 base64图片数据 及 保存的路径
        $post = input('post.');
        if (empty($post['base64']))
            throw new ErrorMessage(['msg' => 'base64参数必填']);
        // $post['base64'] = "data:image/jpeg;base64,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";
        // 因前端值不一样 这里手动加了文件格式属性,和换行符
        $base64 = $post['base64'];
        $base64 = str_replace('\n','',$base64);
        $res = $this->base64_image_content($base64, 'uploads/user', $user_id);
        return $res;
    }


    //base64图片转换
    function base64_image_content($base64_image_content, $path, $user_id)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2]; // 文件后缀jpeg 不加
            $new_file = $path . "/" . date('Ymd', time()) . "/";
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $filename = md5(time()) . ".{$type}"; // 文件名
            $new_file = $new_file . $filename;  // 完整路径+文件名 "uploads/user/20190601/7060f88960ba0f86c3ea414c7e9af83c.jpeg"

            $result = file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content))); // 大小

            if ($result) {
                $data = [];
                $data['module'] = 'user';
                $data['filename'] = $filename;//文件名
                $data['filepath'] = '/'.$new_file;//文件路径
                $data['fileext'] = $type;//文件后缀
                $data['filesize'] = $result;//文件大小
                $data['create_time'] = time();//时间
                $data['uploadip'] = $this->request->ip();//IP
                $data['user_id'] = $user_id;
                $data['status'] = 1;//通过后台上传的文件直接审核通过
                $data['admin_id'] = $data['user_id'];
                $data['audit_time'] = time();
                $data['use'] = 'user';//用处
                $res['id'] = Db::name('attachment')->insertGetId($data);
                $res['src'] = is_https() .DS. $new_file;
                throw new SuccessMessage(['data' => $res]);
            } else {
                throw new ErrorMessage(['msg' => '上传失败']);
            }
        } else {
            throw new ErrorMessage(['msg' => '图片格式错误']);
        }
    }


    // 朋友圈图片直接上传七牛云 处理
    public function squareImgUpload(){
        $filename=input('post.filename');
        if(empty($filename)){
            throw new ErrorMessage(['msg'=>'请上传文件路径']);
        }
        $user_id = Token::getCurrentTokenUserId();
        $data = [];
        $data['module'] = 'user';
        $data['filepath'] = $filename;//文件名
        $data['create_time'] = time();//时间
        $data['uploadip'] = $this->request->ip();//IP
        $data['user_id'] = $user_id;
        $data['status'] = 1;//通过后台上传的文件直接审核通过
        $data['admin_id'] = $data['user_id'];
        $data['audit_time'] = time();
        $data['use'] = 'user';//用处
        $data['source']='qiniu';
        $res['id'] = Db::name('attachment')->insertGetId($data);
        $prefix_url = Qiniuconfig::where('id',1)->value('prefix_url');
        $res['src'] = $prefix_url .DS. $filename;
        throw new SuccessMessage(['data' => $res]);
    }

}
