<?php
require dirname(__FILE__) . "/include/header.php";
require_once dirname(__FILE__) . '/include/ECPay.Payment.Integration.php';
$selfpaycode = "";
$selfpaybank = 0;
$selfpayway = "";
$selfpaycode_bank = "";
$selfpaycode_cvs = "";
try {
    require dirname(__FILE__) . "/include/config.php";
    // 重新整理回傳參數。
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
    if (($_POST['RtnCode'] == '2' || $_POST['RtnCode'] == '10100073') && $CheckMacValue == $_POST['CheckMacValue']) {
        // 
        // 要處理的程式放在這裡，例如將線上服務啟用、更新訂單資料庫付款資訊等
        // 
        //echo 'TradeNo=' . $_POST['TradeNo'];
        $MerchantTradeNo = $_POST['MerchantTradeNo'];
        if ($_POST['RtnCode'] == '2') {
            $selfpayway = "bank";
            $selfpaybank = $_POST['BankCode'];
            $selfpaycode = $_POST['vAccount'];
            $selfpaycode_bank = $_POST['vAccount'];
        }
        if ($_POST['RtnCode'] == '10100073') {
            $selfpayway = "CVS";
            $selfpaycode = $_POST['PaymentNo'];
            $selfpaycode_cvs = $_POST['PaymentNo'];
        }
        require_once dirname(__FILE__) . "/include/dbconfig.php";
        require dirname(__FILE__) . "/inc/connMySQL_pay.php";

        $selfpaybank = mysqli_real_escape_string($mysqli2, $selfpaybank);
        $selfpaycode_bank = mysqli_real_escape_string($mysqli2, $selfpaycode_bank);
        $selfpaycode_cvs = mysqli_real_escape_string($mysqli2, $selfpaycode_cvs);
        $MerchantTradeNo = mysqli_real_escape_string($mysqli2, $MerchantTradeNo);
        $p_TradeNo = mysqli_real_escape_string($mysqli2, $_POST['TradeNo']);

        $sql_query = " update record set bank_num='" . $selfpaybank . "' , bank_account='" . $selfpaycode_bank .
            "' , cvs_num ='" . $selfpaycode_cvs . "' ,TradeNo='" . $p_TradeNo . "'  where MerchantTradeNo='" . $MerchantTradeNo . "' ";
        $result_name = $mysqli2->query($sql_query);
    }
} catch (Exception $e) {
    echo '0|' . $e->getMessage();
}
?>

<?php

if ($selfpayway == "bank") {
?>
    <div class="my-3 p-3 bg-white rounded shadow-sm">
        <h4 class="border-bottom border-gray pb-2 mb-0">虛擬帳戶</h4>
        <div class="form-group row pt-3">
            <div class="col-xs-4 col-md-2">銀行代碼</div>
            <div class="col-xs-8 col-md-10"> <?php echo $selfpaybank ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">銀行帳號</div>
            <div class="col-8 col-md-10"> <?php echo $selfpaycode ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">贊助金額</div>
            <div class="col-8 col-md-10">NT. <?php echo $_POST['TradeAmt'] ?> 元</div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">遊戲幣</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField1'] ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">遊戲帳號</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField2'] ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">角色名稱</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField3'] ?></div>
        </div>
    </div>
<?php }
if ($selfpayway == "CVS") {
?>
    <div class="my-3 p-3 bg-white rounded shadow-sm">
        <h4 class="border-bottom border-gray pb-2 mb-0">超商代碼</h4>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">繳費單號 </div>
            <div class="col-8 col-md-10"><?php echo $selfpaycode ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">贊助金額</div>
            <div class="col-8 col-md-10">NT. <?php echo $_POST['TradeAmt'] ?> 元</div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">遊戲幣</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField1'] ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">遊戲帳號</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField2'] ?></div>
        </div>
        <div class="form-group row pt-3">
            <div class="col-4 col-md-2">角色名稱</div>
            <div class="col-8 col-md-10"> <?php echo $_POST['CustomField3'] ?></div>
        </div>
    </div>
<?php } ?>

<div class="alert alert-success" role="alert">
    感謝您的支持，繳費完成後需10~15分鐘作業時間，請耐心等候。
</div>


<?php include("include/footer.php"); ?>