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

use think\Db;

use think\cache\driver\Redis;

use think\Request;

use think\Route;

use app\admin\model\ThemeModel;

use app\portal\model\PortalNewsModel;



class CommonController extends HomeBaseController

{

    public function _initialize()
    {

        if (\think\Request::instance()->isMobile()) {
            define('VIEW_PATH',PUBLIC_PATH .'themes/mobile/');

        }else{
            define('VIEW_PATH',PUBLIC_PATH .'themes/');
        }
    }

   public function index()
    {
     //echo APP_PATH .'404.html';die;
     $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(strpos($url ,"%2F") !== false ){
            return $this->error1();
        }
            $post=$this->request->param();

            //判断参数位置，以及位移参数对应
            // $post_id=$post['id'];
            // $post_classname=$post['classname'];
            // $post_num=$post['num'];
            // $post_address=$post['address'];
            // $post_page=$post['page'];
            if(isset($post['id'])){
                $idlen=strlen($post['id']);
                if($idlen!=(int)$post['id']){//当前不是数字，测不是id
                    $arraylist=$post;
                    // $post['id']=$post['address']=$post['num']=$post['page']="";
                    unset($arraylist["classname"]);
                    foreach ($arraylist as $value) {
                        if($this->regionMode($value)){
                            $post['address']=$value;
                        }else if($this->investmentAmount($value)){
                            $post['num']=$value;
                        }else if($this->getPageModel($value)){
                            $post['page']=$value;
                        }

                    }

                }
            }

            // if($post['classname']=='top' && ($post['id']!= '') ){

            //   $lieng = substr($post['id'],-2);

            //   if($lieng == 'jm'){
            //     return $this->list_top($post);
            //   }else{
            //     return $this->index_top($post);
            //   }
            // }
            if($post['classname'] == 'guanyu91'){
              return $this->guanyu91();
            }else if($post['classname'] == 'guanyuwomen'){
              return $this->guanyuwomen();
            }else if($post['classname'] == 'lianxiwomen'){
               return $this->lianxiwomen();
            }else if($post['classname'] == 'mianzeshengming'){
              return $this->mianzeshengming();
            }else if($post['classname'] == 'falvguwen'){
              return $this->falvguwen();
            }else if($post['classname'] == 'youqinglianjie'){
              return $this->youqinglianjie();
            }else if($post['classname'] == 'tousushanchu'){
              return $this->tousushanchu();
            }else if($post['classname'] == 'wangzhanditu'){
             return $this->error1();
            }else if($post['classname'] == 'paihangbang'){
              return $this->paihangbang();
            }else if($post['classname'] == 'chuangyezixun'){
                return $this->chuangyezixun();
            }else if($post['classname'] == 'sitemap.xml'){
            	return $this->error1();
            }



         if($post['classname']=='top' && ($post['id']!= '') ){
            $path = 'top/'.$post['id'];
            $info = db('portal_category')->where("path = '$path'")->find();
            session('aid', $info['id']);
              $aid = session('aid');
              $category = db('portal_category')->where("parent_id = '$aid'")->find();
           
              if($category){
                return $this->index_top($post);
                
              }else{
                return $this->list_top($post);
              }
            }
     
           if($post['classname'] == 'xiangmu' || $post['classname'] == 'haoxiangmu'){

               return $this->xm($post);
            }

            if((isset($post['classname']) && $post['classname']!='') || isset($post['num']) || isset($post['address'])) {
              
				 //判断是否有此路由
              if($post['classname']!='plus'){
                $cateRoute = db('portal_category')->where(['path' => $post['classname'] ])->select();
                  if(count($cateRoute) == 0){
                    
                      return $this->error1();
                  }
              }
                
                if(isset($post['id']) && ($post['id']!='')) {

                  if($post['id'] != $post['num']){

                    $id = substr($post['id'],0,1);
                         if(is_numeric($id)){
                           $stringtoID = (int)$post['id'];
                          $res = db("portal_xm")->where("aid = ".$stringtoID)->find();
                          if($res)
                          { 
                              return $this->article_xm($stringtoID);
                          }else{
                              return $this->article_news($stringtoID);
                          }
                        }else{

                          if(isset($post['classname']) && ($post != '')){
                            if($post['classname'] == 'plus'){
                           
                                $idlen=strlen($post['id']);
                                if($idlen!=(int)$post['id']){
                                   action('portal/plus/index',['post'=>$post]);
                                }else{
                                 
                                  return $this->xm($post);
                                }
                            }else{
                              
                              if($post['classname'] == 'news' || $post['classname'] == 'chuangyegushi' || $post['classname'] == 'zhidao' || $post['classname'] == 'gongsizhuce' || $post['classname'] == 'ruhe' || $post['classname'] == 'wenda'){
                                if($post['classname'] == 'news' && is_numeric($post['address'])){
                                	return $this->article_news($post['address']);
                                }else{
                                 	return $this->news($post);
                                }
                               
                              }else{
                                
                                $pathin = $post['classname'].'/'.$post['id'];
                                $sql = db('portal_category')->where("path = '$pathin'")->find();
                                if($sql['parent_id'] == 350){
                                  $post['classname'] = $post['classname'].'/'.$post['id'];
                                  $post['id'] = '';
                                  if(is_numeric($post['address'])){
                                    	return $this->article_xm($post['address']);
                                    	
                                	}else{
                                   	 	return $this->xm($post);
                                    	
                                	}
                                }else{
                                 return $this->xm($post);
                                }
                                
                               // return $this->xm($post);
                              }
                              
                            }
                            
                          }else{
                            
                           $idlen=strlen($post['id']);
                                if($idlen!=(int)$post['id']){
                                   action('portal/plus/index',['post'=>$post]);
                                }
                          }
                          
                        }
                      }else{
                              if(isset($post['classname'])){
                                  $paths = explode('.',$post['classname']);
                              if(in_array('html',$paths)){
                                  $res1 = db("portal_category")->where("path = "."'$paths[0]' and status = 1 and ishidden = 1")->value('channeltype');
                              }else{
                                  $res1 = db("portal_category")->where("path = "."'$post[classname]' and status = 1 and ishidden = 1")->value('channeltype');
                              }
                              if($res1 == 1)

                              {
                                  return $this->news($post);

                              }elseif($res1== 17){

                                  return $this->xm($post);

                              }else if($res1 == 18){
                                  return $this->top($post['classname']);

                              }else{
                                
                                return $this->error1();
                              }
                          }
                      }

                }else{

                    if(isset($post['classname'])){
                        $paths = explode('.',$post['classname']);
                        if(in_array('html',$paths)){
                            $res1 = db("portal_category")->where("path = "."'$paths[0]' and status = 1 and ishidden = 1")->value('channeltype');
                        }else{
                            $res1 = db("portal_category")->where("path = "."'$post[classname]' and status = 1 and ishidden = 1")->value('channeltype');
                        }
                        if($res1 == 1)

                        {
                            return $this->news($post);

                        }elseif($res1== 17){

                            return $this->xm($post);

                        }else if($res1 == 18){
                            return $this->top($post['classname']);

                        }else{
                          return $this->error1();
                        }
                    }

                }

            }else{
                return $this->xm($post);

            }

        }


    public function xm($post)
    {
    	$url = $_SERVER["QUERY_STRING"];
    	if($url){
    		$array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 4);
    	}else{
    		  $page = 1;
    	}
      $page = $this->findNum($page);
      if(!$page){
          $page = 1;
      }
        if(strpos($post['address'],'list_')!==false ){
            unset($post['address']);
        }
        //var_dump($post);

        $path = explode('/',VIEW_PATH);
          if(in_array('mobile',$path)){
              $selcttag1='';//行业分类
              $selcttag2='';//行业子分类
              $selcttag3='';//投资金额
              $selcttag4='';//热门地区
              $selcttag5='';//页面当前分类
				
            if(isset($post['address'])){
            	if(is_numeric(substr($post['address'],0,1))){
              	
            	$post['address'] = '';
            	}
            }
            
              if(isset($post['q']) && ($post['q'] != '')){
                  $selcttag1='';//行业分类
                  $selcttag2='';//行业子分类
                  $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
                  $selcttag4=isset($post['address'])?$post['address']:'';//热门地区
                  $selcttag5='xiangmu';//页面当前分类
//                $post="";
              }else{
                  if(isset($post['classname']) && ($post['classname']=='xiangmu')){
                      $selcttag1='';//行业分类
                      $selcttag2='';//行业子分类
                      $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
                      $selcttag4=isset($post['address'])?$post['address']:'';//热门地区
                      $selcttag5='xiangmu';//页面当前分类
                      // $post="";
                  }
              }
              
              $array_reverse = "";
              $youlian = "";
              if(isset($post['classname']) && ($post['classname']!='')){
                  $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
                  $array_reverse = $this->position($id);
              }
              $where=[];
                if($selcttag3){
                  if(isset($selcttag3) && ($selcttag3!=''))
                  {
                      if($selcttag3 == 100)
                      {
                          $res = $selcttag3.'万以上';
                      }else{
                          $res = $selcttag3.'万';
                      }
                      $where['a.invested'] = $res;
                  }
              }else{
                  if(isset($post['num']) && ($post['num']!=''))
                  {
                      if($post['num'] == 100)
                      {
                          $res = $post['num'].'万以上';
                      }else{
                          $res = $post['num'].'万';
                      }
                      $where['a.invested'] = $res;
                  }
              }
                  
               if($selcttag4){
                  if(isset($selcttag4) && ($selcttag4!=''))
                  {
                      $py = $selcttag4;
                      $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }else{
                  if(isset($post['address']) && ($post['address']!=''))
                  {
                      $py = $post['address'];
                      $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }
             
              if(isset($post['q']) && ($post['q'] != '')){
                  $cates = '';
              }else{

                  if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'portal')){
                      $res = db("portal_category")->field('id,path,parent_id')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();

                      $id=$res['id'];
                      $parent_id=$res['parent_id'];
                      // $parent_id = db("portal_category")->where("path="."'$post[classname]'")->value('parent_id');

                      if(isset($parent_id) && ($parent_id == 0))
                      {
                          // $where['a.typeid'] = $id;
                          //查询出当前以及分类下所有二级分类
                          $cates =  db("portal_category")->where("parent_id = $id and status = 1 and ishidden = 1")->select();


                          $ca = json_encode($cates);
                          $cates = json_decode($ca,true);
                          $ids = array_column($cates,'id');
                          $where['a.typeid'] = array('in',$ids);

                          $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                          $selcttag1=$post['classname'];
                          $selcttag2='';
                          $selcttag5=$selcttag1;
                      }else{
                          if((int)$id == 0){
                              $cates = '';
                          }else{
                              $where['a.typeid'] = $id;
                              $selcttag1= db("portal_category")->where("id = ".$parent_id.' and status = 1 and ishidden = 1')->value("path");

                              $selcttag2=$post['classname'];
                              $selcttag5=$selcttag2;
                              $cates =  db("portal_category")->where("parent_id = $parent_id and status = 1 and ishidden = 1")->select();
                              $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                          }
                      }
                  }else{
                      $cates = '';
                  }
              }

              if(isset($post['sum']) && ($post['sum']!='')){
                  $where['a.sum']=['=',$post['sum']];
              }
              $where['a.arcrank'] = 1;
              $where['a.status'] = 1;
//        SELECT * FROM `#@__sys_enum` WHERE egroup='nativeplace' AND (evalue MOD 500)=0 ORDER BY disorder ASC
              $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();
              $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->order('list_order asc')->limit(15)->select();
              // print_r($cate);die;
              $datas = db('portal_xm a')->where($where)->order('pubdate desc')->paginate(15,false,['query' => request()->param(),'page'=>$page]);
              $dataArray = $datas->all();
              foreach ($dataArray as $k=>$v){
                  $category = db('portal_category')->where('id = '.$v['typeid'])->find();
                  $dataArray[$k]['category_name'] = $category['name'];
              }
            	if(empty($dataArray)){
                 $dataArray = db('portal_xm')->where('arcrank = 1 and status = 1 and recommend = 1')->order('update_time desc')->paginate(15,false,['query' => request()->param(),'page'=>$page]);
                 $dataArray = $dataArray->all();
                  foreach ($dataArray as $key => $value) {
                      $dataArray[$key]['category_name'] = db('portal_category')->where('id = '.$value['typeid'].' and status = 1 and ishidden = 1')->value('name');
                      $dataArray[$key]['class'] = substr($value['class'],0,1) == '/' ? substr($value['class'],1) : $value['class'];

                  }
              }
              $data = $dataArray;

              $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
           if(isset($id)){
                  $lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id ))  order by click desc limit 6 ");
                  $lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by weight desc limit 10 ");
              }else{
                  $lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->where("litpic != ' '")->order('click desc')->limit(6)->select();
                  $lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('weight desc')->limit(10)->select();
              }
              //$lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(6)->select();
              //$lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('aid asc')->limit(10)->select();
              $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id asc')->limit(10)->select();
              // $catess = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(12)->select();
              $catess = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();
              //查询底部数据
              $website = DB('website')->where(['id' => 1])->find();
               if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'xiangmu')){
                $seo = db("portal_category")->where("path="."'$post[classname]' and status =1 and ishidden = 1")->find();

                 $py = $post['address'];
                $nativeplace = db('sys_enum')->where("py = '$py'")->value("ename");

                if($seo['parent_id'] != 0 || $post['num'] || $nativeplace){
                  $seo_title = $nativeplace.$post['num'].$seo['name']."加盟项目_".$nativeplace.$post['num'].$seo['name']."加盟店排行榜_第 ".$page." 页-91创业网";

                  $seo_keywords = $nativeplace.$post['num'].$seo['name'].",".$nativeplace.$post['num'].$seo['name']."店,".$nativeplace.$post['num'].$seo['name']."排行榜,".$nativeplace.$post['num'].$seo['name']."十大品牌,".$selcttag4.$post['num'].$seo['name']."费多少钱";

                  $seo_description = "91创业网-汇集各种".$nativeplace.$post['num'].$seo['name'].",".$nativeplace.$post['num'].$seo['name']."连锁品牌,".$nativeplace.$post['num'].$seo['name']."十大品牌排行榜等".$nativeplace.$post['num'].$seo['name']."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$post['num'].$seo['name']."加盟项目, 让创业者轻松创业！";
                }else{
                  $seo_title = $seo['seo_title'];
                  $seo_keywords = $seo['seo_keywords'];
                  $seo_description = $seo['seo_description'];
                }
                
              }else{
                  $seo_title = "加盟项目大全_2018招商加盟项目推荐_第 ".$page." 页-91创业网";
                  $seo_keywords = "加盟,招商加盟,品牌加盟,品牌加盟网";
                  $seo_description = "91创业网-汇集各种品牌加盟项目大全,招商连锁加盟,品牌加盟十大排行榜等2018招商加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的品牌加盟项目,让创业者轻松创业";
              }
           
              

              $this->assign('seo_title',$seo_title);
              $this->assign('seo_keywords',$seo_keywords);
              $this->assign('seo_description',$seo_description);
              $this->assign('website',$website);
              $this->daohang();
              $this->dibu();
              $this->assign('selcttag1',$selcttag1);
              $this->assign('selcttag2',$selcttag2);
              $this->assign('selcttag3',$selcttag3);
              $this->assign('selcttag4',$selcttag4);
              $this->assign('selcttag5',$selcttag5);

              $this->assign('num',$post['num']);
              $this->assign('address',$post['address']);

              $this->assign('youlian',$youlian);
              $this->assign('data',$data);
              $this->assign('datas',$datas);
              $this->assign('sys',$sys);
              $this->assign('cate',$cate);
              $this->assign('cates',$cates);
              $this->assign('catess',$catess);
              $this->assign('lick5',$lick5);
              $this->assign('lick1',$lick1);
              $this->assign('lick2',$lick2);
              $this->assign('lick3',$lick3);
              $this->assign('array_reverse',$array_reverse);
              return $this->fetch(':mobile/list');
          }else{
            if(isset($post['address'])){
            	if(is_numeric(substr($post['address'],0,1))){
              	
            	$post['address'] = '';
            	}
            }
            
              $selcttag1='';//行业分类
              $selcttag2='';//行业子分类
              $selcttag3=isset($post['num'])?$post['num']:'';;//投资金额
              $selcttag4=isset($post['address'])?$post['address']:'';;//热门地区
              $selcttag5='';//页面当前分类

              if(isset($post['q']) && ($post['q'] != '')){
                  $selcttag1='';//行业分类
                  $selcttag2='';//行业子分类
                  $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
                  $selcttag4=isset($post['address'])?$post['address']:'';//热门地区
                  $selcttag5='xiangmu';//页面当前分类
//                $post="";
              }else{
                  if(isset($post['classname']) && ($post['classname']=='xiangmu')){
                      $selcttag1='';//行业分类
                      $selcttag2='';//行业子分类
                      $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
                      $selcttag4=isset($post['address'])?$post['address']:'';//热门地区
                    
                      $selcttag5='xiangmu';//页面当前分类
                      $post="";
                  }
              }

              $array_reverse = "";
              $youlian = "";
              if(isset($post['classname']) && ($post['classname']!='')){
                  $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
                  $array_reverse = $this->position($id);
              }
              $where=[];
              if($selcttag3){
                  if(isset($selcttag3) && ($selcttag3!=''))
                  {
                      if($selcttag3 == 100)
                      {
                          $res = $selcttag3.'万以上';
                      }else{
                          $res = $selcttag3.'万';
                      }
                      $where['a.invested'] = $res;
                  }
              }else{
                  if(isset($post['num']) && ($post['num']!=''))
                  {
                      if($post['num'] == 100)
                      {
                          $res = $post['num'].'万以上';
                      }else{
                          $res = $post['num'].'万';
                      }
                      $where['a.invested'] = $res;
                  }
              }
              if($selcttag4){
                  if(isset($selcttag4) && ($selcttag4!=''))
                  {
                      $py = $selcttag4;
                      $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }else{
                  if(isset($post['address']) && ($post['address']!=''))
                  {
                      $py = $post['address'];
                      $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }

              // if(isset($post['address']) && ($post['address']!=''))
              // {
              //     $selcttag4=$post['address'];
              //     $py = $post['address'];
              //     $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
              //     $where['a.nativeplace'] = ['=',$nativeplace];
              // }
              if(isset($post['q']) && ($post['q'] != '')){
                  $cates = '';
              }else{
                  if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'portal')){
                      $res = db("portal_category")->field('id,path,parent_id')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                      $id=$res['id'];
                      $parent_id=$res['parent_id'];
                      // $parent_id = db("portal_category")->where("path="."'$post[classname]'")->value('parent_id');
                      if(isset($parent_id) && ($parent_id == 0))
                      {
                          // $where['a.typeid'] = $id;
                          //查询出当前以及分类下所有二级分类
                          $cates =  db("portal_category")->where("parent_id = $id and status = 1 and ishidden = 1")->select();
                          $ca = json_encode($cates);
                          $cates = json_decode($ca,true);
                          $ids = array_column($cates,'id');
                          // print_r($ids);die;
                          $where['a.typeid'] = array('in',$ids);

                          $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                          $selcttag1=$post['classname'];
                          $selcttag2='';
                          $selcttag5=$selcttag1;
                      }else{

                          $where['a.typeid'] = $id;
                          $selcttag1= db("portal_category")->where("id = ".$parent_id.' and status = 1 and ishidden = 1')->value("path");
                          $selcttag2=$post['classname'];
                          $selcttag5=$selcttag2;
                          $cates =  db("portal_category")->where("parent_id = $parent_id and status = 1 and ishidden = 1")->select();
                          $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                      }
                  }else{
                      $youlian = db("flink")->where("typeid = 9999 and ischeck = 1")->order("dtime desc")->limit(50)->select();
                      $cates = '';
                  }
              }

              if(isset($post['sum']) && ($post['sum']!='')){
                  $where['a.sum']=['=',$post['sum']];
              }

              $where['a.arcrank'] = 1;
              $where['a.status'] = 1;
//        SELECT * FROM `#@__sys_enum` WHERE egroup='nativeplace' AND (evalue MOD 500)=0 ORDER BY disorder ASC
              $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();

              $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->order('list_order asc')->limit(15)->select();

              $data = db('portal_xm a')->where($where)->order('update_time desc')->paginate(15,false,['query' => request()->param(),'page'=>$page]);

              $dataa = $data->all();

              foreach ($dataa as $key => $value) {
                  $dataa[$key]['categoryname'] = db('portal_category')->where('id = '.$value['typeid'].' and status = 1 and ishidden = 1')->value('name');
              }  
            	if(empty($dataa)){
                  $dataa = db('portal_xm')->where('arcrank = 1 and status = 1 and recommend = 1')->order('update_time desc')->paginate(15,false,['query' => request()->param(),'page'=>$page]);
                  $dataa = $dataa->all();
                  foreach ($dataa as $key => $value) {
                      $dataa[$key]['categoryname'] = db('portal_category')->where('id = '.$value['typeid'].' and status = 1 and ishidden = 1')->value('name');
                      $dataa[$key]['class'] = substr($value['class'],0,1) == '/' ? substr($value['class'],1) : $value['class'];

                  }
                }
        

              $w = "FIND_IN_SET('a',flag)";
              $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->limit(0,5)->select();
              if(isset($id)){
                $lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by click desc limit 6 ");
          		$lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by weight desc limit 10 ");
                //$lick1 = db('portal_xm')->where('status = 1 and arcrank = 1 and typeid = '.$id)->where("litpic != ' '")->order('click desc')->limit(6)->select();
                //$lick2 = db('portal_xm')->where('status = 1 and arcrank = 1 and typeid = '.$id)->order('weight desc')->limit(10)->select();
                $lick3 = db('portal_post')->where('status = 1 and post_status = 1 and parent_id = '.$id)->order('id desc')->limit(10)->select();
                $lick3 = $lick3->all();
                foreach ($lick3 as $k => $v) {
                    $lick3[$k]['classs'] = substr($v['class'],0,4);
                }
                if(empty($lick3)){
                 $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
                  $lick3 = $lick3->all();
                  foreach ($lick3 as $k => $v) {
                      $lick3[$k]['classs'] = substr($v['class'],0,4);
                  }
                }
                
              }else{
                //$lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by click desc limit 6 ");
          		//$lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by weight desc limit 10 ");
                $lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->where("litpic != ' '")->order('click desc')->limit(6)->select();
                $lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('weight desc')->limit(10)->select();
                $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
                $lick3 = $lick3->all();
                  foreach ($lick3 as $k => $v) {
                      $lick3[$k]['classs'] = substr($v['class'],0,4);
                  }
              }
              
              //查询底部数据
              $website = DB('website')->where(['id' => 1])->find();
              if(isset($post['classname']) && ($post['classname']!='')){

                $seo = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                 $py = $selcttag4;
                $nativeplace = db('sys_enum')->where("py = '$py'")->value("ename");

                if($seo['parent_id'] != 0 || $selcttag3 || $nativeplace){
                  $seo_title = $nativeplace.$selcttag3.$seo['name']."加盟项目_".$nativeplace.$selcttag3.$seo['name']."加盟店排行榜_第 ".$page." 页-91创业网";
                  $seo_keywords = $nativeplace.$selcttag3.$seo['name'].",".$nativeplace.$selcttag3.$seo['name']."店,".$nativeplace.$selcttag3.$seo['name']."排行榜,".$nativeplace.$selcttag3.$seo['name']."十大品牌,".$selcttag4.$selcttag3.$seo['name']."费多少钱";
                  $seo_description = "91创业网-汇集各种".$nativeplace.$selcttag3.$seo['name'].",".$nativeplace.$selcttag3.$seo['name']."连锁品牌,".$nativeplace.$selcttag3.$seo['name']."十大品牌排行榜等".$nativeplace.$selcttag3.$seo['name']."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$selcttag3.$seo['name']."加盟项目, 让创业者轻松创业！";
                }else{
                  $seo_title = $seo['seo_title'];
                  $seo_keywords = $seo['seo_keywords'];
                  $seo_description = $seo['seo_description'];
                }
                
              }else{
                  $seo_title = "加盟项目大全_2018招商加盟项目推荐_第 ".$page." 页-91创业网";
                  $seo_keywords = "加盟,招商加盟,品牌加盟,品牌加盟网";
                  $seo_description = "91创业网-汇集各种品牌加盟项目大全,招商连锁加盟,品牌加盟十大排行榜等2018招商加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的品牌加盟项目,让创业者轻松创业";
              }
              $advert = db()->query("select *  from (SELECT  *,IF(`status`=1,unix_timestamp(now())+30 ,timeend ) as newsendtime from cmf_advertisement ) as  t   where newsendtime> ".time()." and is_delete =2 and type = 3 order by id asc");
              $this->assign('advert',$advert);

              $this->assign('seo_title',$seo_title);
              $this->assign('seo_keywords',$seo_keywords);
              $this->assign('seo_description',$seo_description);
              $this->assign('website',$website);
              $this->daohang();
              $this->dibu();
              $this->assign('selcttag1',$selcttag1);
              $this->assign('selcttag2',$selcttag2);
              $this->assign('selcttag3',$selcttag3);
              $this->assign('selcttag4',$selcttag4);
              $this->assign('selcttag5',$selcttag5);
              $this->assign('youlian',$youlian);
              $this->assign('data',$data);
              $this->assign('dataa',$dataa);
              $this->assign('sys',$sys);
              $this->assign('cate',$cate);
              $this->assign('cates',$cates);
              $this->assign('lick5',$lick5);
              $this->assign('lick1',$lick1);
              $this->assign('lick2',$lick2);
              $this->assign('lick3',$lick3);
              $this->assign('array_reverse',$array_reverse);
              return $this->fetch(':list');
          }

    }



    public function classtype()
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $post=$this->request->param();
            $selcttag1='';//行业分类
            $selcttag2='';//行业子分类
            $selcttag3='';//投资金额
            $selcttag4='';//热门地区
            $selcttag5='';//页面当前分类


            $array_reverse = "";
            $youlian = "";
            if(isset($post['classname']) && ($post['classname']!='')){
                $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
                $array_reverse = $this->position($id);
            }

            $where=[];
            if(isset($post['q']) && ($post['q']!=''))
            {
                $where['a.title'] = [ 'like', "%".$post['q']."%"];
            }

            if(isset($post['num']) && ($post['num']!=''))
            {
                if($post['num'] == 100)
                {
                    $post['invested'] = $post['num'].'万以上';
                }else{
                    $post['invested'] = $post['num'].'万';
                }
                $where['a.invested'] = ['=',$post['invested']];
                $selcttag3=$post['num'];
            }

            if(isset($post['address']) && ($post['address']!=''))
            {
                $selcttag4=$post['address'];
                $py = $post['address'];
                $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                $where['a.nativeplace'] = ['=',$nativeplace];
            }

            if(isset($post['classname']) && ($post['classname']!='')){
                $res = db("portal_category")->field('id,path,parent_id')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                $id=$res['id'];
                $parent_id=$res['parent_id'];

                // $parent_id = db("portal_category")->where("path="."'$post[classname]'")->value('parent_id');

                if($parent_id == 0)
                {
                    $where['a.typeid'] = $id;
                    $cates =  db("portal_category")->where("parent_id = $id and status = 1 and ishidden = 1")->select();
                    $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                    $selcttag1=$post['classname'];
                    $selcttag2='';
                    $selcttag5=$selcttag1;
                }else{
                    $where['a.typeid'] = $id;
                    $selcttag1= db("portal_category")->where("id = ".$parent_id.' and status = 1 and ishidden = 1')->value("path");
                    $selcttag2=$post['classname'];
                    $selcttag5=$selcttag2;
                    $cates =  db("portal_category")->where("parent_id = $parent_id and status = 1 and ishidden = 1")->select();
                    $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                }
            }else{
                $cates = '';

            }
            if(isset($post['sum']) && ($post['sum']!='')){
                $where['a.sum']=['=',$post['sum']];
            }
            $where['a.arcrank'] = ['=',1];
            $where['a.status'] = ['=',1];
//        SELECT * FROM `#@__sys_enum` WHERE egroup='nativeplace' AND (evalue MOD 500)=0 ORDER BY disorder ASC
            $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            $data = db('portal_xm a')->where($where)->paginate(15);
            $data->appends($post);//持分页条件


            $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
          $lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by click desc limit 6 ");
          $lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by weight desc limit 10 ");
            //$lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(6)->select();
            //$lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('aid asc')->limit(10)->select();
            $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id asc')->limit(10)->select();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
            $this->assign('selcttag1',$selcttag1);
            $this->assign('selcttag2',$selcttag2);
            $this->assign('selcttag3',$selcttag3);
            $this->assign('selcttag4',$selcttag4);
            $this->assign('selcttag5',$selcttag5);

            $this->assign('youlian',$youlian);
            $this->assign('data',$data);

            $this->assign('sys',$sys);
            $this->assign('cate',$cate);
            $this->assign('cates',$cates);
            $this->assign('lick5',$lick5);
            $this->assign('lick1',$lick1);
            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('array_reverse',$array_reverse);
            return $this->fetch(':mobile/list');
        }else{
            $post=$this->request->param();
            $selcttag1='';//行业分类
            $selcttag2='';//行业子分类
            $selcttag3='';//投资金额
            $selcttag4='';//热门地区
            $selcttag5='';//页面当前分类


            $array_reverse = "";
            $youlian = "";
            if(isset($post['classname']) && ($post['classname']!='')){
                $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
                $array_reverse = $this->position($id);
            }

            $where=[];
            if(isset($post['q']) && ($post['q']!=''))
            {
                $where['a.title'] = [ 'like', "%".$post['q']."%"];
            }

            if(isset($post['num']) && ($post['num']!=''))
            {
                if($post['num'] == 100)
                {
                    $post['invested'] = $post['num'].'万以上';
                }else{
                    $post['invested'] = $post['num'].'万';
                }
                $where['a.invested'] = ['=',$post['invested']];
                $selcttag3=$post['num'];
            }

            if(isset($post['address']) && ($post['address']!=''))
            {
                $selcttag4=$post['address'];
                $py = $post['address'];
                $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                $where['a.nativeplace'] = ['=',$nativeplace];
            }

            if(isset($post['classname']) && ($post['classname']!='')){
                $res = db("portal_category")->field('id,path,parent_id')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                $id=$res['id'];
                $parent_id=$res['parent_id'];

                // $parent_id = db("portal_category")->where("path="."'$post[classname]'")->value('parent_id');

                if($parent_id == 0)
                {
                    $where['a.typeid'] = $id;
                    $cates =  db("portal_category")->where("parent_id = $id and status = 1 and ishidden = 1")->select();
                    $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                    $selcttag1=$post['classname'];
                    $selcttag2='';
                    $selcttag5=$selcttag1;
                }else{
                    $where['a.typeid'] = $id;
                    $selcttag1= db("portal_category")->where("id = ".$parent_id.' and status = 1 and ishidden = 1')->value("path");
                    $selcttag2=$post['classname'];
                    $selcttag5=$selcttag2;
                    $cates =  db("portal_category")->where("parent_id = $parent_id and status = 1 and ishidden = 1")->select();
                    $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                }
            }else{
                $cates = '';

            }
            if(isset($post['sum']) && ($post['sum']!='')){
                $where['a.sum']=['=',$post['sum']];
            }
            $where['a.arcrank'] = ['=',1];
            $where['a.status'] = ['=',1];
//        SELECT * FROM `#@__sys_enum` WHERE egroup='nativeplace' AND (evalue MOD 500)=0 ORDER BY disorder ASC
            $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            $data = db('portal_xm a')->where($where)->paginate(15);
            $data->appends($post);//持分页条件


            $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
          $lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by click desc limit 6 ");
          $lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=$id) order by weight desc limit 10 ");

            //$lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(6)->select();
            //$lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('aid asc')->limit(10)->select();
            $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id asc')->limit(10)->select();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
            $this->assign('selcttag1',$selcttag1);
            $this->assign('selcttag2',$selcttag2);
            $this->assign('selcttag3',$selcttag3);
            $this->assign('selcttag4',$selcttag4);
            $this->assign('selcttag5',$selcttag5);

            $this->assign('youlian',$youlian);
            $this->assign('data',$data);

            $this->assign('sys',$sys);
            $this->assign('cate',$cate);
            $this->assign('cates',$cates);
            $this->assign('lick5',$lick5);
            $this->assign('lick1',$lick1);
            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('array_reverse',$array_reverse);
            return $this->fetch(':list');
        }


    }


    //项目详情
    public function article_xm($id)
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $array_reverse = "";

            $data = db('portal_xm')->where("aid = $id")->find();
          	$post=$this->request->param();
          //判断当前class是不是对应的
            if($data['class'] != $post['classname'] && $post['classname'] != 'yangsheng'){
              return $this->error1();
             }
            if($data['nativeplace'])
            {
              $nativeplace = db('sys_enum')->where("evalue = ".$data['nativeplace']." and py != ''")->value("ename");
              $data['address'] = $nativeplace;
            }else{
              $data['address'] = $data['address'];
            }

            $typeid = $data['typeid'];

            $name = db("portal_category")->where("id = ".$typeid.' and status = 1 and ishidden = 1')->value("name");

            $array_reverse = $this->position($typeid);

            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(0,3)->select();

            $lick2 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(3,3)->select();

            $lick3 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(6,3)->select();

            $lick4 = db('portal_post')->where("parent_id = 51 and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();

            $w = "FIND_IN_SET('a',flag)";
            $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->limit(0,5)->select();

            $lick8 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(2,2)->select();

            $lick6 = db('portal_post')->where("parent_id = ".$data['typeid']." and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();
         
         
			if(!empty($lick6)){
              $wherew['post_title'] = ['like','%'.$data['title'].'%'];
              $wherew['status'] = 1;
              $wherew['post_status'] = 1;
             
             $lick6 = db("portal_post")->where($wherew)->limit(10)->select(); 

            }
            $typeid = db('portal_xm')->where("aid = $id and status = 1 and arcrank = 1")->value('typeid');

            $imgs = db("uploads")->where("arcid = ".$id)->select();
          	$imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }

            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->limit(7)->select();

            $this->daohang();

            $this->dibu();

            $this->assign("name",$name);
			$this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);

            $this->assign('data',$data);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('lick8',$lick8);

            $this->assign('lick6',$lick6);

            $this->assign("lick7",$lick7);

            $this->assign('array_reverse',$array_reverse);

//        $this->assign('url',$url);

            return $this->fetch(':mobile/article_xm');
        }else{

            $array_reverse = "";
				$post=$this->request->param();
            $data = db('portal_xm')->where("aid = $id")->find();
          //判断当前class是不是对应的
            if($data['class'] != $post['classname'] && $post['classname'] != 'yangsheng'){
              return $this->error1();
             }
          
            if($data['nativeplace'])
            {
              $nativeplace = db('sys_enum')->where("evalue = ".$data['nativeplace']." and py != ''")->value("ename");
              $data['address'] = $nativeplace;
            }else{
              $data['address'] = $data['address'];
            }

            $typeid = $data['typeid'];

            $name = db("portal_category")->where("id = ".$typeid.' and status = 1 and ishidden = 1')->value("name");

            $array_reverse = $this->position($typeid);

            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(0,3)->select();

            $lick2 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(3,3)->select();

            $lick3 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(6,3)->select();

            $lick4 = db('portal_post')->where("parent_id = 11 and status = 1 and post_status = 1")->order("id desc")->limit(10)->select();

           $w = "FIND_IN_SET('a',flag)";
           $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->limit(0,5)->select();

            $lick6 = db('portal_post')->where("parent_id = 399 and status = 1 and post_status = 1")->order("id desc")->limit(10)->select();

            $typeid = db('portal_xm')->where("aid = $id and status = 1 and arcrank = 1")->value('typeid');

           	$imgs = db("uploads")->where("arcid = ".$id)->select();
           	$imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }

            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->limit(7)->select();
            if(empty($lick7)){
              $wherew['post_title'] = ['like','%'.$data['title'].'%'];
              $wherew['status'] = 1;
              $wherew['post_status'] = 1;
             
             $lick7 = db("portal_post")->where($wherew)->limit(7)->select(); 
            }
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);

            $this->daohang();

            $this->dibu();

            $this->assign('cate',$cate);

            $this->assign("name",$name);
			$this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);

            $this->assign('data',$data);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('lick6',$lick6);

            $this->assign("lick7",$lick7);

            $this->assign('array_reverse',$array_reverse);

//        $this->assign('url',$url);

            return $this->fetch(':article_xm');
        }

    }





    public function article_jiameng()
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $this->fetch(':mobile/article_jiameng');
        }else{
            $this->fetch(':article_jiameng');
        }
    }




    //新闻列表页
    public function news($post)
    {
       $post_url=$this->request->param();
        $str_flg = strpos($post_url['address'],'list_')!==false;
        if(!empty($post_url['address']) && $str_flg == false){
            return $this->error1();
        }
     $url = $_SERVER["QUERY_STRING"];

        if($url){
            $array = explode('/', $url);
            $key = '';
            foreach ($array as $k=>$v){
                if(strpos($v,'list_')  == 0){
                    $key = $k;
                }
            }
            $page = substr($array[$key], 5, 4);
        }else{
            $page = 1;
        }
        $page = $this->findNum($page);
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $where=[];
            $youlian = '';
            if(!empty($post['id']) && ($post['id'] != ' ') && ($post['classname'] == 'news') && strpos($post['id'],'list_') === false){
                $classname = $post['classname'].'/'.$post['id'];
            }else{
                $classname = $post['classname'];
            }

            if(isset($post['classname']) && ($post['classname']!=''))

            {
                $id = db("portal_category")->where("path="."'$classname' and status = 1 and ishidden = 1")->value('id');

                $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(50)->select();
                $name = db('portal_category')->where("id = "."'$id'")->value('name');
                $name1 = str_replace('资讯','',$name);
                $name1 = str_replace('加盟','',$name1);
                $ids = db("portal_category")->where("parent_id="."'$id' and status = 1 and ishidden = 1")->column('id');
                array_unshift($ids,$id);
                $where['parent_id'] = ['in',$ids];

                $where['status'] = 1;
              	$where['post_status'] = 1;

                $array_reverse = $this->position($id);
            }
            //if($classname != 'news'){
            //    $where['class'] = ['in',$post['classname'].$post['id']];
           // }
          $tmp_classname = $post['classname'];
              $tmp_flg = strpos($post['id'],'list_') !== false;
              if($post['id'] && $tmp_flg == false){
                  $tmp_classname .='/'.$post['id'];
              }
          
            $seo_arr = db("portal_category")->where("path="."'$tmp_classname' and status = 1 and ishidden = 1")->find();
            $data = db('portal_post')->where($where)->order('published_time desc')->paginate(10,false,['page'=>$page]);
            $data1 = db('portal_post')->where('parent_id = 11 and status = 1 and post_status = 1')->order('click asc')->limit(10)->select();

            $data2 = db('portal_post')->where('parent_id = 399 and status = 1 and post_status = 1')->order('click asc')->limit(10)->select();

            $data3 = db('portal_xm')->where("status = 1 and arcrank = 1")->order('click asc')->limit(10)->select();

            $cate = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();


            foreach($cate as $key=>$val)

            {

                $val['data'] = db("portal_xm")->where("typeid = ".$val['id'].' and status = 1 and arcrank = 1')->order("click asc")->limit(35)->select();

                $data4[] = $val;

            }
            $cate = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();
			 $wheres['parent_id'] = ['in',$ids];
           	$wheres['status'] = 1;	
          	$wheres['post_status'] = 1;	
            $canyin_new = db('portal_post')->where($wheres)->order("post_hits asc")->limit(12)->select();
            $news1 = db('portal_post')->where('status = 1 and post_status = 1')->order('published_time desc')->find();

            if(isset($post['id']) && $post['id']!='' && $post['classname']){
                $cat_arr = db('portal_category')->where(['path' => $post['classname'].$post['id']])->select()->toArray();
                if(count($cat_arr)){
                    $a = substr($cat_arr['0']['path'],4);
                    $array_reverse[] = array(
                        'id'    =>  $cat_arr['0']['id'],
                        'name'  =>  $cat_arr['0']['name'],
                        'path'  =>  "news/".$a,
                        'parent_id' =>  $cat_arr['0']['parent_id'],
                    );
                }
            }
            //去重处理
            $del_arr = [];
            foreach ($array_reverse as $k => $v){
                if(in_array($v['id'],$del_arr)){
                    unset($array_reverse[$k]);
                }
                $del_arr[] = $v['id'];
            }
          
          	$this->assign('seo_arr',$seo_arr);

            $this->assign('cate',$cate);

            $this->daohang();

            $this->dibu();

            $this->assign('youlian',$youlian);

            $this->assign('data',$data);

            $this->assign('canyin_new',$canyin_new);

            $this->assign('data1',$data1);

            $this->assign('data2',$data2);

            $this->assign('data3',$data3);

            $this->assign('data4',$data4);

            $this->assign('page',$page);

            $this->assign('news1',$news1);

            $this->assign('array_reverse',$array_reverse);

            return $this->fetch(':mobile/news_list');
        }else{
            $where=[];
            $youlian = '';
           
            if(!empty($post['id']) && ($post['id'] != ' ') && ($post['classname'] == 'news') && strpos($post['id'],'list_') === false){
              $classname = $post['classname'].'/'.$post['id'];
            }else{
              $classname = $post['classname'];
            }
            if(isset($post['classname']) && ($post['classname']!=''))
            {
              if($classname!='news'){
                 $id = db('portal_category')->where("path = "."'$classname' and status = 1 and ishidden = 1")->value('id');

              }else{
                $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
              }
              $tmp_classname = $post['classname'];
             $tmp_classname = $post['classname'];
              $tmp_flg = strpos($post['id'],'list_') !== false;
              if($post['id'] && $tmp_flg == false){
                  $tmp_classname .='/'.$post['id'];
              }
              $seo_arr = db("portal_category")->where("path="."'$tmp_classname' and status = 1 and ishidden = 1")->find();
			 	//$array_reverse = $this->position($id);
                $name = db('portal_category')->where("id = "."'$id'")->value('name');
                $name1 = str_replace('资讯','',$name);
                $name1 = str_replace('加盟','',$name1);
                $ids = db("portal_category")->where("parent_id="."'$id' and status = 1 and ishidden = 1")->column('id');

                array_unshift($ids,$id);

                $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(50)->select();

                $where['parent_id'] = ['in',$ids]; 

                $where['status'] = 1;

                $where['post_status'] = 1;

                $array_reverse = $this->position($id);
            }
          
            if(isset($post['id'])){
                $data = db('portal_post')->where($where)->order('published_time desc')->paginate(10,false,['page'=>$page]);

               // $data1 = db('portal_post')->where('parent_id = "'.$id.'" ')->where('status = 1 and post_status = 1')->order('click asc')->limit(10)->select();
				 $data1 = db('portal_post')->where('parent_id = 11 and status = 1 and post_status = 1')->order('click desc')->limit(10)->select();
                $data2 = db('portal_post')->where('parent_id = "'.$id.'" ')->where('status = 1 and post_status = 1')->order('click asc')->limit(10)->select();


            }else{
                $data = db('portal_post')->where($where)->order('published_time desc')->paginate(10,false,['page'=>$page]);
              
                $data1 = db('portal_post')->where('parent_id = 11 and status = 1 and post_status = 1')->order('click desc')->limit(10)->select();

                $data2 = db('portal_post')->where('parent_id = 399 and status = 1 and post_status = 1')->order('click asc')->limit(10)->select();

                
            }
              $data3 = db('portal_xm')->where("status = 1 and arcrank = 1")->order('click asc')->limit(10)->select();
           

            $cate = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();
            $cates_arr = $cate->all();
            foreach($cates_arr as $k=>$v){
                  $cated = db('portal_category')->where(['parent_id'=>$v['id'],'status'=>1,'ishidden'=>1])->column('id');
                  array_unshift($cated,$v['id']);
                  $cates_arr[$k]['ids'] = implode(',',$cated);
                }
            foreach($cates_arr as $key=>$val)
            {
                $whereq['typeid'] = array('in',$val['ids']);
                $val['data'] = db("portal_xm")->where($whereq)->where('status = 1 and arcrank = 1')->order("click asc")->limit(35)->select();
                $data4[] = $val;
            }
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            if(isset($post['id']) && $post['id']!='' && $post['classname']){
                $cat_arr = db('portal_category')->where(['path' => $post['classname'].$post['id']])->select()->toArray();
                if(count($cat_arr)){
                    $a = substr($cat_arr['0']['path'],4);
                    $array_reverse[] = array(
                        'id'    =>  $cat_arr['0']['id'],
                        'name'  =>  $cat_arr['0']['name'],
                        'path'  =>  "news/".$a,
                        'parent_id' =>  $cat_arr['0']['parent_id'],
                    );
                }
            }
            //去重处理
            $del_arr = [];
            foreach ($array_reverse as $k => $v){
                if(in_array($v['id'],$del_arr)){
                    unset($array_reverse[$k]);
                }
                $del_arr[] = $v['id'];
            }
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            $this->assign('cate',$cate);
            $this->daohang();

            $this->dibu();
          
          	$this->assign('seo_arr',$seo_arr);

            $this->assign('name',$name1);

            $this->assign('page',$page);

            $this->assign('youlian',$youlian);

            $this->assign('data',$data);

            $this->assign('data1',$data1);

            $this->assign('data2',$data2);

            $this->assign('data3',$data3);

            $this->assign('data4',$data4);

            $this->assign('array_reverse',$array_reverse);

            return $this->fetch(':news');
        }

    }



    public function article_news($id)

    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
          $data = db('portal_post')->where(" status = 1 and post_status = 1 and id = ".$id)->find();
            if(!$data) return $this->error1();
            $post=$this->request->param();

            if($post['classname'] != 'news' && $post['classname'] != $data['class']){
                return $this->error1();
            }
            //$data = db('portal_post')->where("id = ".$id)->find();
            $array_reverse = $this->position($data['parent_id']);
            if(isset($array_reverse[1])){
                $a = substr($array_reverse[1]['path'],4);
               $paths = 'news'.$a;
               $array_reverse[1]['path'] = $paths;
            }

            $lick1 = db('portal_post')->where("parent_id = 399 and status = 1 and post_status = 1")->order('id desc')->limit(10)->select();

            if(isset($data['did'])){
              $lick2 = db('portal_post')->order('did = '.$data['did'].' and status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
            }else{
              $lick2 = db('portal_post')->order('status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
            }

            $lick3 = db('portal_xm')->where("typeid = 9 and status = 1 and arcrank = 1")->order('click asc')->limit(10)->select();

            $cate = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();

            foreach($cate as $key=>$val)

            {

                $val['data'] = db("portal_xm")->where("typeid = ".$val['id'].' and status = 1 and arcrank = 1')->order("click asc")->limit(35)->select();

                $data4[] = $val;

            }

         //上一页
            $lick4_id = db('portal_post')->where("id < $id ")->max('id');
            $lick4 = [];
            if($lick4_id){
                $lick4 = db('portal_post')->where('status = 1 and post_status = 1')->find($lick4_id);
            }
            //下一页
            $lick5_id = db('portal_post')->where("id > $id")->min('id');
            $lick5 = [];
            if($lick5_id){
                $lick5 = db('portal_post')->where('status = 1 and post_status = 1')->find($lick5_id);
            }
            //上一页

           // $lick4 = db('portal_post')->where("id = ($id-1)")->where('status = 1 and post_status = 1')->find();

            //下一页

            //$lick5 = db('portal_post')->where("id = ($id+1)")->where('status = 1 and post_status = 1')->find();

            $relevant = db('portal_post')->where("parent_id = ".$data['parent_id'].' and status = 1 and post_status = 1')->limit(5)->select();

            $everybody = db('portal_post')->order('click desc')->limit(6)->select();

            $recommend = db('portal_xm')->where("typeid",'in','2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('status = 1 and arcrank = 1')->order('pubdate desc')->limit(2,6)->select();

            $recommend2 = db('portal_xm')->where("typeid",'in','2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('status = 1 and arcrank = 1')->order('pubdate desc')->limit(1)->find();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();

            $this->dibu();

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('data',$data);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('data4',$data4);

            $this->assign('relevant',$relevant);

            $this->assign('everybody',$everybody);

            $this->assign('recommend',$recommend);


            $this->assign('recommend2',$recommend2);

            $this->assign('array_reverse',$array_reverse);

            return $this->fetch(':mobile/article_news');
        }else{
          $data = db('portal_post')->where(" status = 1 and post_status = 1 and id = ".$id)->find();
            if(!$data) return $this->error1();
            $post=$this->request->param();

            if($post['classname'] != 'news' && $post['classname'] != $data['class']){
                return $this->error1();
            }

            $array_reverse = $this->position($data['parent_id']);
            if(isset($array_reverse[1])){
               $array_reverse[1]['path'] = $array_reverse[1]['path'];
            }
             
            if(isset($data['did'])){
               $lick1 = db('portal_post')->where("parent_id = 11 and status = 1 and post_status = 1")->order('click desc')->limit(10)->select();
              	$lick2 = db('portal_post')->where('did = '.$data['did'].' and status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
            }else{
               $lick1 = db('portal_post')->where("parent_id = 11 and status = 1 and post_status = 1")->order('click desc')->limit(10)->select();
              	$lick2 = db('portal_post')->where('status = 1 and post_status = 1')->order('id desc')->limit(10)->select();
            }
            $lick3 = db('portal_xm')->where("typeid = 9")->order('click asc')->limit(10)->select();
           

            $catea = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();

            foreach($catea as $key=>$val)

            {

                //$val['data'] = db("portal_xm")->where("typeid = ".$val['id'].' and status = 1 and arcrank = 1')->order("click asc")->limit(35)->select();
				 $val['data'] = db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and parent_id=".$val['id'].") order by click desc limit 35");
                $data4[] = $val;

            }
          
          
         //上一页
            $lick4_id = db('portal_post')->where("id < $id ")->max('id');
            $lick4 = [];
            if($lick4_id){
                $lick4 = db('portal_post')->where('status = 1 and post_status = 1')->find($lick4_id);
            }
            //下一页
            $lick5_id = db('portal_post')->where("id > $id")->min('id');
            $lick5 = [];
            if($lick5_id){
                $lick5 = db('portal_post')->where('status = 1 and post_status = 1')->find($lick5_id);
            }
            //上一页

           // $lick4 = db('portal_post')->where($data['parent_id'])->where("id = (".$id."-1)")->where('status = 1 and post_status = 1')->order('published_time desc')->find();
            //下一页

			// $lick5 = db('portal_post')->where("id = (".$id."+1)")->where('status = 1 and post_status = 1')->order('published_time desc')->find();
          
          
    		$cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();

            $this->dibu();

            $this->assign('cate',$cate);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('data',$data);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('data4',$data4);

            $this->assign('array_reverse',$array_reverse);

            return $this->fetch(':article_news');
        }
    }

    public function top()
    {
        $redis =new Redis();   //实例化

        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $recommend = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();
            $lick6 = db('portal_post')->where("parent_id = 401 and status = 1 and post_status = 1")->order("id desc")->limit(5)->select();

            $cy = db('portal_category')->where('id = 2')->order('list_order asc')->select()->toArray();
            foreach ($cy as $k1 => $v1) {
              $cy[$k1]['cys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '362,153,164,171')->order('list_order asc')->select();
            }
            $sp = db('portal_category')->where('id = 4')->order('list_order asc')->select()->toArray();
            foreach ($sp as $k1 => $v1) {
              $sp[$k1]['sps'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '109,119,113,111')->order('list_order asc')->select();
            }
            $my = db('portal_category')->where('id = 8')->order('list_order asc')->select()->toArray();
            foreach ($my as $k1 => $v1) {
              $my[$k1]['mys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '177,365,156,183')->order('list_order asc')->select();
            }
            $jy = db('portal_category')->where('id = 10')->order('list_order asc')->select()->toArray();
            foreach ($jy as $k1 => $v1) {
              $jy[$k1]['jys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '216,203,205,210')->order('list_order asc')->select();
            }
            $jc = db('portal_category')->where('id = 313')->order('list_order asc')->select()->toArray();
            foreach ($jc as $k1 => $v1) {
              $jc[$k1]['jcs'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '318,315,400,319')->order('list_order asc')->select();
            }
            
			$seo = db('portal_category')->where('id = 391')->find();
          	$this->assign('seo',$seo);
            $this->assign('cy',$cy);
            $this->assign('sp',$sp);
            $this->assign('my',$my);
            $this->assign('jy',$jy);
            $this->assign('jc',$jc);
            $this->daohang();
            $this->dibu();
            $this->assign('recommend',$recommend);
            $this->assign('lick6',$lick6);

            return $this->fetch(':mobile/top');
        }else{
            //加入redis缓存
            //1 获取redis是否有数据
            // if ( true ) 去数据 并赋值
            // else 查询数据 并存储redis 传值
            if($redis -> get('top_flg')){
                //取出缓存
              	$seo = json_decode($redis->get('top_seo' ),true);

              	$gif = json_decode($redis->get('top_gif' ),true);
                $website = json_decode($redis->get('top_website' ),true);
                $cate = json_decode($redis->get('top_cate' ),true);
                $cate1 = json_decode($redis->get('top_cate1' ),true);
                $cate2 = json_decode($redis->get('top_cate2' ),true);
                $cate3 = json_decode($redis->get('top_cate3' ),true);
                $cate4 = json_decode($redis->get('top_cate4' ),true);
                $cate5 = json_decode($redis->get('top_cate5' ),true);
                $cate6 = json_decode($redis->get('top_cate6' ),true);
                $cate7 = json_decode($redis->get('top_cate7' ),true);
                $cate8 = json_decode($redis->get('top_cate8' ),true);
                $cat1 = json_decode($redis->get('top_cat1'  ),true);
                $data = json_decode($redis->get('top_data'  ),true);
                $datas = json_decode( $redis->get('top_datas' ),true);
                $catess = json_decode($redis->get('top_catess'),true);
                $lick1 = json_decode($redis->get('top_lick1' ),true);
                $lick2 = json_decode($redis->get('top_lick2' ),true);
                $lick3 = json_decode($redis->get('top_lick3' ),true);
                $lick4 = json_decode($redis->get('top_lick4' ),true);
                $lick5 = json_decode($redis->get('top_lick5' ),true);
                $lick6 = json_decode($redis->get('top_lick6' ),true);
                $data1 = json_decode($redis->get('top_data1' ),true);
                $data2 = json_decode($redis->get('top_data2' ),true);
                $data3 = json_decode($redis->get('top_data3' ),true);
                $data4 = json_decode($redis->get('top_data4' ),true);
                $data5 = json_decode($redis->get('top_data5' ),true);

            }else{
                $lick1 = db("portal_xm")->where('title is not null and status = 1 and arcrank = 1')->order('pubdate asc')->limit(16)->select();
              	$whereo['typeid'] =['in','2,1,3,4,5,7']; 
                $lick2 = db("portal_xm")->where($whereo)->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                $lick3 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                $lick4 = db("portal_xm")->where('typeid', 'in', '6,362,265,57')->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                $lick5 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                $lick6 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->order('pubdate desc')->limit(10)->select();
                $arr = '2,312,8,10,5,4,7,313,9,1,3,339,6,396,420';
                $cate = db("portal_category_copy")->where("parent_id = 391")->select();
                $cates = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->select();
                $cates_arr = $cates->all();
                foreach($cates_arr as $k=>$v){
                    $cated = db('portal_category')->where(['parent_id'=>$v['id'],'status'=>1,'ishidden'=>1])->column('id');
                    array_unshift($cated,$v['id']);
                    $cates_arr[$k]['ids'] = implode(',',$cated);
                }
                foreach($cates_arr as $key=>$val)
                {
                    $wheres['typeid'] = array('in',$val['ids']);
                    $val['data'] = db("portal_xm")->where($wheres)->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                    $data[] = array_filter($val);
                }
                $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(7)->select();
                foreach($catess as $key=>$v)
                {
                    $v['data'] = db("portal_xm")->where('typeid = '.$v['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
                    $datas[] = $v;
                }
                $cate1 =  db("portal_category")->where("id = 401 and status = 1 and ishidden = 1")->find();
                $cate2 =  db("portal_category")->where("id = 402 and status = 1 and ishidden = 1")->find();
                $cate3 =  db("portal_category")->where("id = 403 and status = 1 and ishidden = 1")->find();
                $cate4 =  db("portal_category")->where("id = 404 and status = 1 and ishidden = 1")->find();
                $cate5 =  db("portal_category")->where("id = 405 and status = 1 and ishidden = 1")->find();
                $cate6 =  db("portal_category")->where("id = 406 and status = 1 and ishidden = 1")->find();
                $cate7 =  db("portal_category")->where("id = 408 and status = 1 and ishidden = 1")->find();
                $cate8 =  db("portal_category")->where("id = 409 and status = 1 and ishidden = 1")->find();
                $cat = db("portal_category")->where('id', 'in', '401,402')->where('status = 1 and ishidden = 1')->select();
                foreach($cat as $ke=>$va)
                {
                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
                    $data1[] = $va;
                }
                $cat = db("portal_category")->where('id', 'in', '403,404')->select();
                foreach($cat as $ke=>$va)
                {
                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
                    $data2[] = $va;
                }
                $cat = db("portal_category")->where('id', 'in', '405,406')->select();
                foreach($cat as $ke=>$va)
                {
                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
                    $data3[] = $va;
                }
                $cat = db("portal_category")->where('id', 'in', '408,409')->select();
                foreach($cat as $ke=>$va)
                {
                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
                    $data4[] = $va;
                }
                $cat1 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->select();
                foreach($cat1 as $key=>$val)
                {
                    $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();
                    //$val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
                  	$val['data'] = db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and (parent_id=".$val['id']." or id=".$val['id']." )  ) limit 10");
                    $data5[] = $val;
                }
                //查询底部数据
                $website = DB('website')->where(['id' => 1])->find();
				$gifs = db('advertisement')->where('is_delete = 2 and type = 1')->limit(4)->select();
              	foreach($gifs as $k=>$v){
                	if($v['status'] == 2){
                      $time = time();
                      $gif = db('advertisement')->where(" ".$time."between ".$v['timestart']." and ".$v['timeend']." and is_delete = 2 and type = 1")->limit(4)->select();
                    }else{
                     $gif = db('advertisement')->where('is_delete = 2 and type = 1')->limit(4)->select();
                    }
                }
              
              $seo = db('portal_category')->where('id = 391')->find();


                //加入缓存
                $redis->set('top_flg' , 1 , 300);
              	$redis->set('top_gif' , json_encode($gif,JSON_UNESCAPED_UNICODE) , 300);
              	$redis->set('top_seo' , json_encode($seo,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_website' , json_encode($website,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate' , json_encode($cate,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate1' , json_encode($cate1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate2' , json_encode($cate2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate3' , json_encode($cate3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate4' , json_encode($cate4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate5' , json_encode($cate5,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate6' , json_encode($cate6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate7' , json_encode($cate7,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cate8' , json_encode($cate8,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_cat1' , json_encode($cat1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data' , json_encode($data,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_datas' , json_encode($datas,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_catess' , json_encode($catess,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick1' , json_encode($lick1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick2' , json_encode($lick2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick3' , json_encode($lick3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick4' , json_encode($lick4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick5' , json_encode($lick5,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_lick6' , json_encode($lick6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data1' , json_encode($data1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data2' , json_encode($data2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data3' , json_encode($data3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data4' , json_encode($data4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('top_data5' , json_encode($data5,JSON_UNESCAPED_UNICODE) , 300);
            }
          	$this->assign('seo',$seo);
			$this->assign('gif',$gif);
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
            $this->assign('cate',$cate);
            $this->assign('cate1',$cate1);
            $this->assign('cate2',$cate2);
            $this->assign('cate3',$cate3);
            $this->assign('cate4',$cate4);
            $this->assign('cate5',$cate5);
            $this->assign('cate6',$cate6);
            $this->assign('cate7',$cate7);
            $this->assign('cate8',$cate8);
            $this->assign('cat1',$cat1);
            $this->assign('data',$data);
            $this->assign('datas',$datas);
            $this->assign('catess',$catess);
            $this->assign('lick1',$lick1);
            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('lick4',$lick4);
            $this->assign('lick5',$lick5);
            $this->assign('lick6',$lick6);
            $this->assign('data1',$data1);
            $this->assign('data2',$data2);
            $this->assign('data3',$data3);
            $this->assign('data4',$data4);
            $this->assign('data5',$data5);

            return $this->fetch(':top');
        }

    }


    public function list_top()
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $post=$this->request->param();
            $tdk = db('portal_category')->where("path = "."'$post[classname]' and status = 1 and ishidden = 1")->where('status = 1')->find();

            $anking = db('portal_xm')->where("class = "."'$post[classname]'")->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();

            $recommend = db('portal_xm')->where("class = "."'$post[classname]'")->where('status = 1 and arcrank = 1')->order('click desc')->limit(10,6)->select();

            $prev = db('portal_category')->where('id = '.$tdk['parent_id'].' status = 1 and ishidden = 1')->find();
            $this->assign('anking',$anking);
            $this->assign('recommend',$recommend);

            $this->assign('prev',$prev);

            $this->assign('tdk',$tdk);

            return $this->fetch(":mobile/top_list");
        }else{
           // $post = $this->request->param();
           //  $id = $this->request->param('id', 0, 'intval');
            $post =  $this->request->param();
            $path = 'top/'.$post['id'];

          if($path == 'top/yypxjm'){
              $path = 'yingyupeixunjiameng';
            }else if($path == 'top/blspxb'){
              $path = 'yishu';
            }
            $tdk = db('portal_category')->where("path = '$path'")->find();

            $names = db('portal_category')->where("path = '$path'")->value('name');

            // $ids = db('portal_category')->where("id = $id")->value('parent_id');
            $ids = db('portal_category')->where("name = '$names' and parent_id != 0")->find();

            $idsa = db('portal_category')->where("name = '$names' and path = '$path'")->find();

            $name = db('portal_category')->where("id = ".$ids['parent_id'])->value('name');
          
            $paths = db('portal_category')->where("id = ".$ids['parent_id'])->value('path');

            $id = db("portal_category")->where("path = '$path'")->value('id');

            $name1 = db('portal_category')->where("id = ".$ids['id'])->value('name');
            $cate = db("portal_category")->where("parent_id = ".$idsa['parent_id'])->order('list_order asc')->select();

            // $lick1 = db("portal_xm")->where('typeid = '.$ids['id'])->order('click asc')->limit(0,1)->select();

            $lick1 = db("portal_xm")->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(0,1)->select();

            $lick2 = db("portal_xm")->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(1,9)->select();

            $lick3 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(0,1)->select();

            $lick4 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(1,2)->select();

            $lick5 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(3,7)->select();

            $cate1 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(10)->select();

            foreach($cate1 as $key=>$val)

            {

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(4)->select();

                $data[] = $val;

            }

            $data1 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(1,5)->select();

            $data2 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(10,5)->select();

            $data3 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(15,5)->select();

            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();

            foreach($cat2 as $key=>$val)

            {

                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();

                $data5[] = $val;

            }
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();

            $this->dibu();

            $this->assign('id',$id);

            $this->assign('cate',$cate);

            $this->assign('name',$name);

            $this->assign('path',$path);

            $this->assign('name1',$name1);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('data',$data);

            $this->assign('data1',$data1);

            $this->assign('data2',$data2);

            $this->assign('data3',$data3);

            $this->assign('cat2',$cat2);

            $this->assign('data5',$data5);

            $this->assign('tdk',$tdk);

            return $this->fetch(":list_top");
        }

    }



    public function index_top()

    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
           $post =  $this->request->param();
            $path = $post['id'];
            $arr = '2,312,8,10,5,4,7,313,9,1,3,339,6,396,420';

            $name = db("portal_category")->where("path = '$path' and status = 1 and ishidden = 1")->value('name');

            $id = db("portal_category")->where("path = '$path' and status = 1 and ishidden = 1")->value('id');

            $cate = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(15)->select();


            $lick1 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(10)->select();
            $lick2 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(0,8)->select();
            $lick3 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(8,8)->select();
            $lick4 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(16,8)->select();
            $lick5 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(24,8)->select();

            $cates =  db("portal_category")->where('parent_id = '.$id.' and status = 1 and ishidden = 1')->order('list_order asc')->limit(12)->select();

            foreach($cates as $key=>$val)

            {

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();

                $data[] = $val;

            }

            $catess =  db("portal_category")->where('parent_id = '.$id.' and status = 1 and ishidden = 1')->order('list_order asc')->limit(10)->select();

            foreach($cates as $key=>$val)

            {

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('click asc')->limit(8)->select();

                $data1[] = $val;

            }

            $cat1 = db("portal_category")->where('id', 'in', '401,402,403,404')->where('status = 1 and ishidden = 1')->select();

            foreach($cat1 as $key=>$val)

            {

                $val['data'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->limit(1)->select();

                $val['data1'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->limit(1,10)->select();

                $data2[] = $val;

            }

            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->select();

            foreach($cat2 as $key=>$val)

            {

                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();

                $data5[] = $val;

            }
          	
          
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();

            $this->dibu();

            $this->assign('cate',$cate);

            $this->assign('name',$name);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('data',$data);

            $this->assign('data1',$data1);

            $this->assign('data2',$data2);

            $this->assign('cat2',$cat2);

            $this->assign('data5',$data5);

            return $this->fetch(":monile/index_top");
        }else{
          
            // $id =  $this->request->param('id', 0, 'intval');
            $post =  $this->request->param();
            $path = 'top/'.$post['id'];
            // $arr = '2,312,8,10,5,4,7,313,9,1,3,339,6,396,420';
            $cate = db("portal_category")->where("parent_id = 391")->select();

            $cy = db('portal_category')->where('id = 376')->value('path');

            $gx = db('portal_category')->where('id = 379')->value('path');

            $jy = db('portal_category')->where('id = 386')->value('path');

            $sp = db('portal_category')->where('id = 380')->value('path');

            $zb = db('portal_category')->where('id = 381')->value('path');

            $name = db("portal_category")->where("path = '$path'")->value('name');

            $ids = db('portal_category')->where("name = '$name' and parent_id = 0")->find();

             $ids['idas'] = db('portal_category')->where('parent_id = '.$ids['id'].' and status = 1 and ishidden = 1')->column('id');
          
            $id = db("portal_category_copy")->where("path = '$path'")->value('id');
			$where['typeid'] =['in',$ids['idas']];
          	$where['status'] = 1;
          	$where['arcrank'] = 1;
            $lick1 = db("portal_xm")->where($where)->order('click desc')->limit(10)->select();


            $lick2 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(0,8)->select();

            $lick3 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(8,8)->select();

            $lick4 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(16,8)->select();

            $lick5 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(24,8)->select();

            $cates =  db("portal_category")->where('parent_id = '.$id .' and id != 503 and id != 662 and id != 645 and id != 725 and id != 719')->order('list_order asc')->limit(12)->select();

            foreach($cates as $key=>$val)
            {	
              	$names = $val['name'];
              	$path = $val['path'];
              	$idd = db('portal_category')->where("name = '$names' and path != '$path'")->find();
                $val['data'] = db("portal_xm")->where('typeid = '.$idd['id'].' and status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                $data[] = $val;
             
            }
            $catess =  db("portal_category_copy")->where('parent_id = '.$ids['id'])->order('list_order asc')->limit(10)->select();
            foreach($cates as $key=>$val)
            {
              	$namea = $val['name'];
              	$path = $val['path'];
              	$idd = db('portal_category')->where("name = '$namea' and path != '$path'")->find();
                $val['data'] = db("portal_xm")->where('typeid = '.$idd['id'].' and status = 1 and arcrank = 1')->order('click desc')->limit(8)->select();

                $data1[] = $val;

            }

            $cat1 = db("portal_category")->where('id', 'in', '401,402,403,404')->where('status = 1 and ishidden = 1')->select();

            foreach($cat1 as $key=>$val)
            {

                $val['data'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->order('id desc')->limit(1)->select();
              	$val['data'] = $val['data']->all();
              	foreach($val['data'] as $k=>$v){
                	$val['data'][$k]['classs'] = substr($v['class'],0,4);
                }

                $val['data1'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->order('id desc')->limit(1,10)->select();
              	$val['data1'] = $val['data1']->all();
				foreach($val['data1'] as $ks=>$vs){
                	$val['data1'][$ks]['classs'] = substr($vs['class'],0,4);
                }
                $data2[] = $val;

            }

            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();

            foreach($cat2 as $key=>$val)

            {

                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();

                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();

                $data5[] = $val;

            }
           	$patha = 'top/'.$post['id'];
          	$seo = db('portal_category')->where("path = '$patha'")->find();
          
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();

            $this->dibu();
			$this->assign('seo',$seo);
            $this->assign('cy',$cy);

            $this->assign('gx',$gx);

            $this->assign('jy',$jy);

            $this->assign('sp',$sp);

            $this->assign('zb',$zb);

            $this->assign('cate',$cate);

            $this->assign('name',$name);

            $this->assign('lick1',$lick1);

            $this->assign('lick2',$lick2);

            $this->assign('lick3',$lick3);

            $this->assign('lick4',$lick4);

            $this->assign('lick5',$lick5);

            $this->assign('data',$data);

            $this->assign('data1',$data1);

            $this->assign('data2',$data2);

            $this->assign('cat2',$cat2);

            $this->assign('data5',$data5);

            return $this->fetch(":index_top");
        }


    }

    //面包屑导航

    public function position($cid){//传递当前栏目id

        static $pos=array();//创建接受面包屑导航的数组

        if(empty($pos)){//哦，这个就比较重要了，如果需要把当前栏目也放到面包屑导航中的话就要加上


            $cates=db('portal_category')->field('id,name,parent_id,path')->find($cid);

            $pos[]=$cates;

        }

        $data=db('portal_category')->field('id,name,parent_id,path')->select();//所有栏目信息

        $cates=db('portal_category')->field('id,name,parent_id,path')->find($cid);//当前栏目信息

        foreach ($data as $k => $v) {
            if($cates['parent_id']==$v['id']){
                $pos[]=$v;
                $this->position($v['id']);

            }

        }

        return array_reverse($pos);


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


    // public function liuyan()


    // {
    //     echo "留言成功";

    // }

    function findNum($str=''){

      $str=trim($str);
        if(empty($str)){
          return '';
        }
      $temp=array('1','2','3','4','5','6','7','8','9','0');
      $result='';
      for($i=0;$i<strlen($str);$i++){
        if(in_array($str[$i],$temp)){
          $result.=$str[$i];
        }
      }
      return $result;
    }

    public function dibu()

    {

        $dibu = db("portal_category")->where("parent_id",'in','52,53')->select();

        $this->assign('dibu',$dibu);

    }

    public function error1(){
       $youlian = db("flink")->where("typeid = 9999")->order("dtime desc")->limit(50)->select();
            $this->daohang();
            $this->dibu();
            $this->assign('youlian', $youlian);
            
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)) {
           return $this->fetch(":mobile/error1");
        }else{
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
           $this->assign('cate',$cate);
            return $this->fetch(":error1");
        }
    }

         public function guanyu91(){
             $path = explode('/',VIEW_PATH);
             if(in_array('mobile',$path)){
                echo "手机端";
             }else{
                 $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
                 $website = DB('website')->where(['id' => 1])->find();
                 $this->assign('website',$website);
                 $this->dibu();
                 $this->daohang();
                 $this->assign('cate',$cate);
                 return $this->fetch(":guanyu91");
             }

        }
        public function guanyuwomen(){
            $path = explode('/',VIEW_PATH);
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website', $website);
            $this->dibu();
            $this->daohang();
            $this->assign('cate', $cate);

            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/guanyuwomen');
            }else {

                return $this->fetch(":guanyuwomen");
            }
        }
        public function lianxiwomen(){
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->dibu();
           $this->daohang();
           $this->assign('cate',$cate);
          return $this->fetch(":lianxiwomen");
        }
        public function mianzeshengming(){
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->dibu();
           $this->daohang();
           $this->assign('cate',$cate);
//          return $this->fetch(":mianzeshengming");
            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/mianzeshengming');
            }else {

                return $this->fetch(":mianzeshengming");
            }
        }
        public function falvguwen(){
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->dibu();
           $this->daohang();
           $this->assign('cate',$cate);
          return $this->fetch(":falvguwen");
        }
        public function youqinglianjie(){
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->dibu();
           $this->daohang();
           $this->assign('cate',$cate);
          return $this->fetch(":youqinglianjie");
        }
        public function tousushanchu(){
          $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(16)->select();
           $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->dibu();
           $this->daohang();
           $this->assign('cate',$cate);
          return $this->fetch(":tousushanchu");
        }
         public function paihangbang(){
          //总排行
          $zong = db('portal_xm')->where('status = 1 and arcrank = 1')->order('weight desc')->limit(10)->select();
          //本月
          $monthtimeStart = strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date("m"),1,date("Y"))));
          $mothtimeEnd = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y"))));
          $month = db('portal_xm')->where("pubdate between $monthtimeStart and $mothtimeEnd")->order('weight desc')->limit(10)->select();
          //本周
          $weektimeStart = strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"))));
          $weektimeEnd = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))));
          $week = db('portal_xm')->where("pubdate between $weektimeStart and $weektimeEnd")->order('weight desc')->limit(6)->select();
          $where['id'] = ['in','1,2,3,4,5,6,7,8,9,10,312,313,339,350'];
          $category = db('portal_category')->where($where)->limit(14)->select();
          $category = $category->all();
          foreach($category as $k=>$v){
             $category[$k]['types'] = db('portal_xm')->where('typeid = '.$v['id'])->order('click desc')->limit(40)->select();
          }
          $this->assign('category',$category);
          $this->assign('week',$week);
          $this->assign('month',$month);
          $this->assign('zong',$zong);
          return $this->fetch(':mobile/paihangbang');
        }

        public function chuangyezixun(){
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();

            //创业故事
            $cygs = $this->getPortalPostByPid(11);
            //创业知识
            $cyzz = $this->getPortalPostByPid(20);
            //餐饮加盟资讯
            $cyjm = $this->getPortalPostByPid(401);

            //女性加盟资讯
            $nxjm = $this->getPortalPostByPid(408,9);
            //零售加盟资讯
            $lsjm = $this->getPortalPostByPid(405,9);
            //建材加盟资讯
            $jcjm = $this->getPortalPostByPid(409,9);
            //家具加盟资讯
            $jjjm = $this->getPortalPostByPid(403,9);


            $this->assign('website',$website);
            $this->assign('cygs',$cygs);
            $this->assign('cyzz',$cyzz);
            $this->assign('cyjm',$cyjm);
            $this->assign('nxjm',$nxjm);
            $this->assign('lsjm',$lsjm);
            $this->assign('jcjm',$jcjm);
            $this->assign('jjjm',$jjjm);
            return $this->fetch(':mobile/chuangyezixun');
        }

        private function getPortalPostByPid($pid,$limit = 5){
            $arr = DB('portal_post')->where(['parent_id' => $pid ])->order('post_hits desc')->limit($limit)->select();
            return $arr;
        }
		private function get_pic_url($content){
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";//正则
            preg_match_all($pattern,$content,$match);//匹配图片
            return $match[1];//返回所有图片的路径

        }
   private function is_https() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
}