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
    $cfg['grade-list']=[
      0=>'不限',
      1=>'一年级',
      2=>'二年级',
      3=>'三年级',
      4=>'四年级',
      5=>'五年级',
      6=>'六年级'
    ];
    $cfg['course-list']=[
      1=>'语文',
      2=>'数学',
      3=>'英语',
      99=>'其他'
    ];
    return API::data($cfg);
  }
}
