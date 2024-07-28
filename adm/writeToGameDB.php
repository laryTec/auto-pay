<?php
require "header.php";
require_once  "../include/dbconfig.php";
require_once  "../inc/connMySQL_pay.php";

$mtrecall = false;
if (isset($TradeNo) && isset($_GET["TradeNo"])) {
    $sql_query1 = " select * from record where TradeNo='" . $TradeNo . "'";
}
if (!isset($TradeNo) && isset($_GET["TradeNo"])) {
    $sql_query1 = " select * from record where TradeNo='" . $_GET["TradeNo"] . "'";
    $mtrecall = true;
}
$result = $mysqli2->query($sql_query1);
$row = $result->fetch_array();
$_SESSION["server"] = $row["db_name"];
$mtpay_status = 0;

$pay_status = mysqli_real_escape_string($mysqli2, $row["pay_status"]);
$g_TradeNo = mysqli_real_escape_string($mysqli2, $_GET["TradeNo"]);

if ($mtrecall) {
    if ($pay_status == 1) $mtpay_status = 3;
    if ($pay_status == 2) $mtpay_status = 4;
    if ($pay_status == 3) $mtpay_status = 3;
    if ($pay_status == 4) $mtpay_status = 4;
    $sql_update = " update record set pay_status = $mtpay_status , upd_date =DATE_ADD(NOW(),INTERVAL 13 HOUR) where TradeNo='" . $g_TradeNo . "'";
    $result_name = $mysqli2->query($sql_update);
}

require_once "../inc/connMySQL.php";
$db_gamemoney = mysqli_real_escape_string($mysqli, $row["gamemoney"]);
$db_loginid = mysqli_real_escape_string($mysqli, $row["loginId"]);
$db_charname = mysqli_real_escape_string($mysqli, $row["char_name"]);
//新增 ezpay
$sql_query = " INSERT INTO ezpay (ordernumber,amount,payname,state)  VALUES (" . time() . " ,$db_gamemoney,'$db_loginid',1)";
$result1 = $mysqli->query($sql_query);

if ($result1 === true) {
    //更新 人物 money_count
/*    $sql_query = " update characters set money_count = money_count+$db_gamemoney where char_name='$db_charname' ";
    $result2 = $mysqli->query($sql_query);
    if ($result1 === true) {
        if ($mtrecall) echo "<script>alert('完成') ;window.close()</script>";
    } else {
        if ($mtrecall) echo "<script>alert('失敗') ;window.close()</script>";
        //$returnCode = '0|ERROR';
    }
    */
} else {
    if ($mtrecall) echo "<script>alert('失敗') ;window.close()</script>";
    //$returnCode = '0|ERROR';
}
