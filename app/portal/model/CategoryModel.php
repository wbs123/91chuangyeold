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
    //获取子分类
    static public function ALLCate($type=''){
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