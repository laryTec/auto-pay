<?php

require_once dirname(__FILE__) . "/include/config.php";
require_once dirname(__FILE__) . "/include/dbconfig.php";
require_once dirname(__FILE__) . "/include/mwt-newebpay_sdk.php";

try {


    $TradeInfo = file_get_contents("php://input");

    $arr = mb_split("&",$TradeInfo);
    $get_aes = str_replace("TradeInfo=","",$arr[3]);
    
    $data = create_aes_decrypt($get_aes,newebpay_HashKey,newebpay_HashIV);
    $json = json_decode($data);

    if($json->Status == "SUCCESS"){
        require_once dirname(__FILE__) . "/inc/connMySQL_pay.php";
        $MerchantTradeNo = mysqli_real_escape_string($mysqli2, $json->Result->MerchantOrderNo);

        $sql_query1 = " select pay_status from record where MerchantTradeNo='" . $MerchantTradeNo . "'";
        $result_status = $mysqli2->query($sql_query1);
        $row = $result_status->fetch_array();
        if ($row != FALSE) {
            if ($row["pay_status"] == "1") {
                $sql_query = " update record set pay_status = 2 , upd_date =DATE_ADD(NOW(),INTERVAL 13 HOUR) where MerchantTradeNo='" . $MerchantTradeNo . "'";
                $result_name = $mysqli2->query($sql_query);
                $returnCode = '1|OK';
                if ($result_name === true) {
                    //觸發更新遊戲
                    require_once dirname(__FILE__) . "/writeToGameDB.php";

                    echo $returnCode;
                } else {
                    echo '0|ERROR';
                }
            }
            else{
                echo '1|OK';
            }
        } else {
            echo '0|ERROR';
        }
    }

} catch (Exception $e) {
    echo '0|' . $e->getMessage();
}
