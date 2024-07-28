<?php
require dirname(__FILE__) . "/include/header.php";
if (!isset($_SESSION)) {
    session_start();
}
require_once("inc/checkQueryTime.php");
checkQueryTime("pay");
require_once dirname(__FILE__) . "/include/dbconfig.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once dirname(__FILE__) . '/include/mwt-newebpay_sdk.php';
    require_once dirname(__FILE__) . "/include/config.php";
    try {
        $gamemoney = 0;
        $txt_money = (int) $_POST['txt_money'];
        $gamemoney = (int) $txt_money;
        $selfpayway = "";

        //比值表-------------------------
        if ($txt_money >= 5000 && $txt_money <= 9999) $gamemoney = $gamemoney * 1.13;
        if ($txt_money >= 10000 && $txt_money <= 14999) $gamemoney = $gamemoney * 1.16;
        if ($txt_money >= 15000) $gamemoney = $gamemoney * 1.2;
        //比值表-------------------------


        /* 送給藍新資料 */
        $MerchantTradeNo = MAXID_BEFORE . (time()+60*60*13);
        if ($_POST['sele_way'] == 1) {
            $CVS1= 1;
            $VACC1= 0;
            $CREDIT1= 0;
            $selfpayway = "CVS";
            $_SESSION["paytype"] = "超商代碼繳費";
        }
        if ($_POST['sele_way'] == 2) {
            $CVS1= 0;
            $VACC1= 1;
            $CREDIT1= 0;
            $selfpayway = "bank";
            $_SESSION["paytype"] = "ATM轉帳";
        }
        if ($_POST['sele_way'] == 3) {
            $CVS1= 0;
            $VACC1= 0;
            $CREDIT1= 1;
            $selfpayway = "CREDIT";
            $_SESSION["paytype"] = "線上刷卡";
        }
        $_SESSION["paymoney"] = $txt_money;        
        $_SESSION["payloginid"] = $_SESSION['login'];
        $_SESSION["MerchantTradeNo"] =  $MerchantTradeNo;

        $trade_info_arr = array(
            'MerchantID' => newebpay_MerchantID,
            'RespondType' => 'JSON',
            'TimeStamp' => 1485232229,
            'Version' => newebpay_ver,
            'MerchantOrderNo' => $MerchantTradeNo,
            'Amt' => $txt_money ,
            'ItemDesc' => $_SESSION['login'],
            'CREDIT' => $CREDIT1,
            'CVS' => $CVS1,
            'VACC' => $VACC1,//ATM
            'ReturnURL' => ClientRedirectURL_newebpay, //支付完成 返回商店網址
            'NotifyURL' => newebpayRecallUrl, //支付通知網址
            'CustomerURL' =>$CustomerURL, //商店取號網址
            'ClientBackURL' => hosturl //支付取消 返回商店網址
        );

        require dirname(__FILE__) . "/inc/connMySQL_pay.php";
        $loginid = mysqli_real_escape_string($mysqli2,$_SESSION['login']);
        $db_name = mysqli_real_escape_string($mysqli2,$_SESSION["server"]);
        $MerchantTradeNo  = mysqli_real_escape_string($mysqli2,$MerchantTradeNo);
        $txt_money  = mysqli_real_escape_string($mysqli2,$txt_money);
        $selfpayway  = mysqli_real_escape_string($mysqli2,$selfpayway);
        $gamemoney  = mysqli_real_escape_string($mysqli2,$gamemoney);
        $p_txt_name  = mysqli_real_escape_string($mysqli2,$_POST['txt_name']);

        $sql_query = " INSERT INTO record(loginId,MerchantTradeNo, TradeNo, money, gamemoney, payway, bank_num, bank_account, cvs_num, char_name, db_name, pay_status, upd_date) "
            . " VALUES ('" . $loginid . "','" . $MerchantTradeNo . "',''," . $txt_money . "," . $gamemoney .
            ",'" . $selfpayway . "','','','','" . $p_txt_name .
            "','" . $db_name . "',1,DATE_ADD(NOW(),INTERVAL 13 HOUR))";
        $result_name = $mysqli2->query($sql_query);

        $TradeInfo = create_mpg_aes_encrypt($trade_info_arr, newebpay_HashKey, newebpay_HashIV);
        $SHA256 = strtoupper(hash("sha256", SHA256(newebpay_HashKey,$TradeInfo,newebpay_HashIV)));
        echo CheckOut(newebpayServiceURL,newebpay_MerchantID,$TradeInfo,$SHA256,newebpay_ver);

    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    require dirname(__FILE__) . "/inc/connMySQL.php";
    $loginid = $_SESSION['login'];
    $loginid = mysqli_real_escape_string($mysqli,$loginid);
    $sql_query = "select objid,char_name from characters where account_name='$loginid' ";
    $result_name = $mysqli->query($sql_query);
}

?>


<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h4 class="border-bottom border-gray pb-2 mb-0">自動贊助系統</h4>

    <div class="form-group row">
        <label for="txt_name" class="col-sm-2 col-form-label">贊助比值</label>
        <div class="col-sm-10">
            <table class="table table-sm ">
                <tr class="thead-light">
                    <th>金額</th>
                    <th>回饋倍率</th>
                </tr>
                <tr class="table-light">
                    <td>1000~4999</td>
                    <td>無回饋</td>
                </tr>
                <tr class="table-primary">
                    <td>5000~9999</td>
                    <td>１.１３</td>
                </tr>
                <tr class="table-success">
                    <td>10000~14999</td>
                    <td>１.１６</td>
                </tr>
                <tr class="table-warning">
                    <td>15000~20000</td>
                    <td>１.２</td>
                </tr>
            </table>
        </div>
    </div>
    <form class="pt-3" method="post">
        <div class="form-group row">
            <label for="sele_way" class="col-sm-2 col-form-label">贊助方式</label>
            <div class="col-sm-10"><select name="sele_way" id="sele_way" class="form-control">
                    <option value="0">--請選擇贊助方式--</option>
                    <option value="1">超商代碼</option>
                    <option value="2">ATM轉帳</option>
                    <option value="3">信用卡</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="txt_name" class="col-sm-2 col-form-label">角色名稱</label>
            <div class="col-sm-10"><select name="txt_name" class="form-control">
                    <?php
                    if (isset($result_name) && $result_name->num_rows > 0) {
                    ?>
                        <?php
                        while ($row = $result_name->fetch_array()) {
                        ?>
                            <option><?php echo $row['char_name'] ?></option>
                        <?php
                        }
                        ?>
                    <?php
                    }
                    ?>
                </select></div>
        </div>
        <div class="form-group row">
            <label for="txt_money" class="col-sm-2 col-form-label">贊助金額</label>
            <div class="col-sm-10"><input type="number" id="txt_money" name="txt_money" class="form-control" required="" /></div>
        </div>
        <div class="alert alert-info" id="moneymax_alert">
            超商單筆最高上限6000.
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary btn-block mt-3 mt-sm-0">送出</button>
            </div>
        </div>
    </form>

</div>

<script>
    $(function() {

        $("#moneymax_alert").hide();
        $("#txt_money").attr("disabled", true);
        $("#sele_way").change(function() {
            if ($(this).val() == 2 || $(this).val() == 3) {

                $("#moneymax_alert").hide();
                $("#txt_money").removeAttr("disabled");
            } else if ($(this).val() == 1) {

                $("#moneymax_alert").show();
                $("#txt_money").removeAttr("disabled");
            } else {

                $("#moneymax_alert").hide();
                $("#txt_money").attr("disabled", true);
            }
        });
        $("[type='submit']").click(function() {
            if ($("#sele_way").val() == 0) {
                alert("請選擇贊助方式");
                return false;
            }
            if ($("#sele_way").val() == 2 && $("#sele_bank").val() == 0) {
                alert("請選擇銀行");
                return false;
            }
        });
        $("#txt_money").blur(function() {
            if ($("#sele_way").val() == 1 && $(this).val() > 6000) {
                $(this).val("6000");
            }
        });
    });
</script>

<?php require dirname(__FILE__) . "/include/footer.php"; ?>