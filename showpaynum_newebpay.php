<?php 
require_once dirname(__FILE__) . "/include/config.php";
require_once dirname(__FILE__) . "/include/dbconfig.php";
require_once dirname(__FILE__) . "/include/mwt-newebpay_sdk.php";

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

                    //echo $returnCode;
                } else {
                    //echo '0|ERROR';
                }
            }
        } 
    }

?>
<!doctype html>
<html lang="zh-TW">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>自動贊助系統</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon" />
    <link rel=icon href="../../favicon.ico" sizes="32x32" type="image/vnd.microsoft.icon">
 <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
   
</head>

<body class="bg-light">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="index.php"><img src="img/lin.png" width="30" height="30"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                <?php if (strtoupper(Pay_Vender)=="ECPAY"){ ?>
                    <a class="nav-link" href="pay.php">自動贊助系統</a>
                <?php } else if (strtoupper(Pay_Vender)=="NEWEBPAY"){ ?>
                    <a class="nav-link" href="pay_newebpay.php">自動贊助系統</a>
                <?php } ?>
                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">登出</a>
                    </li>
                </ul>
            </div>
        </nav>

    <main role="main" class="container">

    <?php 
        if($json->Status == "SUCCESS"){            
    ?>
        <div class="my-3 p-3 bg-white rounded shadow-sm">
                <h4 class="border-bottom border-gray pb-2 mb-0">贊助完成</h4>
                <div class="form-group row pt-3">
                    <div class="col-4 col-md-2">單號</div>
                    <div class="col-8 col-md-10"> <?php echo $json->Result->MerchantOrderNo;  ?></div>
                </div>
                <div class="form-group row pt-3">
                    <div class="col-4 col-md-2">遊戲帳號</div>
                    <div class="col-8 col-md-10"> <?php echo $json->Result->ItemDesc; ?></div>
                </div>
                <div class="form-group row pt-3">
                    <div class="col-4 col-md-2">贊助方式</div>
                    <div class="col-8 col-md-10"> <?php echo $json->Result->PaymentType; ?></div>
                </div>
                <div class="form-group row pt-3">
                    <div class="col-4 col-md-2">贊助金額</div>
                    <div class="col-8 col-md-10"> <?php echo $json->Result->Amt; ?></div>
                </div>
        </div>
        <div class="alert alert-success" role="alert">
                感謝您的支持，繳費完成後需10~15分鐘作業時間，請耐心等候。
        </div>
    <?php }else{ ?><h4 class="border-bottom border-gray pb-2 mb-0">繳費失敗</h4>
         <?php } ?>

<?php include("include/footer.php"); ?>