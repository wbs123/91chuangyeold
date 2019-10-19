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
namespace app\portal\model;

use think\Model;
use think\Db;

class AreaModel extends Model
{

    //当前地区下项目
    public function projectData($param){

        $areaValue = DB::name('sys_enum')->field('evalue')->where(['py'=>$param['area']])->find();

        if(empty($areaValue['evalue'])){
            return false;
        }
        if($areaValue['evalue'] % 500 == 0){
            $fareavalue = intval($areaValue['evalue']);
            $maxvalue = $fareavalue+500;
            $where['evalue'] = [['gt',$fareavalue],['lt',($fareavalue+500)]];
            $sareavalue = DB::name('sys_enum')->field('evalue')->where('evalue > '.$fareavalue.' and evalue < '
                    .$maxvalue)->group('evalue')->select();
            $areaAll = [];
            foreach ($sareavalue as $value){
                if(!in_array(floor($value['evalue']),$areaAll)){
                    $areaAll[] = floor($value['evalue']);
                }
            }
            $areaAll[] = $areaValue['evalue'];
            $where = [
                'por.arcrank' => 1,
                'por.status' => 1,
                'por.nativeplace'=>['in',implode(',',$areaAll)]
            ];
        }else{
            $where = [
                'por.arcrank' => 1,
                'por.status' => 1,
                'por.nativeplace'=>$areaValue['evalue']
            ];
        }
        //页数
        $page = isset($param['page']) ? str_replace('list_','',$param['page']) : '';

        if(!empty($param['type'])){
            if(isset($param['catid'])){
                $where['cat.id'] = ['in',implode(',',$param['catid'])];
            }else{
                $where['cat.path'] = $param['type'];
            }

        }
        if(isset($param['price']) && !empty($param['price'])){
            $where['por.invested'] = $param['price'].'万';
        }

        $data = DB::name('portal_xm')
                ->alias('por')
                ->field('por.*,cat.name as categoryname')
                ->join('portal_category cat','por.typeid = cat.id')
                ->order('por.pubdate')
                ->where($where)->paginate(15,false,['query' => $param,'page'=>$page]);

        return $data;

    }

    //获取项目中地区
    static public function havearea(){
       return DB::name('portal_xm')->field('nativeplace')->group('nativeplace')->select();
    }
    //获取所有地区
    static public function allarea($where=''){
        return DB::name('sys_enum')->field('ename,py,evalue')->where(['egroup'=>'nativeplace'])->where($where)->order
        ('id asc')->select();
    }
    //获取地区名称
    static public function areaName($where){
        return DB::name('sys_enum')->field('ename,evalue')->where($where)->find();
    }
    //获取分类
    static public function getCategory($condition=''){
        $where = [
            'status'=>1
            ,'ishidden'=>1
            ,'parent_id'=>0
            ,'channeltype'=>17
            ,'id' =>['neq',350]
        ];

        return DB::name("portal_category")
            ->field('id,path,name')->where($where)->where($condition)
            ->limit(15)->order('list_order','asc')
            ->select()->toArray();
    }
    //获取子分类
    static public function getSonCate($type=''){
        $catprint =  DB::name("portal_category")->field('id,parent_id')->where(['path'=>$type])->find();
        $where = [
            'status'=>1
            ,'ishidden'=>1
            ,'parent_id'=> empty($catprint['parent_id']) ? $catprint['id'] : $catprint['parent_id']
        ];
        return DB::name("portal_category")
            ->field('id,path,name')->where($where)
            ->order('list_order','asc')
            ->select()->toArray();
    }
    //获取分类信息
    static public function categoryData($where){
        return DB::name("portal_category")->where($where)->find();
    }

}