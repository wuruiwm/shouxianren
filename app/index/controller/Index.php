<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->redirect($this->request->root(true).'/');//重定向 根目录下
    }
}
