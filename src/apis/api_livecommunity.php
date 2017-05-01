<?php
// ================================
/**
 *  errcode:
 *  
 *  以下编号和客户端代码相关
 *  202001 error userVerify
 *  
 *  以下编号目前和客户端代码不相关
 *  
 */

require_once  dirname( __FILE__ ) . '/class.LIVECOMMUNITY.php';
require_once  dirname( __FILE__ ) . '/class.EBCONFIG.php';

class class_livecommunity{
  public static function main( $para1,$para2) {
    $res=API::data(['time'=>time().' - livecommunity is ready.']);
    return $res;
  }
  static function userVerify() {
    return USER::userVerify();
  }
  static function adminVerify() {
    $uid=API::INP('uid');    
    if(! USER::userVerify())return 0;
    return USER::checkUserRights($uid,EBCONFIG::EB_RIGHTS_ADMIN);
  }
  
  public static function create( ) {
    $r=self::adminVerify();
    if(!$r)
      return API::msg(202001,'error adminVerify');
    
    $r=LIVECOMMUNITY::create();
    return $r;
  }
}

