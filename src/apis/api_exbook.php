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
  public static function config( ) {
    $cfg=[];
    $cfg['data_app']='exbook';
    
    $data_define=[];
    $data_define[]=[
      'column'=>'d1',
      'name'=>'年级',
      'type'=>'radio',
      'data'=>[
        'x0'=>'幼升小',
        'x1'=>'一年级',
        'x2'=>'二年级',
        'x3'=>'三年级',
        'x4'=>'四年级',
        'x5'=>'五年级/小升初',
        'x6'=>'初中以上'
      ]
    ];
    $data_define[]=[
      'column'=>'d2',
      'name'=>'科目',
      'type'=>'radio',
      'data'=>[
        'yu'=>'语文',
        'shu'=>'数学',
        'ying'=>'英语',
        'other'=>'其他'
      ]
    ];

    $cfg['data_define']=$data_define;
    return API::data($cfg);
  }  
  

}

