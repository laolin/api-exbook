<?php
// ================================
/*
*/

class EBCONFIG{
  const EB_RIGHTS_USER    = 0x00000001;
  
  const EB_RIGHTS_ADMIN   = 0x00010000;
  
  
  //先静态写死，以后改数据库
  public static function config( ) {
    $cfg=[];
    $cfg['gradeList']=[
      'all'=>'生活用品',
      'x1'=>'家用电器',
      'x2'=>'家具',
      'x3'=>'家纺',
      'x4'=>'图书',
      'x5'=>'玩具',
      'x6'=>'车位空闲时间'
    ];
    $cfg['courseList']=[
      'yu'=>'小区一',
      'shu'=>'小区二',
      'ying'=>'住宅三',
      'other'=>'小区4'
    ];
    return $cfg;
  }
}
