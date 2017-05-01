<?php
/**
 *  errcode: 204xxx
 *  
 *  (203501,"Error cmnt type") 
 *  (203502, validate err) 
 */


class LIVECOMMUNITY { 
  //获取 数据表名
  static function table_name( $item='livecommunity' ) {
    $prefix=api_g("api-table-prefix");
    return $prefix.$item;
  }
  static function columns() {
    return [
      'id','name','descript',
      'addr','lngE7','latE7',
      'province','city','citycode',
      'pics','mark'
    ];
  }
  
  //判断是否有效
  static function validate($data) {
    $err='';
    if(strlen($data['name'])<4)
      $err.='小区名太短。';
    if(strlen($data['addr'])<4)
      $err.='小区地址太短。';
    //中国东经 70~140度，
    if(intval($data['lngE7'])< 70E7 || intval($data['lngE7'])> 140E7)
      $err.='经度错。';
    //中国北纬 3~60度，
    if(intval($data['latE7'])< 3E7 || intval($data['latE7'])> 60E7)
      $err.='纬度错。';
    if(strlen($data['province'])<2 && strlen($data['city'])<2)
      $err.='省、市错。';//省市至少指定一个
    if(strlen($data['citycode'])<2)
      $err.='城市代码错。';
    return $err;
  }  

  // C--- 新建
  static function create(  ) {
    $db=api_g('db');
    $tblname=self::table_name();

    $name=API::INP('name');
    $descript=API::INP('descript');
    $addr=API::INP('addr');
    $lngE7=intval(API::INP('lngE7'));
    $latE7=intval(API::INP('latE7'));
    $province=API::INP('province');
    $city=API::INP('city');
    $citycode=API::INP('citycode');

    $data=[
      'name'=>$name,
      'descript'=>$descript,
      'addr'=>$addr,
      'lngE7'=>$lngE7,
      'latE7'=>$latE7,
      'province'=>$province,
      'city'=>$city,
      'citycode'=>$citycode,
      'mark'=>''
    ];
    $err=self::validate($data);
    if($err)
      return API::msg(203000,$err);
    
    $r=$db->insert($tblname,$data );
    //var_dump($db);
    if(!$r){
      return API::msg(203000,"Error unknow LvCm-create");
    }
    return API::data($r);;
  }

  
  // -R-- 获取
  static function li(  ) {
    $db=api_g('db');
    $tblname=self::table_name();
    
    $andArray=[];
    $tik=0;
    $andArray["and#t$tik"]=['mark'=>''];//非空即为删除的，不要
    
    
    $lnglat= API::INP('lnglat');
    if($lnglat) {
      $l_arr=explode(',',$lnglat);
      if(count($l_arr)>2 &&
        intval($l_arr[0])>70 && intval($l_arr[0])<140 &&
        intval($l_arr[1])>3 && intval($l_arr[1])<60
      ){
        $lng=intval(1e7*$l_arr[0]);
        $lat=intval(1e7*$l_arr[1]);
        $dist=2000;
        if(count($l_arr)==3) {
          $dist=intval($l_arr[2]);
        }
        if($dist<500)$dist=500;
        if($dist>5000)$dist=5000;
        
        $tik++;
        // 1米$dist 大致相当于1e-5度，乘1e7后再比，故要再乘以100
        $andArray["and#t$tik"]=[
          'lngE7[>]'=>$lng-100*$dist,
          'lngE7[<]'=>$lng+100*$dist,
          'latE7[>]'=>$lat-100*$dist,
          'latE7[<]'=>$lat+100*$dist,
        ];
      }
    }

    $count=200;
    $page=1;
    $lmt=[($page-1)*$count,$count];
    
    $where=["LIMIT" => $lmt , "ORDER" => ["id DESC"]] ;
    if(count($andArray))
      $where['and'] = $andArray ;

    $r=$db->select($tblname,self::columns(),$where);
    //var_dump($db);
    if(!$r) {
      return API::msg(202003,"nothing");
    }
    
    return API::data($r);
  }

}
