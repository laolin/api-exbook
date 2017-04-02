<?php

/**
 *  comment 数据库
 *  
 *  cid     评论id
 *  ctype   like,comment
 *  fid     评论的feed的fid
 *  uid     评论者的uid
 *  re_cid  回复的评论cid，0表示没有回复，-1表示这条是like
 *  re_uid  回复的评论uid，这是个冗余数据，为是方便数据展示用户头像
 *  content
 *  create_at
 *  mark    0正常 , > 1删除
 *  
 */
 
/**
 *  errcode: 203xxx (2035xx~9xx见class.EBCOMMENT.php)
 *  
 *  以下编号和客户端代码相关
 *  202001 error userVerify
 *  
 *  203002
 */


require_once  dirname( __FILE__ ) . '/class.EXBOOK.php';
require_once  dirname( __FILE__ ) . '/class.EBCOMMENT.php';

class class_ebcomment {
  public static function main( $para1,$para2) {
    $res=API::data(['time'=>time().' - ebcomment is ready.']);
    return $res;
  }
  static function userVerify() {
    return USER::userVerify();
  }
  
  // Crud
  //
  /**
   *  新建评论、点赞
   *  $para1: `like` | null 
   */
  public static function add( $para1 ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    $uid=API::INP('uid');
    $fid=API::INP('fid');
    
    $r=EBCOMMENT::create($para1,$uid,$fid);
    return $r;
  }
  /**
   *  获取评论、点赞 
   */
  public static function li(  ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    $uid=API::INP('uid');
    $fid=API::INP('fid');
    
    $r=EBCOMMENT::li( );
    return $r;
  }


}
