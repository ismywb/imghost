<?php
define('DEV',1);
require_once("/home/hosterslice/html/Classes/Defines.php");
foreach(glob("/home/hosterslice/html/Classes/*.php") as $file) {
  require_once($file);
}
if (isset($_GET['fix'])) { new AutoFixer();die();}
if (DEV) {
  ini_set("display_errors",error_reporting(E_ALL));
}
