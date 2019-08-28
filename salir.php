<?php
session_start();
session_destroy();
setcookie('tiempo', time(), time()-1);
require_once('data/escribirLog.php');
$finSesion = "Finalizó la sesión.";
$log = true;
escribirLog($finSesion, $log);
header('Location: index.php');
?>

