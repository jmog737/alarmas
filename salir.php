<?php
session_start();
$motivo = $_SESSION['motivo'];
session_destroy();
setcookie('tiempo', time(), time()-10);
require_once('data/escribirLog.php');
$finSesion = "Finalizó la sesión: $motivo.";
$log = true;
escribirLog($finSesion, $log);
header('Location: index.php');
?>

