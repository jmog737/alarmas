<?php
session_start();
$motivo = $_SESSION['motivo'];
session_destroy();
setcookie('tiempo', time(), time()-10);
require_once('data/escribirLog.php');
if ($motivo === 'NOLOGUEADO'){
  $finSesion = "Intento de acceso de un usuario NO logueado.";
}
else {
  $finSesion = "Finalizó la sesión: $motivo.";
}
$log = true;
escribirLog($finSesion, $log);
header('Location: index.php');
?>

