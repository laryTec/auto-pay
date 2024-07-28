<?php
if (!isset($_SESSION)) {
    session_start();
}
unset($_SESSION['login']);
require_once("checkQueryTime.php");
checkQueryTime("login");

if (isset($_POST["action"]) && ($_POST["action"] == "login")) {
    require_once "../include/config.php";
    require_once "../include/dbconfig.php";
    include("connMySQL.php");

    $login = mysqli_real_escape_string($mysqli,$_POST["login"]) ;
    $pwd = mysqli_real_escape_string($mysqli,$_POST["pwd"]) ;

    $sql_query = "select count(login) as count from accounts where login='$login' and password ='$pwd' ";
    $result = $mysqli->query($sql_query);
    $row = $result->fetch_array();
    
    if ($row["count"] == 1) {
        $_SESSION['login'] = $_POST["login"];
        header("Location: ../index.php");
    } else {
        unset($_SESSION['login']);
        header("Location: ../login.php?err=1");
    }
}
