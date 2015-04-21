<?php
define('DEV',1);
define('PATH',"/home/jerry/ismywb.co/");
require_once(PATH."Classes/Defines.php");
foreach(glob(PATH."Classes/*.php") as $file) {
  require_once($file);
}
if (DEV) {
  ini_set("display_errors",error_reporting(E_ALL));
}
