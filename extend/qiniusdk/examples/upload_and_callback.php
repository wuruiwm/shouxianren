<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

$accessKey = 'IU86FzydSymg2O5Z3-pxx2pCNZcL_w01DPD5wgl5';
$secretKey = '0qHCtSCnGts6-LPjqzrt90Yo0E7tLqd71rMbDNJh';
$bucket = 'file';
$auth = new Auth($accessKey, $secretKey);

// 上传文件到七牛后， 七牛将文件名和文件大小回调给业务服务器.
// 可参考文档: http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
$policy = array(
    'callbackUrl' => 'http://your.domain.com/upload_verify_callback.php',
    'callbackBody' => 'filename=$(fname)&filesize=$(fsize)'
);
$uptoken = $auth->uploadToken($bucket, null, 3600);

//上传文件的本地路径
$filePath = './te.png';

$uploadMgr = new UploadManager();

list($ret, $err) = $uploadMgr->putFile($uptoken, null, $filePath);
echo "\n====> putFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}
