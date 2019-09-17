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

function eliminar_tildes($cadena){
  //Ahora reemplazamos las letras
  $cadena = str_replace(
      array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
      array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
      $cadena
  );

  $cadena = str_replace(
      array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
      array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
      $cadena );

  $cadena = str_replace(
      array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
      array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
      $cadena );

  $cadena = str_replace(
      array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
      array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
      $cadena );

  $cadena = str_replace(
      array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
      array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
      $cadena );

  return $cadena;
}
require_once('data/config.php');

//error_reporting(NULL);
//ini_set('error_reporting', NULL);
//ini_set('display_errors',0);

$vida_session = time() - $_SESSION['tiempo'];

if($vida_session < DURACION ) {
  require_once('data/pdo.php');
  require_once('data/config.php');
  require_once('generarExcel.php');
  require_once('generarPdfs.php');
  require_once('enviarMails.php');
  $seguir = true;
  
  //***************************************************** DESTINATARIOS CORREOS **************************************************************
  $paraListados = array();
  $copiaListados = array();
  $ocultosListados = array();
  //**************** PRUEBAS ************************************************************
  $paraListados['Juan Martín Ortega'] = "jmog737@gmail.com";
  $copiaListados['Juan Martín Ortega'] = "jmog737@gmail.com";
  //**************** FIN PRUEBAS ********************************************************
  //**************************************************** FIN DESTINATARIOS CORREOS ***********************************************************
  
  if (isset($_POST)){
    if (isset($_POST["param"])){
      $param = $_POST["param"];
      $t = stripos($param, "&");
      if ($t === FALSE){
        $param = unserialize($param);
      }
      else {
        $paramSerial = $_POST['paramSerial'];
        $param = unserialize($paramSerial);
      }
    }
    else {
      $param = null;
    }
    if (isset($_POST['query'])){
      $consulta = html_entity_decode($_POST['query']);
      
    }
    else {
      $seguir = false;
      echo "Hubo un error. Por favor verifique!.";
    }
    
    if (isset($_POST['origen'])){
      $origen = $_POST['origen'];
    }
    else {
      echo "Hubo un error. Por favor verifique!.";
      $seguir = false;
    }
    
    if (isset($_POST['mensaje'])){
      $tituloReporte = $_POST['mensaje'];
    }
    $archivo = false;
    if (isset($_POST['nodo'])){
      $nombreNodo = $_POST['nodo'];
      $arrayNodos = array();
      if ($nombreNodo === 'TODOS'){
        $nombreCorto = "TODOS";
        $log = "NO";
        /// Consulto por el listado del nodo para poder hacer el "cambio de nodo":                                                                                                                                                                           
        $consultaNodos = "select distinct idnodo, localidad, nombre from nodos";
        $datosNodos = json_decode(hacerSelect($consultaNodos, $log), true);
        $nombreNodos = $datosNodos['resultado'];
        foreach ($nombreNodos as $ind => $valor){
          $arrayNodos[$valor['idnodo']] = $valor['localidad']." [".$valor['nombre']."]";
        }
      }
      
      if ($nombreNodo === 'archivo'){
        $archivo = true;
        $temp2 = explode('archivo ', $tituloReporte);
        $temp3 = explode('_', $temp2[1]);
        $nombreCorto = $temp3[0];
        /// Comento por ahora, pero es la consulta para recuperar el nombre del nodo según ubicación
  //      $para1 = array($nombreCorto);
  //      $log = false;
  //      $consultaNodo = "select localidad from nodos where nombre=?";
  //      $datoNodo = json_decode(hacerSelect($consultaNodo, $log, $para1), true);
  //      $nombreNodo = $datoNodo['resultado'][0]['localidad'];
        $nombreNodo = $nombreCorto;
      }
    }
    
    if (isset($_POST['nodoCorto'])){
      $nombreCorto = $_POST['nodoCorto'];
    }
    else {
      /// Si no se seteó es porque se viene ó desde usuarios ó desde nodos:
      if ($_POST["origen"] === 'usuarios'){
        $nombreCorto = 'Usuarios';
      }
      else {
        $nombreCorto = 'Nodos';
      }
    }

    if ($seguir){
      $log = "NO";
      $datos = json_decode(hacerSelect($consulta, $log, $param), true);
      $registros = $datos['resultado'];
      $totalFilas = $datos['rows'];
      
      /// Si hay datos los muestro:
      if ($totalFilas > 0){
        /// ************************************************************ PARAMETROS GENERALES **********************************************************
        //
        //********************************************* Defino tamaño de la celda base: c1, y el número ************************************************
        $h = 6;
        $hHeader = 30;
        $hFooter = 7.5;
        $orientacion = 'L';
        if ($orientacion === 'P'){
          $c1 = 18;
        }
        else {
          $c1 = 25;
        }
        $x = 10;
        $marcaAgua = true;
        $textoMarcaAgua = 'CONFIDENCIAL';
        $guardarDisco = true;
        //******************************************************** FIN tamaños de celdas ***************************************************************

        //******************************************************** INICIO Hora y título ****************************************************************
        $fecha = date('d/m/Y');
        $hora = date('H:i');
        //********************************************************** FIN Hora y título *****************************************************************
        //
        /// ******************************************************* FIN PARAMETROS GENERALES ***********************************************************

        //*********************************************** Adaptación nombre del nodo sin tildes ni caracteres especiales *******************************
        /// Acomodo el nombre del nodo para NO tener problemas con el nombre del archivo:
        
        /////Se define el tamaño máximo aceptable para el nombre teniendo en cuenta que el excel admite un máximo de 31 caracteres, y que además, 
        ///ya se tienen 6 fijos del stock_ (movs_ es uno menos).
        $tamMaximoNombreNodo = 20;
        
        ///Caracteres a ser reemplazados en caso de estar presentes en el nombre del producto o la entidad
        ///Esto se hace para mejorar la lectura (en caso de espacios en blanco), o por requisito para el nombre de la hoja de excel
        $aguja = array(0=>" ", 1=>".", 2=>"[", 3=>"]", 4=>"*", 5=>"/", 6=>"\\", 7=>"?", 8=>":", 9=>"_", 10=>"-", 11=>'&');
        
        $nombreNodoMostrar0 = str_replace($aguja, "", ucwords($nombreCorto));
        $nombreNodoMostrar1 = substr($nombreNodoMostrar0, 0, $tamMaximoNombreNodo);
        
        /// Elimino los posibles tildes para evitar errores:
        $nombreNodoMostrar = eliminar_tildes($nombreNodoMostrar1);
        
        $timestamp = date('dmy_His');
        //$nombreArchivo = $nombreNodoMostrar."_".$timestamp.".pdf";
        $nombreArchivo = ucwords($nombreCorto)."_".$timestamp.".pdf";
        //********************************************** FIN Adaptación nombre del nodo sin tildes ni caracteres especiales *****************************
        
        //Instancio objeto de la clase:
        $pdfResumen = new PDF($orientacion,'mm','A4');
        

        switch ($origen){
          case 'usuarios':  $tituloHeader = "REPORTE DE USUARIOS";
                            $pdfResumen->AddPage();
                            $tituloTabla = "USUARIOS";
                            require_once('data/camposUsuarios.php');
                            $pdfResumen->armarTablaUsuarios();
                            break;
          case 'nodos': $tituloHeader = "REPORTE DE NODOS";
                        $pdfResumen->AddPage();
                        $tituloTabla = "NODOS";
                        require_once('data/camposNodos.php');
                        $pdfResumen->armarTablaNodos();
                        break;
          case 'buscar':
          case 'cargar':  $tituloHeader = "REPORTE DE ALARMAS";
                          $pdfResumen->AddPage();
                          $tituloTabla = "ALARMAS";
                          require_once('data/camposAlarmas.php');
                          $pdfResumen->armarTablaAlarmas();
                          break;
          default: break;
        }
        //$pdfResumen->armarTablaAlarmas();
           
        ///***************************************** GUARDADO DEL ARCHIVO EN DISCO y muestra en pantalla: ************************************
        ///***********************************************************************************************************************************
        
        ///***********************************************************************************************************************************
        //********************************* Generación de la carpeta y sub carpetas necesarias segun nombre del nodo y fecha: ****************
        if ($guardarDisco){
          $sigo = true;
          if ($archivo){
            $nombreNodoMostrar = "Archivo";
            $nombreReporte = $nombreNodoMostrar;
          }
          else {
            $nombreReporte = $nombreNodoMostrar;
          }
          $userMayuscula = strtoupper($_SESSION['username']);
          $dirUsuario = $dirReportes.$userMayuscula."/";
          if (is_dir($dirUsuario)){
            //echo "La carpeta del usuario ya existe: $dirUsuario.<br>";
          }
          else {
            $creoCarpetaUsuario = mkdir($dirUsuario);
            if ($creoCarpetaUsuario === FALSE){
              //echo "Error al crear la carpeta: $rutaCarpetaNodo.<br>";
              $sigo = false;
            }
          }
          
          $rutaCarpetaNodo = $dirUsuario.$nombreNodoMostrar;
          if (is_dir($rutaCarpetaNodo)){
            //echo "La carpeta del cliente ya existe: $rutaCarpetaNodo.<br>";
          }
          else {
            $creoCarpeta = mkdir($rutaCarpetaNodo);
            if ($creoCarpeta === FALSE){
              //echo "Error al crear la carpeta: $rutaCarpetaNodo.<br>";
              $sigo = false;
            }
            else {
              //echo "Carpeta creada con éxito: $rutaCarpetaNodo.<br>";
            }
          }

          if (setlocale(LC_ALL, 'esp') === false){
            echo "Hubo un error con la localía. Por favor verifique que se hayan creado bien las carpetas<br>";
          }

          $dia = strftime("%d", time());
          $mes = substr(ucwords(strftime("%b", time())), 0, 3);
          $year = strftime("%Y", time());
          $fechaCarpeta = $dia.$mes.$year;

          $rutaReporteFecha = $rutaCarpetaNodo."/".$fechaCarpeta;
          if (is_dir($rutaReporteFecha)){
            //echo "La carpeta del día ya existe: $rutaReporteFecha.<br>";
          }
          else {
            $creoCarpeta0 = mkdir($rutaReporteFecha);
            if ($creoCarpeta0 === FALSE){
              //echo "Error al crear la carpeta del día: $rutaReporteFecha.<br>";
              $sigo = false;
            }
            else {
              //echo "Carpeta del día creada con éxito: $rutaReporteFecha.<br>";
            }
          } 
          $subRuta = $rutaReporteFecha;

          /// Si por algún motivo, la creación de alguna de las carpetas dio error, guardo en la carpeta ya configurada y creada que sé existe.
          /// Si no hubo problemas en la creación, guardo en la carpeta creada:
          if (!($sigo)){
            $salida = $dirReportes.$nombreArchivo;
          }
          else {
            $salida = $subRuta."/".$nombreArchivo;
            $GLOBALS["dirExcel"] = $subRuta."/";
          }
          escribirLog('Se genera y guarda el pdf: "'.$salida.'"');
          $pdfResumen->Output($salida, 'F');
               
          ///**************************** FIN Generación de la carpeta y sub carpetas necesarias segun nombre del nodo y fecha: ****************
          ///***********************************************************************************************************************************

          ///Además lo muestro en pantalla:        
          $pdfResumen->Output('I');

          $archivo = generarExcelAlarmas($registros);
          escribirLog('Se genera y guarda el excel: "'.$dirExcel.$archivo.'"');
          ///**************************************** FIN GUARDADO DEL ARCHIVO EN DISCO y muestra en pantalla: *********************************

          ///************************************************************ GENERACION ZIP FILE **************************************************
          $zip = new ZipArchive;
          $nombreZip = $nombreReporte."_".$timestamp.".zip";

          /// Si por algún motivo la creación de alguna de las carpetas dio error, guardo en la carpeta ya configurada y creada que sé existe.
          /// Si no hubo problemas en la creación, guardo en la carpeta creada:
          if (!($sigo)){
            $fileDir = $dirReportes.$nombreZip;
          }
          else {
            $fileDir = $subRuta."/".$nombreZip;
          }

          $excel = $dirExcel.$archivo;

          if ($zip->open($fileDir, ZIPARCHIVE::CREATE ) !== TRUE) 
              {
              exit("No se pudo abrir el archivo\n");
              } 
          //agrego el pdf correspondiente al reporte:
          $zip->addFile($salida, $nombreArchivo);
          $zip->addFile($excel, $archivo);

  //        if ($zipSeguridad !== 'nada'){
  //          $zip->setPassword($pwdZip);
  //          $zip->setEncryptionName($archivo, ZipArchive::EM_AES_256);
  //          $zip->setEncryptionName($nombreArchivo, ZipArchive::EM_AES_256);
  //        }

          $zip->close();
          escribirLog('Se genera y guarda el zip: "'.$fileDir.'"');
          ///********************************************************** FIN GENERACION ZIP FILE ************************************************
        } 
        
        ///************************************************************** ENVÍO DE MAILS *****************************************************
        if (isset($mails)){
//          $destinatarios = explode(",", $mails);
//          foreach ($destinatarios as $valor){
//            $para["$valor"] = $valor;
//          }
          //$asunto = $asunto." (MAIL DE TEST!!!)";

          $asunto = "Mail de prueba";
          $cuerpo = utf8_decode("<html><body><h4>Se adjunta el reporte generado.</h4></body></html>");
          echo "mando mail: $asunto<br>";
          $respuesta = enviarMail($paraListados, $copiaListados, $ocultosListados, $asunto, $cuerpo, "REPORTE", $nombreZip, $fileDir);
          escribirLog("Se mandó mail");
          echo $respuesta;
        }
        ///************************************************************ FIN ENVÍO DE MAILS ***************************************************
      } /// Fin if (totalFilas >0)
    } /// Fin if (seguir)
  } /// Fin if (isset($_POST))
  else {
   echo "Hubo un error al cargar la página. Por favor verifique."; 
  } /// Fin else if (isset($_POST))
  error_reporting(E_ALL);
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors',1);
} /// Fin if(isset($_SESSION['tiempo']))
else {
  echo '<script type="text/javascript">'
  . 'alert("Tú sesión expiró.\n¡Por favor vuelve a loguearte!.");window.close();
    window.location.assign("salir.php");
     </script>';
}
?>
