<?php
define('DEV',1);
define('PATH',"/home/hosterslice/html/");
require_once(PATH.Classes/Defines.php");
foreach(glob(PATH."Classes/*.php") as $file) {
  require_once($file);
}
if (isset($_GET['fix'])) { new AutoFixer();die();}
if (DEV) {
  ini_set("display_errors",error_reporting(E_ALL));
}
