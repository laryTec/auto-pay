<?php
require "header.php";
require_once  "../include/dbconfig.php";
require_once  "../inc/connMySQL_pay.php";
if (isset($_POST['sele_month'])) {
    if ($_POST['sele_month'] == "0") {
        $sql_query1 = " select * from record where DATE_FORMAT(upd_date, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') ";
    } else {
        $sele_month = mysqli_real_escape_string($mysqli2, $_POST["sele_month"]);
        $sql_query1 = " select * from record where month(upd_date)='$sele_month'  ";
    }
} else {
    $sql_query1 = " select * from record where DATE_FORMAT(upd_date, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') ";
}
if (isset($_POST['sele_paystatus'])) {
    if ($_POST['sele_paystatus'] != "0") {
        $sele_paystatus = mysqli_real_escape_string($mysqli2, $_POST["sele_paystatus"]);
        $sql_query1 = $sql_query1. " and pay_status= $sele_paystatus ";
    }
}
$sql_query1 = $sql_query1 . " order by upd_date desc ";

$result = $mysqli2->query($sql_query1);
//$row = $result->fetch_array();
?>
<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h4 class="border-bottom border-gray pb-2 mb-0">自動贊助系統-收入</h4>
    <form class="pt-3" method="post">
        <div class="form-group row">
            <label for="sele_month" class="col-sm-1 col-form-label">月份</label>
            <div class="col-sm-2"><select name="sele_month" id="sele_month" class="form-control">
                    <option value="0">本月</option>
                    <option value="1">一月</option>
                    <option value="2">二月</option>
                    <option value="3">三月</option>
                    <option value="4">四月</option>
                    <option value="5">五月</option>
                    <option value="6">六月</option>
                    <option value="7">七月</option>
                    <option value="8">八月</option>
                    <option value="9">九月</option>
                    <option value="10">十月</option>
                    <option value="11">十一月</option>
                    <option value="12">十二月</option>
                </select>
            </div>
            <label for="sele_paystatus" class="col-sm-1 col-form-label">狀態</label>
            <div class="col-sm-2"><select name="sele_paystatus" id="sele_paystatus" class="form-control">
                    <option value="0">全部</option>
                    <option value="1">待付款</option>
                    <option value="2">已付款</option>
                    <option value="3">待付+補觸發</option>
                    <option value="4">已付+補觸發</option>
                </select>
            </div>
            <div class="col-sm-1"><button type="submit" class="btn btn-primary btn-block mt-3 mt-sm-0">查詢</button></div>
        </div>
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <!--<th>流水號</th>-->
                    <th>交易號</th>
                    <th>帳號</th>
                    <th>角色</th>
                    <th>金額</th>
                    <th>付款方式</th>
                    <th>代碼/帳戶</th>
                    <th>狀態</th>
                    <th>時間</th>
                    <th>動作</th>
                </tr>
            </thead>
            <tbody style='font-size:10pt;'>
                <!--`loginId`, `MerchantTradeNo`, `TradeNo`, `money`, `gamemoney`, `payway`, `bank_num`, `bank_account`, `cvs_num`, `char_name`, `db_name`, `pay_status`, `upd_date`-->
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <!--<td><?php //echo $row["MerchantTradeNo"] 
                                ?></td>-->
                        <td><?php echo $row["TradeNo"] ?></td>
                        <td><?php echo $row["loginId"] ?></td>
                        <td><?php echo $row["char_name"] ?></td>
                        <td><?php echo $row["money"] ?></td>
                        <td><?php echo $row["payway"] ?></td>
                        <td><?php if ($row["bank_num"] != "0") {
                                echo "(" . $row["bank_num"] . ") " . $row["bank_account"];
                            } else {
                                echo  $row["cvs_num"];
                            } ?></td>
                        <td><?php if ($row["pay_status"] == "1") {
                                echo "待付款";
                            } elseif ($row["pay_status"] == "2") {
                                echo  "已付款";
                            } elseif ($row["pay_status"] == "3") {
                                echo  "待付+補觸發";
                            } elseif ($row["pay_status"] == "4") {
                                echo  "已付+補觸發";
                            } ?></td>
                        <td><?php echo $row["upd_date"] ?></td>
                        <td><a href="writeToGameDB.php?TradeNo=<?php echo $row["TradeNo"] ?>" target="_blank">觸發</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </form>

</div>

<?php if (isset($_POST['sele_month'])) {
    echo "<script>$('#sele_month').val(" . $_POST["sele_month"] . ");" .
        " $('#sele_paystatus').val(" . $_POST["sele_paystatus"] . ");</script>";
}
?>