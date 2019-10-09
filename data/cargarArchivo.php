<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file cargarArchivo.php
*  @brief Archivo que se encarga de cargar el archivo en la base de datos.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
*
*******************************************************/
require_once("pdo.php");
require_once("escribirLog.php");

function cargarArchivo($archivo){
  if (file_exists($archivo)){
    $i = 1;
    $duplicados = 0;
    $cargados = 0;
    $errores = 0;
    $lineas = 0;
    $fechaCarga = date('Y-m-d');
    $log = "NO";
    $resultado["exito"] = true;
    $resultado["mensaje"] = '';
    $filas = array();
    
    $htmlContent = file_get_contents($archivo);
    $buscarHTML = strpos($htmlContent, "<html");
    $origenPSS32 = false;
    /// Si se encuentra la etiqueta es un xls con las alarmas de un PSS32:
    if ($buscarHTML !== FALSE){
      $origenPSS32 = true;
      libxml_use_internal_errors(true);
      $tempHtml = str_replace("<td/>", "<td>", $htmlContent);
      $nuevoHtml = str_replace("<td><strong></strong></td>", "", $tempHtml);

      $dom = new DOMDocument();
      $dom->loadHTML($nuevoHtml);
      $dom->preserveWhiteSpace = false;

      $tables = $dom->getElementsByTagName('table');
      $tablaEncabezado = $tables->item(0);
      $tablaDatos = $tables->item(1);

      $filasTemp = $tablaEncabezado->getElementsByTagName('tr');
      $filaEncabezado = $filasTemp->item(0);

      $campos = $filaEncabezado->getElementsByTagName('td');
      if ($campos[0]->nodeValue === 'Number'){
        $historico = true;
      }
      else {
        $historico = false;
      }
      $filas = $tablaDatos->getElementsByTagName('tr'); 
    }
    /// Si no se encuentra la etiqueta HTML es un csv con las alarmas de un OCS:
    else {
      $gestor = fopen($archivo, "r");
      if ($gestor) {
        while (($linea = fgets($gestor, 4096)) !== false) {
          $temp = explode(";", $linea);
          if (($temp[0] !== "Name")&&($linea !== "\r\n")){
            array_push($filas, $linea);  
          } /// Fin del if linea ni vacía ni cabecera
        } /// Fin del while que recorre las líneas
        if (!feof($gestor)) {
          $resultado["mensaje"] = "Fallo inesperado de fgets().";
          $resultado["exito"] = false;  
        }
        fclose($gestor);
      }/// Fin if (gestor)  
    }/// Fin del else del HTML 
    
    foreach ($filas as $filita){
      if ($origenPSS32){
        $cols = $filita->getElementsByTagName('td');
        if ($historico === true){
          $primero = 1;
        }
        else {
          $primero = 0;
        }

        $sourceTemp = trim($cols[$primero + 1]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $card = trim($cols[$primero + 2]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $source = str_replace("/", "-", $sourceTemp);
        $nombre = $card.' '.$source;

        /// Manejo de la fecha para separar el día de la hora y reacomodar:
        $fechaTemp = trim($cols[$primero]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $fechaTemp1 = explode(" ", $fechaTemp);
        $horaTemp = explode(":", $fechaTemp1[1]);
        if ($fechaTemp1[2] === "PM"){
          $hora1 = $horaTemp[0] + 12;
        }
        else {
          $hora1 = $horaTemp[0];
        }
        $hora = $hora1.":".$horaTemp[1].":".$horaTemp[2];
        
        $fechaTemp2 = explode("/", $fechaTemp1[0]);
        if ($fechaTemp2[0] < 10){
          $mes = "0".$fechaTemp2[0];
        }
        else {
          $mes = $fechaTemp2[0];
        }
        if ($fechaTemp2[1] < 10){
          $dia = "0".$fechaTemp2[1];
        }
        else {
          $dia = $fechaTemp2[1];
        }
        $year = strftime("%Y", time());
        $fecha = $year."-".$mes."-".$dia;
        
        //echo "fechaTemp1[0]:$fechaTemp1[0]<br>fechaTemp1[1]:$fechaTemp1[1]<br>fechaTemp1[2]:$fechaTemp1[2]<br>fechaTemp:$fechaTemp<br>hora:$hora<br>fecha:$fecha<br>";
        
        $compound = 'WDM';
        $tipoAID = trim($cols[$primero + 3]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $tipoAlarma = trim($cols[$primero + 4]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        switch ($tipoAlarma){
          case "Minor Alarm": 
          case "Minor": $tipoAlarma = "MN";
                        break;
          case "Major Alarm": 
          case "Major": $tipoAlarma = "MJ";
                        break;
          case "Warning Alarm":                  
          case "Warning": $tipoAlarma = "WR";
                          break;
          case "Critical Alarm":
          case "Critical":  $tipoAlarma = "CR";
                            break;
          default: break;                  
        }
        $tipoCondicion = trim($cols[$primero + 6]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $descripcion = trim($cols[$primero + 5]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        $afectacionServicioTemp = trim($cols[$primero + 7]->nodeValue," \t\n\r\0\x0B\xC2\xA0");
        if ($afectacionServicioTemp === 'No'){
          $afectacionServicio = "NSA";
        }
        else {
          $afectacionServicio = "SA";
        }
        $ubicacion = '';
        $direccion = '';
        $valorMonitoreado = '';
        $nivelUmbral = '';
        $periodo = '';
        $datos = str_replace("&nbsp;", '', $cols[$primero + 8]->nodeValue);
        $filtroALM = '';
        $filtroAID = '';
      }
      else {
        $temp = explode(";", $filita);
        $fechaTemp = $temp[7];      
        $temp0 = explode("  ", $fechaTemp);
        $horaTemp1 = explode("-", $temp0[1]);
        $hora = $horaTemp1[0].":".$horaTemp1[1].":".$horaTemp1[2];
        $fecha1 = explode("-", $temp0[0]);
        $year = strftime("%Y", time());
        $fecha = $year."-".$fecha1[0]."-".$fecha1[1];
      
        $nombre = $temp[0];
        $compound = $temp[1];
        $tipoAID = $temp[2];
        $tipoAlarma = $temp[3];
        $tipoCondicion = $temp[4];
        $descripcion = $temp[5];
        $afectacionServicio = $temp[6];
        $ubicacion = $temp[8];
        $direccion = $temp[9];
        $valorMonitoreado = $temp[10];
        $nivelUmbral = $temp[11];
        $periodo = $temp[12];
        $datos = $temp[13];
        $filtroALM = $temp[14];
        $filtroAID = $temp[15];
      }
      
      $i++;

      $existeRegistro = "select count(*) from alarmas where dia=? and hora=? and descripcion=? and tipoCondicion=?";
      $paramExiste = array($fecha, $hora, $descripcion, $tipoCondicion);
      $datosExiste = json_decode(hacerSelect($existeRegistro, $log, $paramExiste), true);
      $cuenta = (int)$datosExiste['rows'];
      /// Seteo cuenta a 0 para OBLIGAR a que se haga el insert en TODOS los casos, sin importar si existe o no la alarma:
      //$cuenta = 0;
      //echo $i." - ".$paramExiste[0]." - ".$paramExiste[1]." - ".$paramExiste[2]." - ".$cuenta."<br>";
      if ($cuenta !== 0){
        $duplicados++;
        $lineas++;
        if ($resultado["mensaje"] === ''){
          $resultado["mensaje"] = "L&iacute;nea/s con la/s duplicada/s: ";
        }
        $dup = $i - 1;
        $resultado["mensaje"] .= $dup." ";
      }
      else {
        $agregarRegistro = "insert into alarmas set usuario=?, nodo=?, archivo=?, fechaCarga=?, estado='Sin procesar', dia=?, hora=?, causa='', solucion='', nombre='".$nombre."', compound='".$compound."', tipoAID='".$tipoAID."', tipoAlarma='".$tipoAlarma."', tipoCondicion='".$tipoCondicion."', descripcion='".$descripcion."', "
        . "                 afectacionServicio='".$afectacionServicio."', ubicacion='".$ubicacion."', direccion='".$direccion."', valorMonitoreado='".$valorMonitoreado."', nivelUmbral='".$nivelUmbral."', periodo='".$periodo."', "
        . "                 datos='".$datos."', filtroALM='".$filtroALM."', filtroAID='".$filtroAID."'";
        $paramAgregar = array($_SESSION['user_id'], $_SESSION['idnodo'], $_SESSION['archivo'], $fechaCarga, $fecha, $hora);
        //echo $i."-".$agregarRegistro."<br>$fecha - $hora<br>";
        //echo "cuenta no >0 - id: $i<br>";
        $resultadoInsert = json_decode(hacerUpdate($agregarRegistro, $log, $paramAgregar), true);
        if ($resultadoInsert === 'ERROR'){
          $resultado["mensaje"] .= "Hubo un problema con la carga de la línea: $linea.<br>";
          $errores++;
          $lineas++;
        }
        else {
          $cargados++;
          $lineas++;
        }
      } /// Fin else $cuenta > 0 
    }
    
    $resultado["cargados"] = $cargados;
    $resultado["duplicados"] = $duplicados;
    $resultado["errores"] = $errores;
    $resultado["lineas"] = $lineas;

    $temp = explode("/", $archivo);
    $tot = count($temp);
    $dest = $temp[$tot - 1];
    if (($duplicados === 0)&&($errores === 0)&&($cargados !== 0)){
      $msg = "Archivo '".$dest."' correctamente subido a la base de datos.";
      $resultado["mensaje"] = $msg."<br>";
      $msgCargados = "Se cargaron ".$cargados."/".$lineas." alarmas nuevas del archivo '".$dest."'.";
      escribirLog($msg); 
      escribirLog($msgCargados);  
    }
    else {
      if ($cargados !== 0){
        $msg = "Archivo '".$dest."' correctamente subido a la base de datos.";
        escribirLog($msg); 
        $msgCargados = "Se cargaron ".$cargados."/".$lineas." alarmas nuevas del archivo '".$dest."'.";
      }
      else {
        $msgCargados = "NO hay alarmas nuevas en el archivo '".$dest."'. No se carga registro alguno.";
      }
      escribirLog($msgCargados);
      if ($duplicados !== 0){
        $msgDuplicados = "Hay ".$duplicados."/".$lineas." alarmas duplicadas en el archivo '".$dest."'.";
        escribirLog($msgDuplicados);
      }
      if ($errores !== 0){
        $msgErrores = "Hubo ".$errores."/".$lineas." alarmas con errores en el archivo '".$dest."'.";
        escribirLog($msgErrores);
      }
    }

    
    return $resultado;
  }/// Fin si archivo existe
  else {
    return false;
  } 
}















?>
