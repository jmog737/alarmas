<?php
require_once 'pdo.php';

$query = $_GET["query"];
$log = $_GET["log"];

$datos = hacerUpdate($query, $log);
echo $datos;
?>