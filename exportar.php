<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
}
/**
******************************************************
*  @file exportar.php
*  @brief Archivo que se encarga de preparar los datos para generar los reportes.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/

//error_reporting(NULL);
//ini_set('error_reporting', NULL);
//ini_set('display_errors',0);
if(isset($_SESSION['tiempo']) ) {
  require_once('data/pdo.php');
  require_once('generarExcel.php');
  require_once('generarPdfs.php');
  
  
  
  
}
else {
  echo '<script type="text/javascript">'
  . 'alert("Tú sesión expiró.\n¡Por favor vuelve a loguearte!.");window.close();
    window.location.assign("salir.php");
     </script>';
}
?>
