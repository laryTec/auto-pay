<?php
if (!isset($_SESSION)) {
    session_start();
}
try {
    //require_once "../include/config.php";

    $db_host = gamedbip;
    $db_userName = gamedbuserName;
    $db_password = gamedbpassword;
    $db_name = gamedb;
    if (!isset($_POST["server"])) {
        $db_name = $_SESSION["server"];
    } else {
        if ($_POST["server"] >= "1" && $_POST["server"] <= count($serverarr)) {
            $serveritem = $serverarr[$_POST["server"]];
            $db_name = $serveritem [1];
        } else {
            die('資料庫連結失敗!');
        }
        $_SESSION["server"] = $db_name;
    }

    //$db_link = @mysqli_connect($db_host, $db_userName, $db_password, $db_name);
    $mysqli = new mysqli($db_host, $db_userName, $db_password, $db_name);
    if (!$mysqli) {
        die('資料庫連結失敗!');
    } else {
        //echo '資料庫連結成功';
    }
    mysqli_query($mysqli, "SET NAMES 'utf8'");  //設定資料庫編碼 utf8

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
