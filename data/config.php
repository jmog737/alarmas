<?php
/**
******************************************************
*  @file config.php
*  @brief Archivo con el seteo de las carpetas y direcciones usadas en todo el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/

/**
  \param DURACION Constante que indica el tiempo de sesión permitido sin actividad (en segundos).
*/
define('DURACION', 3600);

/**
  \param TIEMPOCOOKIE Constante que indica el tiempo de vida del cookie a setear (en segundos).
*/
define('TIEMPOCOOKIE', 3610);

/**
  \param TAM_ARCHIVO Constante que define el tamaño máximo permitido del archivo a cargar en la base de datos (en bytes).
*/
define('TAM_ARCHIVO', 512000);

/**
  \param MAX_TAMPAGINA Constante que define el tamaño máximo para la visualización de las páginas.
*/
define('MAX_TAMPAGINA', 100);

/**
  \param MAX_LIMITESELECTS Constante que define el tamaño máximo para los selects.
*/
define('MAX_LIMITESELECTS', 15);

///Consulto nombre del HOST y en base al mismo, configuro la IP (porque el HSA tiene diferente rango de IPs):
$hostname = getHostName();
$ip = 'http://192.168.200.146';

$unidad = "C:";
if (!file_exists($unidad)) {
  $unidad = "D:";
}
$dirCargados = $unidad."/ArchivosCargados";
$dirReportes = $unidad."/Reportes/";

$dirExcel = $dirReportes."Excel/";
$dirLog = $dirReportes."Logs/";
$dirGraficas = $dirReportes."/graficas/";
$rutaFotos = "images/snapshots";


if (!file_exists($dirReportes)){
  echo "No existe la carpeta: $dirReportes. <br>Por favor verifique.";
}

if (!isset($_SESSION["tamPagina"])){
  $_SESSION["tamPagina"] = 50;
}
if (!isset($_SESSION["limiteSelects"])){
  $_SESSION["limiteSelects"] = 10;
}
?>