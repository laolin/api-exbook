<?php
// ================================
/**
 *  errcode:
 *  
 *  以下编号和客户端代码相关
 *  202001 error userVerify
 *  202002 发布内容无效
 *  202003 获取内容结果为空
 *  
 *  以下编号目前和客户端代码不相关
 *  2021xx feed get err
 *  202201 undelete err
 *  
 */

require_once  dirname( __FILE__ ) . '/class.EXBOOK.php';

class class_exbook{
  public static function main( $para1,$para2) {
    $res=API::data(['time'=>time().' - exbook is ready.']);
    return $res;
  }
  static function userVerify() {
    return USER::userVerify();
  }
  
  //test
  public static function test( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    return API::data('Test passed.');
  }
  
  


  /**
   *  API:
   *    /exbook/draft_create
   *  输入: 
   *    uid
   *  
   *  返回:
   *    各字段
   *  
   *  注：为简化系统， 默认规定只能有一个 草稿
   *  所以当 uid 用户已有草稿时，此API是返回原有的草稿
   */
  public static function draft_create( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    $uid=API::INP('uid');    
    $r=EXBOOK::__draft_get_by_uid($uid);
    if(!$r) {
      $r=EXBOOK::__draft_create($uid);
    }
    return API::data($r);
  }
  
  /**
   *  API:
   *    /exbook/draft_get
   *  输入: 
   *    uid
   *    fid 
   *  
   *  返回:
   *    各字段
   */
  public static function draft_get( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $fid=API::INP('fid');
    $r=EXBOOK::__feed_get($uid,$fid,'draft');
    //if(API::is_error($r)){
    //  return $r;
    //}
    return $r;
  }

  /**
   *  API:
   *    /exbook/draft_get_by_user
   *    自动按uid查找一个draft
   *  输入: 
   *    uid
   *  
   *  返回:
   *    各字段
   */
  public static function draft_get_by_user( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $r=EXBOOK::__draft_get_by_uid($uid);
    if(!$r) {
      $r=EXBOOK::__draft_create($uid);
    }
    return API::data($r);
  }

  /**
   *  API:
   *    /exbook/draft_get_deleted
   *    自动按uid查找一个已删除的draft
   *  输入: 
   *    uid
   *  
   *  返回:
   *    各字段
   */
  public static function draft_get_deleted( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $r=EXBOOK::__draft_get_deleted_by_uid($uid);
    return API::data($r);
  }

  /**
   *  API:
   *    /exbook/draft_delete
   *  输入: 
   *    uid
   *    fid
   *  
   *  返回:
   *    1 or 0 表示有无更新
   */
  public static function draft_delete( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $fid=API::INP('fid');
    //要确保fid是对应一个存在的数据
    $r=EXBOOK::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }
    $r=EXBOOK::__feed_delete( $fid );
    return API::data($r);
  }

  /**
   *  API:
   *    /exbook/draft_undelete
   *  输入: 
   *    uid
   *    fid
   *  
   *  返回:
   *    1 or 0 表示有无更新
   */
  public static function draft_undelete( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $fid=API::INP('fid');
    //要确保fid是对应一个存在的数据
    $r=EXBOOK::__feed_get($uid,$fid,'draft',true ); //true 表示可以是已删除的
    if(API::is_error($r)){
      return $r;
    }
    
    if(!$r['data']['del'])
      return API::msg(202201,"fid $fid was normal");
    $r=EXBOOK::__feed_undelete( $fid );
    return API::data($r);
  }
  
  /**
   *  API:
   *    /exbook/draft_update
   *  输入: 
   *    uid
   *    fid
   *    其他要更新的字段 : EXBOOK::__data_all()函数
   *  
   *  返回:
   *    1 or 0 表示有无更新
   *  
   */
  public static function draft_update( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');
    $fid=API::INP('fid');
    
    //要确保fid是对应一个存在的数据
    $r=EXBOOK::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }

    $data=EXBOOK::__data_all();
    $r=EXBOOK:: __feed_update($fid,$data);
    return API::data($r);
  }

  /**
   *  API:
   *    /exbook/draft_publish
   *  输入: 
   *    uid
   *    fid
   *  
   *  返回:
   *    1 or 0 表示有无更新
   */
  public static function draft_publish( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');

    $uid=API::INP('uid');    
    $fid=API::INP('fid');
    //要确保fid是对应一个存在的数据
    $r=EXBOOK::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }
    $err=EXBOOK::__feed_validate($r['data']);
    if($err){
      return API::msg(202002,$err);
    }
    $r=EXBOOK::__draft_publish($fid );
    return API::data($r);
  }
  
  //fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
  
  /**
   *  API:
   *    /exbook/feed_get
   *  输入: 
   *    uid
   *    fid 
   *  
   *  返回:
   *    各字段
   */
  public static function feed_get( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $fid=API::INP('fid');
    $r=EXBOOK::__feed_get($uid,$fid,'publish');
    return $r;
  }
 
  /**
   *  API:
   *    /exbook/feed_list
   *  输入: 
   *    uid
   *  
   *  返回:
   *    各字段
   */
  public static function feed_list( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(202001,'error userVerify');
    
    $uid=API::INP('uid');    
    $r=EXBOOK::__feed_list($uid,'publish');
    return $r;
  }
 
}

