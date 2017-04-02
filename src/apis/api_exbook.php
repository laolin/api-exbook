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
      return API::msg(202201,"fid $fid was normal");
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
    $err=self::__feed_validate($r['data']);
    if($err){
      return API::msg(202002,$err);
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
    $r=self::__feed_list($uid,'publish');
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
    self::__data_val('pics',$data);
    self::__data_val('grade',$data);
    self::__data_val('course',$data);
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
      'pics'=>'',
      'update_at'=>time(),
      'grade'=>'0',
      'course'=>'0',
      'tags'=>'',
      'anonymous'=>'1'
    ];
    $r=$db->insert($tblname,$data );
    if(!$r)return false;
    $data['fid']=$r;
    return $data;
  }

  // -R-- 获取 uid 的最新【草稿】
  static function __draft_get_by_uid( $uid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      ['fid','uid','flag','del','content','pics','create_at','update_at','grade','course','tags','anonymous'],
      ['and'=>['uid'=>$uid,'flag'=>'draft','del'=>0]]);
    
    return $r;
  }
  
  // -R-- 获取 uid 的最新【已删除草稿】
  static function __draft_get_deleted_by_uid( $uid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      ['fid','uid','flag','del','content','pics','create_at','update_at','grade','course','tags','anonymous'],
      ['and'=>['uid'=>$uid,'flag'=>'draft','del'=>1]]);
    
    return $r;
  }
  
  // --U- 更新 草稿 同  feed

  // ---D 删除 草稿 同  feed

  //发布 【草稿】
  static function __draft_publish( $fid ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');

    //发布的算法：
    //1,复制一份草稿为 正式 publish
    //2,然后把草稿文字内容清空（科目等保留）
    $sth = $db->pdo->prepare("INSERT INTO $tblname 
      (`content`,`pics`,`grade`,`course`,`anonymous`,
        `uid`,`flag`,`del`,`publish_at`)
      SELECT `content`,`pics`,`grade`,`course`,`anonymous`,
        :uid ,'publish','0', :now
      FROM $tblname 
      WHERE fid = $fid" );
 
    $sth->bindParam(':uid', API::INP('uid'), PDO::PARAM_INT);
    $sth->bindParam(':now', time(), PDO::PARAM_INT);
     
    $sth->execute();
    
    return self::__feed_update($fid,['content'=>'','pics'=>'']);
  }
  
  
  //WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW
  static function __feed_columns() {
    return ['fid','uid','flag','del','content','pics','publish_at','update_at','grade','course','tags','anonymous'];
  }
  // C--- 不提供创建 feed 的接口，使用草稿的发布功能创建 feed
  
  // -R-- 获取
  static function __feed_get( $uid, $fid, $type='publish',$include_del=false ) {
    if(!$fid) {
      return API::msg(202101,"fid required");
    }
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    $r=$db->get($tblname,
      self::__feed_columns(),
      ['and'=>['fid'=>$fid]]);
      
      
      
    if(!$r) {
      return API::msg(202102,"fid $fid not exist");
    }
    
    //草稿只允许自己看
    if($type=='draft' && $r['uid']!=$uid) {
      return API::msg(202103,"draft $fid is not belongs to uid $uid");
    }
    if($r['flag']!=$type) {
      return API::msg(202104,"fid $fid is not $type");
    }
    if( !$include_del && $r['del']) {
      return API::msg(202105,"fid $fid was deleted yet");
    }
    return API::data($r);
  }
  
  // -R-- 获取
  static function __feed_list( $uid, $type='publish',$include_del=false ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
    
    $andArray=[];
    $tik=0;
    
    $oldmore=API::INP('oldmore');
    if($oldmore) {
      $tik++;
      $andArray["and#t$tik"]=['publish_at[<]'=>intval($oldmore)];
    }
    $newmore=API::INP('newmore');
    if($newmore) {
      $tik++;
      $andArray["and#t$tik"]=['publish_at[>]'=>intval($newmore)];
    }
    
    
    
    $tik++;
    $and=['flag'=>$type];
    $andArray["and#t$tik"]=$and;
    

    $and_DEL=false;
    if($include_del=='only') {
      $and_DEL=['del'=>1];
    } else if( ! $include_del) {
      $and_DEL=['del'=>0];
    }
    if($and_DEL) {
      $tik++;
      $andArray["and#t$tik"]=$and_DEL;
    }
    
    
    $count=intval(API::INP('count'));
    if($count==0)$count=20;
    else if($count<2)$count=20;
    else if($count>200)$count=200;
    
    $where=["LIMIT" => $count , "ORDER" => ["publish_at DESC", "update_at DESC"]] ;
    if(count($andArray))
      $where['and'] = $andArray ;


    $r=$db->select($tblname,self::__feed_columns(),$where);
      
      
    //var_dump($db);
    if(!$r) {
      return API::msg(202003,"nothing");
    }
    
    return API::data($r);
  }

  // --U- 更新
  static function __feed_update( $fid, $data ) {
    $db=api_g('db');
    $tblname=self::__table_name('eb_feed');
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

  //判断是否有效，主要用于草稿发布为正式feed  
  static function __feed_validate($feed) {
    $err='';
    if( !$feed['content'] && !$feed['pics']) {
      $err.='发布内容是空的。';
    }
    if( !$feed['grade']) {
      $err.='未选择年级。';
    }
    if( !$feed['course']) {
      $err.='未选择科目。';
    }
    return $err;
  }
  // 撤销删除
  static function __feed_undelete( $fid ) {
    return self::__feed_update($fid,['del'=>0]);
  }
  // 撤销为草稿
  static function __feed_to_draft( $fid ) {
    return self::__feed_update($fid,['flag'=>'draft']);
  }
}
