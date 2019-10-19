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
//        print_r($post);die;
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
            }else if($post['classname'] == 'sitemap'){
            	return $this->sitemap();
            }
            if($post['classname'] == 'nav'){
                return $this->nav();
            }
            if($post['classname'] == 'explain'){
                return $this->explain();
            }
            if($post['classname'] == 'article_poster'){
                return $this->article_poster($post['id']);
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
//                  print_r($post['classname']);die;
                $cateRoute = db('portal_category')->where(['path' => $post['classname'] ])->select();
//                echo db('portal_category')->getLastSql();die;
//                print_r($cateRoute);die;
                  if(count($cateRoute) == 0){

                      return $this->error1();
                  }
              }
//                echo 13;die;
                if(isset($post['id']) && ($post['id']!='')) {

                  if($post['id'] != $post['num']){

                    $id = substr($post['id'],0,1);
                         if(is_numeric($id)){

                           $stringtoID = (int)$post['id'];
                          $res = db("portal_xm")->where("aid = ".$stringtoID)->find();
//                          $ress = db("portal_post")->where("id = ".$stringtoID)->find();

                          if($res)
                          {
                              return $this->article_xm($stringtoID);
                          }else {
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
                                }
//                                else if($sql == ''){
//                                 return $this->error1();
//                                }
                                else{
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

          if(isset($post['classname']) && ($post['classname']=='xiangmu')){
              $selcttag1='';//行业分类
              $selcttag2='';//行业子分类
              $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
              $selcttag4=isset($post['address'])?$post['address']:'';//热门地区
              $selcttag5='xiangmu';//页面当前分类
              // $post="";
          }


//              $array_reverse = "";
              $youlian = "";
              if(isset($post['classname']) && ($post['classname']!='')){
                  $id = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->value('id');
//                  $array_reverse = $this->position($id);
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
                      $ename = db('sys_enum')->where("py = '$py'")->value("ename");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }else{
                  if(isset($post['address']) && ($post['address']!=''))
                  {
                      $py = $post['address'];
                      $nativeplace = db('sys_enum')->where("py = '$py'")->value("disorder");
                      $ename = db('sys_enum')->where("py = '$py'")->value("ename");
                      $where['a.nativeplace'] = $nativeplace;
                  }
              }

                  if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'portal')){
                      $res = db("portal_category")->field('id,path,parent_id,name')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();

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

              if(isset($post['sum']) && ($post['sum']!='')){
                  $where['a.sum']=['=',$post['sum']];
              }
              $where['a.arcrank'] = 1;
              $where['a.status'] = 1;
//              print_r($where);die;
              $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();
              $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->order('list_order asc')->limit(15)->select();
              $datas = db('portal_xm a')->where($where)->order('update_time desc')->paginate(10,false,['query' => request()->param(),'page'=>$page]);
              $dataArray = $datas->all();
              $infos = $datas->all();
              foreach ($dataArray as $k=>$v){
                  $category = db('portal_category')->where('id = '.$v['typeid'])->find();
                  $dataArray[$k]['category_name'] = $category['name'];
              }
              //当检索条件不满足时，显示该分类下的十个项目
            	if(empty($dataArray)){
                    $res = db("portal_category")->field('id,path,parent_id,name')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                    $id=$res['id'];
                    $wherere['typeid'] = array('in',$id);
                    $dataArray = db('portal_xm')->where('arcrank = 1 and status = 1')->where($wherere)->order('update_time desc')->limit(10)->select()->toArray();
                  foreach ($dataArray as $key => $value) {
                      $dataArray[$key]['category_name'] = db('portal_category')->where('id = '.$value['typeid'].' and status = 1 and ishidden = 1')->value('name');
                      $dataArray[$key]['class'] = substr($value['class'],0,1) == '/' ? substr($value['class'],1) : $value['class'];
                  }
              }
              $data = $dataArray;
            	//获取所有一级分类
              $arr = '2,1,4,5,7,10,3,6,8,9,312,313,396,420';
              $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,parent_id,name,path')->order('list_order asc')->select();
              $cated = db('portal_category')->where(['parent_id' => 2,'ishidden' => 1,'status' => 1])->field('id,path,name,mobile_thumbnail')->select();
              //创业资讯
              $where25['parent_id'] = ['in','399,401,402,403,404,405,406,407,408,409,433'];
              $zixun = db('portal_post')->where($where25)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();

              //创业知识
              $where26['parent_id'] = ['in','20,22,27,31'];
              $zhishi = db('portal_post')->where($where26)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();

              //创业故事
              $where27['parent_id'] = ['in','11'];
              $gushi = db('portal_post')->where($where27)->where('post_status = 1 and status = 1')->field('id,post_title,post_excerpt,thumbnail,published_time,class')->order('published_time desc')->limit(10)->select();


//              $lick5 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(20,5)->select();
//           if(isset($id)){
//                  $lick1= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id ))  order by click desc limit 6 ");
//                  $lick2= db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by weight desc limit 10 ");
//              }else{
//                  $lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->where("litpic != ' '")->order('click desc')->limit(6)->select();
//                  $lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->order('weight desc')->limit(10)->select();
//              }
//              $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->order('id asc')->limit(10)->select();
//              $catess = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();
              //查询底部数据
//              $website = DB('website')->where(['id' => 1])->find();
//               if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'xiangmu')){
//                $seo = db("portal_category")->where("path="."'$post[classname]' and status =1 and ishidden = 1")->find();
//                 $py = $post['address'];
//                $nativeplace = db('sys_enum')->where("py = '$py'")->value("ename");
//                if($seo['parent_id'] != 0 || $post['num'] || $nativeplace){
//                  $seo_title = $nativeplace.$post['num'].$seo['name']."加盟项目_".$nativeplace.$post['num'].$seo['name']."加盟店排行榜_第 ".$page." 页-91创业网";
//                  $seo_keywords = $nativeplace.$post['num'].$seo['name'].",".$nativeplace.$post['num'].$seo['name']."店,".$nativeplace.$post['num'].$seo['name']."排行榜,".$nativeplace.$post['num'].$seo['name']."十大品牌,".$selcttag4.$post['num'].$seo['name']."费多少钱";
//                  $seo_description = "91创业网-汇集各种".$nativeplace.$post['num'].$seo['name'].",".$nativeplace.$post['num'].$seo['name']."连锁品牌,".$nativeplace.$post['num'].$seo['name']."十大品牌排行榜等".$nativeplace.$post['num'].$seo['name']."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$post['num'].$seo['name']."加盟项目, 让创业者轻松创业！";
//                }else{
//
//                  $seo_title = $seo['seo_title'].'_第 '.$page.' 页-91创业网';
//                  $seo_keywords = $seo['seo_keywords'];
//                  $seo_description = $seo['seo_description'];
//                }
//
//              }else{
//                  $seo_title = "加盟项目大全_2018招商加盟项目推荐_第 ".$page." 页-91创业网";
//                  $seo_keywords = "加盟,招商加盟,品牌加盟,品牌加盟网";
//                  $seo_description = "91创业网-汇集各种品牌加盟项目大全,招商连锁加盟,品牌加盟十大排行榜等2018招商加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的品牌加盟项目,让创业者轻松创业";
//              }
//              print_r($post['address']);die;
              $seo = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
              $py = $post['address'];
              $nativeplace = db('sys_enum')->where("py = '$py'")->value("ename");
              $nativeplace = str_replace('市','',$nativeplace);
              if(isset($post['classname']) && ($post['classname']=='xiangmu')){

                  if($seo['parent_id'] != 0 || $post['num'] || $nativeplace){
                      $seo_name = str_replace('加盟','',$seo['name']);
                      $seo_title = $nativeplace.$post['num'].'万'.$seo_name."加盟项目_".$nativeplace.$post['num'].'万'.$seo_name."加盟店排行榜_第 ".$page." 页-91创业网";
                      $seo_keywords = $nativeplace.$post['num'].'万'.$seo_name.",".$nativeplace.$post['num'].'万'.$seo_name."店,".$nativeplace.$post['num'].'万'.$seo_name."排行榜,".$nativeplace.$post['num'].$seo_name."十大品牌,".$nativeplace.$post['num'].'万'.$seo_name."费多少钱";
                      $seo_description = "91创业网-汇集各种".$nativeplace.$post['num'].'万'.$seo_name.",".$nativeplace.$post['num'].'万'.$seo_name."连锁品牌,".$nativeplace.$post['num'].'万'.$seo_name."十大品牌排行榜等".$nativeplace.$post['num'].'万'.$seo_name."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$post['num'].'万'.$seo_name."加盟项目, 让创业者轻松创业！";
                  }else {
                      $seo_title = "加盟项目大全_2019招商加盟项目推荐_第 " . $page . "页-91创业网";
                      $seo_keywords = "加盟,招商加盟,品牌加盟,品牌加盟网";
                      $seo_description = "91创业网-汇集各种品牌加盟项目大全,招商连锁加盟,品牌加盟十大排行榜等2019招商加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的品牌加盟项目,让创业者轻松创业";

                  }

              }else{
                  if($seo['parent_id'] != 0 || $post['num'] || $nativeplace){
                      $seo_name = str_replace('加盟','',$seo['name']);
                      $seo_title = $nativeplace.$post['num'].'万'.$seo_name."加盟项目_".$nativeplace.$post['num'].'万'.$seo_name."加盟店排行榜_第 ".$page." 页-91创业网";
                      $seo_keywords = $nativeplace.$post['num'].'万'.$seo_name.",".$nativeplace.$post['num'].'万'.$seo_name."店,".$nativeplace.$post['num'].'万'.$seo_name."排行榜,".$nativeplace.$post['num'].'万'.$seo_name."十大品牌,".$nativeplace.$post['num'].'万'.$seo_name."费多少钱";
                      $seo_description = "91创业网-汇集各种".$nativeplace.$post['num'].'万'.$seo_name.",".$nativeplace.$post['num'].'万'.$seo_name."连锁品牌,".$nativeplace.$post['num'].'万'.$seo_name."十大品牌排行榜等".$nativeplace.$post['num'].'万'.$seo_name."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$post['num'].'万'.$seo_name."加盟项目, 让创业者轻松创业！";
                  }else{
                      $seo_title = $seo['seo_title'].'_第 '.$page.' 页-91创业网';
                      $seo_keywords = $seo['seo_keywords'];
                      $seo_description = $seo['seo_description'];
                  }
              }

              session('classname',$post['classname']);
              $this->assign('seo_title',$seo_title);
              $this->assign('seo_keywords',$seo_keywords);
              $this->assign('seo_description',$seo_description);
//              $this->assign('website',$website);
              $this->daohang();
              $this->dibu();
//              print_r($res['name']);die;
              $this->assign('name_type',str_replace('加盟','',$res['name']));
              $this->assign('parnet_id',$parent_id);
              $this->assign('id',$id);
              if(isset($where['a.invested'])){
                  $this->assign('money',$where['a.invested']);
              }
              if(isset($ename)){
                  $this->assign('addresa',$ename);
              }
              $this->assign('selcttag1',$selcttag1);
              $this->assign('selcttag2',$selcttag2);
              $this->assign('selcttag3',$selcttag3);
              $this->assign('selcttag4',$selcttag4);
              $this->assign('selcttag5',$selcttag5);
              $this->assign('num',$post['num']);
              $this->assign('address',$post['address']);
              $this->assign('youlian',$youlian);
//              print_r($infos);die;
//              if(empty($infos)){
//                  echo 123;die;
//              }else{
//                  echo 234;die;
//              }
              $this->assign('infos',$infos);
              $this->assign('data',$data);
              $this->assign('datas',$datas);
              $this->assign('sys',$sys);
              $this->assign('cate',$cate);
              $this->assign('cates',$cates);
              $this->assign('catess',$catess);
              $this->assign('cated',$cated);
              $this->assign('zixun',$zixun);
              $this->assign('zhishi',$zhishi);
              $this->assign('gushi',$gushi);
              $this->assign('dataArray',$dataArray);
//              $this->assign('lick5',$lick5);
//              $this->assign('lick1',$lick1);
//              $this->assign('lick2',$lick2);
//              $this->assign('lick3',$lick3);
//              $this->assign('array_reverse',$array_reverse);
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
              $selcttag5=isset($post['classname'])?$post['classname']:'';//页面当前分类
              if(isset($post['classname']) && ($post['classname']=='xiangmu')){
                  $selcttag1='';//行业分类
                  $selcttag2='';//行业子分类
                  $selcttag3=isset($post['num'])?$post['num']:'';//投资金额
                  $selcttag4=isset($post['address'])?$post['address']:'';//热门地区

                  $selcttag5='xiangmu';//页面当前分类
//                  $post="";
              }

              $array_reverse = "";
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


              if(isset($post['sum']) && ($post['sum']!='')){
                  $where['a.sum']=['=',$post['sum']];
              }
              $where['a.arcrank'] = 1;
              $where['a.status'] = 1;
              $sys = db('sys_enum')->where("egroup= 'nativeplace' AND (evalue MOD 500)=0")->order('disorder asc')->select();
//              一级分类
              $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->field('id,name,path')->order('list_order asc')->limit(15)->select();
              if(isset($post['classname']) && ($post['classname']!='') && ($post['classname'] != 'portal')){
                  $res = db("portal_category")->field('id,path,parent_id')->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
                  $id=$res['id'];
                  $parent_id=$res['parent_id'];
                  if(isset($parent_id) && ($parent_id == 0))
                  {
                      // $where['a.typeid'] = $id;
                      //查询出当前以及分类下所有二级分类
                      $cates =  db("portal_category")->field('path,name,id')->where("parent_id = $id and status = 1 and ishidden = 1")->select();
                      $cate = $cates->all();
                      $ids = array_column($cate,'id');
                      $where['a.typeid'] = array('in',$ids);
                      $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                      $selcttag1=$post['classname'];
                      $selcttag2='';
                      $selcttag5=$selcttag1;
                  } else{
//                      print_r($post);die;
                      $where['a.typeid'] = $id;
//                      print_r($parent_id);die;
                      $selcttag1= db("portal_category")->where("id = ".$parent_id.' and status = 1 and ishidden = 1')->value("path");
                      $selcttag2=$post['classname'];
                      $selcttag5=$selcttag2;
                      $cate =  db("portal_category")->where("parent_id = $parent_id and status = 1 and ishidden = 1")->select();
                      $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(30)->select();
                  }

              }else{
                  $youlian = db("flink")->where("typeid = 9999 and ischeck = 1")->order("dtime desc")->limit(50)->select();
                  $cates = '';
              }
              //项目列表数据
              $data = db('portal_xm a')->where($where)->order('update_time desc')->paginate(10,false,['query' => request()->param(),'page'=>$page]);

              $dataa = $data->all();
              foreach ($dataa as $key => $value) {
                  $dataa[$key]['categoryname'] = db('portal_category')->where('id = '.$value['typeid'].' and status = 1 and ishidden = 1')->value('name');
                  $nativeplaceas = db('sys_enum')->where("evalue = ".$value['nativeplace']." and py != ''")->value("ename");
                  $dataa[$key]['address'] = $nativeplaceas;
              }


              $where19['aid'] = ['in','75136,76038,77197,92156,119502'];
              $lick7 = db('portal_xm')->where($where19)->field('aid,thumbnail,title,class')->select();
              if(isset($id)){
//                  //热门推荐
                $lick1= db()->query("select aid,typeid,title,class,invested,litpic,sum,click from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by click desc limit 4 ");
                  //十大餐饮排行榜
          		$lick2= db()->query("select aid,typeid,title,class,invested from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and ( parent_id=$id  or id=$id )) order by weight desc limit 10 ");
//                  $lick2 = $lick2->all();
//                  foreach ($lick2 as $kes=>$v){
//                      $name = db('portal_category')->where('id = '.$v['typeid'])->field('parent_id')->find();
//                      $nams = db('portal_category')->where('id = '.$name['parent_id'])->field('name')->find();
//                      $lick2[$kes]['catename'] = str_replace('加盟','',$nams['name']);
//                  }
              }else{
                $lick1 = db('portal_xm')->where('status = 1 and arcrank = 1')->where("litpic != ' '")->field('aid,title,class,invested,litpic,click,sum')->order('click desc')->limit(4)->select();
                  //十大餐饮排行榜
                $lick2 = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,typeid,title,class,invested')->order('weight desc')->limit(10)->select();
                  $lick2 = $lick2->all();
                  foreach ($lick2 as $kes=>$v){
                      $name = db('portal_category')->where('id = '.$v['typeid'])->field('parent_id')->find();
                      $nams = db('portal_category')->where('id = '.$name['parent_id'])->field('name')->find();
                      $lick2[$kes]['catename'] = str_replace('加盟','',$nams['name']);
                  }
              }

              //最新资讯
              $lick3 = db('portal_post')->where('status = 1 and post_status = 1')->field('id,post_title,class,published_time')->order('id desc')->limit(10)->select();
              //热门专题
              $lick4 = db('portal_post')->where('status = 1 and post_status = 1')->field('id,post_title,class,published_time')->order('click desc')->limit(10)->select();
              //十大品牌
              $lick5 = db('portal_post')->where('status = 1 and post_status = 1')->field('id,post_title,class,published_time')->order('id desc')->limit(10,10)->select();
            //底部项目
              $arr = '2,312,8,10,5,4,7,313,9,1';
              $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,name')->order('list_order asc')->select();
              $cates = $catess->all();
              foreach($cates as $keys=>$v)
              {
                $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated, $v['id']);
                $cates[$keys]['ids'] = implode(',', $cated);
              }
              foreach ($cates as $key => $val) {
                    $wheres['typeid'] = array('in', $val['ids']);
                    $where3['status'] = 1;
                    $where3['arcrank'] = 1;
                    $val['data'] = db("portal_xm")->where($wheres)->where($where3)->field('aid,title,invested,litpic,class')->order('pubdate asc')->limit(14)->select();
                    $datas[] = $val;
                }

              //导航行业以及热门行业
              $type = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->field('id,name,path')->order('list_order asc')->limit(15)->select();
              //推荐项目品牌
              $tuijian = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,class')->order('aid desc')->limit('22')->select();

              $where18['aid'] = ['in','15867,15878,76068'];
              $dapai = db('portal_xm')->where($where18)->where('status = 1 and arcrank = 1')->field('aid,title,thumbnail,invested,typeid,sum,class')->select();


              //查询底部数据
              $website = DB('website')->where(['id' => 1])->find();
              $seo = db("portal_category")->where("path="."'$post[classname]' and status = 1 and ishidden = 1")->find();
              $py = $selcttag4;
              $nativeplace = db('sys_enum')->where("py = '$py'")->value("ename");
              $nativeplace = str_replace('市','',$nativeplace);
              if(isset($post['classname']) && ($post['classname']=='xiangmu')){
                  if($seo['parent_id'] != 0 || $selcttag3 || $nativeplace){
                      $seo_name = str_replace('加盟','',$seo['name']);
                      $seo_title = $nativeplace.$selcttag3.'万'.$seo_name."加盟项目_".$nativeplace.$selcttag3.'万'.$seo_name."加盟店排行榜_第 ".$page." 页-91创业网";
                      $seo_keywords = $nativeplace.$selcttag3.'万'.$seo_name.",".$nativeplace.$selcttag3.'万'.$seo_name."店,".$nativeplace.$selcttag3.'万'.$seo_name."排行榜,".$nativeplace.$selcttag3.'万'.$seo_name."十大品牌,".$nativeplace.$selcttag3.'万'.$seo_name."费多少钱";
                      $seo_description = "91创业网-汇集各种".$nativeplace.$selcttag3.'万'.$seo_name.",".$nativeplace.$selcttag3.'万'.$seo_name."连锁品牌,".$nativeplace.$selcttag3.'万'.$seo_name."十大品牌排行榜等".$nativeplace.$selcttag3.'万'.$seo_name."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$selcttag3.'万'.$seo_name."加盟项目, 让创业者轻松创业！";
                  }else {
                      $seo_title = "加盟项目大全_2019招商加盟项目推荐_第 " . $page . "页-91创业网";
                      $seo_keywords = "加盟,招商加盟,品牌加盟,品牌加盟网";
                      $seo_description = "91创业网-汇集各种品牌加盟项目大全,招商连锁加盟,品牌加盟十大排行榜等2019招商加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的品牌加盟项目,让创业者轻松创业";

                  }

              }else{
                  if($seo['parent_id'] != 0 || $selcttag3 || $nativeplace){
                      $seo_name = str_replace('加盟','',$seo['name']);
                      $seo_title = $nativeplace.$selcttag3.'万'.$seo_name."加盟项目_".$nativeplace.$selcttag3.'万'.$seo_name."加盟店排行榜_第 ".$page." 页-91创业网";
                      $seo_keywords = $nativeplace.$selcttag3.'万'.$seo_name.",".$nativeplace.$selcttag3.'万'.$seo_name."店,".$nativeplace.$selcttag3.'万'.$seo_name."排行榜,".$nativeplace.$selcttag3.'万'.$seo_name."十大品牌,".$nativeplace.$selcttag3.'万'.$seo_name."费多少钱";
                      $seo_description = "91创业网-汇集各种".$nativeplace.$selcttag3.'万'.$seo_name.",".$nativeplace.$selcttag3.'万'.$seo_name."连锁品牌,".$nativeplace.$selcttag3.'万'.$seo_name."十大品牌排行榜等".$nativeplace.$selcttag3.'万'.$seo_name."加盟费信息,帮助广大创业者找到适合自己的加盟项目,选择好的".$nativeplace.$selcttag3.'万'.$seo_name."加盟项目, 让创业者轻松创业！";
                  }else{
                      $seo_title = $seo['seo_title'].'_第 '.$page.' 页-91创业网';
                      $seo_keywords = $seo['seo_keywords'];
                      $seo_description = $seo['seo_description'];
                  }
              }


              if(isset($post['classname'])){
                  $catename = db('portal_category')->where("path="."'$post[classname]'")->field('name')->find();
                  $catename = str_replace('加盟','',$catename['name']);
              }else{
                  $catename = '热门';
              }
              $this->assign('dapai',$dapai);
              $this->assign('catename',$catename);
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
//              $this->assign('youlian',$youlian);
              $this->assign('data',$data);
              $this->assign('dataa',$dataa);
              $this->assign('sys',$sys);
              $this->assign('cate',$cate);
              $this->assign('type',$type);
              $this->assign('lick1',$lick1);
              $this->assign('lick2',$lick2);
              $this->assign('lick3',$lick3);
              $this->assign('lick4',$lick4);
              $this->assign('lick5',$lick5);
              $this->assign('lick7',$lick7);
              $this->assign('datas',$datas);
              $this->assign('catess',$catess);
              $this->assign('tuijian',$tuijian);
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

                    // 二级分类
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
//            $array_reverse = "";
            $data = db('portal_xm')->where("aid = $id")->find();
            $category = db('portal_category')->where('id = '.$data['typeid'])->field('name,path')->find();
            $data['category'] = $category['name'];
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
            //项目咨询
            $did = db('portal_post')->where('did = '.$id.' and status = 1 and post_status = 1')->field('id,post_title,published_time,class')->limit(5)->select();
            if(isset($did)){
                $where['a.post_title'] = [ 'like', "%".$data['title']."%"];
                $where['a.post_status'] = 1;
                $where['a.status'] = 1;
                $did = db('portal_post a')->where($where)->field('id,post_title,published_time,class')->limit(5)->select();
            }
            if(isset($did)){
                $did = db('portal_post')->where('post_status = 1 and status = 1')->field('id,post_title,published_time,class')->order('id desc')->limit(5)->select();
            }
            //项目推荐
            $txiangmu = db('portal_xm')->where('typeid = '.$data['typeid'])->field('aid,title,sum,companyname,invested,class,litpic')->limit(10)->select();
//            $array_reverse = $this->position($typeid);
//            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(0,3)->select();
//            $lick2 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(3,3)->select();
//            $lick3 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(6,3)->select();
//            $lick4 = db('portal_post')->where("parent_id = 51 and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();
//            $w = "FIND_IN_SET('a',flag)";
//            $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->limit(0,5)->select();
//            $lick8 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(2,2)->select();
//            $lick6 = db('portal_post')->where("parent_id = ".$data['typeid']." and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();


//			if(!empty($lick6)){
//              $wherew['post_title'] = ['like','%'.$data['title'].'%'];
//              $wherew['status'] = 1;
//              $wherew['post_status'] = 1;
//
//             $lick6 = db("portal_post")->where($wherew)->limit(10)->select();
//            }
            $typeid = db('portal_xm')->where("aid = $id and status = 1 and arcrank = 1")->value('typeid');
            $imgs = db("uploads")->where("arcid = ".$id)->select();
          	$imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }
//            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->limit(7)->select();
            $this->daohang();
            $this->dibu();
            $this->assign("name",$name);
			$this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);
            $this->assign('data',$data);
            $this->assign('did',$did);
            $this->assign('txiangmu',$txiangmu);
//            $this->assign('lick1',$lick1);
//            $this->assign('lick2',$lick2);
//            $this->assign('lick3',$lick3);
//            $this->assign('lick4',$lick4);
//            $this->assign('lick5',$lick5);
//            $this->assign('lick8',$lick8);
//            $this->assign('lick6',$lick6);
//            $this->assign("lick7",$lick7);
//            $this->assign('array_reverse',$array_reverse);
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
            $name = db("portal_category")->where("id = ".$typeid.' and status = 1 and ishidden = 1')->field('name,path')->find();
            $array_reverse = $this->position($typeid);
            //相关项目
            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->field('aid,title,litpic,invested,sum,class')->order('click asc')->limit(0,4)->select();
            //品牌项目
            $pinpai = db('portal_xm')->where("invested = "."'$data[invested]' and status = 1 and arcrank = 1")->field('aid,title,litpic,invested,sum,class')->order('click desc')->limit(4)->select();
            //相关分类
            $about = db('portal_category')->where('id = '.$data['typeid'])->field('parent_id')->find();
            $abouttype = db('portal_category')->where('parent_id = '.$about['parent_id'])->field('name,path')->limit(14)->select();
            $hotpinpai = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,typeid,class,invested,litpic')->order('click desc')->limit(19)->select();
            $hotpinpai = $hotpinpai->all();
            foreach ($hotpinpai as $k=>$v){
                $catetype = db('portal_category')->where('id = '.$v['typeid'])->field('name,path')->find();
                $hotpinpai[$k]['cate'] = $catetype['name'];
                $hotpinpai[$k]['class2'] = $catetype['path'];
            }
            $hotpinpai1 = array_slice($hotpinpai,0, 3);
            $hotpinpai2 = array_slice($hotpinpai,3, 16);
            //品牌排行
            $pinpaipaihang = db('portal_xm')->where("class = "."'$data[class]' and status = 1 and arcrank = 1")->field('aid,title,invested,litpic,sum,class')->order('click desc')->limit(10)->select();

           $w = "FIND_IN_SET('a',flag)";
           $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->field('aid,title,class,litpic,click,invested')->limit(0,5)->select();
            //项目关联图片表
           	$imgs = db("uploads")->where("arcid = ".$id)->select();
           	$imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }
            //项目相关新闻
            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->field('id,post_title,class,published_time')->limit(7)->select();
            if(empty($lick7)){
              $wherew['post_title'] = ['like','%'.$data['title'].'%'];
              $wherew['status'] = 1;
              $wherew['post_status'] = 1;
             $lick7 = db("portal_post")->where($wherew)->field('id,post_title,class,published_time')->limit(10)->select();
            }
            if(isset($lick7)){
                $lick7 = db("portal_post")->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10)->select();
            }
            //专题新闻
            $lick8 = db('portal_post')->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10,10)->select();
            //导航行业以及热门行业
            $type = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->field('id,name,path')->order('list_order asc')->limit(15)->select();
            //更多内容
            $neirong = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,class,title')->order('click desc')->limit(15)->select();
            //最新资讯
            $newsInfo = db('portal_post')->where('status = 1 and post_status = 1')->field('id,class,post_title,published_time')->order('id desc')->limit(6)->select();
            //问答
            $newsWenda = db('portal_post')->where('parent_id = 392 and status = 1 and post_status = 1')->field('id,class,post_title,published_time')->order('id desc')->limit(6)->select();
            //友链
            $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(50)->select();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();

            $this->assign("name",$name);
			$this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);
            $this->assign('data',$data);
            $this->assign('lick1',$lick1);
            $this->assign('pinpai',$pinpai);
            $this->assign('abouttype',$abouttype);
            $this->assign('hotpinpai1',$hotpinpai1);
            $this->assign('hotpinpai2',$hotpinpai2);
            $this->assign('pinpaipaihang',$pinpaipaihang);
            $this->assign('neirong',$neirong);
            $this->assign('newsInfo',$newsInfo);
            $this->assign('newsWenda',$newsWenda);
            $this->assign('lick5',$lick5);
            $this->assign("lick7",$lick7);
            $this->assign("lick8",$lick8);
            $this->assign('array_reverse',$array_reverse);
            $this->assign('type',$type);
            $this->assign('youlian',$youlian);
            $this->assign('id',$id);
            return $this->fetch(':article_xm');
        }
    }
    //项目详情招商海报页面
    public function article_poster($id)
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
//            $array_reverse = "";
            $data = db('portal_xm')->where("aid = $id")->find();
            $category = db('portal_category')->where('id = '.$data['typeid'])->field('name,path')->find();
            $data['category'] = $category['name'];
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
            //项目咨询
            $did = db('portal_post')->where('did = '.$id.' and status = 1 and post_status = 1')->field('id,post_title,published_time,class')->limit(5)->select();
            if(isset($did)){
                $where['a.post_title'] = [ 'like', "%".$data['title']."%"];
                $where['a.post_status'] = 1;
                $where['a.status'] = 1;
                $did = db('portal_post a')->where($where)->field('id,post_title,published_time,class')->limit(5)->select();
            }
            //项目推荐
            $txiangmu = db('portal_xm')->where('typeid = '.$data['typeid'])->field('aid,title,sum,companyname,invested,class,litpic')->limit(10)->select();
//            $array_reverse = $this->position($typeid);
//            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(0,3)->select();
//            $lick2 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(3,3)->select();
//            $lick3 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(6,3)->select();
//            $lick4 = db('portal_post')->where("parent_id = 51 and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();
//            $w = "FIND_IN_SET('a',flag)";
//            $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->limit(0,5)->select();
//            $lick8 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->order('click asc')->limit(2,2)->select();
//            $lick6 = db('portal_post')->where("parent_id = ".$data['typeid']." and status = 1 and post_status = 1")->order("published_time desc")->limit(10)->select();


//			if(!empty($lick6)){
//              $wherew['post_title'] = ['like','%'.$data['title'].'%'];
//              $wherew['status'] = 1;
//              $wherew['post_status'] = 1;
//
//             $lick6 = db("portal_post")->where($wherew)->limit(10)->select();
//            }
            $typeid = db('portal_xm')->where("aid = $id and status = 1 and arcrank = 1")->value('typeid');
            $imgs = db("uploads")->where("arcid = ".$id)->select();
            $imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }
//            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->limit(7)->select();
            $this->daohang();
            $this->dibu();
            $this->assign("name",$name);
            $this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);
            $this->assign('data',$data);
            $this->assign('did',$did);
            $this->assign('txiangmu',$txiangmu);
//            $this->assign('lick1',$lick1);
//            $this->assign('lick2',$lick2);
//            $this->assign('lick3',$lick3);
//            $this->assign('lick4',$lick4);
//            $this->assign('lick5',$lick5);
//            $this->assign('lick8',$lick8);
//            $this->assign('lick6',$lick6);
//            $this->assign("lick7",$lick7);
//            $this->assign('array_reverse',$array_reverse);
//        $this->assign('url',$url);
            return $this->fetch(':mobile/article_xm');
        }else{
            $array_reverse = "";
            $post=$this->request->param();
//            print_r($post);die;
            $data = db('portal_xm')->where("aid = $id")->find();

            //判断当前class是不是对应的
//            if($data['class'] != $post['classname'] && $post['classname'] != 'yangsheng'){
//                return $this->error1();
//            }

            if($data['nativeplace'])
            {
                $nativeplace = db('sys_enum')->where("evalue = ".$data['nativeplace']." and py != ''")->value("ename");
                $data['address'] = $nativeplace;
            }else{
                $data['address'] = $data['address'];
            }

            $typeid = $data['typeid'];
            $name = db("portal_category")->where("id = ".$typeid.' and status = 1 and ishidden = 1')->field('name,path')->find();
            $array_reverse = $this->position($typeid);
            //相关项目
            $lick1 = db('portal_xm')->where("typeid = $typeid and status = 1 and arcrank = 1")->field('aid,title,litpic,invested,sum,class')->order('click asc')->limit(0,4)->select();
            //品牌项目
            $pinpai = db('portal_xm')->where("invested = "."'$data[invested]' and status = 1 and arcrank = 1")->field('aid,title,litpic,invested,sum,class')->order('click desc')->limit(4)->select();
            //相关分类
            $about = db('portal_category')->where('id = '.$data['typeid'])->field('parent_id')->find();
            $abouttype = db('portal_category')->where('parent_id = '.$about['parent_id'])->field('name,path')->limit(14)->select();
            $hotpinpai = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,typeid,class,invested,litpic')->order('click desc')->limit(19)->select();
            $hotpinpai = $hotpinpai->all();
            foreach ($hotpinpai as $k=>$v){
                $catetype = db('portal_category')->where('id = '.$v['typeid'])->field('name,path')->find();
                $hotpinpai[$k]['cate'] = $catetype['name'];
                $hotpinpai[$k]['class2'] = $catetype['path'];
            }
            $hotpinpai1 = array_slice($hotpinpai,0, 3);
            $hotpinpai2 = array_slice($hotpinpai,3, 16);
            //品牌排行
            $pinpaipaihang = db('portal_xm')->where("class = "."'$data[class]' and status = 1 and arcrank = 1")->field('aid,title,invested,litpic,sum,class')->order('click desc')->limit(10)->select();

            $w = "FIND_IN_SET('a',flag)";
            $lick5 = db('portal_xm')->where($w)->where('status = 1 and arcrank = 1')->field('aid,title,class,litpic,click,invested')->limit(0,5)->select();
            //项目关联图片表
            $imgs = db("uploads")->where("arcid = ".$id)->select();
            $imgs_arr  = $this->get_pic_url(htmlspecialchars_decode($data['jieshao'].$data['tiaojian'].$data['liucheng']));
            $imgs_arrs = [];
            foreach ($imgs_arr as $k => $v ){
                $imgs_arrs[$k]['url'] = $v;
                $imgs_arrs[$k]['title'] = '';
            }
            //项目相关新闻
            $lick7 = db("portal_post")->where('did = '.$data['aid'].' and status = 1 and post_status = 1')->field('id,post_title,class,published_time')->limit(7)->select();
            if(empty($lick7)){
                $wherew['post_title'] = ['like','%'.$data['title'].'%'];
                $wherew['status'] = 1;
                $wherew['post_status'] = 1;
                $lick7 = db("portal_post")->where($wherew)->field('id,post_title,class,published_time')->limit(10)->select();
            }
            if(isset($lick7)){
                $lick7 = db("portal_post")->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10)->select();
            }
            //专题新闻
            $lick8 = db('portal_post')->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10,10)->select();
            //导航行业以及热门行业
            $type = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1 and id != 350")->field('id,name,path')->order('list_order asc')->limit(15)->select();
            //更多内容
            $neirong = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,class,title')->order('click desc')->limit(15)->select();
            //最新资讯
            $newsInfo = db('portal_post')->where('status = 1 and post_status = 1')->field('id,class,post_title,published_time')->order('id desc')->limit(6)->select();
            //问答
            $newsWenda = db('portal_post')->where('parent_id = 392 and status = 1 and post_status = 1')->field('id,class,post_title,published_time')->order('id desc')->limit(6)->select();
            //项目海报
            $haibao = db('uploads')->where('arcid = '.$id)->field('url')->select();


            //友链
            $youlian = db("flink")->where("typeid = ".$id." and ischeck = 1")->order("dtime desc")->limit(50)->select();
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();

            $this->assign("name",$name);
            $this->assign('imgs_arrs',$imgs_arrs);
            $this->assign("imgs",$imgs);
            $this->assign('data',$data);
            $this->assign('lick1',$lick1);
            $this->assign('pinpai',$pinpai);
            $this->assign('abouttype',$abouttype);
            $this->assign('hotpinpai1',$hotpinpai1);
            $this->assign('hotpinpai2',$hotpinpai2);
            $this->assign('pinpaipaihang',$pinpaipaihang);
            $this->assign('neirong',$neirong);
            $this->assign('newsInfo',$newsInfo);
            $this->assign('newsWenda',$newsWenda);
            $this->assign('lick5',$lick5);
            $this->assign("lick7",$lick7);
            $this->assign("lick8",$lick8);
            $this->assign('array_reverse',$array_reverse);
            $this->assign('type',$type);
            $this->assign('id',$id);
            $this->assign('haibao',$haibao);
            $this->assign('youlian',$youlian);

            return $this->fetch(':article_poster');
        }
    }

//    public function article_jiameng()
//    {
//        $path = explode('/',VIEW_PATH);
//        if(in_array('mobile',$path)){
//            $this->fetch(':mobile/article_jiameng');
//        }else{
//            $this->fetch(':article_jiameng');
//        }
//    }
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
//            $arr = '2,312,8,10,5,4,7,313,9,1,3,6,';
//            $cates = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->select();
//            $cates_arr = $cates->all();


            $cates_arr = db('portal_category')->where('parent_id = 391 and status = 1 and ishidden = 1 and id != 390 and id != 428')->field('id,path,name,mobile_thumbnail')->select();
//            echo db('portal_category')->getLastSql();die;


//            foreach($cates_arr as $k=>$v){
//                $path2 = db('portal_category')->where("name = "."'$v[name]' and parent_id = 391 and status = 1 and ishidden = 1")->value('path');
////                echo db('portal_category')->getLastSql();die;
//                $cates_arr[$k]['path2'] = $path2;
////                $cated = db('portal_category')->where(['parent_id'=>$v['id'],'status'=>1,'ishidden'=>1])->column('id');
////                array_unshift($cated,$v['id']);
////                $cates_arr[$k]['ids'] = implode(',',$cated);
//            }
//            print_r($cates_arr);die;
            $whereas['typeid'] =['in','2,1,3,4,5,7'];
            $zonghe = db("portal_xm")->where($whereas)->where('status = 1 and arcrank = 1')->field('aid,typeid,title,click,class,invested,litpic,sum,description')->order('click desc')->limit(10)->select()->toArray();
            foreach($zonghe as $k=>$v){
                $name4 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                $zonghe[$k]['category4'] = str_replace('加盟','',$name4['name']);
            }

            $where1['aid'] = ['in','75128,75136'];
            $lick1 = db('portal_xm')->where($where1)->orderRaw("field(aid,75128,75136)")->field('aid,class,title,litpic')->select();

            //餐饮排行榜
            $catess = db("portal_category")->where('id', 'in', '2')->where('status = 1 and ishidden = 1')->field('id,name,path')->select()->toArray();
            foreach($catess as $keys=>$v)
            {
                $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated, $v['id']);
                $types = implode(',', $cated);
            }
            $where2['typeid'] = array('in', $types);
            $where2['status'] = 1;
            $where2['arcrank'] = 1;
            $canyin = db('portal_xm')->where($where2)->field('aid,title,class,litpic,invested,description')->order('click desc')->limit(10)->select();

            //服装排行榜
            $catess2 = db("portal_category")->where('id', 'in', '1')->where('status = 1 and ishidden = 1')->field('id,name,path')->select()->toArray();
            foreach($catess2 as $keys=>$v)
            {
                $cated2 = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated2, $v['id']);
                $types2 = implode(',', $cated2);
            }
            $where3['typeid'] = array('in', $types2);
            $where3['status'] = 1;
            $where3['arcrank'] = 1;
            $fuzhuang = db('portal_xm')->where($where3)->field('aid,title,class,litpic,invested,description')->order('click desc')->limit(10)->select();

            //母婴排行榜
            $catess3 = db("portal_category")->where('id', 'in', '8')->where('status = 1 and ishidden = 1')->field('id,name,path')->select()->toArray();
            foreach($catess3 as $keys=>$v)
            {
                $cated3 = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated3, $v['id']);
                $types3 = implode(',', $cated3);
            }
            $where4['typeid'] = array('in', $types3);
            $where4['status'] = 1;
            $where4['arcrank'] = 1;
            $muying = db('portal_xm')->where($where4)->field('aid,title,class,litpic,invested,description')->order('click desc')->limit(10)->select();

            //教育排行榜
            $catess4 = db("portal_category")->where('id', 'in', '10')->where('status = 1 and ishidden = 1')->field('id,name,path')->select()->toArray();
            foreach($catess4 as $keys=>$v)
            {
                $cated4 = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated4, $v['id']);
                $types4 = implode(',', $cated4);
            }
            $where5['typeid'] = array('in', $types4);
            $where5['status'] = 1;
            $where5['arcrank'] = 1;
            $jiaoyu = db('portal_xm')->where($where5)->field('aid,title,class,litpic,invested,description')->order('click desc')->limit(10)->select();
            //相关文章
            $news = db('portal_post')->where('post_status = 1 and status = 1')->field('id,post_title,class,published_time')->order('published_time desc')->limit(9)->select();
            //最新入驻商家
            $newsxm = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,class,title')->order('aid desc')->limit(15)->select();

//            $recommend = db('portal_xm')->where('status = 1 and arcrank = 1')->order('click desc')->limit(21)->select();
//            $lick6 = db('portal_post')->where("parent_id = 401 and status = 1 and post_status = 1")->order("id desc")->limit(5)->select();
//            $cy = db('portal_category')->where('id = 2')->order('list_order asc')->select()->toArray();
//            foreach ($cy as $k1 => $v1) {
//              $cy[$k1]['cys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '362,153,164,171')->order('list_order asc')->select();
//            }
//            $sp = db('portal_category')->where('id = 4')->order('list_order asc')->select()->toArray();
//            foreach ($sp as $k1 => $v1) {
//              $sp[$k1]['sps'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '109,119,113,111')->order('list_order asc')->select();
//            }
//            $my = db('portal_category')->where('id = 8')->order('list_order asc')->select()->toArray();
//            foreach ($my as $k1 => $v1) {
//              $my[$k1]['mys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '177,365,156,183')->order('list_order asc')->select();
//            }
//            $jy = db('portal_category')->where('id = 10')->order('list_order asc')->select()->toArray();
//            foreach ($jy as $k1 => $v1) {
//              $jy[$k1]['jys'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '216,203,205,210')->order('list_order asc')->select();
//            }
//            $jc = db('portal_category')->where('id = 313')->order('list_order asc')->select()->toArray();
//            foreach ($jc as $k1 => $v1) {
//              $jc[$k1]['jcs'] = db('portal_category')->where('parent_id = '.$v1['id'].' and status = 1 and ishidden = 1')->where('id', 'in', '318,315,400,319')->order('list_order asc')->select();
//            }
//
			$seo = db('portal_category')->where('id = 391')->find();
          	$this->assign('seo',$seo);
//            $this->assign('cy',$cy);
//            $this->assign('sp',$sp);
//            $this->assign('my',$my);
//            $this->assign('jy',$jy);
//            $this->assign('jc',$jc);
//            $this->daohang();
//            $this->dibu();
//            $this->assign('recommend',$recommend);
//            $this->assign('lick6',$lick6);
            $this->assign('cates_arr',$cates_arr);
            $this->assign('zonghe',$zonghe);
            $this->assign('lick1',$lick1);
            $this->assign('canyin',$canyin);
            $this->assign('fuzhuang',$fuzhuang);
            $this->assign('muying',$muying);
            $this->assign('jiaoyu',$jiaoyu);
            $this->assign('news',$news);
            $this->assign('newsxm',$newsxm);
            return $this->fetch(':mobile/top');
        }else{
            //加入redis缓存
            //1 获取redis是否有数据
            // if ( true ) 去数据 并赋值
            // else 查询数据 并存储redis 传值
//            if($redis -> get('top_flg')){
//                //取出缓存
//              	$seo = json_decode($redis->get('top_seo' ),true);
////              	$gif = json_decode($redis->get('top_gif' ),true);
//                $website = json_decode($redis->get('top_website' ),true);
//                $hot = json_decode($redis->get('top_hot' ),true);
//                $youlian = json_decode($redis->get('top_youlian' ),true);
//                $zonghe = json_decode($redis->get('top_zonghe' ),true);
////                $news_hot = json_decode($redis->get('top_news_hot' ),true);
////                $news_hot2 = json_decode($redis->get('top_news_hot2' ),true);
//                $tuijian = json_decode($redis->get('top_tuijian' ),true);
////                $cate6 = json_decode($redis->get('top_cate6' ),true);
////                $cate7 = json_decode($redis->get('top_cate7' ),true);
////                $cate8 = json_decode($redis->get('top_cate8' ),true);
////                $cat1 = json_decode($redis->get('top_cat1'  ),true);
//
//                $cates_arr = json_decode($redis->get('top_data'  ),true);
//                $datas = json_decode( $redis->get('top_datas' ),true);
//                $catess = json_decode($redis->get('top_catess'),true);
//                $cate1 = json_decode($redis->get('top_cate1'),true);
//                $cate2 = json_decode($redis->get('top_cate2'),true);
//                $cate3 = json_decode($redis->get('top_cate3'),true);
//                $cate4 = json_decode($redis->get('top_cate4'),true);
//                $cate5 = json_decode($redis->get('top_cate5'),true);
//                $cate6 = json_decode($redis->get('top_cate6'),true);
//                $cate7 = json_decode($redis->get('top_cate7'),true);
//                $cate8 = json_decode($redis->get('top_cate8'),true);
//                $cate9 = json_decode($redis->get('top_cate9'),true);
//                $cate10 = json_decode($redis->get('top_cate10'),true);
//                $cate11 = json_decode($redis->get('top_cate11'),true);
//                $cate12 = json_decode($redis->get('top_cate12'),true);
//                $cate13 = json_decode($redis->get('top_cate13'),true);
//                $cate14 = json_decode($redis->get('top_cate14'),true);
//                $cate15 = json_decode($redis->get('top_cate15'),true);
//                $cate16 = json_decode($redis->get('top_cate16'),true);
////                $lick1 = json_decode($redis->get('top_lick1' ),true);
////                $lick2 = json_decode($redis->get('top_lick2' ),true);
//                $lick3 = json_decode($redis->get('top_lick3' ),true);
//                $lick4 = json_decode($redis->get('top_lick4' ),true);
//                $lick5 = json_decode($redis->get('top_lick5' ),true);
//                $lick6 = json_decode($redis->get('top_lick6' ),true);
////                $data1 = json_decode($redis->get('top_data1' ),true);
////                $data2 = json_decode($redis->get('top_data2' ),true);
////                $data3 = json_decode($redis->get('top_data3' ),true);
////                $data4 = json_decode($redis->get('top_data4' ),true);
////                $data5 = json_decode($redis->get('top_data5' ),true);
//            }else{
                //热门排行榜
                $wheresq['aid'] = ['in','75128,75136,76038,76221,77197,79114,92156,82626,119502,100944'];
                $hot = db('portal_xm')->where($wheresq)->field('aid,class,sum,litpic,address,title')->select();
                $whereas['typeid'] =['in','2,1,3,4,5,7'];
                //综合排行榜
                $zonghe = db("portal_xm")->where($whereas)->where('status = 1 and arcrank = 1')->field('aid,typeid,title,click,class,invested,litpic,sum,description')->order('click desc')->limit(10)->select()->toArray();
                foreach($zonghe as $k=>$v){
                    $name4 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $zonghe[$k]['category4'] = str_replace('加盟','',$name4['name']);
                }
                //热门文章
//                $news = db('portal_post')->where('post_status = 1 and status = 1 ')->where("thumbnail != ''")->field('thumbnail,id,post_title,published_time')->order('click desc')->limit(7)->select();
//                $news = $news->all();
//                $news_hot = array_slice($news,0,2);
//                $news_hot2 = array_slice($news,2,5);

//                $lick1 = db("portal_xm")->where('title is not null and status = 1 and arcrank = 1')->order('pubdate asc')->limit(16)->select();
//              	$whereo['typeid'] =['in','2,1,3,4,5,7'];
//                $lick2 = db("portal_xm")->where($whereo)->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
                //年度排行
                $lick3 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
                foreach($lick3 as $k=>$v){
                    $name1 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $lick3[$k]['category1'] = str_replace('加盟','',$name1['name']);
                }
                //本月排行
                $lick4 = db("portal_xm")->where('typeid', 'in', '6,362,265,57')->where('status = 1 and arcrank = 1')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
                foreach($lick4 as $k2=>$v){
                    $name2 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $lick4[$k2]['category2'] = str_replace('加盟','',$name2['name']);
                }
                //本周排行
                $lick5 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
                foreach($lick5 as $k3=>$v){
                    $name3 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $lick5[$k3]['category3'] = str_replace('加盟','',$name3['name']);
                }

//                $lick6 = db("portal_xm")->where('typeid', 'in', '2,312,8')->where('status = 1 and arcrank = 1')->order('pubdate desc')->limit(10)->select();
                $arr = '2,312,8,10,5,4,7,313,9,1,3,6,';
//                $arr = '376,378,379,380,381,382,383,384,385,386,387,388';
//                $cate = db("portal_category_copy")->where("parent_id = 391")->select();
                $cates = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->select();

//                echo '<pre/>';
//                print_r($cates);die;
//                foreach ($cates as $k=>$v){
//                    $cateyu = db('portal_category')->where("name = "."'$v[name]'and parent_id = 0 and status = 1 and ishidden = 1")->select();
//                    foreach ($cateyu as $kv=>$v1){
//                        $cated = db('portal_category')->where(['parent_id'=>$v1['id'],'status'=>1,'ishidden'=>1])->column('id');
//                        array_unshift($cated,$v1['id']);
//                        $cateyu[$kv]['ids'] = implode(',',$cated);
//
//                    }
//                    echo '<pre/>';
//                    print_r($cateyu);die;
//                }


                $cates_arr = $cates->all();
                foreach($cates_arr as $k=>$v){

                    $path2 = db('portal_category')->where("name = "."'$v[name]' and parent_id != 0 and status = 1 and ishidden = 1 and parent_id = 391")->value('path');
                    $cates_arr[$k]['path2'] = $path2;
                    $cated = db('portal_category')->where(['parent_id'=>$v['id'],'status'=>1,'ishidden'=>1,])->column('id');
                    array_unshift($cated,$v['id']);
                    $cates_arr[$k]['ids'] = implode(',',$cated);
                }
                foreach($cates_arr as $key=>$val)
                {
                    $where = [
                        'a.typeid' => ['in',$val['ids']],
                        'a.status' => 1,
                        'a.arcrank'=> 1
                    ];
                    $data = db("portal_xm")
                        ->where($where)
                        ->alias('a')
                        ->field('a.aid,a.typeid,a.title,a.class,a.sum,a.click,a.invested,a.litpic,b.name,a.description')
                        ->join('portal_category b','a.typeid = b.id')
                        ->order('click desc')
                        ->limit(10)
                        ->select();

                    $cates_arr[$key]['data'] = $data;
                }

                $arr = '2,312,8,10,5,4,7,313,9,1';
                $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,name')->order('list_order asc')->select();
                $cates = $catess->all();
                foreach($cates as $keys=>$v)
                {
                    $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                    array_unshift($cated, $v['id']);
                    $cates[$keys]['ids'] = implode(',', $cated);
                }

                foreach ($cates as $key => $val) {
                    $wheres['typeid'] = array('in', $val['ids']);
                    $wherewe['status'] = 1;
                    $wherewe['arcrank'] = 1;
                    $val['data'] = db("portal_xm")->where($wheres)->where($wherewe)->field('aid,title,invested,litpic,class')->order('pubdate asc')->limit(14)->select();
                    $datas[] = $val;
                }
                $tuijian = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,class')->order('aid desc')->limit('22')->select();

                $where1['parent_id'] = ['in','2'];
                $cate1 = db("portal_category")->where($where1)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where2['parent_id'] = ['in','734'];
                $cate2 = db("portal_category")->where($where2)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where3['parent_id'] = ['in','8'];
                $cate3 = db("portal_category")->where($where3)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where4['parent_id'] = ['in','10'];
                $cate4 = db("portal_category")->where($where4)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where5['parent_id'] = ['in','312'];
                $cate5 = db("portal_category")->where($where5)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where6['parent_id'] = ['in','5'];
                $cate6 = db("portal_category")->where($where6)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where7['parent_id'] = ['in','4'];
                $cate7 = db("portal_category")->where($where7)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where8['parent_id'] = ['in','4'];
                $cate8 = db("portal_category")->where($where8)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where9['parent_id'] = ['in','9'];
                $cate9 = db("portal_category")->where($where9)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where10['parent_id'] = ['in','339'];
                $cate10 = db("portal_category")->where($where10)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where11['parent_id'] = ['in','1'];
                $cate11 = db("portal_category")->where($where11)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where12['parent_id'] = ['in','313'];
                $cate12 = db("portal_category")->where($where12)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where13['parent_id'] = ['in','6'];
                $cate13 = db("portal_category")->where($where13)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where14['parent_id'] = ['in','3'];
                $cate14 = db("portal_category")->where($where14)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where15['parent_id'] = ['in','396'];
                $cate15 = db("portal_category")->where($where15)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                $where16['parent_id'] = ['in','420'];
                $cate16 = db("portal_category")->where($where16)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
                //排行榜中间八个项目
                $where17['aid'] = ['in','75128,75136,76038,76221,77197,79114,92156,82626'];
                $lick6 = db('portal_xm')->where($where17)->orderRaw("field(aid,75128,75136,76038,76221,77197,79114,92156,82626)")->field('aid,class,title,click,invested,litpic,sum,companyname')->select();


//                $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(7)->select();
//                foreach($catess as $key=>$v)
//                {
//                    $v['data'] = db("portal_xm")->where('typeid = '.$v['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
//                    $datas[] = $v;
//                }
//                $cate1 =  db("portal_category")->where("id = 401 and status = 1 and ishidden = 1")->find();
//                $cate2 =  db("portal_category")->where("id = 402 and status = 1 and ishidden = 1")->find();
//                $cate3 =  db("portal_category")->where("id = 403 and status = 1 and ishidden = 1")->find();
//                $cate4 =  db("portal_category")->where("id = 404 and status = 1 and ishidden = 1")->find();
//                $cate5 =  db("portal_category")->where("id = 405 and status = 1 and ishidden = 1")->find();
//                $cate6 =  db("portal_category")->where("id = 406 and status = 1 and ishidden = 1")->find();
//                $cate7 =  db("portal_category")->where("id = 408 and status = 1 and ishidden = 1")->find();
//                $cate8 =  db("portal_category")->where("id = 409 and status = 1 and ishidden = 1")->find();
//                $cat = db("portal_category")->where('id', 'in', '401,402')->where('status = 1 and ishidden = 1')->select();
//                foreach($cat as $ke=>$va)
//                {
//                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
//                    $data1[] = $va;
//                }
//                $cat = db("portal_category")->where('id', 'in', '403,404')->select();
//                foreach($cat as $ke=>$va)
//                {
//                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
//                    $data2[] = $va;
//                }
//                $cat = db("portal_category")->where('id', 'in', '405,406')->select();
//                foreach($cat as $ke=>$va)
//                {
//                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
//                    $data3[] = $va;
//                }
//                $cat = db("portal_category")->where('id', 'in', '408,409')->select();
//                foreach($cat as $ke=>$va)
//                {
//                    $va['data'] = db('portal_post')->where("parent_id = ".$va['id'].' and status = 1 and post_status = 1')->order("published_time desc")->limit(5)->select();
//                    $data4[] = $va;
//                }
//                $cat1 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->select();
//                foreach($cat1 as $key=>$val)
//                {
//                    $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();
//                    //$val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
//                  	$val['data'] = db()->query("select * from  cmf_portal_xm where status = 1 and arcrank = 1 and typeid in (select id from  cmf_portal_category  where `status` = 1 and ishidden = 1  and (parent_id=".$val['id']." or id=".$val['id']." )  ) limit 10");
//                    $data5[] = $val;
//                }
//                //查询底部数据
//                $website = DB('website')->where(['id' => 1])->find();
//				$gifs = db('advertisement')->where('is_delete = 2 and type = 1')->limit(4)->select();
//              	foreach($gifs as $k=>$v){
//                	if($v['status'] == 2){
//                      $time = time();
//                      $gif = db('advertisement')->where(" ".$time."between ".$v['timestart']." and ".$v['timeend']." and is_delete = 2 and type = 1")->limit(4)->select();
//                    }else{
//                     $gif = db('advertisement')->where('is_delete = 2 and type = 1')->limit(4)->select();
//                    }
//                }
//
              $seo = db('portal_category')->where('id = 391')->find();
              $website = DB('website')->where(['id' => 1])->find();
              $youlian = db("flink")->where("typeid = 9999 and ischeck =1 ")->order("dtime desc")->limit(50)->select();

            //加入缓存
//                $redis->set('top_flg' , 1 , 300);
////              	$redis->set('top_gif' , json_encode($gif,JSON_UNESCAPED_UNICODE) , 300);
//              	$redis->set('top_seo' , json_encode($seo,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_website' , json_encode($website,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_hot' , json_encode($hot,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_youlian' , json_encode($youlian,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_zonghe' , json_encode($zonghe,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_news_hot' , json_encode($news_hot,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_news_hot2' , json_encode($news_hot2,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_tuijian' , json_encode($tuijian,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_cate6' , json_encode($cate6,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_cate7' , json_encode($cate7,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_cate8' , json_encode($cate8,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_cat1' , json_encode($cat1,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_data' , json_encode($cates_arr,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_datas' , json_encode($datas,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_catess' , json_encode($catess,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate1' , json_encode($cate1,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate2' , json_encode($cate2,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate3' , json_encode($cate3,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate4' , json_encode($cate4,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate5' , json_encode($cate5,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate6' , json_encode($cate6,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate7' , json_encode($cate7,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate8' , json_encode($cate8,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate9' , json_encode($cate9,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate10' , json_encode($cate10,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate11' , json_encode($cate11,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate12' , json_encode($cate12,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate13' , json_encode($cate13,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate14' , json_encode($cate14,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate15' , json_encode($cate15,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_cate16' , json_encode($cate16,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_lick1' , json_encode($lick1,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_lick2' , json_encode($lick2,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_lick3' , json_encode($lick3,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_lick4' , json_encode($lick4,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_lick5' , json_encode($lick5,JSON_UNESCAPED_UNICODE) , 300);
//                $redis->set('top_lick6' , json_encode($lick6,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_data1' , json_encode($data1,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_data2' , json_encode($data2,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_data3' , json_encode($data3,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_data4' , json_encode($data4,JSON_UNESCAPED_UNICODE) , 300);
////                $redis->set('top_data5' , json_encode($data5,JSON_UNESCAPED_UNICODE) , 300);
//            }
          	$this->assign('seo',$seo);
//			$this->assign('gif',$gif);
//            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
            $this->assign('hot',$hot);
            $this->assign('youlian',$youlian);
            $this->assign('website',$website);
            $this->assign('zonghe',$zonghe);
//            $this->assign('news_hot',$news_hot);
//            $this->assign('news_hot2',$news_hot2);
            $this->assign('catess',$catess);
            $this->assign('datas',$datas);
            $this->assign('tuijian',$tuijian);
//            $this->assign('cate',$cate);
//            $this->assign('cate1',$cate1);
//            $this->assign('cate2',$cate2);
//            $this->assign('cate3',$cate3);
//            $this->assign('cate4',$cate4);
//            $this->assign('cate5',$cate5);
//            $this->assign('cate6',$cate6);
//            $this->assign('cate7',$cate7);
//            $this->assign('cate8',$cate8);
//            $this->assign('cat1',$cat1);
            $this->assign('data',$cates_arr);
            $this->assign('cate1',$cate1);
            $this->assign('cate2',$cate2);
            $this->assign('cate3',$cate3);
            $this->assign('cate4',$cate4);
            $this->assign('cate5',$cate5);
            $this->assign('cate6',$cate6);
            $this->assign('cate7',$cate7);
            $this->assign('cate8',$cate8);
            $this->assign('cate9',$cate9);
            $this->assign('cate10',$cate10);
            $this->assign('cate11',$cate11);
            $this->assign('cate12',$cate12);
            $this->assign('cate13',$cate13);
            $this->assign('cate14',$cate14);
            $this->assign('cate15',$cate15);
            $this->assign('cate16',$cate16);
//            $this->assign('datas',$datas);
//            $this->assign('catess',$catess);
//            $this->assign('lick1',$lick1);
//            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('lick4',$lick4);
            $this->assign('lick5',$lick5);
            $this->assign('lick6',$lick6);
//            $this->assign('data1',$data1);
//            $this->assign('data2',$data2);
//            $this->assign('data3',$data3);
//            $this->assign('data4',$data4);
//            $this->assign('data5',$data5);
            return $this->fetch(':top');
        }
    }
    public function list_top()
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
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $post=$this->request->param();
            $path = 'top/'.$post['id'];
            if($path == 'top/yypxjm'){
                $path = 'yingyupeixunjiameng';
            }else if($path == 'top/blspxb'){
                $path = 'yishu';
            }
            //获取该分类下的数据
            $names = db('portal_category')->where("path = '$path'")->value('name');
            $ids = db('portal_category')->where("name = '$names' and parent_id != 0")->find();
            $data = db('portal_xm')->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->field('aid,typeid,title,litpic,sum,click,address,company_address,class,invested,description')->order('weight desc')->limit(10)->select();

            //中间两个商品
            $where1['aid'] = ['in','75128,75136'];
            $lick2 = db('portal_xm')->where($where1)->orderRaw("field(aid,75128,75136)")->field('aid,class,title,litpic')->select();

            //相关文章
            $newpath = 'news'.'/'.$post['id'];
            $newsid = db('portal_category')->where("path = '$newpath'")->value('id');
            //如果为空则取最新文章
            if(empty($newsid)){
                $AboutNews = db('portal_post')->where('post_status = 1 and status = 1')->field('id,post_title,class,published_time')->order('published_time desc')->limit(9)->select();
            }else{
                $AboutNews = db('portal_post')->where('parent_id = '.$newsid.' and status = 1 and post_status = 1')->field('id,post_title,class,published_time')->order('id desc')->limit(9)->select();
            }
            //新商户入驻
            $newsxm = db('portal_xm')->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->field('aid,typeid,class,title')->order('aid desc')->limit(15)->select();

            $tdk = db('portal_category')->where("path = "."'$post[classname]' and status = 1 and ishidden = 1")->where('status = 1')->find();
//            $anking = db('portal_xm')->where("class = "."'$post[classname]'")->where('status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
//            $recommend = db('portal_xm')->where("class = "."'$post[classname]'")->where('status = 1 and arcrank = 1')->order('click desc')->limit(10,6)->select();
//            $prev = db('portal_category')->where('id = '.$tdk['parent_id'].' status = 1 and ishidden = 1')->find();
//            $this->assign('anking',$anking);
//            $this->assign('recommend',$recommend);
//            $this->assign('prev',$prev);
            $this->assign('AboutNews',$AboutNews);
            $this->assign('newsxm',$newsxm);
            $this->assign('names',str_replace('加盟','',$names));
            $this->assign('lick2',$lick2);
            $this->assign('data',$data);
            $this->assign('tdk',$tdk);
            return $this->fetch(":mobile/list_top");
        }else{
           //  $id = $this->request->param('id', 0, 'intval');
            $post =  $this->request->param();
            $path = 'top/'.$post['id'];
          if($path == 'top/yypxjm'){
              $path = 'yingyupeixunjiameng';
            }else if($path == 'top/blspxb'){
              $path = 'yishu';
            }
            $tdk = db('portal_category')->where("path = '$path'")->find();

            $where1['parent_id'] = ['in','2'];
            $cate1 = db("portal_category")->where($where1)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where2['parent_id'] = ['in','734'];
            $cate2 = db("portal_category")->where($where2)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where3['parent_id'] = ['in','8'];
            $cate3 = db("portal_category")->where($where3)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where4['parent_id'] = ['in','10'];
            $cate4 = db("portal_category")->where($where4)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where5['parent_id'] = ['in','312'];
            $cate5 = db("portal_category")->where($where5)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where6['parent_id'] = ['in','5'];
            $cate6 = db("portal_category")->where($where6)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where7['parent_id'] = ['in','4'];
            $cate7 = db("portal_category")->where($where7)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where8['parent_id'] = ['in','4'];
            $cate8 = db("portal_category")->where($where8)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where9['parent_id'] = ['in','9'];
            $cate9 = db("portal_category")->where($where9)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where10['parent_id'] = ['in','339'];
            $cate10 = db("portal_category")->where($where10)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where11['parent_id'] = ['in','1'];
            $cate11 = db("portal_category")->where($where11)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where12['parent_id'] = ['in','313'];
            $cate12 = db("portal_category")->where($where12)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where13['parent_id'] = ['in','6'];
            $cate13 = db("portal_category")->where($where13)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where14['parent_id'] = ['in','3'];
            $cate14 = db("portal_category")->where($where14)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where15['parent_id'] = ['in','396'];
            $cate15 = db("portal_category")->where($where15)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where16['parent_id'] = ['in','420'];
            $cate16 = db("portal_category")->where($where16)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();

            //获取页面底部分类以及项目
            $arr = '2,312,8,10,5,4,7,313,9,1';
            $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,name')->order('list_order asc')->select();
            $cates = $catess->all();
            foreach($cates as $keys=>$v)
            {
                $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                array_unshift($cated, $v['id']);
                $cates[$keys]['ids'] = implode(',', $cated);
            }
            foreach ($cates as $key => $val) {
                $wheres['typeid'] = array('in', $val['ids']);
                $wherewe['status'] = 1;
                $wherewe['arcrank'] = 1;
                $val['data'] = db("portal_xm")->where($wheres)->where($wherewe)->field('aid,title,invested,litpic,class')->order('pubdate asc')->limit(14)->select();
                $datas[] = $val;
            }

            $names = db('portal_category')->where("path = '$path'")->value('name');
            //排行榜相关行业
            $cateid = db('portal_category')->where("path = '$path'")->value('parent_id');
            $xiangguan = db('portal_category')->where('parent_id = '.$cateid)->field('id,path,name')->limit(14)->select();
//            // $ids = db('portal_category')->where("id = $id")->value('parent_id');
            $ids = db('portal_category')->where("name = '$names' and parent_id != 0")->find();

//            $idsa = db('portal_category')->where("name = '$names' and path = '$path'")->find();
            //获取一级分类的名称
            $onename = db('portal_category')->where("id = ".$ids['parent_id'])->value('name');
            //获取相关项目以及分类名称
            $xmid = db('portal_category')->where('parent_id = '.$ids['parent_id'])->column('id');
            $arrer['typeid'] = ['in',$xmid];
            $arrer['status'] = 1;
            $arrer['arcrank'] = 1;
            $xgxm = db('portal_xm')->where($arrer)->field('aid,typeid,title,class')->limit(20)->order('pubdate desc')->select()->toArray();
            foreach ($xgxm as $k1=>$v1){
                $info = db('portal_category')->where('id = '.$v1['typeid'])->field('name,path')->find();
                $xgxm[$k1]['name'] = str_replace('加盟','',$info['name']);
                $xgxm[$k1]['path'] = $info['path'];
            }

//            $paths = db('portal_category')->where("id = ".$ids['parent_id'])->value('path');
//            $id = db("portal_category")->where("path = '$path'")->value('id');
            $name1 = db('portal_category')->where("id = ".$ids['id'])->value('name');
//            $cate = db("portal_category")->where("parent_id = ".$idsa['parent_id'])->order('list_order asc')->select();
//            // $lick1 = db("portal_xm")->where('typeid = '.$ids['id'])->order('click asc')->limit(0,1)->select();
//            $lick1 = db("portal_xm")->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(0,1)->select();
            $data = db('portal_xm')->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->field('aid,typeid,title,litpic,sum,click,address,company_address,class,invested')->order('weight desc')->paginate(10,false,['page'=>$page]);
            $lick12 = $data->all();
            foreach ($lick12 as $k=>$v){
                $name = db('portal_category')->where('id = '.$v['typeid'])->field('name,path')->find();
                $lick12[$k]['category1'] = str_replace('加盟','',$name['name']);
                $lick12[$k]['path1'] = $name['path'];
            }

            $cates = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,parent_id,path,name')->order('list_order asc')->select();
            $cates_arr = $cates->all();
            foreach($cates_arr as $k=>$v){
                $path2 = db('portal_category')->where("name = "."'$v[name]' and parent_id != 0 and status = 1 and ishidden = 1")->value('path');
                $cates_arr[$k]['path2'] = $path2;
            }
            $lick7 = db("portal_post")->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10)->select();
            $lick8 = db('portal_post')->where('status = 1 and post_status = 1 and parent_id = 401')->field('id,post_title,class,published_time')->limit(10,10)->select();

            $tuijian = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,class')->order('aid desc')->limit('22')->select();

//            $lick2 = db("portal_xm")->where('typeid = '.$ids['id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(1,9)->select();
//            $lick3 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(0,1)->select();
//            $lick4 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(1,2)->select();
//            $lick5 = db("portal_xm")->where('typeid = '.$ids['parent_id'].' and status = 1 and arcrank = 1')->order('weight desc')->limit(3,7)->select();
//            $cate1 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(10)->select();
//            foreach($cate1 as $key=>$val)
//            {
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(4)->select();
//                $data[] = $val;
//            }
//            $data1 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(1,5)->select();
//            $data2 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(10,5)->select();
//            $data3 = db("portal_post")->where("parent_id = 399 and status = 1 and post_status = 1")->limit(15,5)->select();
//            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();
//            foreach($cat2 as $key=>$val)
//            {
//                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
//                $data5[] = $val;
//            }
            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
            $this->assign('cate1',$cate1);
            $this->assign('cate2',$cate2);
            $this->assign('cate3',$cate3);
            $this->assign('cate4',$cate4);
            $this->assign('cate5',$cate5);
            $this->assign('cate6',$cate6);
            $this->assign('cate7',$cate7);
            $this->assign('cate8',$cate8);
            $this->assign('cate9',$cate9);
            $this->assign('cate10',$cate10);
            $this->assign('cate11',$cate11);
            $this->assign('cate12',$cate12);
            $this->assign('cate13',$cate13);
            $this->assign('cate14',$cate14);
            $this->assign('cate15',$cate15);
            $this->assign('cate16',$cate16);
//            $this->assign('id',$id);
//            $this->assign('cate',$cate);
            $this->assign('onename',str_replace('加盟','',$onename));
//            $this->assign('path',$path);
            $this->assign('name1',$name1);
            $this->assign('data',$data);
            $this->assign('lick12',$lick12);
            $this->assign('catess',$catess);
            $this->assign('datas',$datas);
            $this->assign('cates_arr',$cates_arr);
            $this->assign('xiangguan',$xiangguan);
            $this->assign('xgxm',$xgxm);
            $this->assign('lick7',$lick7);
            $this->assign('lick8',$lick8);
            $this->assign('tuijian',$tuijian);
//            $this->assign('lick2',$lick2);
//            $this->assign('lick3',$lick3);
//            $this->assign('lick4',$lick4);
//            $this->assign('lick5',$lick5);
//            $this->assign('data',$data);
//            $this->assign('data1',$data1);
//            $this->assign('data2',$data2);
//            $this->assign('data3',$data3);
//            $this->assign('cat2',$cat2);
//            $this->assign('data5',$data5);
            $this->assign('tdk',$tdk);
            return $this->fetch(":list_top");
        }
    }
    public function index_top()
    {
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
           $post =  $this->request->param();
            $path = 'top/'.$post['id'];
            //根据路径获取名称
            $name = db("portal_category")->where("path = '$path'")->value('name');

            $id = db("portal_category")->where("path = '$path'")->value('id');
            $cates =  db("portal_category")->where('parent_id = '.$id .' and id != 503 and id != 662 and id != 645 and id != 725 and id != 719')->field('name,path,mobile_thumbnail')->order('list_order asc')->limit(30)->select()->toArray();

            //获取二级分类以及项目
            $cates2 =  db("portal_category")->where('parent_id = '.$id .' and id != 503 and id != 662 and id != 645 and id != 725 and id != 719')->field('name,path,mobile_thumbnail')->order('list_order asc')->limit(20)->select()->toArray();
            foreach($cates2 as $key=>$val)
            {
                $names = $val['name'];
                $pathd = $val['path'];
                $idd = db('portal_category')->where("name = '$names' and path != '$pathd'")->field('id,parent_id,name,path')->find();
                $val['data'] = db("portal_xm")->where('typeid = '.$idd['id'].' and status = 1 and arcrank = 1')->field('aid,class,litpic,click,sum,invested,title,typeid,description')->order('click desc')->limit(10)->select()->toArray();
                $data[] = $val;
            }

            //加盟项目TOP10
            $ids = db('portal_category')->where("name = '$name' and parent_id = 0")->find();
            $ids['idas'] = db('portal_category')->where('parent_id = '.$ids['id'].' and status = 1 and ishidden = 1')->column('id');
            $where['typeid'] =['in',$ids['idas']];
            $where['status'] = 1;
            $where['arcrank'] = 1;
            $lick1 = db("portal_xm")->where($where)->order('click desc')->field('aid,typeid,title,invested,litpic,description,class')->limit(10)->select();

            //中间两个商品
            $where1['aid'] = ['in','75128,75136'];
            $lick2 = db('portal_xm')->where($where1)->orderRaw("field(aid,75128,75136)")->field('aid,class,title,litpic')->select();

            //相关文章
            $newpath = 'news'.'/'.$post['id'];
            $newsid = db('portal_category')->where("path = '$newpath'")->value('id');

            //如果为空则取最新文章
            if(empty($newsid)){
                $AboutNews = db('portal_post')->where('post_status = 1 and status = 1')->field('id,post_title,class,published_time')->order('published_time desc')->limit(9)->select();
            }else{
                $AboutNews = db('portal_post')->where('parent_id = '.$newsid.' and status = 1 and post_status = 1')->field('id,post_title,class,published_time')->order('id desc')->limit(9)->select();
            }

            //新品入驻商家
            $newsxm = db("portal_xm")->where($where)->order('aid desc')->field('aid,typeid,title,invested,litpic,description,class')->limit(15)->select();
            if(empty($newsxm)){
                $newsxm = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,class,title')->order('aid desc')->limit(15)->select();
            }



                //获取上级id = 0 的同名分类
//            $ids = db('portal_category')->where("name = '$name' and parent_id = 0")->find();
//            $idss = db('portal_category')->where('parent_id = '.$ids['id'].' and status = 1 and ishidden = 1')->field('name')->select();
//            print_r($idss);die;
//            $path = $post['id'];
//            $arr = '2,312,8,10,5,4,7,313,9,1,3,339,6,396,420';
//            $name = db("portal_category")->where("path = '$path' and status = 1 and ishidden = 1")->value('name');
//            $id = db("portal_category")->where("path = '$path' and status = 1 and ishidden = 1")->value('id');
//            $cate = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(15)->select();
//            $lick1 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(10)->select();
//            $lick2 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(0,8)->select();
//            $lick3 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(8,8)->select();
//            $lick4 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(16,8)->select();
//            $lick5 = db("portal_xm")->where('typeid = '.$id.' and status = 1 and arcrank = 1')->order('click asc')->limit(24,8)->select();
//            $cates =  db("portal_category")->where('parent_id = '.$id.' and status = 1 and ishidden = 1')->order('list_order asc')->limit(12)->select();
//            foreach($cates as $key=>$val)
//            {
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('click desc')->limit(10)->select();
//                $data[] = $val;
//            }
//            $catess =  db("portal_category")->where('parent_id = '.$id.' and status = 1 and ishidden = 1')->order('list_order asc')->limit(10)->select();
//            foreach($cates as $key=>$val)
//            {
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('click asc')->limit(8)->select();
//                $data1[] = $val;
//            }
//            $cat1 = db("portal_category")->where('id', 'in', '401,402,403,404')->where('status = 1 and ishidden = 1')->select();
//            foreach($cat1 as $key=>$val)
//            {
//                $val['data'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->limit(1)->select();
//                $val['data1'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->limit(1,10)->select();
//                $data2[] = $val;
//            }
//            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->select();
//            foreach($cat2 as $key=>$val)
//            {
//                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
//                $data5[] = $val;
//            }
//
//
//            //查询底部数据
//            $website = DB('website')->where(['id' => 1])->find();
            $seo = db('portal_category')->where("path = '$path'")->find();
//            $this->assign('website',$website);
            $this->assign('seo',$seo);
//            $this->daohang();
//            $this->dibu();
//            $this->assign('cate',$cate);
            $this->assign('name',str_replace('加盟','',$name));
            $this->assign('cates',$cates);
            $this->assign('lick1',$lick1);
            $this->assign('data0',$data[0]);
            $this->assign('data1',$data[1]);
            $this->assign('data2',$data[2]);
            $this->assign('data3',$data[3]);
            $this->assign('aboutnews',$AboutNews);
            $this->assign('newsxm',$newsxm);
            $this->assign('lick2',$lick2);
//            $this->assign('lick3',$lick3);
//            $this->assign('lick4',$lick4);
//            $this->assign('lick5',$lick5);
//            $this->assign('data',$data);
//            $this->assign('data1',$data1);
//            $this->assign('data2',$data2);
//            $this->assign('cat2',$cat2);
//            $this->assign('data5',$data5);
            return $this->fetch(":mobile/index_top");
        }else{

            // $id =  $this->request->param('id', 0, 'intval');
            $post =  $this->request->param();
            $path = 'top/'.$post['id'];

            $where1['parent_id'] = ['in','2'];
            $cate1 = db("portal_category")->where($where1)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where2['parent_id'] = ['in','734'];
            $cate2 = db("portal_category")->where($where2)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where3['parent_id'] = ['in','8'];
            $cate3 = db("portal_category")->where($where3)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where4['parent_id'] = ['in','10'];
            $cate4 = db("portal_category")->where($where4)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where5['parent_id'] = ['in','312'];
            $cate5 = db("portal_category")->where($where5)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where6['parent_id'] = ['in','5'];
            $cate6 = db("portal_category")->where($where6)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where7['parent_id'] = ['in','4'];
            $cate7 = db("portal_category")->where($where7)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where8['parent_id'] = ['in','4'];
            $cate8 = db("portal_category")->where($where8)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where9['parent_id'] = ['in','9'];
            $cate9 = db("portal_category")->where($where9)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where10['parent_id'] = ['in','339'];
            $cate10 = db("portal_category")->where($where10)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where11['parent_id'] = ['in','1'];
            $cate11 = db("portal_category")->where($where11)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where12['parent_id'] = ['in','313'];
            $cate12 = db("portal_category")->where($where12)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where13['parent_id'] = ['in','6'];
            $cate13 = db("portal_category")->where($where13)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where14['parent_id'] = ['in','3'];
            $cate14 = db("portal_category")->where($where14)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where15['parent_id'] = ['in','396'];
            $cate15 = db("portal_category")->where($where15)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            $where16['parent_id'] = ['in','420'];
            $cate16 = db("portal_category")->where($where16)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
            //排行榜中间八个项目
            $where17['aid'] = ['in','75128,75136,76038,76221,77197,79114,92156,82626'];
            $lick6 = db('portal_xm')->where($where17)->orderRaw("field(aid,75128,75136,76038,76221,77197,79114,92156,82626)")->field('aid,class,title,click,invested,litpic,sum,companyname')->select();

            //根据路径获取名称
            $name = db("portal_category")->where("path = '$path'")->value('name');
            //获取上级id = 0 的同名分类
            $ids = db('portal_category')->where("name = '$name' and parent_id = 0")->find();
            $ids['idas'] = db('portal_category')->where('parent_id = '.$ids['id'].' and status = 1 and ishidden = 1')->column('id');

            //年度排行
            $wh['typeid'] = ['in',$ids['idas']];
            $wh['status'] = 1;
            $wh['arcrank'] = 1;
//            $lick3 = db("portal_xm")->where($wh)->whereTime('pubdate','year')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
            $lick3 = db("portal_xm")->where($wh)->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10,10)->select()->toArray();
            foreach($lick3 as $k=>$v){
                $name1 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                $lick3[$k]['category1'] = str_replace('加盟','',$name1['name']);
            }
            //本月排行
//            $lick4 = db("portal_xm")->where($wh)->whereTime('pubdate','month')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
            $lick4 = db("portal_xm")->where($wh)->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(13,10)->select()->toArray();
            foreach($lick4 as $k2=>$v){
                $name2 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                $lick4[$k2]['category2'] = str_replace('加盟','',$name2['name']);
            }
            //本周排行
//            $lick5 = db("portal_xm")->where($wh)->whereTime('pubdate','week')->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(10)->select()->toArray();
            $lick5 = db("portal_xm")->where($wh)->field('aid,typeid,title,class,click,sum,invested,description,litpic')->order('click desc')->limit(16,10)->select()->toArray();
            foreach($lick5 as $k3=>$v){
                $name3 = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                $lick5[$k3]['category3'] = str_replace('加盟','',$name3['name']);
            }

            // $arr = '2,312,8,10,5,4,7,313,9,1,3,339,6,396,420';
//            $cate = db("portal_category")->where("parent_id = 391")->select();
//            $cy = db('portal_category')->where('id = 376')->value('path');
//            $gx = db('portal_category')->where('id = 379')->value('path');
//            $jy = db('portal_category')->where('id = 386')->value('path');
//            $sp = db('portal_category')->where('id = 380')->value('path');
//            $zb = db('portal_category')->where('id = 381')->value('path');


			$where['typeid'] =['in',$ids['idas']];
          	$where['status'] = 1;
          	$where['arcrank'] = 1;
            $lick1 = db("portal_xm")->where($where)->order('click desc')->field('aid,typeid,title,invested,litpic,click,sum,description,class')->limit(10)->select()->toArray();
            foreach ($lick1 as $k=>$v){
                $category4 = db('portal_category')->where('id = '.$v['typeid'])->value('name');
                $lick1[$k]['category4'] = $category4;
            }
//            $lick2 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(0,8)->select();
//            $lick3 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(8,8)->select();
//            $lick4 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(16,8)->select();
//            $lick5 = db("portal_category")->where('parent_id = '.$id)->order('list_order asc')->limit(24,8)->select();

            //获取二级分类以及项目
            $id = db("portal_category")->where("path = '$path'")->value('id');
            $cates =  db("portal_category")->where('parent_id = '.$id .' and id != 503 and id != 662 and id != 645 and id != 725 and id != 719')->order('list_order asc')->limit(12)->select()->toArray();
            foreach($cates as $key=>$val)
            {
              	$names = $val['name'];
              	$path = $val['path'];
              	$idd = db('portal_category')->where("name = '$names' and path != '$path'")->find();
                $val['data'] = db("portal_xm")->where('typeid = '.$idd['id'].' and status = 1 and arcrank = 1')->field('aid,class,litpic,click,sum,invested,title,typeid,description')->order('click desc')->limit(10)->select()->toArray();
                if(count($val['data'])<10){
                    unset($val);
                }else{
                    foreach ($val['data'] as $k1=>$v1){
                        $names = db('portal_category')->where('id = '.$v1['typeid'])->value('name');
                        $val['data'][$k1]['name'] = $names;
                    }
                    $data[] = $val;
                }

            }
//            $catess =  db("portal_category_copy")->where('parent_id = '.$ids['id'])->order('list_order asc')->limit(10)->select();
            $cates2 = array_slice($cates,0,10);
            foreach($cates2 as $key=>$val)
            {
              	$namea = $val['name'];
              	$path = $val['path'];
              	$idd = db('portal_category')->where("name = '$namea' and path != '$path'")->find();
                $val['data'] = db("portal_xm")->where('typeid = '.$idd['id'].' and status = 1 and arcrank = 1')->order('click desc')->limit(14)->select();
                $data1[] = $val;
            }
//            $cat1 = db("portal_category")->where('id', 'in', '401,402,403,404')->where('status = 1 and ishidden = 1')->select();
//            foreach($cat1 as $key=>$val)
//            {
//                $val['data'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->order('id desc')->limit(1)->select();
//              	$val['data'] = $val['data']->all();
//              	foreach($val['data'] as $k=>$v){
//                	$val['data'][$k]['classs'] = substr($v['class'],0,4);
//                }
//                $val['data1'] = db("portal_post")->where('parent_id = '.$val['id'].' and status = 1 and post_status = 1')->order('id desc')->limit(1,10)->select();
//              	$val['data1'] = $val['data1']->all();
//				foreach($val['data1'] as $ks=>$vs){
//                	$val['data1'][$ks]['classs'] = substr($vs['class'],0,4);
//                }
//                $data2[] = $val;
//            }
//            $cat2 = db("portal_category")->where('id', 'in', '2,1,3,339,4,5,6,7,8,9,10,312')->where('status = 1 and ishidden = 1')->select();
//            foreach($cat2 as $key=>$val)
//            {
//                $val['son'] = db("portal_category")->where('parent_id', 'in', $val['id'])->where('status = 1 and ishidden = 1')->limit(12)->select();
//                $val['data'] = db("portal_xm")->where('typeid = '.$val['id'].' and status = 1 and arcrank = 1')->order('pubdate asc')->limit(10)->select();
//                $data5[] = $val;
            $tuijian = db('portal_xm')->where('status = 1 and arcrank = 1')->field('aid,title,class')->order('aid desc')->limit('22')->select();
//            }
           	$patha = 'top/'.$post['id'];
          	$seo = db('portal_category')->where("path = '$patha'")->find();

            //查询底部数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
			$this->assign('seo',$seo);
            $this->assign('cate1',$cate1);
            $this->assign('cate2',$cate2);
            $this->assign('cate3',$cate3);
            $this->assign('cate4',$cate4);
            $this->assign('cate5',$cate5);
            $this->assign('cate6',$cate6);
            $this->assign('cate7',$cate7);
            $this->assign('cate8',$cate8);
            $this->assign('cate9',$cate9);
            $this->assign('cate10',$cate10);
            $this->assign('cate11',$cate11);
            $this->assign('cate12',$cate12);
            $this->assign('cate13',$cate13);
            $this->assign('cate14',$cate14);
            $this->assign('cate15',$cate15);
            $this->assign('cate16',$cate16);
            $this->assign('lick6',$lick6);
            $this->assign('ids',$ids);
            $this->assign('tuijian',$tuijian);

//            $this->assign('cy',$cy);
//            $this->assign('gx',$gx);
//            $this->assign('jy',$jy);
//            $this->assign('sp',$sp);
//            $this->assign('zb',$zb);
//            $this->assign('cate',$cate);
            $this->assign('name',$name);
            $this->assign('lick1',$lick1);
//            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('lick4',$lick4);
            $this->assign('lick5',$lick5);
//            print_r($data);die;
            $this->assign('data',$data);

            $this->assign('data1',$data1);
//            $this->assign('data2',$data2);
//            $this->assign('cat2',$cat2);
//            $this->assign('data5',$data5);
            return $this->fetch(":index_top");
        }
    }

    //服务条款
    public function explain(){
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)) {
            return $this->fetch(':mobile/explain');
        }
    }


    //nav页面
    public function nav(){
        $path = explode('/',VIEW_PATH);
        if(in_array('mobile',$path)){
            $arr = '2,1,4,5,7,10,3,6,8,9,312,313,396,420';
            $catess = db("portal_category")->where('id', 'in', $arr)->where('status = 1 and ishidden = 1')->field('id,name,path')->order('list_order asc')->select();

              $cated = db('portal_category')->where(['parent_id' => 2,'ishidden' => 1,'status' => 1])->field('id,path,name,mobile_thumbnail')->select();
            $this->assign('catess',$catess);
            $this->assign('cated',$cated);
            return $this->fetch(':mobile/nav');
        }else{
            $where24['id'] = ['in','1,2,3,4,5,6,7,8,9,10'];
            $categ = db("portal_category")->where($where24)->where("ishidden =1 and status =1")->orderRaw("field(id,1,2,3,4,5,6,7,8,9,10)")->select();

            foreach ($categ as $key => $val) {
                $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->field('id,name,path')->limit(10)->select();
                $class1[] = $val;
            }
            $website = DB('website')->where(['id' => 1])->find();
            $this->assign('website',$website);
            $this->assign('class1',$class1);
            $this->daohang();
            return $this->fetch(":sitemap");
//            return $this->fetch(":nav");
        }
    }

    //网站地图
//    public function sitemap(){
//        $where['id'] = ['in','1,2,3,4,5,6,7,8,9,10'];
//        $cate = db('portal_category')->where($where)->field('id,path,name')->select();
//        $cate = $cate->all();
//        foreach($cate as $k=>$v){
//            $cated = db('portal_category')->where(['parent_id'=>$v['id'],'status'=>1,'ishidden'=>1])->column('id');
//            array_unshift($cated,$v['id']);
//            $cates_arr[$k]['ids'] = implode(',',$cated);
//            $cates_arr[$k]['name'] = $v['name'];
//            $cates_arr[$k]['path'] = $v['path'];
//        }
//        foreach($cates_arr as $key=>$val)
//        {
//            $wheres['typeid'] = array('in',$val['ids']);
//            $val['data'] = db("portal_xm")->where($wheres)->where('status = 1 and arcrank = 1')->field('aid,class,title')->order('click desc')->limit(10)->select();
//            $data[] = array_filter($val);
//        }
//        $website = DB('website')->where(['id' => 1])->find();
//        $this->assign('website',$website);
//        $this->assign('cate',$data);
//        $this->daohang();
//        return $this->fetch(":sitemap");
//    }
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
        $where1['id'] = ['in','2,5,8,396'];
        $cates1 = db("portal_category")->where($where1)->where("ishidden = 1 and status =1 ")->select();
        foreach($cates1 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(40)->select();
            $c1[] = $val;
        }
        $where2['id'] = ['in','10,312,4,7,9,313'];
        $cates2 = db("portal_category")->where($where2)->where('status =1 and ishidden = 1')->select();

        foreach($cates2 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(6)->select();
            $c2[] = $val;
        }
        $where3['id'] = ['in','420,1,3,6,339'];
        $cates3 = db("portal_category")->where($where3)->select();
        foreach($cates3 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(6)->select();
            $c3[] = $val;
        }

        $this->assign("cates1",$c1);
        $this->assign("cates2",$c2);
        $this->assign("cates3",$c3);
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
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/guanyuwomen');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":guanyuwomen");
            }
        }
        public function lianxiwomen(){
            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/lianxiwomen');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":lianxiwomen");
            }
        }
        public function mianzeshengming(){

            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/mianzeshengming');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":mianzeshengming");
            }
        }
        public function falvguwen(){
            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/mianzeshengming');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":falvguwen");
            }
        }
        public function youqinglianjie(){
            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/mianzeshengming');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":youqinglianjie");
            }
        }
        public function tousushanchu(){
            $path = explode('/',VIEW_PATH);
            if(in_array('mobile',$path)){
                return $this->fetch(':mobile/tousushanchu');
            }else {
                $this->zuoce();
                $this->dibu();
                $this->daohang();
                return $this->fetch(":tousushanchu");
            }
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

    public function zuoce(){
        //左侧导航
        $where1['parent_id'] = ['in','2'];
        $cate1 = db("portal_category")->where($where1)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where2['parent_id'] = ['in','734'];
        $cate2 = db("portal_category")->where($where2)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where3['parent_id'] = ['in','8'];
        $cate3 = db("portal_category")->where($where3)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where4['parent_id'] = ['in','10'];
        $cate4 = db("portal_category")->where($where4)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where5['parent_id'] = ['in','312'];
        $cate5 = db("portal_category")->where($where5)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where6['parent_id'] = ['in','5'];
        $cate6 = db("portal_category")->where($where6)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where7['parent_id'] = ['in','4'];
        $cate7 = db("portal_category")->where($where7)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where8['parent_id'] = ['in','4'];
        $cate8 = db("portal_category")->where($where8)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where9['parent_id'] = ['in','9'];
        $cate9 = db("portal_category")->where($where9)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where10['parent_id'] = ['in','339'];
        $cate10 = db("portal_category")->where($where10)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where11['parent_id'] = ['in','1'];
        $cate11 = db("portal_category")->where($where11)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where12['parent_id'] = ['in','313'];
        $cate12 = db("portal_category")->where($where12)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where13['parent_id'] = ['in','6'];
        $cate13 = db("portal_category")->where($where13)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where14['parent_id'] = ['in','3'];
        $cate14 = db("portal_category")->where($where14)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where15['parent_id'] = ['in','396'];
        $cate15 = db("portal_category")->where($where15)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $where16['parent_id'] = ['in','420'];
        $cate16 = db("portal_category")->where($where16)->where('status = 1 and ishidden = 1')->field('name,path')->order('list_order asc')->limit(20)->select();
        $this->assign('cate1',$cate1);
        $this->assign('cate2',$cate2);
        $this->assign('cate3',$cate3);
        $this->assign('cate4',$cate4);
        $this->assign('cate5',$cate5);
        $this->assign('cate6',$cate6);
        $this->assign('cate7',$cate7);
        $this->assign('cate8',$cate8);
        $this->assign('cate9',$cate9);
        $this->assign('cate10',$cate10);
        $this->assign('cate11',$cate11);
        $this->assign('cate12',$cate12);
        $this->assign('cate13',$cate13);
        $this->assign('cate14',$cate14);
        $this->assign('cate15',$cate15);
        $this->assign('cate16',$cate16);
    }
}