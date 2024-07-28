<?php
    define('Pay_Vender', 'newebpay'); //ECpay:綠界 , newebpay:藍新
    define('MAXID_BEFORE', 'NEWSVR'); //流水號開頭勿超過8碼    
    define('hosturl', 'https://你的網址'); //自動贊助網址

//-綠界設定-------------------------------------------------------------
    define('ECPay_MerchantID', '2000132');
    define('ECPay_HashKey', '5294y06JbISpM5x9');
    define('ECPay_HashIV', 'v77hoKGq4kWxNNIS');
    define('ECPayRecallUrl', hosturl.'/ECpayRecall.php'); //綠界回傳本機位置
    define('ClientRedirectURL', hosturl.'/showpaynum.php'); //取得單號導頁位置
    define('ECPayServiceURL', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5'); //綠界測試服務位置
    //define('ECPayServiceURL', 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5'); //綠界服務位置

//-藍新設定--------------------------------------------------------------
    define('newebpay_MerchantID', '商店號');
    define('newebpay_HashKey', 'HashKey');
    define('newebpay_HashIV', 'HashIV');
    define('newebpay_ver', '1.5');
    define('newebpayRecallUrl', hosturl.'/newebpayRecall.php'); //藍新回傳本機位置
    define('ClientRedirectURL_newebpay', hosturl.'/showpaynum_newebpay.php'); //取得單號導頁位置
    define('newebpayServiceURL', 'https://ccore.newebpay.com/MPG/mpg_gateway'); //藍新測試服務位置
    //define('newebpayServiceURL', 'https://core.newebpay.com/MPG/mpg_gateway'); //藍新服務位置

//-登入畫面&遊戲DB設定----------------------------------------------------------------------

    $serverarr = array(
        '1' => array('OO天堂(1服)', 'gameDBName')
    );
    
    /*$serverarr = array(
        '1' => array('OO天堂(1服)', '72new'),
        '2' => array('OO天堂(2服)', '72new2')
    );
    */
    

?>
