<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file consultarLargosAlarmas.php
*  @brief Archivo que contiene la función que realiza la consulta a la base de datos para conocer el largo de los campos.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Noviembre 2019
*
*******************************************************/
require_once("pdo.php");
require_once("config.php");

function consultarLargosAlarmas(){
  $log = false;
  $consultaLargos = "select column_name as campo, character_maximum_length as tam from information_schema.columns where table_name = 'alarmas' and data_type = 'varchar'";
  $datos = json_decode(hacerSelect($consultaLargos, $log), true);
  $datosLargos = $datos["resultado"]; 
  $registros = array();
  foreach ($datosLargos as $ind => $valor){
    $registros[$valor['campo']] = $valor['tam'];
  }
  return $registros;
}