<?php


class Auth {
  public function __construct() {
    if(!($_SERVER['PHP_AUTH_USER'] == "18") AND ($_SERVER['PHP_AUTH_PW'] == "showmethenudes")) {
      header("WWW-Authenticate: " ."Basic realm=\"\"");
      header("HTTP/1.0 401 Unauthorized");
      die;
    }
  }
}
