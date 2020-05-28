<?php
session_start();
 
mysql_connect ("localhost","root","");
mysql_select_db ("registr");
mysql_query("SET NAMES utf8");
 
$login = $_SESSION['login'];
$password = $_SESSION['password'];
$id_user = $_SESSION['id'];
?>