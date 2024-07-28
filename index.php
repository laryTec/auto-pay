<?php
require dirname(__FILE__) . "/include/header.php";
require_once dirname(__FILE__) . "/include/config.php";
if (strtoupper(Pay_Vender)=="ECPAY"){
    echo "<script> window.location.href ='pay.php'</script>";
}
else if (strtoupper(Pay_Vender)=="NEWEBPAY"){
    echo "<script> window.location.href ='pay_newebpay.php'</script>";
}
else{
    echo "<script> alert('未設定金流或設定錯誤')</script>";
}

?>


<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h4 class="border-bottom border-gray pb-2 mb-0">已登入</h4>
</div>


<?php include("include/footer.php"); ?>