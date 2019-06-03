<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;



class FunCommon
{
    /**
     * @param string $content 文章内容
     * @param string $attr 标签 默认为src
     * @param string $tag  标签 img
     * @return mixed 返回第一张图片
     */
    public  static  function get_html_attr_by_tag($content="",$attr="src",$tag="img")
    {
        $arr=array();
        $cache_arr=array();
        $attr=explode(',',$attr);
        $tag=explode(',',$tag);
        foreach($tag as $i=>$t)
        {
            foreach($attr as $a)
            {
                $content = htmlspecialchars_decode($content);
                preg_match_all("/<\s*".$t."\s+[^>]*?".$a."\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i",$content,$match);
                foreach($match[2] as $n=>$m)
                {
                    $arr[$t][$n][$a]=$m;
                }
            }
        }
        if(count($arr) == 0 ){
            return false;
        }else{
            return $arr['img'][0]['src'];
        }
    }
    /**
     * @param $page    当前页
     * @param $_total_page 共多少页
     * @param $showPage    显示几个页码
     * @param $utl         url
     * @param string $Parameter 参数
     * @param string $pageGetParam get参数设置 默认page
     * @param string $countLinks 共多条记录
     * @return string
     */
    public static function page($page, $_total_page, $showPage, $utl, $Parameter = "",$pageGetParam = 'page',$countLinks = 0){
        $showPage = 7;
        $pageOffset = ($showPage - 1) / 2;//计算偏移量；
        $start = 1;//初始化数据；
        //加上分页效果
        $page_banner = '<ul class="pagination">';//用来存放分页信息；
        if ($page > 1) {
            $page_banner .= '<li class="page-item"><a class="page-link" href="' . $utl . '?'.$pageGetParam.'=1'.$Parameter.'">首页';
            $page_banner .= '<li class="page-item"><a class="page-link" href="' . $utl . '?'.$pageGetParam.'=' . ($page - 1) . $Parameter . '">上一页';
        } else {
            $page_banner .= '<li class="page-item disabled"><span class="page-link">首页</span></li>';
            $page_banner .= '<li class="page-item disabled"><span class="page-link">上一页</span></li>';
        }
        if ($_total_page > $showPage) {
            if ($page > $pageOffset + 1) {
                $page_banner .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            if ($page > $pageOffset) {
                $start = $page - $pageOffset;//计算起始位置；
                $end = $_total_page > $page + $pageOffset ? $page + $pageOffset : $_total_page;
            } else {
                $start = 1;
                $end = $_total_page > $showPage ? $showPage : $_total_page;
            }
            if ($page + $pageOffset > $_total_page) {
                $start = $start - ($page + $pageOffset - $end);
            }
        } else {
            $end = $_total_page;
        }
        //显示数字页码；
        for ($i = $start; $i <= $end; $i++) {
            if ($page == $i) {
                $page_banner .= '<li class="page-item disabled"><span class="page-link">' . $i . '</span></li>';
            } else {
                $page_banner .= '<li class="page-item"><a class="page-link" href="' . $utl . '?'.$pageGetParam.'=' . $i . $Parameter . '">' . $i . '</a></li>';

            }
        }
        //尾部省略；
        if ($_total_page > $showPage && $_total_page > $page + $pageOffset) {
            $page_banner .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        if ($page < $_total_page) {
            $page_banner .= '<li class="page-item"><a class="page-link" href="' . $utl . '?'.$pageGetParam.'=' . ($page + 1) . $Parameter . '">下一页</a></li>';
            $page_banner .= '<li class="page-item"><a class="page-link" href="' . $utl . '?'.$pageGetParam.'=' . $_total_page. $Parameter . '">末页</a></li>';

        } else {
            $page_banner .= '<li class="page-item disabled"><span class="page-link">下一页</span></li>';
            if($page == $_total_page){
                $page_banner .= '<li class="page-item disabled"><span class="page-link">末页</span></li>';

            }
        }
        $page_banner .= '<li class="page-item disabled"><span class="page-link">共'.$_total_page.'页/'.$countLinks.'条记录</span></li>';
        return $page_banner;

    }

}