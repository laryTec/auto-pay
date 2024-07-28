<?php
require_once dirname(__FILE__) . "/include/dbconfig.php";
require_once dirname(__FILE__) . "/inc/connMySQL_pay.php";

if (strtoupper(Pay_Vender)=="ECPAY"){
    if (isset($CheckMacValue)) {
        if ($CheckMacValue != $_POST['CheckMacValue']) {
            echo "0|ERROR";
            die(); 
        }
    } else {
        echo "0|ERROR";
        die();
    }
}
if (isset($TradeNo)) {
    $sql_query1 = " select * from record where TradeNo='" . $TradeNo . "'";
}
if (isset($MerchantTradeNo)) {
    $sql_query1 = " select * from record where MerchantTradeNo='" . $MerchantTradeNo . "'";
}

$result = $mysqli2->query($sql_query1);
$row = $result->fetch_array();
$_SESSION["server"] = $row["db_name"];

//新增 ezpay
require dirname(__FILE__) . "/inc/connMySQL.php";
$db_gamemoney = mysqli_real_escape_string($mysqli,$row["gamemoney"]);
$db_loginid = mysqli_real_escape_string($mysqli,$row["loginId"]);
$db_charname = mysqli_real_escape_string($mysqli,$row["char_name"]);
$pay_status = mysqli_real_escape_string($mysqli,$row["pay_status"]);
$sql_query = " INSERT INTO ezpay (ordernumber,amount,payname,state)  VALUES (" . time() . " ,$db_gamemoney,'$db_loginid',1)";
$result1 = $mysqli->query($sql_query);

if ($result1 === true) {
    //更新 人物 money_count
/*    $sql_query = " update characters set money_count = money_count+$db_gamemoney where char_name='$db_charname' ";
    $result2 = $mysqli->query($sql_query);
    if ($result1 === true) {
        //if ($mtrecall) echo "<script>window.close()</script>";
    } else {
        //if ($mtrecall) echo "<script>alert('失敗') window.close()</script>";
        $returnCode = '0|ERROR';
    }
    */
} else {
    //if ($mtrecall) echo "<script>alert('失敗') window.close()</script>";
    $returnCode = '0|ERROR';
}
