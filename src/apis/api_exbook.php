<?php
// ================================
/*
*/

class class_exbook{
  public static function main( $para1,$para2) {
    $res=API::data(['time'=>time().' - exbook is ready.']);
    return $res;
  }
  
  //test
  public static function test( ) {
    
    $r=USER::userVerify();
    return API::data($r);
  }

}
