<?php
require dirname(__FILE__) . "/include/header.php";
if (!isset($_SESSION)) {
    session_start();
}
require_once("inc/checkQueryTime.php");
checkQueryTime("pay");
require_once dirname(__FILE__) . "/include/dbconfig.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once dirname(__FILE__) . '/include/ECPay.Payment.Integration.php';
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

        $obj = new ECPay_AllInOne();

        //服務參數
        $obj->ServiceURL  = ECPayServiceURL;
        $obj->HashKey     = ECPay_HashKey;
        $obj->HashIV      = ECPay_HashIV;
        $obj->MerchantID  = ECPay_MerchantID;
        $obj->EncryptType = '1'; //CheckMacValue加密類型，請固定填入1，使用SHA256加密


        //基本參數(請依系統規劃自行調整)
        $MerchantTradeNo = MAXID_BEFORE . (time()+60*60*13);
        $obj->Send['ReturnURL']         = ECPayRecallUrl;    //付款完成通知回傳的網址
        $obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                          //訂單編號
        $obj->Send['MerchantTradeDate'] = date("Y/m/d H:i:s", strtotime ("+13 hour"));                       //交易時間
        $obj->Send['TotalAmount']       = $txt_money;                                      //交易金額
        $obj->Send['TradeDesc']         = "good to drink";                          //交易描述

        if ($_POST['sele_way'] == 1) {
            $obj->Send['ChoosePayment']     = ECPay_PaymentMethod::CVS;
            $obj->Send['ChooseSubPayment']  = ECPay_PaymentMethodItem::CVS;
            $selfpayway = "CVS";
        }
        if ($_POST['sele_way'] == 2) {
            $obj->Send['ChoosePayment']     = ECPay_PaymentMethod::ATM;                 //付款方式:ATM
            $obj->Send['ChooseSubPayment']  = $_POST['sele_bank'];
            $selfpayway = "bank";
        }

        $obj->Send['CustomField1']  = $gamemoney;
        $obj->Send['CustomField2']  = $_SESSION["login"];
        $obj->Send['CustomField3']  = $_POST['txt_name'];

        //訂單的商品資料
        array_push($obj->Send['Items'], array(
            'Name' => "donate", 'Price' => $txt_money,
            'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"
        ));

        //ATM 延伸參數(可依系統需求選擇是否代入)
        $obj->SendExtend['ExpireDate'] = 3;     //繳費期限 (預設3天，最長60天，最短1天)
        $obj->SendExtend['PaymentInfoURL'] = ""; //伺服器端回傳付款相關資訊。
        $obj->SendExtend['ClientRedirectURL'] = ClientRedirectURL;
        require dirname(__FILE__) . "/inc/connMySQL_pay.php";
        $loginid = mysqli_real_escape_string($mysqli2,$_SESSION['login']);
        $db_name = mysqli_real_escape_string($mysqli2,$_SESSION["server"]);
        $MerchantTradeNo  = mysqli_real_escape_string($mysqli2,$MerchantTradeNo);
        $txt_money  = mysqli_real_escape_string($mysqli2,$txt_money);
        $selfpayway  = mysqli_real_escape_string($mysqli2,$selfpayway);
        $gamemoney  = mysqli_real_escape_string($mysqli2,$gamemoney);
        $p_txt_name  = mysqli_real_escape_string($mysqli2,$_POST['txt_name']);
        //CustomField3
        $sql_query = " INSERT INTO record(loginId,MerchantTradeNo, TradeNo, money, gamemoney, payway, bank_num, bank_account, cvs_num, char_name, db_name, pay_status, upd_date) "
            . " VALUES ('" . $loginid . "','" . $MerchantTradeNo . "',''," . $txt_money . "," . $gamemoney .
            ",'" . $selfpayway . "','','','','" . $p_txt_name .
            "','" . $db_name . "',1,DATE_ADD(NOW(),INTERVAL 13 HOUR))";
        $result_name = $mysqli2->query($sql_query);

        //產生訂單(auto submit至ECPay)
        $html = $obj->CheckOut();
        echo $html;
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
                </select>
            </div>
        </div>
        <div id="sel_bank" class="form-group row">
            <label for="sele_bank" class="col-sm-2 col-form-label">選擇銀行</label>
            <div class="col-sm-10"><select name="sele_bank" id="sele_bank" class="form-control">
                    <option value="0">--請選擇銀行--</option>
                    <option value="BOT">台灣銀行</option>
                    <option value="FUBON">台北富邦</option>
                    <option value="CHINATRUST">中國信託</option>
                    <!--<option value="FIRST">第一銀行</option>-->
                    <option value="TAISHIN">台新銀行</option>
                    <option value="LAND">土地銀行</option>
                    <option value="CATHAY">國泰世華</option>
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
        $("#sel_bank").hide();
        $("#moneymax_alert").hide();
        $("#txt_money").attr("disabled", true);
        $("#sele_way").change(function() {
            if ($(this).val() == 2) {
                $("#sel_bank").show();
                $("#moneymax_alert").hide();
                $("#txt_money").removeAttr("disabled");
            } else if ($(this).val() == 1) {
                $("#sel_bank").hide();
                $("#moneymax_alert").show();
                $("#txt_money").removeAttr("disabled");
            } else {
                $("#sel_bank").hide();
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