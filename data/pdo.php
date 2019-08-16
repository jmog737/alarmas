<?php
try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlalarmas;charset=utf8','jm', 'jm');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}

function hacerSelect($query){
  global $pdo;
  $stmt = $pdo->query($query);

  $queryTemp = explode('from', $query);
  $query1 = "select count(*) from ".$queryTemp[1];

  $datos = array();
  $datos['rows'] = $pdo->query($query1)->fetchColumn();
  while (($fila = $stmt->fetch(PDO::FETCH_ASSOC)) != NULL) { 
    $datos['resultado'][] = $fila;
  }
  return $datos;
}

function hacerUpdate($queryInsert){
  global $pdo;
  $result = $pdo->query($queryInsert);

  if ($result !== FALSE) {
    $dato = "OK";
  }
  else {
    $dato = "ERROR";
  }
  return $dato;
}