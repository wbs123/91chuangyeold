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
//                //取出缓存
                $cate1 = json_decode($redis->get('index_cate1' ),true);
                $cate2 = json_decode($redis->get('index_cate2'),true);
                $cate3 = json_decode($redis->get('index_cate3'),true);
                $cate4 = json_decode($redis->get('index_cate4'),true);
                $cate5 = json_decode($redis->get('index_cate5'),true);
                $cate6 = json_decode($redis->get('index_cate6'),true);
                $cate7 = json_decode($redis->get('index_cate7'),true);
                $cate8 = json_decode($redis->get('index_cate8'),true);
                $cate9 = json_decode($redis->get('index_cate9'),true);
                $cate10 = json_decode($redis->get('index_cate10'),true);
                $cate11 = json_decode($redis->get('index_cate11' ),true);
                $cate12 = json_decode($redis->get('index_cate12' ),true);
                $cate13 = json_decode($redis->get('index_cate13' ),true);
                $cate14 = json_decode($redis->get('index_cate14'),true);
                $cate15 = json_decode($redis->get('index_cate15'),true);
                $cate16 = json_decode($redis->get('index_cate16'),true);
                $zuice = json_decode($redis->get('index_zuice'),true);
                $news_hot = json_decode($redis->get('index_news_hot'),true);
                $news_hot2 = json_decode($redis->get('index_news_hot2' ),true);
                $dapai = json_decode($redis->get('index_dapai'),true);
                $lick6 = json_decode($redis->get('index_lick6'),true);
                $lick7 = json_decode($redis->get('index_lick7'),true);
                $ban = json_decode($redis->get('index_ban'),true);
                $hot = json_decode($redis->get('index_hot'),true);
                $meishi = json_decode($redis->get('index_meishi'),true);
                $peixun = json_decode($redis->get('index_peixun'),true);
                $muying = json_decode($redis->get('index_muying'),true);
                $zhuantixm = json_decode($redis->get('index_zhuantixm'),true);
                $class1 = json_decode($redis->get('index_class1'),true);
                $mothhot = json_decode($redis->get('index_mothhot'),true);
                $newsxm = json_decode($redis->get('index_newsxm'),true);
                $zixun1 = json_decode($redis->get('index_zixun1'),true);
                $zixun2 = json_decode($redis->get('index_zixun2'),true);
                $zhishi1 = json_decode($redis->get('index_zhishi1'),true);
                $zhishi2 = json_decode($redis->get('index_zhishi2'),true);
                $gushi1 = json_decode($redis->get('index_gushi1'),true);
                $gushi2 = json_decode($redis->get('index_gushi2'),true);
                $zhidao1 = json_decode($redis->get('index_zhidao1'),true);
                $zhidao2 = json_decode($redis->get('index_zhidao2'),true);
                $zhinan1 = json_decode($redis->get('index_zhinan1'),true);
                $zhinan2 = json_decode($redis->get('index_zhinan2'),true);
                $youlian = json_decode($redis->get('index_youlian'),true);
                $website = json_decode($redis->get('index_website'),true);

            }else {
                //左侧导航
                $where1['parent_id'] = ['in','2'];
                $cate1 = db("portal_category")->where($where1)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where2['parent_id'] = ['in','734'];
                $cate2 = db("portal_category")->where($where2)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where3['parent_id'] = ['in','8'];
                $cate3 = db("portal_category")->where($where3)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where4['parent_id'] = ['in','10'];
                $cate4 = db("portal_category")->where($where4)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where5['parent_id'] = ['in','312'];
                $cate5 = db("portal_category")->where($where5)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where6['parent_id'] = ['in','5'];
                $cate6 = db("portal_category")->where($where6)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where7['parent_id'] = ['in','4'];
                $cate7 = db("portal_category")->where($where7)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where8['parent_id'] = ['in','4'];
                $cate8 = db("portal_category")->where($where8)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where9['parent_id'] = ['in','9'];
                $cate9 = db("portal_category")->where($where9)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where10['parent_id'] = ['in','339'];
                $cate10 = db("portal_category")->where($where10)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where11['parent_id'] = ['in','1'];
                $cate11 = db("portal_category")->where($where11)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where12['parent_id'] = ['in','313'];
                $cate12 = db("portal_category")->where($where12)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where13['parent_id'] = ['in','6'];
                $cate13 = db("portal_category")->where($where13)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where14['parent_id'] = ['in','3'];
                $cate14 = db("portal_category")->where($where14)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where15['parent_id'] = ['in','396'];
                $cate15 = db("portal_category")->where($where15)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where16['parent_id'] = ['in','420'];
                $cate16 = db("portal_category")->where($where16)->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(20)->select();
                $where17['typeid'] =['in','2,1,3,4,5,7'];
                $zuice = db("portal_xm")->where($where17)->where('status = 1 and arcrank = 1')->field('aid,title,click,class,invested')->order('click desc')->limit(10)->select();
                $news = db('portal_post')->where('post_status = 1 and status = 1 ')->where("thumbnail != ''")->field('thumbnail,id,post_title,published_time')->order('click desc')->limit(7)->select();
                $news = $news->all();
                $news_hot = array_slice($news,0,2);
                $news_hot2 = array_slice($news,2,5);

                $where18['aid'] = ['in','15867,15878,76068'];
                $dapai = db('portal_xm')->where($where18)->where('status = 1 and arcrank = 1')->select();
                $lick6 = db('portal_xm')->where('arcrank =1 and status = 1 and find_in_set("c",flag) ')->order('pubdate desc')->limit(30)->select();
                //火爆招商
//                $lick7 = db('portal_xm')->where('arcrank =1 and status = 1 and thumbnail!="" and find_in_set("a",flag) ')->limit(3,10)->select();
                $where19['aid'] = ['in','75128,75136,76038,76221,77197,79114,92156,82626,119502,100944'];
                $lick7 = db('portal_xm')->where($where19)->select();
                //项目推荐
                $ban = db('portal_xm')->where('aid = 89613')->find();
                $mothhot = db('portal_xm')->where('arcrank =1 and status = 1')->field('aid,title,class')->order('click desc')->limit(8)->select();
                //热门
                $where20['aid'] = ['in','98102,84327,84335,119350,118876,80367,78235,93250,93996'];
                $hot = db('portal_xm')->where($where20)->select();
                $hot = $hot->all();
                foreach ($hot as $k=>$v){
                    $name = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $hot[$k]['cate'] = $name['name'];
                }
                //餐饮美食
                $where21['aid'] = ['in','92183,1387,77596,93055,72114,97174,91118,91119,104352'];
                $meishi = db('portal_xm')->where($where21)->select();
                $meishi = $meishi->all();
                foreach ($meishi as $k=>$v){
                    $name = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $meishi[$k]['cate'] = $name['name'];
                }
                //教育培训
                $where22['aid'] = ['in','86879,86886,86877,86936,86993,86895,86913,86931,83968'];
                $peixun = db('portal_xm')->where($where22)->select();
                $peixun = $peixun->all();
                foreach ($peixun as $k=>$v){
                    $name = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $peixun[$k]['cate'] = $name['name'];
                }
                //母婴推荐
                $where23['aid'] = ['in','118441,9130,9057,9365,8968,9050,10120,87245,9214'];
                $muying = db('portal_xm')->where($where23)->select();
                $muying = $muying->all();
                foreach ($muying as $k=>$v){
                    $name = db('portal_category')->where('id = '.$v['typeid'])->field('name')->find();
                    $muying[$k]['cate'] = $name['name'];
                }
                //创业专题项目
                $zhuantixm = db('portal_xm')->where('arcrank =1 and status = 1')->order('click desc')->limit('3','4')->select();
                //行业分类
                $where24['id'] = ['in','2,1,4,5,7,10,3,6,8,9,312,313,396,420'];
                $categ = db("portal_category")->where($where24)->where("ishidden =1 and status =1")->orderRaw("field(id,2,1,4,5,7,10,3,6,8,9,312,313,396,420)")->select();

                foreach ($categ as $key => $val) {
                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(8)->select();
                    $class1[] = $val;
                }
                $newsxm = db('portal_xm')->where('arcrank =1 and status = 1')->field('aid,typeid,title,class')->order('inputtime desc')->limit(28)->select();
                $newsxm = $newsxm->all();
                foreach($newsxm as $k=>$v){
                    $type = db('portal_category')->where('id = '.$v['typeid'])->field('name,path')->find();
                    $newsxm[$k]['cate'] = $type['name'];
                    $newsxm[$k]['paths'] = $type['path'];
                }

                //创业资讯
                $where25['parent_id'] = ['in','399,401,402,403,404,405,406,407,408,409,433'];
                $zixun = db('portal_post')->where($where25)->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                $zixun = $zixun->all();
                $zixun1 = array_slice($zixun,0, 1);
                $zixun2 = array_slice($zixun,1, 8);

                //创业知识
                $where26['parent_id'] = ['in','20,22,27,31'];
                $zhishi = db('portal_post')->where($where26)->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                $zhishi = $zhishi->all();
                $zhishi1 = array_slice($zhishi,0, 1);
                $zhishi2 = array_slice($zhishi,1, 8);

                //创业故事
                $where27['parent_id'] = ['in','11'];
                $gushi = db('portal_post')->where($where27)->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                $gushi = $gushi->all();
                $gushi1 = array_slice($gushi,0, 1);
                $gushi2 = array_slice($gushi,1, 8);

                //创业之道
                $where28['parent_id'] = ['in','32'];
                $zhidao = db('portal_post')->where($where28)->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                $zhidao = $zhidao->all();
                $zhidao1 = array_slice($zhidao,0, 1);
                $zhidao2 = array_slice($zhidao,1, 8);

                //创业指南
                $where29['parent_id'] = ['in','37,38,41,43,392'];
                $zhinan = db('portal_post')->where($where29)->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
                $zhinan = $zhinan->all();
                $zhinan1 = array_slice($zhinan,0, 1);
                $zhinan2 = array_slice($zhinan,1, 8);

//                //左侧二级导航
//              	$cate = db("portal_category")->where("parent_id = 0")->where('status = 1 and ishidden = 1')->order('list_order asc')->limit(15)->select();
//                $a = json_encode($cate);
//                $cates = array_slice(json_decode($a, true), 0, 12);
//                foreach ($cates as $k => $v) {
//                    $cated = db('portal_category')->where(['parent_id' => $v['id'],'ishidden' => 1,'status' => 1])->column('id');
//                    array_unshift($cated, $v['id']);
//                    $cates[$k]['ids'] = implode(',', $cated);
//                }
//                //品牌上榜
//                $infos = db('portal_xm')->where("typeid", 'in', '2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('arcrank = 1 ')->where('status = 1');
//                $lick1 = $infos->order('pubdate asc')->limit(16, 5)->select();
//                $lick3 = $infos->order('pubdate desc')->limit(10, 16)->select();
//                $lick2 = db('portal_xm')->where("arcrank = 1 and status = 1 and thumbnail != '' and find_in_set('h',flag)")->order('update_time desc')->limit(4)->select();
//
//                $lick4 = db('portal_post')->where('post_status=1 and status=1 ')->order('published_time desc')->limit(10,8)->select();
//                $lick4 = $lick4->all();
//                foreach ($lick4 as $kw => $vw) {
//                    $lick4[$kw]['classs'] = substr($vw['class'],0,4);
//                }
//                $infoa = db('portal_xm')->where("typeid", 'in', '2,1,3,4,5,6,7,8,9,10,20,339,312,313,350,396,420')->where('arcrank =1 and status = 1')->order('aid asc')->limit(117)->select();
//                $a = json_encode($infoa);
//                $lick5 = db('portal_xm')->where("arcrank = 1 and status = 1 and thumbnail!=' ' and find_in_set('d',flag) ")->order('pubdate desc')->limit(1,12)->select();//array_slice(json_decode($a, true), 0, 12);
//
//                $lick6 = db('portal_xm')->where('arcrank =1 and status = 1 and find_in_set("c",flag) ')->order('pubdate desc')->limit(16)->select();//array_slice(json_decode($a, true), 100, 16);
//                //新品上线
//                $lick8 = db('portal_xm')->where('arcrank =1 and status = 1 ')->order('pubdate desc')->limit(16,10)->select();//array_slice(json_decode($a, true), 100, 10);
//                //品牌推荐
//                // $a = json_encode($lick8);
//                $lick7 = db('portal_xm')->where('arcrank =1 and status = 1 and thumbnail!="" and find_in_set("a",flag) ')->limit(3,8)->select();//array_slice(json_decode($a, true), 0, 8);
//                //热门投资项目
//                $lick9 = db('portal_category')->where("id", 'in', '2,1,3')->where('ishidden = 1 and status = 1')->select();
//                //创业故事
//                $cate1 = db('portal_category')->where("id", 'in', '11,32')->where('ishidden = 1 and status = 1')->select();
//                foreach ($cate1 as $key => $val) {
//                    $info = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(9)->select();
//                    $a = json_encode($info);
//                    //创业之道
//                    $val['data'] = array_slice(json_decode($a, true), 1, 9);
//                    //热门品牌
//                    $val['img'] = array_slice(json_decode($a, true), 0, 1);
//                    $data1[] = $val;
//                }
//                $cate2 = db('portal_category')->where("id", 'in', '20,38')->where('ishidden = 1 and status = 1')->order('id desc')->select();
//                foreach ($cate2 as $key => $val) {
//                    //创业之道
//                    $info = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(0, 4)->select();
//                    $a = json_encode($info);
//                    $val['img'] = array_slice(json_decode($a, true), 0, 1);
//                    $val['data'] = array_slice(json_decode($a, true), 1, 3);
//                    $data2[] = $val;
//                }
//                $infod = db('portal_post')->where('parent_id=392 and post_status = 1 and status =1 ')->order('published_time desc')->limit(13)->select();
//                $a = json_encode($infod);
//                $lick13 = array_slice(json_decode($a, true), 0, 1);
//                $lick14 = array_slice(json_decode($a, true), 1, 12);
//                $cate3 = db('portal_category')->where("id", 'in', '401,403,404')->order('id desc')->select();
//                foreach ($cate3 as $key => $val) {
//                    $val['img'] = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status = 1 and status = 1')->order('published_time desc')->limit(0, 1)->select();
//                    $val['img'] = json_decode($val['img'],true);
//                    foreach ($val['img'] as $ks => $vs) {
//                        $val['img'][$ks]['classs'] = substr($vs['class'],0,4);
//                    }
//                    $val['data'] = db('portal_post')->where("parent_id", 'in', $val['id'])->where('post_status =1 and status = 1' )->order('published_time desc')->limit(1, 7)->select();
//                    $val['data'] = json_decode($val['data'], true);
//                    foreach ($val['data'] as $k => $v) {
//                        #如 news/jj 取news
//                        $arrpath = explode('/', $v['class']);//字符拆分数组
//                        $path = $arrpath[0];//取第一个数组
//                        $val['data'][$k]['class'] = substr($path,0,4);
//                    }
//                    $data3[] = $val;
//
//                }
//              sort($data3);
//                 // print_r($data3);die;
//                $lick15 = db("portal_post")->where(" post_status =1 and status = 1  ")->order("published_time desc")->limit(8,10)->select();
//                $lick15 = json_decode($lick15,true);
//                foreach ($lick15 as $ka => $va) {
//                    $lick15[$ka]['classs'] = substr($va['class'],0,4);
//                }
//                foreach ($cates as $key => $val) {
//                    $wheres['typeid'] = array('in', $val['ids']);
//                    $where['status'] = 1;
//                    $where['arcrank'] = 1;
//                    $val['pp1'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(0, 10)->select();
//                    $val['pp2'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(10, 10)->select();
//                    $val['pp3'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(20, 10)->select();
//                    $val['pp4'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(30, 10)->select();
//                    $val['pp5'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(40, 10)->select();
//                    $val['pp6'] = db('portal_xm')->where($wheres)->where($where)->where(' find_in_set("s",flag)')->order('pubdate desc')->limit(50, 10)->select();
//                    $cates1[] = $val;
//                }
//                //投资推荐
//                $tz1 = db('portal_xm')->where(['invested' => ['like', '1-5万'],'status' => 1,'arcrank' => 1 ])->where(' find_in_set("i",flag)')->limit('0,14')->select();
//                $tz2 = db('portal_xm')->where(['invested' => ['like', '5-10万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
//                $tz3 = db('portal_xm')->where(['invested' => ['like', '10-20万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
//                $tz4 = db('portal_xm')->where(['invested' => ['like', '20-50万'],'status' => 1,'arcrank' => 1])->where(' find_in_set("i",flag)')->limit('0,14')->select();
//                $tz5 = db('portal_xm')->where(['invested' => ['like', '50-100万'] ,'status' => 1,'arcrank' => 1])->limit('0,14')->where(' find_in_set("i",flag)')->select();
//                $tz6 = db('portal_xm')->where(['invested' => ['like', '100万以上'] ,'status' => 1,'arcrank' => 1])->limit('0,14')->where(' find_in_set("i",flag)')->select();
//                //行业分类
//                $cate4 = db("portal_category")->where("id= 2 and ishidden =1 and status =1")->select();
//                foreach ($cate4 as $key => $val) {
//                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(300)->select();
//                    $class1[] = $val;
//                }
//                $cate5 = db("portal_category")->where("id", "in", "1,4,5,7,10")->where('status =1 and ishidden = 1')->select();
//                foreach ($cate5 as $key => $val) {
//                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(100)->select();
//                    $class2[] = $val;
//                }
//                $cate6 = db("portal_category")->where("id", "in", "3,6,8,9,63,312,313,396,420")->where('status =1 and ishidden = 1')->select();
//                foreach ($cate6 as $key => $val) {
//                    $val['cate'] = db("portal_category")->where("parent_id", 'in', $val['id'])->where('status =1 and ishidden = 1')->limit(100)->select();
//                    $class3[] = $val;
//                }
//                //查询数据
                $website = DB('website')->where(['id' => 1])->find();
                $youlian = db("flink")->where("typeid = 9999 and ischeck =1 ")->order("dtime desc")->limit(50)->select();

                //加入缓存
                $redis->set('index_flg' , 1 , 300);
                $redis->set('index_cate1' , json_encode($cate1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate2' , json_encode($cate2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate3' , json_encode($cate3,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate4' , json_encode($cate4,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate5' , json_encode($cate5,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate6' , json_encode($cate6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate7' , json_encode($cate7,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate8' , json_encode($cate8,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate9' , json_encode($cate9,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate10' , json_encode($cate10,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate11' , json_encode($cate11,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate12' , json_encode($cate12,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate13' , json_encode($cate13,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate14' , json_encode($cate14,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate15' , json_encode($cate15,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_cate16' , json_encode($cate16,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zuice' , json_encode($zuice,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_news_hot' , json_encode($news_hot,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_news_hot2' , json_encode($news_hot2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_dapai' , json_encode($dapai,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick6' , json_encode($lick6,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_lick7' , json_encode($lick7,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_ban' , json_encode($ban,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_hot' , json_encode($hot,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_meishi' , json_encode($meishi,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_peixun' , json_encode($peixun,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_muying' , json_encode($muying,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhuantixm' , json_encode($zhuantixm,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_class1' , json_encode($class1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_mothhot' , json_encode($mothhot,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_newsxm' , json_encode($newsxm,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zixun1' , json_encode($zixun1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zixun2' , json_encode($zixun2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhishi1' , json_encode($zhishi1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhishi2' , json_encode($zhishi2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_gushi1' , json_encode($gushi1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_gushi2' , json_encode($gushi2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhidao1' , json_encode($zhidao1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhidao2' , json_encode($zhidao2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhinan1' , json_encode($zhinan1,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_zhinan2' , json_encode($zhinan2,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_youlian' , json_encode($youlian,JSON_UNESCAPED_UNICODE) , 300);
                $redis->set('index_website' , json_encode($website,JSON_UNESCAPED_UNICODE) , 300);
            }

//            $this->assign('cate',$cate);
//            $this->assign('lick1',$lick1);
//            $this->assign('lick2',$lick2);
//            $this->assign('lick3',$lick3);
//            $this->assign('lick4',$lick4);
//            $this->assign('lick5',$lick5);
//            $this->assign('lick6',$lick6);
//            $this->assign('lick7',$lick7);
//            $this->assign('lick8',$lick8);
//            $this->assign('lick9',$lick9);
//            $this->assign('lick13',$lick13);
//            $this->assign('lick14',$lick14);
//            $this->assign('lick15',$lick15);
//            $this->assign('tz1',$tz1);
//            $this->assign('tz2',$tz2);
//            $this->assign('tz3',$tz3);
//            $this->assign('tz4',$tz4);
//            $this->assign('tz5',$tz5);
//            $this->assign('tz6',$tz6);
//            $this->assign('cates',$cates1);
//            $this->assign('data1',$data1);
//            $this->assign('data2',$data2);
//            $this->assign('data3',$data3);
//            $this->assign('class1',$class1);
//            $this->assign('class2',$class2);
//            $this->assign('class3',$class3);
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
            $this->assign('zuice',$zuice);
            $this->assign('news_hot',$news_hot);
            $this->assign('news_hot2',$news_hot2);
            $this->assign('dapai',$dapai);
            $this->assign('lick6',$lick6);
            $this->assign('lick7',$lick7);
            $this->assign('ban',$ban);
            $this->assign('hot',$hot);
            $this->assign('meishi',$meishi);
            $this->assign('peixun',$peixun);
            $this->assign('muying',$muying);
            $this->assign('zhuantixm',$zhuantixm);
            $this->assign('class1',$class1);
            $this->assign('mothhot',$mothhot);
            $this->assign('newsxm',$newsxm);
            $this->assign('zixun1',$zixun1);
            $this->assign('zixun2',$zixun2);
            $this->assign('zhishi1',$zhishi1);
            $this->assign('zhishi2',$zhishi2);
            $this->assign('gushi1',$gushi1);
            $this->assign('gushi2',$gushi2);
            $this->assign('zhidao1',$zhidao1);
            $this->assign('zhidao2',$zhidao2);
            $this->assign('zhinan1',$zhinan1);
            $this->assign('zhinan2',$zhinan2);
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

    public function dibu()
    {
        $dibu = db("portal_category")->where("parent_id",'in','52,53')->where('status =1 and ishidden = 1')->select();
        $this->assign('dibu',$dibu);
    }
}
