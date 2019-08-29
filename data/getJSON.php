<?php
require_once 'pdo.php';

$query = $_GET["query"];
$log = $_GET["log"];

$datos = hacerSelect($query, $log);
echo $datos;
?>