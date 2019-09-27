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
*  @date Setiembre 2019
*
*******************************************************/
require_once("pdo.php");
require_once("escribirLog.php");

function cargarArchivo($archivo){
  if (file_exists($archivo)){
    $gestor = fopen($archivo, "r");
    if ($gestor) {
      $i = 0;
      $duplicados = 0;
      $cargados = 0;
      $errores = 0;
      $fechaCarga = date('Y-m-d');
      $log = "NO";
      $resultado["exito"] = true;
      $resultado["mensaje"] = '';
      while (($linea = fgets($gestor, 4096)) !== false) {
        $temp = explode(";", $linea);
        if (($temp[0] !== "Name")&&($linea !== "\r\n")){
          $i++;
          $nombre = $temp[0];
          
          $fechaTemp = $temp[7];
          $temp0 = explode("  ", $fechaTemp);
          $horaTemp1 = explode("-", $temp0[1]);
          $hora = $horaTemp1[0].":".$horaTemp1[1].":".$horaTemp1[2];
          $fecha1 = explode("-", $temp0[0]);
          
          $year = strftime("%Y", time());
          $fecha = $year."-".$fecha1[0]."-".$fecha1[1];

          $existeRegistro = "select count(*) from alarmas where nombre=? and dia=? and hora=?";
          $paramExiste = array($nombre, $fecha, $hora);
          $datosExiste = json_decode(hacerSelect($existeRegistro, $log, $paramExiste), true);
          $cuenta = (int)$datosExiste['rows'];
          /// Seteo cuenta a 0 para OBLIGAR a que se haga el insert en TODOS los casos, sin importar si existe o no la alarma:
          //$cuenta = 0;
          //echo $i." - ".$paramExiste[0]." - ".$paramExiste[1]." - ".$paramExiste[2]." - ".$cuenta."<br>";
          if ($cuenta !== 0){
            $duplicados++;
            if ($resultado["mensaje"] === ''){
              $resultado["mensaje"] = "L&iacute;neas duplicadas: ";
            }
            $resultado["mensaje"] .= $i." ";
          }
          else {
            $agregarRegistro = "insert into alarmas set usuario=?, nodo=?, archivo=?, fechaCarga=?, estado='Sin procesar', dia=?, hora=?, causa='', solucion='', nombre='".$temp[0]."', compound='".$temp[1]."', tipoAID='".$temp[2]."', tipoAlarma='".$temp[3]."', tipoCondicion='".$temp[4]."', descripcion='".$temp[5]."', "
            . "                 afectacionServicio='".$temp[6]."', ubicacion='".$temp[8]."', direccion='".$temp[9]."', valorMonitoreado='".$temp[10]."', nivelUmbral='".$temp[11]."', periodo='".$temp[12]."', "
            . "                 datos='".$temp[13]."', filtroALM='".$temp[14]."', filtroAID='".$temp[15]."'";
            $paramAgregar = array($_SESSION['user_id'], $_SESSION['idnodo'], $_SESSION['archivo'], $fechaCarga, $fecha, $hora);
            //echo "cuenta no >0 - id: $i<br>";
            $resultadoInsert = json_decode(hacerUpdate($agregarRegistro, $log, $paramAgregar), true);
            if ($resultadoInsert === 'ERROR'){
              $resultado["mensaje"] .= "Hubo un problema con al carga de la línea: $linea.<br>";
              $errores++;
            }
            else {
              $cargados++;//echo "debe subir: $cargados<br>";
            }
          } /// Fin else $cuenta > 0 
        } /// Fin del if linea ni vacía ni cabecera
      } /// Fin del while que recorre las líneas
      $resultado["cargados"] = $cargados;
      $resultado["duplicados"] = $duplicados;
      $resultado["errores"] = $errores;
      $resultado["lineas"] = $i;
      if (($duplicados === 0)&&($errores === 0)){
        $resultado["mensaje"] = "¡Archivo correctamente subido a la base de datos!.<br>";
      }
      if (!feof($gestor)) {
        $resultado["mensaje"] = "Fallo inesperado de fgets().";
        $resultado["exito"] = false;  
      }
      fclose($gestor);
      return $resultado;
    }
  }
  else {
    return false;
  } 
}















?>
