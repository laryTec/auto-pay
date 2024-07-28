<?php
require "header.php";
?>
<link rel="stylesheet" type="text/css" href="../include/style.css">
<form class="form-signin" method="post" name="FLOGIN">
  <img class="mb-4" src="../img/lin.png" alt="" width="72" height="72">
  <h1 class="h3 mb-3 font-weight-normal">管理員登入</h1>
  <h6 class="mb-3 font-weight-normal">請輸入帳號密碼</h6>
  <?php
  if (!empty($_POST["login"]) && !empty($_POST["pwd"])) {
    if ($_POST["pwd"] == "00000000") {
      $_SESSION["admlogin"] = "Y";
      echo "<script> window.location.href ='index.php'</script>";
    }
    else{
      echo "<div class='alert alert-danger' role='alert'>密碼錯誤！</div>";
    }
  }
  ?>
  <label for="inputEmail" class="sr-only">帳號</label>
  <input type="text" name="login" class="form-control" placeholder="帳號" value="admin" required="" autocomplete="off">
  <label for="inputPassword" class="sr-only">密碼</label>
  <input type="password" name="pwd" class="form-control" placeholder="密碼" required="" autocomplete="off"  autofocus="">
  <button class="btn btn-lg btn-primary btn-block" type="submit">登入</button>
  <p class="mt-5 mb-3 text-muted">ＭasterUFO© 2020-2022</p>
  <input type="hidden" name="action" value="login">
</form>

<?php include("footer.php"); ?>