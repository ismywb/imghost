<?php

class AutoFixer {
  public function __construct() {
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $query = "select * from imgs where filezise=0";
    $items = mysql_query($query);
    while ($item = mysql_fetch_array($items)) {
      $md5 = md5(file_get_contents(PATH."/img/".$item['img'].".png"));
      $size = filesize(PATH."/img/".$item['img'].".png");
      if($md5 != $item['md5']) {
        mysql_query("delete from `imgs` where `id`='".$item['id']."' limit 1");
        unlink(PATH."img/".$item['img'].".png");
        continue;
      }
      mysql_query("update `imgs` set `md5`='".$md5."', filezise='".$size."' where id='".$item['id']."'") or die(mysql_error());
    }
  }
}
