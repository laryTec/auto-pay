<?php
function checkQueryTime($action)
{
    if (isset($_SESSION["querytime"]) && isset($_SESSION["cqtaction"])) {
        if (time() - $_SESSION["querytime"] < 4  && $_SESSION["cqtaction"] == $action && $_SERVER['REQUEST_METHOD']=="POST" ) {
            echo "<h2><font style='color:red;'>執行速度太快！請5秒後再試！</font><h2>";
            if ($action == "login") {
                echo "<script>setTimeout(function(){ history.back(); }, 3000);</script>";
            } else {
                echo "<script>setTimeout(function(){ window.location.replace(location.href) }, 3000);</script>";
            }
            die();
        } elseif ($_SERVER['REQUEST_METHOD']=="POST") {
            $_SESSION["cqtaction"] = $action;
            $_SESSION["querytime"] = time();
        }
    } else {
        $_SESSION["querytime"] = time();
        $_SESSION["cqtaction"] = $action;
    }
}
