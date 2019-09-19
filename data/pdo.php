<?php
require_once('escribirLog.php');

try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlalarmas;charset=utf8','usuariodwdm', 'usuario.Dwdm');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}

function hacerSelect($query, $log, $paramSelect = false){
  global $pdo;
  
  $sth = $pdo->prepare($query);
  if ($paramSelect){
    $sth->execute($paramSelect);
  }
  else {
    $sth->execute();
  }

  $queryTemp = explode('from', $query);
  $query1 = "select count(*) from ".$queryTemp[1];
  $sth1 = $pdo->prepare($query1);
  if ($paramSelect){
    $sth1->execute($paramSelect);
  }
  else {
    $sth1->execute();
  }

  $datos = array();
  $datos['rows'] = $sth1->fetchColumn();
  while (($fila = $sth->fetch(PDO::FETCH_ASSOC)) != NULL) {  
    $datos['resultado'][] = $fila;
  }

  if ($log === "SI"){
    if ($paramSelect){
      $paramAString = implode(' --- ', $paramSelect);
      $guardar = $query." --- ".$paramAString;
    }
    else {
      $guardar = $query;
    }
    escribirLog($guardar);
  }

  $json = json_encode($datos);
  return $json;
}

function hacerUpdate($queryInsert, $log, $paramUpdate = false){
  global $pdo;
  
  $sth = $pdo->prepare($queryInsert);
  if ($paramUpdate){
    $result = $sth->execute($paramUpdate);
  }
  else {
    $result = $sth->execute();
  }
  
  if ($result !== FALSE) {
    $dato = "OK";
  }
  else {
    $dato = "ERROR";
  }
  if ($log === "SI"){
    if ($paramUpdate){
      $paramAString = implode(' --- ', $paramUpdate);
      $guardar = $queryInsert." --- ".$paramAString;
    }
    else {
      $guardar = $queryInsert;
    }
    escribirLog($guardar);
  }
  $json = json_encode($dato);
  return $json;
}