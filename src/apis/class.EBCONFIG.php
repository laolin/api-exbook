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
      'all'=>'各年级',
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
      'other'=>'其他科目'
    ];
    return $cfg;
  }
}
