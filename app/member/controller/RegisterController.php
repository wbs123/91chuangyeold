<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\member\controller;

use app\member\controller\BaseController;
use app\admin\model\ThemeModel;
use think\Db;
use think\Session;
use think\Validate;
use tree\Tree;
use think\Request;

class RegisterController extends BaseController
{
    private $stateMap = [
        'ERROR_PHONE_FORMAT'        =>  "手机号格式错误!",
        'ERROR_PHONE_EXISTS'        =>  "手机号已存在!",
        'ERROR_PWASSWORD_FORMAT'    =>  "密码位数少于8位!",
        'ERROR_UNKNOWN'             =>  "注册失败!",
        'ERROR_SEND_SMS_FAIL'       =>  "短信发送失败!",
        'ERROR_SEND_SMS'            =>  "请先发送验证码!",
        'ERROR_CODE'                =>  "验证码错误或已过期!",
        'ERROR_SEND_SMS_REPEAT'     =>  "请求频繁，稍后再试!",
        'ERROR_PRODUCT_NAME_EMTPY'  =>  "项目名称不能为空!",
        'SUCCESS'                   =>  "成功!",
        'ERROR_IMG_UPLOAD'          =>  "图片上传失败!",
    ];

    public function register(){
        return $this->fetch('/register1');
    }

    public function registerCheck(){
        $data = Request::instance()->param();

        if(FunCommon::isPhone($data['phone'])==false){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_PHONE_FORMAT']);
        }
        //校验短信验证码
        if($data['phone'] != Session::get('reg_sms_phone')){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_SEND_SMS']);
        }
        if($data['code'] != Session::get('reg_sms_code') || time() > Session::get('reg_sms_time')+300){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_CODE']);
        }
//        if($data['code'] != Session::get('reg_sms_code')){
//            return $this->returnAjaxJson(201,$this->stateMap['ERROR_SEND_SMS']);
//        }
        //查询手机号是否存在
        $flg = db('member')->where(['phone' => $data['phone']])->find();
        if($flg){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_PHONE_EXISTS']);
        }
        //判断密码是否超过8位
        if(strlen(trim($data['pwd']))<8){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_PWASSWORD_FORMAT']);
        }
        //保存数据
        $member['phone']    = $data['phone'];
        $member['password'] = cmf_password($data['pwd']);
        $member['type']     = $data['type'];
        $member['reg_time'] = time();
        if(db('member')->insert($member)){
            return $this->returnAjaxJson(200,$this->stateMap['SUCCESS']);
        }else{
            return $this->returnAjaxJson(202,$this->stateMap['ERROR_UNKNOWN']);
        }

    }

    public function sendSmsCode(){
        $data = Request::instance()->param();

        if(FunCommon::isPhone($data['phone'])==false){
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_PHONE_FORMAT']);
        }
        $reg_sms_code = rand(100000,999999);
        $reg_sms_time = time();
        $reg_sms_phone = $data['phone'];
        //校验短信验证码
//        if($data['phone'] == Session::get('reg_sms_phone') && time()<(Session::get('reg_sms_time')+60)){
//            return $this->returnAjaxJson(201,$this->stateMap['ERROR_SEND_SMS_REPEAT']);
//        }

        //发送短信验证码
        $flg = 1;

        if($flg){
            Session::set('reg_sms_code',$reg_sms_code);
            Session::set('reg_sms_time',$reg_sms_time);
            Session::set('reg_sms_phone',$reg_sms_phone);
            return $this->returnAjaxJson(200,$this->stateMap['SUCCESS'].$reg_sms_code);
        }else{
            return $this->returnAjaxJson(202,$this->stateMap['ERROR_SEND_SMS_FAIL']);
        }
    }

    /**
     * 注册查询项目
     * @return mixed
     */
    public function regSelectProduct(){
        $data = Request::instance()->param();
        if(empty($data['name'])){
            return $this->retunAjaxJosn(201,$this->stateMap['ERROR_PRODUCT_NAME_EMTPY']);
        }
        $list = db('portal_xm')->where(['status' => 1,  'arcrank' => 1 ,'title' => ['like',"%".$data['name']."%"] ])->field(['aid','title','class'])->select()->toArray();
        foreach ($list as $k => $v ){
            $list[$k]['url'] = '/'.$v['class'].'/'.$v['aid'].'.html';
        }
        return $this->returnAjaxJson(200,$this->stateMap['SUCCESS'],$list);
    }

    /**注册上传图片
     * @return mixed
     */
    public function uploadShopImg(){
        $data = Request::instance()->param();
        #删除旧图片
        if(!empty($data['old_img']) && FunCommon::get_files_ext($data['old_img'])=='jpg' ){
            $del_flg = FunCommon::del_file($data['old_img']);
        }
        #上传图片流
        $url = FunCommon::base64_upload($data['img']);
        if($url){
            return $this->returnAjaxJson(200,$this->stateMap['SUCCESS'],['img_url' => $url]);
        }else{
            return $this->returnAjaxJson(201,$this->stateMap['ERROR_IMG_UPLOAD'],['img_url' => $url]);
        }

    }
}