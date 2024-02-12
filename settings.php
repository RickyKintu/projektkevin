<?php
$_SITE['id'] = "projektkevin";
$_SITE['name'] = "Projekt Kevin";
$_SITE['title'] = $_SITE['name'] . ".com - Extra titel text";
$_SITE['url'] = "https://kevin.com/";


$_SITE['local'] = true;

include 'config.php';
if ($_SITE['local'] === true) {
    $_SITE['url'] = "http://localhost/kevin/";
    $use_online_db = false;
} else {
    $use_online_db = true;
}



$db_config = $use_online_db ? $config['online'] : $config['local'];
$con = mysqli_connect($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['dbname']);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
