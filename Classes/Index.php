<?php
class Index {
  public static $f = '';
  public function __construct() {
    self::$f = preg_replace("/\/(.*)/is","$1",$_SERVER["REQUEST_URI"]);
    if (preg_match("/(.*)\.(png|gif|jpg|jpeg)/is",self::$f)) {
      self::go();
      die();
    }
    self::ShowMenu();
    die();
    self::go();
  }
  private static function go() {
    if (preg_match("/htt(p|ps):\/\/(.*)/",self::$f)) {
      $img = self::$f;
      self::$f = explode(":",self::$f);
      $name = '';
      if (count(self::$f) == 1) {
	self::$f = self::$f[0];
	$name = preg_replace("/(.*)\/(.*)/is","$2",$img);
      } else {
	$name = self::$f[0].".png";
	unset(self::$f[0]);
	self::$f = implode(":",self::$f);
      }
      Index::AddIMG($name,self::$f,1);
      die();
    } else {
      self::displayImage();
      die();
    }
  }
  public static function AddIMG($name=null,$url=null,$redir=0) {
    $hash = md5(sha1(md5(sha1(base64_encode($name)))));
    $ext = preg_replace("/(.*)\.(.*)/is","$2",$url);
    if (!in_array(strtolower($ext),array("jpg","jpeg","gif","png")) && !preg_match("/https:\/\/i.chzbgr.com\/(.*)/is",$url))
      if ($redir) die('403'); else return;
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $data = file_get_contents($url);
    if (!$data) { if ($redir) die("Upload Failed"); else return; }
    if (!file_put_contents(PATH."/img/".$hash.".png",$data)) {
      if ($redir) die('failed'); else return;
    }
    $md5 = md5($data);
    $sql = mysql_query("select * from `imgs` where `md5`='".$md5."'");

    if (mysql_num_rows($sql) != 0) {
      $img = mysql_fetch_array($sql);
      header("Location: /".$img['name']);
    }
    $sql = "insert into `imgs` set `name`='".addslashes(strtolower($name))."', `img`='".$hash."',`md5`='".$md5."'";

    mysql_query($sql);

    new AutoFixer();
    if ($redir == 1) {
      header("Location: /".$name."");
      die();
    }
  }
  private static function displayImage() {
    $name = self::$f;
    self::getIMG($name);
  }
  private static function ShowMenu() {
    if (@self::$f[strlen(self::$f)-1] != "/") {
      self::$f[strlen(self::$f)] = "/";
    }
    new IndexPage(self::$f);
  }

  private static function img($name) {
    $number = preg_replace("/(.*)\/([0-9]*)\.png/","$2",$name);
    $next_number = 0;
    mysql_connect(H,U,P);
    mysql_select_db(D);
    $s = mysql_query("select id from imgs where name = '".$name."' limit 1");
    $r = mysql_fetch_array($s);
    $cur_id = $r['id'];
    $sec = preg_replace("/(.*)\/(.*)/","$1",$name);
    $next_id = null;
    $s = mysql_query("select name from imgs where id > ".$cur_id." && name LIKE '".$sec."%' order by ID ASC limit 1");
    if (mysql_num_rows($s) == 0) $next_id = -1;  else {
      $r = mysql_fetch_array($s);
      $next_id = $r['name'];
    }
    $s = mysql_query("select name from imgs where id < ".$cur_id." && name LIKE '".$sec."%' order by ID DESC limit 1");
    $pre_id = null;
    if (mysql_num_rows($s) == 0) $pre_id = -1;  else {
      $r = mysql_fetch_array($s);
      $pre_id = $r['name'];
    }
    $js = "<script>
      function change(code) {
        // RIGHT: 39
        // LEFT:  37
        // UP:    38
        var key = code.keyCode;
        var next = '".$next_id."';
        var pre = '".$pre_id."';
        if (key == 39 ) { // RIGHT
          if (next != -1) {
            window.location = '/'+next;
          }
        }
        if (key == 37 ) { // RIGHT
          if (pre != -1) {
            window.location = '/'+pre;
          }
        }
        var sec = '".$sec."';
        if (key == 38 ) { // RIGHT
          window.location = '/'+sec;
        }
      }
    </script>";
    echo "<html><body onkeyup='change(window.event);' style='margin:0px;'>".$js."<img src='/".$name."?d' style='max-width:100%; max-height:100%' /></body></html>";
die;
    define('override',1);
    self::getIMG($name);
  }

  private static function getIMG($name) {
    if (!isset($_GET['d']) && !defined('override')) self::img($name);
    $name = preg_replace("/(.*)\?d/","$1",$name);
    mysql_connect(H,U,P);
    mysql_select_db(D);
    if (!Config::$showFolders)    $name = preg_replace("/(.*?)\.png/","$1",$name);
    $md5 = preg_replace("/(.*)\/(.*)\.png/","$2",$name);
    //    die($name);
    $sql = "select * from `imgs` where `name` = '".strtolower(addslashes($name))."' OR `img` = '".$name."' OR `md5`='".$md5."'";
    $sql2 = mysql_query($sql);
    if (mysql_num_rows($sql2) < 1) { die('404');}
    $img = mysql_fetch_array($sql2);
    if (isset($_GET['md5'])) { $md5 = $img['md5']; header("Location: /direct/".$md5.".png");die(); }
    $id = $img['id'];
    $time = time();
    $views = $img['views'] + 1;
            $sql3 = "update `imgs` set
                `views`='{$views}',
                `lastaccess`='{$time}'
                 where `id`='{$id}'
                limit 1";
	    mysql_query($sql3) or die(mysql_error());
	    $data = @file_get_contents(PATH."/img/".$img['img'].'.png');
	    if (!$data) {mysql_query("delete from `imgs` where `id`='".$img['id']."' limit 1");die('Bad Image! Removed from site.');}
	    header("Content-type:image/png");
	    die($data);
  }
}
