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
use think\cache\driver\Redis;

class IndexController extends HomeBaseController
{
    public function index()
    {
        if (\think\Request::instance()->isMobile()) {
            set_time_limit(0);
            //左侧二级导航
            $cate = db("portal_category")->where("parent_id = 0 and status = 1 and ishidden = 1")->order('list_order asc')->limit(12)->select();
            $lick2 = db('portal_xm')->where("typeid",'in','2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('status = 1 and arcrank = 1')->order('pubdate desc')->limit(6)->select();
            $youlian = db("flink")->where("typeid = 9999 and ischeck = 1")->order("dtime desc")->limit(50)->select();

            //餐饮
            $cates1 = db('portal_xm')->where("typeid = 2 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //服饰
            $cates2 = db('portal_xm')->where("typeid = 1 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //干洗
            $cates3 = db('portal_xm')->where("typeid = 3 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //鞋业
            $cates4 = db('portal_xm')->where("typeid = 339 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //饰品
            $cates5 = db('portal_xm')->where("typeid = 119 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //珠宝
            $cates6 = db('portal_xm')->where("typeid = 5 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //建材
            $cates7 = db('portal_xm')->where("typeid = 314 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //家居
            $cates8 = db('portal_xm')->where("typeid = 7 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //母婴
            $cates9 = db('portal_xm')->where("typeid = 8 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //女性
            $cates10 = db('portal_xm')->where("typeid = 9 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //教育
            $cates11 = db('portal_xm')->where("typeid = 10 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //酒水
            $cates12 = db('portal_xm')->where("typeid = 326 and arcrank = 1 and status = 1")->order("sortrank desc")->limit(6)->select();
            //创业头条
            $headlines = db('portal_post')->where("class = 'zixuncaiji' and post_status = 1 and status = 1")->order("published_time desc")->limit(6)->select();
            //餐饮咨询
            $canyin_new = db('portal_post')->where("class = 'wenda' and post_status=1 and status=1")->order("id desc")->limit(6)->select();
            //行业咨询
            $hangye_new = db('portal_post')->where("class = 'news/cy' and post_status=1 and status=1")->order('published_time desc')->limit(10)->select();
            $hangye_new = $hangye_new->all();
            foreach ($hangye_new as $key => $val) {
                $hangye_new[$key]['classs'] = substr($val['class'],0,4);
            }
            //加盟行业导航
            $data = db('portal_xm')->where("arcrank = 1 and status = 1")->paginate(21);
            //查询数据
            $website = DB('website')->where(['id' => 1])->find();
            $this->daohang();
            $this->dibu();
            $this->assign('cate',$cate);
            $this->assign('cates1',$cates1);
            $this->assign('lick2',$lick2);
            $this->assign('youlian',$youlian);
            $this->assign('data',$data);
            $this->assign('headlines',$headlines);
            $this->assign('cates1',$cates1);
            $this->assign('cates2',$cates2);
            $this->assign('cates3',$cates3);
            $this->assign('cates4',$cates4);
            $this->assign('cates5',$cates5);
            $this->assign('cates6',$cates6);
            $this->assign('cates7',$cates7);
            $this->assign('cates8',$cates8);
            $this->assign('cates9',$cates9);
            $this->assign('cates10',$cates10);
            $this->assign('cates11',$cates11);
            $this->assign('cates12',$cates12);
            $this->assign('canyin_new',$canyin_new);
            $this->assign('hangye_new',$hangye_new);
            $this->assign('website',$website);

            return $this->fetch(':mobile/index');
        } else {
          
          
            //$t1 = microtime(true);
            set_time_limit(0);
            //加入redis缓存
            //1 获取redis是否有数据
            // if ( true ) 去数据 并赋值
            // else 查询数据 并存储redis 传值
            $redis = new Redis();
            if($redis -> get('index_flg')){
                //取出缓存
                $cate = json_decode($redis->get('index_cate' ),true);
                $lick1 = json_decode($redis->get('index_lick1'),true);
                $lick2 = json_decode($redis->get('index_lick2'),true);
                $lick3 = json_decode($redis->get('index_lick3'),true);
                $lick4 = json_decode($redis->get('index_lick4'),true);
                $lick5 = json_decode($redis->get('index_lick5'),true);
                $lick6 = json_decode($redis->get('index_lick6'),true);
                $lick7 = json_decode($redis->get('index_lick7'),true);
                $lick8 = json_decode($redis->get('index_lick8'),true);
                $lick9 = json_decode($redis->get('index_lick9'),true);
                $lick13 = json_decode($redis->get('index_lick13' ),true);
                $lick14 = json_decode($redis->get('index_lick14' ),true);
                $lick15 = json_decode($redis->get('index_lick15' ),true);
                $tz1 = json_decode($redis->get('index_tz1'),true);
                $tz2 = json_decode($redis->get('index_tz2'),true);
                $tz3 = json_decode($redis->get('index_tz3'),true);
                $tz4 = json_decode($redis->get('index_tz4'),true);
                $tz5 = json_decode($redis->get('index_tz5'),true);
                $tz6 = json_decode($redis->get('index_tz6' ),true);
                $cates1 = json_decode($redis->get('index_cates1'),true);
                $data1 = json_decode($redis->get('index_data1'),true);
                $data2 = json_decode($redis->get('index_data2'),true);
                $data3 = json_decode($redis->get('index_data3'),true);
                $class1 = json_decode($redis->get('index_class1'),true);
                $class2 = json_decode($redis->get('index_class2'),true);
                $class3 = json_decode($redis->get('index_class3'),true);
                $youlian = json_decode($redis->get('index_youlian'),true);
                $website = json_decode($redis->get('index_website'),true);

            }else {
                //左侧二级导航
              	$cate = db("portal_category")->where("parent_id = 0")->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(15)->select();
                $a = json_encode($cate);
                $cates = array_slice(json_decode($a, true), 0, 12);
                foreach ($cates as $k => $v) {
                    $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
                    array_unshift($cated, $v['id']);
                    $cates[$k]['ids'] = implode(',', $cated);
                }
                //品牌上榜
                $infos = db('portal_xm')->where("typeid", 'in', '2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('arcrank = 1 ')->where('status = 1');
                $lick1 = $infos->order('pubdate asc')->limit(16, 5)->select();
                $lick3 = $infos->order('pubdate desc')->limit(10, 16)->select();
                $lick2 = db('portal_xm')->where("arcrank = 1 and status = 1 and thumbnail != '' and find_in_set('h',flag)")->order('update_time desc')->limit(4)->select();

                $lick4 = db('portal_post')->where('post_status=1 and status=1 ')->order('published_time desc')->limit(10,8)->select();
                $lick4 = $lick4->all();
                foreach ($lick4 as $kw => $vw) {
                    $lick4[$kw]['classs'] = substr($vw['class'],0,4);
                }
                $infoa = db('portal_xm')->where("typeid", 'in', '2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('arcrank =1 and status = 1')->order('aid asc')->limit(117)->select();
                $a = json_encode($infoa);
                $lick5 = db('portal_xm')->where("arcrank = 1 and status = 1 and thumbnail!=' ' and find_in_set('d',flag) ")->order('pubdate desc')->limit(1,12)->select();//array_slice(json_decode($a, true), 0, 12);

                $lick6 = db('portal_xm')->where('arcrank =1 and status = 1 and find_in_set("c",flag) ')->order('pubdate desc')->limit(16)->select();//array_slice(json_decode($a, true), 100, 16);
                //新品上线
                $lick8 = db('portal_xm')->where('arcrank =1 and status = 1 ')->order('pubdate desc')->limit(16,10)->select();//array_slice(json_decode($a, true), 100, 10);
                //品牌推荐
                // $a = json_encode($lick8);
                $lick7 = db('portal_xm')->where('arcrank =1 and status = 1 and thumbnail!="" and find_in_set("a",flag) ')->limit(3,8)->select();//array_slice(json_decode($a, true), 0, 8);
                //热门投资项目
                $lick9 = db('portal_category')->where("id", 'in', '2,1,3')->where('ishidden = 1 and status = 1')->select();
                //创业故事
                $cate1 = db('portal_category')->where("id", 'in', '11,32')->where('ishidden = 1 and status = 1')->select();
                foreach ($cate1 as $key => $val) {
                    $info = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                    $a = json_encode($info);
                    //创业之道
                    $val['data'] = array_slice(json_decode($a, true), 1, 9);
                    //热门品牌
                    $val['img'] = array_slice(json_decode($a, true), 0, 1);
                    $data1[] = $val;
                }
                $cate2 = db('portal_category')->where("id", 'in', '20,38')->where('ishidden = 1 and status = 1')->order('id desc')->select();
                foreach ($cate2 as $key => $val) {
                    //创业之道
                    $info = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(0, 4)->select();
                    $a = json_encode($info);
                    $val['img'] = array_slice(json_decode($a, true), 0, 1);
                    $val['data'] = array_slice(json_decode($a, true), 1, 3);
                    $data2[] = $val;
                }
                $infod = db('portal_post')->where('parent_id=392 and post_status = 1 and status =1 ')->order('published_time desc')->limit(13)->select();
                $a = json_encode($infod);
                $lick13 = array_slice(json_decode($a, true), 0, 1);
                $lick14 = array_slice(json_decode($a, true), 1, 12);
                $cate3 = db('portal_category')->where("id", 'in', '401,403,404')->order('id desc')->select();
                foreach ($cate3 as $key => $val) {
                    $val['img'] = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(0, 1)->select();
                    $val['img'] = json_decode($val['img'],true);
                    foreach ($val['img'] as $ks => $vs) {
                        $val['img'][$ks]['classs'] = substr($vs['class'],0,4);
                    }
                    $val['data'] = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status =1 and status = 1' )->order('published_time desc')->limit(1, 7)->select();
                    $val['data'] = json_decode($val['data'], true);
                    foreach ($val['data'] as $k => $v) {
                        #如 news/jj 取news
                        $arrpath = explode('/', $v['class']);//字符拆分数组
                        $path = $arrpath[0];//取第一个数组
                        $val['data'][$k]['class'] = substr($path,0,4);
                    }
                    $data3[] = $val;

                }
              sort($data3);
                 // print_r($data3);die;
                $lick15 = db("portal_post")->where(" post_status =1 and status = 1  ")->order("published_time desc")->limit(8,10)->select();
                $lick15 = json_decode($lick15,true);
                foreach ($lick15 as $ka => $va) {
                    $lick15[$ka]['classs'] = substr($va['class'],0,4);
                }
                foreach ($cates as $key => $val) {
                    $wheres['typeid'] = array('in', $val['ids']);
                    $where['status'] = 1;
                    $where['arcrank'] = 1;
                    $val['pp1'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(0, 10)->select();
                    $val['pp2'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(10, 10)->select();
                    $val['pp3'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(20, 10)->select();
                    $val['pp4'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(30, 10)->select();
                    $val['pp5'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(40, 10)->select();
                    $val['pp6'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(50, 10)->select();
                    $cates1[] = $val;
                }
                //投资推荐
                $tz1 = db('portal_xm')->where(['invested' => ['like', '1-5万'],'status' => 1,'arcrank' => 1 ])->where(' find_in_set("i",flag)')->limit('0,14')->select();
                $tz2 = db('portal_xm')->where(['invested' => ['like', '5-10万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
                $tz3 = db('portal_xm')->where(['invested' => ['like', '10-20万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
                $tz4 = db('portal_xm')->where(['invested' => ['like', '20-50万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
                $tz5 = db('portal_xm')->where(['invested' => ['like', '50-100万'] ,'status' => 1,'arcrank' => 1])->limit('0,14')->where(' find_in_set("i",flag)')->select();
                $tz6 = db('portal_xm')->where(['invested' => ['like', '100万以上'] ,'status' => 1,'arcrank' => 1])->limit('0,14')->where(' find_in_set("i",flag)')->select();
                //行业分类
                $cate4 = db("portal_category")->where("id= 2 and ishidden =1 and status =1")->select();
                foreach ($cate4 as $key => $val) {
                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(300)->select();
                    $class1[] = $val;
                }
                $cate5 = db("portal_category")->where("id", "in", "1,4,5,7,10")->where('status =1 and ishidden = 1')->select();
                foreach ($cate5 as $key => $val) {
                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(100)->select();
                    $class2[] = $val;
                }
                $cate6 = db("portal_category")->where("id", "in", "3,6,8,9,63,312,313,396,420")->where('status =1 and ishidden = 1')->select();
                foreach ($cate6 as $key => $val) {
                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(100)->select();
                    $class3[] = $val;
                }
                //查询数据
                $website = DB('website')->where(['id' => 1])->find();
                $youlian = db("flink")->where("typeid = 9999 and ischeck =1 ")->order("dtime desc")->limit(50)->select();

                //加入缓存
                $redis->set('index_flg' , 1 , 300);
                $redis->set('index_cate' , json_encode($cate,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick1' , json_encode($lick1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick2' , json_encode($lick2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick3' , json_encode($lick3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick4' , json_encode($lick4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick5' , json_encode($lick5,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick6' , json_encode($lick6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick7' , json_encode($lick7,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick8' , json_encode($lick8,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick9' , json_encode($lick9,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick13' , json_encode($lick13,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick14' , json_encode($lick14,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick15' , json_encode($lick15,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz1' , json_encode($tz1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz2' , json_encode($tz2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz3' , json_encode($tz3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz4' , json_encode($tz4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz5' , json_encode($tz5,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_tz6' , json_encode($tz6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cates1' , json_encode($cates1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_data1' , json_encode($data1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_data2' , json_encode($data2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_data3' , json_encode($data3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_class1' , json_encode($class1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_class2' , json_encode($class2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_class3' , json_encode($class3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_youlian' , json_encode($youlian,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_website' , json_encode($website,JSON_UNESCAPED_UNICODE) , 300);
            }

            $this->assign('cate',$cate);
            $this->assign('lick1',$lick1);
            $this->assign('lick2',$lick2);
            $this->assign('lick3',$lick3);
            $this->assign('lick4',$lick4);
            $this->assign('lick5',$lick5);
            $this->assign('lick6',$lick6);
            $this->assign('lick7',$lick7);
            $this->assign('lick8',$lick8);
            $this->assign('lick9',$lick9);
            $this->assign('lick13',$lick13);
            $this->assign('lick14',$lick14);
            $this->assign('lick15',$lick15);
            $this->assign('tz1',$tz1);
            $this->assign('tz2',$tz2);
            $this->assign('tz3',$tz3);
            $this->assign('tz4',$tz4);
            $this->assign('tz5',$tz5);
            $this->assign('tz6',$tz6);
            $this->assign('cates',$cates1);
            $this->assign('data1',$data1);
            $this->assign('data2',$data2);
            $this->assign('data3',$data3);
            $this->assign('class1',$class1);
            $this->assign('class2',$class2);
            $this->assign('class3',$class3);
            $this->assign('youlian',$youlian);
            $this->assign('website',$website);
            $this->daohang();
            $this->dibu();
//            $t2 = microtime(true);
//            echo '耗时'.round($t2-$t1,3).'秒<br>';
            return $this->fetch(':index');
        }


    }

    public function daohang()
    {
        $cates1 = db("portal_category")->where("id = 2 and ishidden = 1 and status =1 ")->select();
        foreach($cates1 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(40)->select();
            $c1[] = $val;
        }
        $cates2 = db("portal_category")->where("id ","in","312,8,10,5,396")->where('status =1 and ishidden = 1')->select();
        foreach($cates2 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(6)->select();
            $c2[] = $val;
        }
        $cates3 = db("portal_category")->where("id","in", " 4,7,313,9,420")->select();
        foreach($cates3 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(6)->select();
            $c3[] = $val;
        }
        $cates4 = db("portal_category")->where("id","in"," 1,3,339,6")->where('status =1 and ishidden = 1')->select();
        foreach($cates4 as $key=>$val)
        {
            $val['data'] = db("portal_category")->where("parent_id =".$val['id'])->where('status =1 and ishidden = 1')->limit(6)->select();
            $c4[] = $val;
        }

        $this->assign("cates1",$c1);
        $this->assign("cates2",$c2);
        $this->assign("cates3",$c3);
        $this->assign("cates4",$c4);
    }

    public function dibu()
    {
        $dibu = db("portal_category")->where("parent_id",'in','52,53')->where('status =1 and ishidden = 1')->select();
        $this->assign('dibu',$dibu);
    }
}
