<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\portal\model\PortalCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;

class AdminMessageController extends AdminBaseController
{
    //项目留言列表
    public function index()
    {

        $url = $_SERVER["QUERY_STRING"];
        $check = strpos($url, 'list_');
        if($check){
            $array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 1);
        }else{
            $page = 1;
        }

        $user_info = db('user_info')->where("type = 'xm'")->paginate(30,false,['page'=>$page]);
        $this->assign('user_info', $user_info);
        $this->assign('page', $user_info->render());
        return $this->fetch();
    }

    public function edit()
    {

        $id = $this->request->param('id', 0, 'intval');
        $post = db('user_info')->where('id = '.$id)->find();
        if($post['source'] == 1){
            $this->assign('post', $post);
            return $this->fetch();
        }else{
            $this->assign('post',$post);
            return $this->fetch(':admin_message/edits');
        }
        
    }

    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        $info = db('user_info')->where('id = '.$id)->delete();
        
        if($info){
            $this->success("删除成功！", '');
        }else{
             $this->error("删除失败！", '');
        }
    }

    //资讯留言列表
    public function news_index()
    {
        $url = $_SERVER["QUERY_STRING"];
        $check = strpos($url, 'list_');
        if($check){
            $array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 1);
        }else{
            $page = 1;
        }

        $user_info = db('user_info')->where("type = 'news' and source = 1")->paginate(30,false,['page'=>$page]);
        $this->assign('user_info', $user_info);
        $this->assign('page', $user_info->render());
        return $this->fetch();
    }

    public function news_edit()
    {

        $id = $this->request->param('id', 0, 'intval');
        $post = db('user_info')->where('id = '.$id)->find();
        if($post['source'] == 1){
            $this->assign('post', $post);
            return $this->fetch();
        }else{
            $this->assign('post',$post);
            return $this->fetch(':admin_message/edits');
        }
        
    }

    public function news_delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        $info = db('user_info')->where('id = '.$id)->delete();
        
        if($info){
            $this->success("删除成功！", '');
        }else{
             $this->error("删除失败！", '');
        }
    }


}
