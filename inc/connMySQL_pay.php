<?php
if (!isset($_SESSION)) {
    session_start();
}
try {
    $db_host2 = paydbip;
    $db_userName2 = paydbuserName;
    $db_password2 = paydbpassword;
    $db_name2 = paydb;
    $mysqli2 = new mysqli($db_host2, $db_userName2, $db_password2, $db_name2);
    if (!$mysqli2) {
        die('pay資料庫連結失敗!');
    } else {
        //echo '資料庫連結成功';
    }
    mysqli_query($mysqli2, "SET NAMES 'utf8'");  //設定資料庫編碼 utf8


} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
