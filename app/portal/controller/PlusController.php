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

namespace app\portal\controller;


use cmf\controller\HomeBaseController;

use think\view;
use think\Session;
use think\Db;
use think\Route;
use think\Request;

use app\admin\model\ThemeModel;

use app\portal\model\PortalXmModel;


class PlusController extends HomeBaseController

{
    public function _initialize()
    {
         if (\think\Request::instance()->isMobile()) {
                if(defined('VIEW_PATH')){
                }else{
                    define('VIEW_PATH',PUBLIC_PATH .'themes/mobile/');
                }

                }else{
                    if(defined('VIEW_PATH')){}else{
                        define('VIEW_PATH',PUBLIC_PATH .'themes/');
                    }
                    
                }
       
    }

    public function index(){
         $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
           $post=$this->request->param();
            if($post && isset($post['q'])){
                Session::set('q',$post['q']);
                $q = session('q');
            }else{
                $q = session('q');
            }
            $url = $_SERVER["QUERY_STRING"];
            $array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 1);
            $where=[];
            if(isset($q) && ($q!=''))
            {
                $q = $q;
                $where['a.title'] = [ 'like', "%".$q."%"];
            }else{
                $q = '';
            }
            $where['a.arcrank'] = 1;
            $where['a.status'] = 1;
            $datas = db('portal_xm a')->where($where)->paginate(15,false,['query' => request()->param(),'page'=>$page]);
            $dataArray = $datas->all();
            foreach ($dataArray as $k=>$v){
                $category = db('portal_category')->where('id = '.$v['typeid'].' and status = 1 and ishidden = 1')->find();
                $dataArray[$k]['category_name'] = $category['name'];
            }
            $data = $dataArray;
            $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
            $youlian = db("flink")->where("typeid = 9999")->order("dtime desc")->limit(50)->select();
            $lick3 = db('portal_xm')->where("typeid",'in','2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('status = 1 and arcrank = 1')->order('sortrank asc')->limit(11,16)->select();
            // $catess = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(12)->select();
            $catess = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();

            //创业资讯
            $where25['parent_id'] = ['in','399,401,402,403,404,405,406,407,408,409,433'];
            $zixun = db('portal_post')->where($where25)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();

            //创业知识
            $where26['parent_id'] = ['in','20,22,27,31'];
            $zhishi = db('portal_post')->where($where26)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();

            //创业故事
            $where27['parent_id'] = ['in','11'];
            $gushi = db('portal_post')->where($where27)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();

            $this->daohang();
            $this->dibu();
            $this->assign('q',$q);
            $this->assign('data',$data);
            if(isset($data[0]) && ($data[0])){
                $data1 = 1;
            }else{
                $data1 = 0;
            }
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->assign('data1',$data1);
            $this->assign('datas',$datas);
            $this->assign('catess',$catess);
            $this->assign('lick5',$lick5);
            $this->assign('youlian',$youlian);
            $this->assign('lick3',$lick3);
            $this->assign('zixun',$zixun);
            $this->assign('zhishi',$zhishi);
            $this->assign('gushi',$gushi);
            echo $this->fetch(':mobile/plus/search');
        }else{
            $post=$this->request->param();
            if($post && isset($post['keyword'])){
                $post['q'] = $post['keyword'];
                Session::set('q',$post['q']);
                $q = session('q');
            }else{
                $q = session('q');
            }
            $url = $_SERVER["QUERY_STRING"];
            $array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 1);
            $where=[];
            if(isset($q) && ($q!=''))
            {
                $q = $q;
                $where['a.title'] = [ 'like', "%".$q."%"];
            }else{
                $q = '';
            }
            $where['a.arcrank'] = 1;
            $where['a.status'] = 1;
            $data = db('portal_xm a')->where($where)->order('pubdate desc')->paginate(10,false,['query' => request()->param(),'page'=>$page]);
            $dataa = $data->all();
            foreach($dataa as $kr=>$vr){
              $infp = db('portal_category')->where("id = ".$vr['typeid'])->find();
              $dataa[$kr]['category'] = $infp['name'];
            }
            $lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->where("litpic != ' '")->order('click desc')->limit(6)->select();
            $lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('weight desc')->limit(10)->select();
            $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
            $lick3 = $lick3->all();
            foreach ($lick3 as $k => $v) {
                $lick3[$k]['classs'] = substr($v['class'],0,4);
            }
            $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
            $youlian = db("flink")->where("typeid = 9999")->order("dtime desc")->limit(50)->select();
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            $this->daohang();
            $this->dibu();
            $this->assign('cate',$cate);
            $this->assign('lick1',$lick1);
            $this->assign('lick2',$lick2);
            $this->assign('q',$q);
            $this->assign('page',$page);
            $this->assign('data',$data);
          	$this->assign('dataa',$dataa);
            if(isset($data[0]) && ($data[0])){
                $data1 = 1;
            }else{
                $data1 = 0;
            }
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->assign('data1',$data1);
            $this->assign('lick5',$lick5);
            $this->assign('youlian',$youlian);
            $this->assign('lick3',$lick3);
            echo $this->fetch(':plus/search1');
        }
    }
    //搜索结果ajax点击加载更多
    public function ajaxkeyword(){
        $post=$this->request->param();
        $q = $post['keyword'];
        $page = $post['page'] * 10;
        $where['a.title'] = [ 'like', "%".$q."%"];
        $where['a.arcrank'] = 1;
        $where['a.status'] = 1;
        $data = db('portal_xm a')->where($where)->order('pubdate desc')->limit($page,10)->select()->toArray();
        foreach ($data as $k=>$v){
            $category = db('portal_category')->where('id = '.$v['typeid'])->find();
            $data[$k]['category_name'] = $category['name'];
        }
        $html='';
        foreach ($data as $k=>$v){
            $html.='<li>';
            $html.='<div class="img">';
            $url = cmf_url('portal/common/index',['id'=>$v['aid'],'classname'=>$v['class']]);
            $html.='<a href="'.$url.'">';
            $html.='<img class="lazy" src="/themes/simpleboot3/public/mobile/xin/images/44feb2a189bb6a55ade0a5349fcccfb2.jpg" data-original="'.$v['litpic'].'" alt="">';
            $html.='</a></div>';
            $html.='<div class="text">';
            $html.='<div class="left">';
            $html.='<div class="title">';
            $html.='<h2>';
            $html.='<a href="'.$url.'">'.$v['title'].'</a>';
            $html.='</h2>';
            $html.='</div>';
            $html.='<div class="price">￥'.$v['invested'].'</div>';
            $html.='<div class="smallTab">';
            $html.='<a href="javascript:;">'.$v['category_name'].'</a>';
            $html.='<a href="javascript:;">'.$v['company_address'].'</a>';
            $html.='</div>';
            $html.='<div class="desc">'.$v['companyname'].'</div>';
            $html.='</div>';
            $html.='<div class="right">';
            $html.='<div class="join"><a href="'.$url.'">咨询</a></div>';
            $html.='</div>';
            $html.='</div>';
            $html.='</li>';
        }
        $dataa = array('html'=>$html);
        echo json_encode($dataa);
    }

    public function daohang()
    {
        $cates1 = db("portal_category")->where("id = 2")->select();
        foreach($cates1 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->limit(40)->select();
            $c1[] = $val;
        }
        $cates2 = db("portal_category")->where("id ","in","312,8,10,5,396")->select();
        foreach($cates2 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->limit(6)->select();
            $c2[] = $val;
        }
        $cates3 = db("portal_category")->where("id","in", " 4,7,313,9,420")->select();
        foreach($cates3 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->limit(6)->select();
            $c3[] = $val;
        }
        $cates4 = db("portal_category")->where("id","in"," 1,3,339,6")->select();
        foreach($cates4 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->limit(6)->select();
            $c4[] = $val;
        }
        $this->assign("cates1",$c1); 
        $this->assign("cates2",$c2);
        $this->assign("cates3",$c3);
        $this->assign("cates4",$c4);
    }
    public function dibu()
    {
        $dibu = db("portal_category")->where("parent_id",'in','52,53')->select();
        $this->assign('dibu',$dibu);
    }

}