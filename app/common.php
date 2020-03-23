<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 根据附件表的id返回url地址
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function geturl($id)
{
    if ($id) {
        $geturl = \think\Db::name("attachment")->where(['id' => $id])->find();
        if ($geturl['status'] == 1) {
            //审核通过
            return $geturl['filepath'];
        } elseif ($geturl['status'] == 0) {
            //待审核
            return '/uploads/xitong/beiyong1.jpg';
        } else {
            //不通过
            return '/uploads/xitong/beiyong2.jpg';
        }
    }
    return false;
}


/**
 * [SendMail 邮件发送]
 * @param [type] $address  [description]
 * @param [type] $title    [description]
 * @param [type] $message  [description]
 * @param [type] $from     [description]
 * @param [type] $fromname [description]
 * @param [type] $smtp     [description]
 * @param [type] $username [description]
 * @param [type] $password [description]
 */
function SendMail($address)
{
    vendor('phpmailer.PHPMailerAutoload');
    //vendor('PHPMailer.class#PHPMailer');
    $mail = new \PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet = 'UTF-8';
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);

    $data = \think\Db::name('emailconfig')->where('email', 'email')->find();
    $title = $data['title'];
    $message = $data['content'];
    $from = $data['from_email'];
    $fromname = $data['from_name'];
    $smtp = $data['smtp'];
    $username = $data['username'];
    $password = $data['password'];
    // 设置邮件正文
    $mail->Body = $message;
    // 设置邮件头的From字段。
    $mail->From = $from;
    // 设置发件人名字
    $mail->FromName = $fromname;
    // 设置邮件标题
    $mail->Subject = $title;
    // 设置SMTP服务器。
    $mail->Host = $smtp;
    // 设置为"需要验证" ThinkPHP 的config方法读取配置文件
    $mail->SMTPAuth = true;
    //设置html发送格式
    $mail->isHTML(true);
    // 设置用户名和密码。
    $mail->Username = $username;
    $mail->Password = $password;
    // 发送邮件。
    return ($mail->Send());
}


/**
 * 阿里大鱼短信发送
 * @param [type] $appkey    [description]
 * @param [type] $secretKey [description]
 * @param [type] $type      [description]
 * @param [type] $name      [description]
 * @param [type] $param     [description]
 * @param [type] $phone     [description]
 * @param [type] $code      [description]
 * @param [type] $data      [description]
 */
function SendSms($param, $phone)
{
    // 配置信息
    import('dayu.top.TopClient');
    import('dayu.top.TopLogger');
    import('dayu.top.request.AlibabaAliqinFcSmsNumSendRequest');
    import('dayu.top.ResultSet');
    import('dayu.top.RequestCheckUtil');

    //获取短信配置
    $data = \think\Db::name('smsconfig')->where('sms', 'sms')->find();
    $appkey = $data['appkey'];
    $secretkey = $data['secretkey'];
    $type = $data['type'];
    $name = $data['name'];
    $code = $data['code'];

    $c = new \TopClient();
    $c->appkey = $appkey;
    $c->secretKey = $secretkey;

    $req = new \AlibabaAliqinFcSmsNumSendRequest();
    //公共回传参数，在“消息返回”中会透传回该参数。非必须
    $req->setExtend("");
    //短信类型，传入值请填写normal
    $req->setSmsType($type);
    //短信签名，传入的短信签名必须是在阿里大于“管理中心-验证码/短信通知/推广短信-配置短信签名”中的可用签名。
    $req->setSmsFreeSignName($name);
    //短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开。
    $req->setSmsParam($param);
    //短信接收号码。支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码。
    $req->setRecNum($phone);
    //短信模板ID，传入的模板必须是在阿里大于“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($code);
    //发送


    $resp = $c->execute($req);
}


function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return 'https'. '://' . $_SERVER['HTTP_HOST'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return 'https'. '://' . $_SERVER['HTTP_HOST'];
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return 'https' . '://' . $_SERVER['HTTP_HOST'];
    }
    return 'http' . '://' . $_SERVER['HTTP_HOST'];
}

function GetMkTime($dtime)
{
    if (!preg_match("/[^0-9]/", $dtime)) {
        return $dtime;
    }
    $dtime = trim($dtime);
    $dt = Array(1970, 1, 1, 0, 0, 0);
    $dtime = preg_replace("/[\r\n\t]|日|秒/", " ", $dtime);
    $dtime = str_replace("年", "-", $dtime);
    $dtime = str_replace("月", "-", $dtime);
    $dtime = str_replace("时", ":", $dtime);
    $dtime = str_replace("分", ":", $dtime);
    $dtime = trim(preg_replace("/[ ]{1,}/", " ", $dtime));
    $ds = explode(" ", $dtime);
    $ymd = explode("-", $ds[0]);
    if (!isset($ymd[1])) {
        $ymd = explode(".", $ds[0]);
    }
    if (isset($ymd[0])) {
        $dt[0] = $ymd[0];
    }
    if (isset($ymd[1])) $dt[1] = $ymd[1];
    if (isset($ymd[2])) $dt[2] = $ymd[2];
    if (strlen($dt[0]) == 2) $dt[0] = '20' . $dt[0];
    if (isset($ds[1])) {
        $hms = explode(":", $ds[1]);
        if (isset($hms[0])) $dt[3] = $hms[0];
        if (isset($hms[1])) $dt[4] = $hms[1];
        if (isset($hms[2])) $dt[5] = $hms[2];
    }
    foreach ($dt as $k => $v) {
        $v = preg_replace("/^0{1,}/", '', trim($v));
        if ($v == '') {
            $dt[$k] = 0;
        }
    }
    $mt = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);
    if (!empty($mt)) {
        return $mt;
    } else {
        return time();
    }

}

function timeToStr($time = NULL, $format = NULL)
{
    if (!$time) {
        $time = time();
    }
    if (!$format) {
        $format = 'Y-m-d H:i:s';
    }
    return date($format, $time);
}


function encryptDecrypt($skey, $string, $decrypt)
{
    if ($decrypt) {
        $strArr  =  str_split ( base64_encode ( $string ));
        $strCount  =  count ( $strArr );
        foreach ( str_split ( $skey ) as  $key  =>  $value )
            $key  <  $strCount  &&  $strArr [ $key ].= $value ;
        return  str_replace (array( '=' ,  '+' ,  '/' ), array( 'O0O0O' ,  'o000o' ,  'oo00o' ),  join ( '' ,  $strArr ));
    } else {
        $strArr  =  str_split ( str_replace (array( 'O0O0O' ,  'o000o' ,  'oo00o' ), array( '=' ,  '+' ,  '/' ),  $string ),  2 );
        $strCount  =  count ( $strArr );
        foreach ( str_split ( $skey ) as  $key  =>  $value )
            $key  <=  $strCount   && isset( $strArr [ $key ]) &&  $strArr [ $key ][ 1 ] ===  $value  &&  $strArr [ $key ] =  $strArr [ $key ][ 0 ];
        return  base64_decode ( join ( '' ,  $strArr ));
    }
}


function findNum($str=''){
    $str=trim($str);
    if(empty($str)){return '';}
    $temp=array('1','2','3','4','5','6','7','8','9','0');
    $result='';
    for($i=0;$i<strlen($str);$i++){
        if(in_array($str[$i],$temp)){
            $result.=$str[$i];
        }
    }
    return $result;
}

function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}
/**
 * 发送HTTP请求方法
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
 function httpCurl($url, $params, $method = 'POST', $header = array(), $multi = false){
    date_default_timezone_set('PRC');
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_COOKIESESSION  => true,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_COOKIE         =>session_name().'='.session_id(),
    );
    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            // $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            // 链接后拼接参数  &  非？
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) throw new Exception('请求发生错误：' . $error);
    return  $data;
}
/**
 * 微信信息解密
 * @param  string  $appid  小程序id
 * @param  string  $sessionKey 小程序密钥
 * @param  string  $encryptedData 在小程序中获取的encryptedData
 * @param  string  $iv 在小程序中获取的iv
 * @return array 解密后的数组
 */
function decryptData( $appid , $sessionKey, $encryptedData, $iv ){
    $OK = 0;
    $IllegalAesKey = -41001;
    $IllegalIv = -41002;
    $IllegalBuffer = -41003;
    $DecodeBase64Error = -41004;
 
    if (strlen($sessionKey) != 24) {
        return $IllegalAesKey;
    }
    $aesKey=base64_decode($sessionKey);
 
    if (strlen($iv) != 24) {
        return $IllegalIv;
    }
    $aesIV=base64_decode($iv);
 
    $aesCipher=base64_decode($encryptedData);
 
    $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
    $dataObj=json_decode( $result );
    if( $dataObj  == NULL )
    {
        return $IllegalBuffer;
    }
    if( $dataObj->watermark->appid != $appid )
    {
        return $DecodeBase64Error;
    }
    $data = json_decode($result,true);
 
    return $data;
}
 
/**
 * 请求过程中因为编码原因+号变成了空格
 * 需要用下面的方法转换回来
 */
function define_str_replace($data)
{
    return str_replace(' ','+',$data);
}
function showjson($arr){
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    exit();
}
function msg($status,$msg){
    echo json_encode(['status'=>$status,'msg'=>$msg],JSON_UNESCAPED_UNICODE);
    exit();
}
	/**
	 * 用户post方法请求xml信息用的
	 * @author write by leoyi 2018-04-8
	*/
	function postXmlCurl($xml, $url, $useCert = false, $second = 10)
	{
	    $ch = curl_init();
	    //设置超时
	    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
	    curl_setopt($ch,CURLOPT_URL, $url);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验2
	    //设置header
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    //要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	    //运行curl
	    $data = curl_exec($ch);
	    //返回结果
	    if($data){
	      curl_close($ch);
	      return $data;
	    } else {
	      $error = curl_errno($ch);
	      curl_close($ch);
	      return $error;
	    }
	}
	/*
	*   用于微信支付转换认证的信息用的
	*   by:leoyi
	*   date:2018-4-8
	*/
	function ToUrlParams($data)
	{
	  $buff = "";
	  foreach ($data as $k => $v)
	  {
	    if($k != "sign" && $v != "" && !is_array($v)){
	      $buff .= $k . "=" . $v . "&";
	    }
	  }

	  $buff = trim($buff, "&");
	  return $buff;
	}
	/*
	*   微信支付-数组转xml
	*   by:leoyi
	*   date:2018-4-8
	*/
	function arrayToXml($arr)
	{
	    $xml = "<xml>";
	    foreach ($arr as $key=>$val)
	    {
	        if (is_numeric($val)){
	            $xml.="<".$key.">".$val."</".$key.">";
	        }else{
	             $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
	        }
	    }
	    $xml.="</xml>";
	    return $xml;
	}
	/*
	*   微信支付-数组转xml
	*   by:leoyi
	*   date:2018-4-8
	*/
	function  xml_to_json($xmlstring) {
	    return json_encode(xml_to_array($xmlstring),JSON_UNESCAPED_UNICODE);
	}
	/*
	*   post方法
	*   by:leoyi
	*   date:2018-4-8
	*/
	function post_url($post_data, $url)
	{
	  $ch = curl_init();
	  //设置超时
	  curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	  curl_setopt($ch,CURLOPT_URL, $url);

	  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	  curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验2

	  curl_setopt($ch, CURLOPT_HEADER, FALSE);

	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);


	  curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

	  $data = curl_exec($ch);
	  curl_close($ch);
	  return $data;
	}
	/*
	* 把xml转换成array
	* by:leoyi
	* Date:2018-4-11
	*/
	function xml_to_array($xml) {
	    //return ((array) simplexml_load_string($xmlstring));
	  return simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);

	    //return json_decode(xml_to_json($xmlstring));
	}
	function domain_name(){
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }
    function page(){
        $page = input('page');
        $limit = input('limit');
        if (empty($page) || !is_numeric($page)) {
            msg(0,'请输入正确的页码');
        }
        if (empty($limit) || !is_numeric($limit)) {
            msg(0,'请输入正确的条数');
        }
        $page = intval($page);
        $limit = intval($limit);
        $number = ($page - 1) * $limit;
        $data = ['number'=>$number,'limit'=>$limit];
        return $data;
    }
    function array_date($data = [],$field = [],$string = 'Y/n/j G:i:s'){
        foreach ($data as $k => $v) {
            foreach($field as $k2 => $v2){
                $data[$k][$v2] = date($string,$v[$v2]);
            }
        }
        return $data;
    }