<?php
class TotalServed {
  public function __toString() {
    mysql_connect(H,U,P);
    $p = P;
    $size2 = `du -sch /home/porn/html/img/ | grep -i total`;
    $r =  'We are hosting: '.$size2.' in images';
    return $r; 
  }
  private function getImageSize($id,$o=false) {
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $item = mysql_query("select `views`,`filezise` from `imgs` where `id`='".$id."' limit 1") or die(mysql_error());
    $item = mysql_fetch_array($item);
    if ($o) {
      return $item['filezise'];
    }
    return($item['views'] * $item['filezise']);
    
  }
}
