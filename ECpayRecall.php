<?php
require_once dirname(__FILE__) . '/include/ECPay.Payment.Integration.php';
require_once dirname(__FILE__) . "/include/config.php";
require_once dirname(__FILE__) . "/include/dbconfig.php";

try {
    $arParameters = $_POST;
    foreach ($arParameters as $keys => $value) {
        if ($keys != 'CheckMacValue') {
            if ($keys == 'PaymentType') {
                $value = str_replace('_CVS', '', $value);
                $value = str_replace('_BARCODE', '', $value);
                $value = str_replace('_CreditCard', '', $value);
            }
            if ($keys == 'PeriodType') {
                $value = str_replace('Y', 'Year', $value);
                $value = str_replace('M', 'Month', $value);
                $value = str_replace('D', 'Day', $value);
            }
            $arFeedback[$keys] = $value;
        }
    }

    // 計算出 CheckMacValue
    $CheckMacValue = ECPay_CheckMacValue::generate($arParameters, ECPay_HashKey, ECPay_HashIV, 1);

    // 必須要支付成功並且驗證碼正確
    if ($_POST['RtnCode'] == '1' && $CheckMacValue == $_POST['CheckMacValue']) {
        // 
        // 要處理的程式放在這裡，例如將線上服務啟用、更新訂單資料庫付款資訊等
        require_once dirname(__FILE__) . "/inc/connMySQL_pay.php";
        $TradeNo = mysqli_real_escape_string($mysqli2, $_POST['TradeNo']);

        $sql_query1 = " select pay_status from record where TradeNo='" . $TradeNo . "'";
        $result_status = $mysqli2->query($sql_query1);
        $row = $result_status->fetch_array();
        if ($row != FALSE) {
            if ($row["pay_status"] == "1") {
                $sql_query = " update record set pay_status = 2 , upd_date =DATE_ADD(NOW(),INTERVAL 13 HOUR) where TradeNo='" . $TradeNo . "'";
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
