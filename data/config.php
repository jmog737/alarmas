<?php
/**
******************************************************
*  @file config.php
*  @brief Archivo con el seteo de las carpetas y direcciones usadas en todo el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Enero 2018
*
*******************************************************/

/**
  \param DURACION Constante que indica el tiempo de sesión permitido sin actividad (en segundos).
*/
define('DURACION', 600);

/**
  \param TIEMPOCOOKIE Constante que indica el tiempo de vida del cookie a setear (en segundos).
*/
define('TIEMPOCOOKIE', 610);

///Consulto nombre del HOST y en base al mismo, configuro la IP (porque el HSA tiene diferente rango de IPs):
$hostname = getHostName();
$ip = '';

//echo "host: ".$hostname."<br>ip: ".$ip;

$unidad = "C:";
$dirCargados = "C:\\\ArchivosCargados";

$rutaFotos = "images/snapshots";




?>