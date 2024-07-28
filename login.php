<?php
require dirname(__FILE__) . "/include/header.php";
require_once dirname(__FILE__) . "/include/config.php";
?>
<link rel="stylesheet" type="text/css" href="include/style.css">
<form class="form-signin" method="post" name="FLOGIN" action="inc/authlogin.php">
  <img class="mb-4" src="img/lin.png" alt="" width="72" height="72">
  <h1 class="h3 mb-3 font-weight-normal">登入</h1>

  <h6 class="mb-3 font-weight-normal">請輸入遊戲帳號密碼</h6>
  <select name="server" id="server" class="form-control">
    <?php
    $servcount = count($serverarr);
    if ($servcount > 1) { ?>
      <option value="0">請選擇伺服器</option>
    <?php }
    foreach ($serverarr as $key => $value) { ?>
      <option value=<?php echo $key ?> <?php if ($servcount == 1) echo 'selected="selected"'; ?>><?php echo $value[0] ?></option>
    <?php }    ?>
  </select>
  <br>
  <?php
  if (!empty($_GET['err'])) {
    $error = $_GET['err'];
    if ($error == 1) {
      echo "<div class='alert alert-danger' role='alert'>
    帳號或密碼錯誤！
  </div>";
    }
  }
  ?>
  <label for="inputEmail" class="sr-only">帳號</label>
  <input type="text" name="login" class="form-control" placeholder="帳號" required="" autofocus="" autocomplete="off">
  <label for="inputPassword" class="sr-only">密碼</label>
  <input type="password" name="pwd" class="form-control" placeholder="密碼" required="" autocomplete="off">
  <button class="btn btn-lg btn-primary btn-block" type="submit">登入</button>
  <p class="mt-5 mb-3 text-muted">ＭasterUFO© 2020-2022</p>
  <input type="hidden" name="action" value="login">
</form>

<script>
  $(function() {
    $("button").click(function() {
      if ($("#server").val() == "0") {
        alert("請選擇伺服器");
        return false;
      }
    });
  });
</script>

<?php include("include/footer.php"); ?>