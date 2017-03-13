<?php
// ================================
/*
*/

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
      return API::msg(1001,'error userVerify');
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
    $r=self::__draft_get_by_uid($uid);
    if(!$r) {
      $r=self::__draft_create($uid);
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
    $r=self::__feed_get($uid,$fid,'draft');
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
    $r=self::__draft_get_by_uid($uid);
    if(!$r) {
      $r=self::__draft_create($uid);
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
    $r=self::__draft_get_deleted_by_uid($uid);
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
    $r=self::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }
    $r=self::__feed_delete( $fid );
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
    $r=self::__feed_get($uid,$fid,'draft',true ); //true 表示可以是已删除的
    if(API::is_error($r)){
      return $r;
    }
    
    if(!$r['data']['del'])
      return API::msg(203001,"fid $fid was normal");
    $r=self::__feed_undelete( $fid );
    return API::data($r);
  }
  
  /**
   *  API:
   *    /exbook/draft_update
   *  输入: 
   *    uid
   *    fid
   *    其他要更新的字段 : 详见self::__data_all()函数
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
    $r=self::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }

    $data=self::__data_all();
    $r=self:: __feed_update($fid,$data);
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
    $r=self::__feed_get($uid,$fid,'draft');
    if(API::is_error($r)){
      return $r;
    }
    
    $r=self::__draft_publish($fid );
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
    $r=self::__feed_get($uid,$fid,'publish');
    return $r;
  }
  
  
  //MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
  //===================================================================
  
  //获取 数据表名
  static function __table_name( $item ) {
    $prefix=api_g("api-table-prefix");
    return $prefix.$item;
  }
  
  static function __data_val( $key, & $data ) {
    if(false === API::INP($key)) return;
    $data[$key]=API::INP($key);
  }
  
  //TODO: 有效性检查
  static function __data_all( ) {
    $data=[];
    self::__data_val('content',$data);
    self::__data_val('pic1',$data);
    self::__data_val('pic2',$data);
    self::__data_val('pic3',$data);
    self::__data_val('pic4',$data);
    self::__data_val('pic5',$data);
    self::__data_val('pic6',$data);
    self::__data_val('pic7',$data);
    self::__data_val('pic8',$data);
    self::__data_val('pic9',$data);
    self::__data_val('grade',$data);
    self::__data_val('course_id',$data);
    self::__data_val('tags',$data);
    self::__data_val('anonymous',$data);
    return $data;
  }
  
  //-----------------------------------------------
  
  // C--- 新建  【草稿】
  static function __draft_create( $uid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $data=[
      'uid'=>$uid,
      'flag'=>'draft',
      'del'=>0,
      'content'=>'',
      'pic1'=>'',
      'pic2'=>'',
      'pic3'=>'',
      'pic4'=>'',
      'pic5'=>'',
      'pic6'=>'',
      'pic7'=>'',
      'pic8'=>'',
      'pic9'=>'',
      'update_at'=>time(),
      'grade'=>'',
      'course_id'=>'1',
      'tags'=>'',
      'anonymous'=>'1'
    ];
    $r=$db->insert($tblname,$data );
    if(!r)return false;
    $data['fid']=$r;
    return $data;
  }

  // -R-- 获取 uid 的最新【草稿】
  static function __draft_get_by_uid( $uid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      ['fid','uid','flag','del','content','pic1','pic2','pic3','pic4','pic5','pic6','pic7','pic8','pic9','create_at','update_at','grade','course_id','tags','anonymous'],
      ['and'=>['uid'=>$uid,'flag'=>'draft','del'=>0]]);
    
    return $r;
  }
  
  // -R-- 获取 uid 的最新【已删除草稿】
  static function __draft_get_deleted_by_uid( $uid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      ['fid','uid','flag','del','content','pic1','pic2','pic3','pic4','pic5','pic6','pic7','pic8','pic9','create_at','update_at','grade','course_id','tags','anonymous'],
      ['and'=>['uid'=>$uid,'flag'=>'draft','del'=>1]]);
    
    return $r;
  }
  
  // --U- 更新 草稿 同  feed

  // ---D 删除 草稿 同  feed

  //发布 【草稿】
  static function __draft_publish( $fid ) {
    return self::__feed_update($fid,['flag'=>'publish']);
  }
  
  
  //WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW
  // C--- 不提供创建 feed 的接口，使用草稿的发布功能创建 feed
  
  // -R-- 获取
  static function __feed_get( $uid, $fid, $type='piblish',$include_del=false ) {
    if(!$fid) {
      return API::msg(200001,"fid required");
    }
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      ['fid','uid','flag','del','content','pic1','pic2','pic3','pic4','pic5','pic6','pic7','pic8','pic9','create_at','update_at','grade','course_id','tags','anonymous'],
      ['and'=>['fid'=>$fid]]);
      
      
      
    if(!$r) {
      return API::msg(200002,"fid $fid not exist");
    }
    
    //草稿只允许自己看
    if($type=='draft' && $r['uid']!=$uid) {
      return API::msg(200003,"draft $fid is not belongs to uid $uid");
    }
    if($r['flag']!=$type) {
      return API::msg(202011,"fid $fid is not $type");
    }
    if( !$include_del && $r['del']) {
      return API::msg(200012,"fid $fid was deleted yet");
    }
    return API::data($r);
  }
  // --U- 更新
  static function __feed_update( $fid, $data ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $data['update_at']=time();
    $r=$db->update($tblname, $data,
      ['and'=>['fid'=>$fid],'LIMIT'=>1]);
    return $r;
  }
  // ---D 删除
  static function __feed_delete( $fid ) {
    return self::__feed_update($fid,['del'=>1]);
    /*$db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    
    $r=$db->delete($tblname, 
      ['and'=>['fid'=>$fid],'LIMIT'=>1]);
    */
  }
  
  //QQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQ
  
  // 撤销删除
  static function __feed_undelete( $fid ) {
    return self::__feed_update($fid,['del'=>0]);
  }
  // 撤销为草稿
  static function __feed_to_draft( $fid ) {
    return self::__feed_update($fid,['flag'=>'draft']);
  }
}
