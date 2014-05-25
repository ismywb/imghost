<?php
class IndexPage {
  private static $c = 0;
  private static $folders = array();
  public function __construct($like = "") { self::output(self::getData($like),$like);}
  private static function output($a,$like) {
    $sizeOfAll = new TotalServed();
    $folder = is_array($like)?"/":$like;
    if (!isset($_GET['all'])) {
      echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">';
      echo '<html><head><title>Index</title></head><body>';
      echo '<h1>Index of '.$folder.'</h1>';
      
      echo '<pre>      Name';
      echo str_repeat(" ",50);
      echo 'Last Visited';
      echo str_repeat(" ",25);
      echo 'Size';
      echo str_repeat(" ",25);
      echo 'Views<hr>';
      echo $a;
      echo '<hr></pre><address>';
   //   echo $_SERVER['SERVER_SIGNATURE'];
      
      echo $sizeOfAll;
      echo '</address></body></html>';
      die();
    } else {
      header("Content-type:text/plain");
      die($a);
    }
  }

  private static function getFolders($like) {
    if (!Config::$showFolders) return '';
    if (isset($_GET['all'])) return;
    if (@$like[0] == "/") $like = "";
    $numberOfSlashThereShouldBe = substr_count($like,"/");
    $class = 'b';
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $order = "`name` ASC";
    $sql = "select * from imgs where `name` LIKE '".$like."%' order by ".$order;
    $sql = mysql_query($sql) or die(mysql_error());
    if (substr_count($like,"/") > 0 && $like != "") {
      $a = '      <a href="../">../</a>'.str_repeat(" ",56)."-".str_repeat(" ",32)."-".str_repeat(" ",29)."-\n";
    } else {
      $a = "";
    }
    while($img = mysql_fetch_array($sql)) {
      $numberOfSlashThereAre = substr_count($img['name'],"/");
      $folder = preg_replace("/(.*)\/(.*)/","$1",$img['name']);
      if (preg_match("/(.*)\.(png|gif|jpg|jpeg)/is",$folder)) {
	continue;
      }
      $folder = explode("/",$folder);
      if (@$folder[$numberOfSlashThereShouldBe] == '') {
	continue;
      }
      if (in_array($folder[$numberOfSlashThereShouldBe],self::$folders)) {
	continue;
      }
      self::$folders[] = $folder[$numberOfSlashThereShouldBe];
      if (self::$c == 1) $a .= "\n";
      $a .= '      <a href="'.$folder[$numberOfSlashThereShouldBe].'/">';
      $a .= $folder[$numberOfSlashThereShouldBe]."/";
      $a .= '</a>';
      $a .= str_repeat(" ",54-strlen($folder[$numberOfSlashThereShouldBe]."/"));
      $a .= ($img['lastaccess'] == '-1')?'Never':date ("M d Y h:i:s A",$img['lastaccess']);
      $a .= str_repeat(" ",25+12-strlen(($img['lastaccess'] == '-1')?'Never':date ("M d Y h:i:s A",$img['lastaccess'])));
      $a .= "-";
      $a .= str_repeat(" ",25+4);
      $a .= $img['views'];
      self::$c = 1;
    }
    return $a;
  }
  private static function getIMGFileSize($size,$views) {
    $bytes = $size;
    $bytes2 = $bytes*$views;
    $decimals = 2;
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    $factor2 = floor((strlen($bytes2) - 1) / 3);
    $size = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    $total = "(".sprintf("%.{$decimals}f", $bytes2 / pow(1024, $factor2)) . @$sz[$factor2]." xfer)";
    return $size . str_repeat(" ",8-strlen($size)) . $total;
  }
  private static function getData($like) { 
    if ($like[0] == "/") $like = "";
    $numberOfSlashThereShouldBe = substr_count($like,"/");
    $a = "";
    $a .= self::getFolders($like);
    $class = 'b';
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $order = "`id` ASC";
    $where = '';
    if (Config::$showFolders) $where = "WHERE `name` LIKE '".$like."%' ";
    $sql = "select `name`,`img`,`lastaccess`,`filezise`,`views`,`md5` from imgs ".$where."order by ".$order;
    $sql = mysql_query($sql);
    if (mysql_num_rows($sql) < 1 && $like != "") {die('404');}
    while($img = mysql_fetch_array($sql)) {
    $name = $img['name'];
    if (!Config::$showFolders) { $name = $img['img'].".png";} 
if (Config::$showFolders) {
  $numberOfSlashThereAre = substr_count($img['name'],"/");

                        if ($numberOfSlashThereShouldBe != $numberOfSlashThereAre && !isset($_GET['all'])) {
                            continue;
                        }
}
      if (isset($_GET['all'])) {
	   
	echo $img['md5'].".png\n";
	continue;
      }
      if (self::$c == 1) $a .= "\n";
      $a .= '      <a href="/'.$name.'">';
      $a .= preg_replace("/".str_replace('/','\/',$like)."(.*)/","$1",$name);
      $a .= "";
      $a .= '</a>';
      $a .= str_repeat(" ",54-strlen(preg_replace("/".str_replace('/','\/',$like)."(.*)/","$1",$name).""));
      $a .= ($img['lastaccess'] == '-1')?'Never':date ("M d Y h:i:s A",$img['lastaccess']);
      $a .= str_repeat(" ",25+12-strlen(($img['lastaccess'] == '-1')?'Never':date ("M d Y h:i:s A",$img['lastaccess'])));
      $a .= self::getIMGFileSize($img['filezise'],$img['views']);
      $a .= str_repeat(" ",25+5-strlen(self::getIMGFileSize($img['filezise'],$img['views'])));
      $a .= $img['views'];
      self::$c = 1;
    }
    return $a;
  }
}
