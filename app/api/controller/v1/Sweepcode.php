<?php
namespace app\api\controller\v1;

use think\Db;

class Sweepcode extends BaseController
{
    public function is_sweepcode(){
      if(!empty(config('is_sweepcode'))){
      	 return ['status'=>1];
      }else{
     	 return ['status'=>0];
      }
    }
}