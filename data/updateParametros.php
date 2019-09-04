<?php
require_once("pdo.php");
require_once ("escribirLog.php");

$query = "update usuarios";

$newPageSize = $_GET["tamPagina"];
$newSelectSize = $_GET["tamSelects"];

$cambioPagina = false;
$cambioLimiteSelects = false;

if ($newPageSize !== "-1"){
  $_SESSION["tamPagina"] = $newPageSize;
  $queryPage = " tamPagina=".$newPageSize;
  $cambioPagina = true;
}

if ($newSelectSize !== "-1"){
  $_SESSION["limiteSelects"] = $newSelectSize;
  $queryLimiteSelects = " limiteSelects=".$newSelectSize;
  $cambioLimiteSelects = true;
}

if ($cambioPagina){
  $query = $query." set".$queryPage;
}

if ($cambioLimiteSelects){
  if ($cambioPagina){
    $query = $query.",".$queryLimiteSelects;
  }
  else {
    $query = $query." set".$queryLimiteSelects;
  }
}

$query = $query." where idusuario=".$_SESSION['user_id'];

if ($cambioPagina || $cambioLimiteSelects){
  $result1 = $pdo->query($query);
  if ($result1 !== FALSE) {
    $datos["resultadoDB"] = "OK";
  }
  else {
    $datos["resultadoDB"] = "ERROR";
  }
}
else {
  $datos["resultadoDB"] = "ERROR";
}

//$datos["resultadoDB"] = $query;
$json = json_encode($datos);
echo $json;
?>