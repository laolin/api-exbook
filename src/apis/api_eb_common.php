<?php
// ================================
/*
*/

class class_eb_common{
  public static function main( $para1,$para2) {
    $res=API::data(['time'=>time().' - exbook is ready.']);
    return $res;
  }
  static function userVerify() {
    return USER::userVerify();
  }
  
  //先静态写死，以后改数据库
  public static function config( ) {
    $r=self::userVerify();
    if(!$r)
      return API::msg(1001,'error userVerify');
    $cfg=[];
    $cfg['gradeList']=[
      'all'=>'不限',
      'x1'=>'一年级',
      'x2'=>'二年级',
      'x3'=>'三年级',
      'x4'=>'四年级',
      'x5'=>'五年级',
      'x6'=>'六年级'
    ];
    $cfg['courseList']=[
      'yu'=>'语文',
      'shu'=>'数学',
      'ying'=>'英语',
      'other'=>'其他'
    ];
    return API::data($cfg);
  }
}
