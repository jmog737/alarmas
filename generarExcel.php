<?php
/**
******************************************************
*  @file generarExcel.php
*  @brief Archivo con las funciones que generan los archivos de excel.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
*
*******************************************************/
require 'vendor/autoload.php';
require_once("css/colores.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generarExcelAlarmas($reg) {
  global $nombreReporte, $tituloReporte, $camposAlarmas, $nombreNodo, $arrayNodos;
  
  /// ****************************************************** INICIO GENERAL HOJA DE DATOS ****************************************************
  $spreadsheet = new Spreadsheet();

  $locale = 'es_UY'; 
  $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
  if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

  // Set document properties
  $spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
                               ->setLastModifiedBy("Juan Martín Ortega")
                               ->setTitle("Alarmas")
                               ->setSubject("Datos exportados")
                               ->setDescription("Archivo excel con el resultado de la consulta realizada.")
                               ->setKeywords("alarmas excel php")
                               ->setCategory("Resultado");

  /// Declaro hoja activa:
  $hoja = $spreadsheet->getSheet(0);
  /// ******************************************************** FIN GENERAL HOJA DE DATOS *****************************************************
  
  ///Trabajo con el nombre para la hoja activa debido a la limitante del largo (31 caracteres):
  ///Además, ya se tienen 8 de la fecha a la cual se consultó el stock
  $timestamp = date('dmy_His');
  $timestampCorto = date('dmy');
  
  $nombreReporte1 = $nombreReporte."_".$timestampCorto;
  $hoja->setTitle($nombreReporte1);
  $hoja->getTabColor()->setRGB($GLOBALS["colorTabAlarmas"]);
  
  $colId = 'A';
  $filaBordeInicial = '3';
  $filaEncabezado = '4';
    
  $totalCampos = 0;
  $nombreCampos = array();
  foreach ($camposAlarmas as $ind => $fila ) {
    if ($fila['mostrarExcel'] === 'si'){
      $nombreCampos[] = html_entity_decode($fila['nombreMostrar']);
      $totalCampos++;
      if ($fila['nombreDB'] === 'tipoAlarma'){
        $colTipoAlarma0 = $totalCampos;
      }
    }
  } /// Fin foreach campos visibles
  $colFinal = chr(ord($colId)+$totalCampos-1);
  $colTipoAlarma = chr(ord($colId)+$colTipoAlarma0-1);
  
  ///******************************************************** INICIO formato TIPO CONSULTA ***************************************************
  $hoja->mergeCells($colId.'1:'.$colFinal.'1');
  $totalDatos = count($reg);
  $hoja->setCellValue($colId."1", $tituloReporte." (Total: ".$totalDatos.")");
  
  /// Formato del mensaje con el tipo de consulta:
  $mensajeTipo = $colId.'1:'.$colFinal.'1';

  $styleMensajeTipo = array(
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoTitulo"]),
          'size' => 16,
        ),
//      'borders' => array(
//              'allBorders' => array(
//                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
//                'color' => array('rgb' => $GLOBALS["colorBordeTitulo"]),
//                ),
//              ), 
      'alignment' => array(
         'wrap' => true,
         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
    // Comento porque ahora está con fondo blanco!:
//      'fill' => array(
//          'color' => array('rgb' => $GLOBALS["colorFondoTitulo"]),
//          'fillType' => 'solid',
//        ),
      );
  $hoja->getStyle($mensajeTipo)->applyFromArray($styleMensajeTipo);
  ///*********************************************************** FIN formato TIPO CONSULTA ***************************************************
  
  ///**************************************************** INICIO formato BORDES INICIAL Y FINAL **********************************************
  $styleBordeFinal = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoBorde"]),
          'fillType' => 'solid',
      ),
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoSubTitulo"]),
          'size' => 15,
        ),
      'alignment' => array(
       'wrap' => true,
       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
       'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
  );
  ///****************************************************** FIN formato BORDES INICIAL Y FINAL ***********************************************
  
  $rangoFilaBordeInicial = $colId.$filaBordeInicial.":".$colFinal.$filaBordeInicial;
  $hoja->mergeCells($colId.$filaBordeInicial.':'.$colFinal.$filaBordeInicial);
  $hoja->setCellValue($colId.$filaBordeInicial, "ALARMAS");
  $hoja->getStyle($rangoFilaBordeInicial)->applyFromArray($styleBordeFinal);
  
  ///***************************************************************** INICIO CAMPOS *********************************************************
  // Agrego los títulos:
  $celda0 = $colId.$filaEncabezado;
  $hoja->fromArray($nombreCampos, '""', $celda0);
  
  /// Formato de los títulos:
  $header = $colId.$filaEncabezado.':'.$colFinal.$filaEncabezado;
  $styleHeader = array(
    'fill' => array(
        'color' => array('rgb' => $GLOBALS["colorFondoCampos"]),
        'fillType' => 'solid',
      ),
    'font' => array(
        'bold' => true,
        'color' => array ('rgb' => $GLOBALS["colorTextoCampos"]),
        'size' => 14,
      ),
    'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
  );
  $hoja->getStyle($header)->applyFromArray($styleHeader);
  ///******************************************************************* FIN CAMPOS **********************************************************

  ///***************************************************************** INICIO DATOS **********************************************************
  if ($nombreNodo === 'TODOS'){
    $nodoAnterior = '';
  }
  
  /// Defino estilos para los nodos:
  $styleNodo = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoNodo"]),
          'fillType' => 'solid',
      ),
      'font' => array(
        'bold' => true,
        'color' => array ('rgb' => $GLOBALS["colorTextoNodo"]),
        'size' => 14,
      ),
  );
  
  $j = $filaEncabezado + 1;
  $cambio = false;
  /// Datos de los campos:
  foreach ($reg as $i => $filita) {
    $fila = array();
    
    /// Para el caso en que se consultan TODOS los nodos, detecto el cambio de nodo y agrego subtítulo indicando el  nuevo nodo:
    if (isset($nodoAnterior)&&($nodoAnterior === '')){
      $nodoAnterior = $filita['nodo'];
      $hoja->mergeCells($colId.$j.':'.$colFinal.$j);
      $hoja->setCellValue($colId.$j, html_entity_decode($arrayNodos[$nodoAnterior]));
      $hoja->getStyle($colId.$j)->applyFromArray($styleNodo);
      $j++;
    }
    else {
      $cambio = false;
    }
    $nodoActual = $filita['nodo'];
    if (isset($nodoAnterior)&&($nodoActual !== $nodoAnterior)){
      $nodoAnterior = $nodoActual;
      $hoja->mergeCells($colId.$j.':'.$colFinal.$j);
      $hoja->setCellValue($colId.$j, html_entity_decode($arrayNodos[$nodoAnterior]));
      $hoja->getStyle($colId.$j)->applyFromArray($styleNodo);
      $cambio = true;
    }
    else {
      $cambio = false;
    }
    
    foreach ($camposAlarmas as $indice => $datoCampo ) {
      if ($datoCampo['mostrarExcel'] === 'si'){
        switch ($datoCampo['nombreDB']){
          case 'dia': $temp = explode('-', $filita[$datoCampo['nombreDB']]);
                      $datito = $temp[2].'/'.$temp[1].'/'.$temp[0];
                      break;
          case 'id':  //if (!$cambio) {
                        $datito = $i+1;
                      //}
                      //else {
                        //$datito = $j;
                      //}  
                      break;         
          default:  $datito = trim(utf8_decode(html_entity_decode($filita[$datoCampo['nombreDB']])));
                    break;
        }
        $fila[] = $datito;
      } /// Fin if datoCampo['mostrar'] === si
    } /// Fin foreach datoCampo

    /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
    if ($cambio){
      $j++;
    }
    
    $celda = $colId.$j;
    $hoja->fromArray($fila, '""', $celda);
    $j++;
  }
  
  $bordeFinal = $colId.$j.":".$colFinal.$j;
  $hoja->getStyle($bordeFinal)->applyFromArray($styleBordeFinal);
  
  $filaFinal = $j - 1;
  ///******************************************************************* FIN DATOS ***********************************************************

  /// ******************************************************** INICIO formato GENERAL ********************************************************
  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = $colId.$filaEncabezado.":".$colFinal.$filaFinal;
  /// Defino el formato para las celdas:
  $styleGeneral = array(
      'borders' => array(
          'allBorders' => array(
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
              'color' => array('rgb' => $GLOBALS["colorBordeRegular"]),
          ),
      ),
      'alignment' => array(
         'wrap' => true,
         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
  );
  $hoja->getStyle($rango)->applyFromArray($styleGeneral);
  /// ********************************************************** FIN formato GENERAL *********************************************************
  
  /// ***************************************************** INICIO ESTILOS TIPO DE ALARMAS ***************************************************
  /// Defino estilos para los tipo de alarmas:
  $styleMJ = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoMJ"]),
          'fillType' => 'solid',
      ),
  );

  $styleMN = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoMN"]),
          'fillType' => 'solid',
      ),
  );
  
  $styleCR = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoCR"]),
          'fillType' => 'solid',
      ),
  );
  
  $styleWR = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoWR"]),
          'fillType' => 'solid',
      ),
  );
  
  $styleNA = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoNA"]),
          'fillType' => 'solid',
      ),
  );
  
  $styleNR = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoNR"]),
          'fillType' => 'solid',
      ),
  );

  /// Aplico color de fondo de la columna tipo de alarma según el valor:
  for ($k = $filaEncabezado + 1; $k <= $filaFinal; $k++) {
    $fila = $colId.$k.':'.$colFinal.$k;
    $celda = $colTipoAlarma.$k;
    $valorCelda = $hoja->getCell($celda)->getValue();
    switch ($valorCelda){
      case 'CR': $hoja->getStyle($fila)->applyFromArray($styleCR);
                 break;
      case 'MN':  $hoja->getStyle($fila)->applyFromArray($styleMN);
                  break;
      case 'MJ':  $hoja->getStyle($fila)->applyFromArray($styleMJ);
                  break;
      case 'WR':  $hoja->getStyle($fila)->applyFromArray($styleWR);
                  break;
      case 'NA':  $hoja->getStyle($fila)->applyFromArray($styleNA);
                  break;
      case 'NR':  $hoja->getStyle($fila)->applyFromArray($styleNR);
                  break;          
      default: break;  
    }
  }  
  /// ***************************************************** FIN ESTILOS TIPO DE ALARMAS ******************************************************
  
  
  
  /// ****************************************************** INICIO AUTOAJUSTE COLUMNAS ******************************************************
  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord(''.$colId.''); $col <= ord(''.$colFinal.''); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
  }
  /// ***************************************************** FIN AUTOAJUSTE COLUMNAS **********************************************************
  
  /// ********************************************************** INICIO SEGURIDAD ************************************************************
//  switch ($planilla){
//    case "nada": break;
//    case "misma": if ($zipSeguridad !== 'nada') {
//                    $pwdPlanilla = $pwdZip;
//                  }
//                  break;
//    case "fecha": $pwdPlanilla = $timestamp; 
//                  break;
//    case "random": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    case "manual": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    default: break;
//  } 
//  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
//    ///Agrego protección para la hoja activa:
//    $hoja->getProtection()->setPassword($pwdPlanilla);
//    $hoja->getProtection()->setSheet(true);
//  }
  /// ************************************************************ FIN SEGURIDAD *************************************************************
  
  /// ********************************************************* INICIO GUARDADO **************************************************************
  // Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);
  
  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"].$nombreArchivo;
  $writer->save($salida);
  /// *********************************************************** FIN GUARDADO ***************************************************************
  
  return $nombreArchivo;
}

function generarExcelNodos($reg) {
  global $nombreReporte, $tituloReporte, $camposNodos;
  
  /// ****************************************************** INICIO GENERAL HOJA DE DATOS ****************************************************
  $spreadsheet = new Spreadsheet();

  $locale = 'es_UY'; 
  $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
  if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

  // Set document properties
  $spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
                               ->setLastModifiedBy("Juan Martín Ortega")
                               ->setTitle("Nodos")
                               ->setSubject("Datos exportados")
                               ->setDescription("Archivo excel con el resultado de la consulta realizada.")
                               ->setKeywords("nodos excel php")
                               ->setCategory("Resultado");

  /// Declaro hoja activa:
  $hoja = $spreadsheet->getSheet(0);
  /// ******************************************************** FIN GENERAL HOJA DE DATOS *****************************************************
  
  ///Trabajo con el nombre para la hoja activa debido a la limitante del largo (31 caracteres):
  ///Además, ya se tienen 8 de la fecha a la cual se consultó el stock
  $timestamp = date('dmy_His');
  $timestampCorto = date('dmy');
  
  $nombreReporte1 = $nombreReporte."_".$timestampCorto;
  $hoja->setTitle($nombreReporte1);
  $hoja->getTabColor()->setRGB($GLOBALS["colorTabAlarmas"]);
  
  $colId = 'A';
  $filaBordeInicial = '3';
  $filaEncabezado = '4';
  
  $colIp = '';
  $totalCampos = 0;
  $nombreCampos = array();
  foreach ($camposNodos as $ind => $fila ) {
    if ($fila['mostrarExcel'] === 'si'){
      $nombreCampos[] = html_entity_decode($fila['nombreMostrar']);
      if ($fila['nombreMostrar'] === 'IP'){
        $colIp = chr(ord($colId) + $totalCampos);
      }
      $totalCampos++;
    }
  } /// Fin foreach campos visibles
  $colFinal = chr(ord($colId)+$totalCampos-1);

  ///******************************************************** INICIO formato TIPO CONSULTA ***************************************************
  $hoja->mergeCells($colId.'1:'.$colFinal.'1');
  $hoja->setCellValue($colId."1", $tituloReporte);
  
  /// Formato del mensaje con el tipo de consulta:
  $mensajeTipo = $colId.'1:'.$colFinal.'1';
  $styleMensajeTipo = array(
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoTitulo"]),
          'size' => 16,
        ),
//      'borders' => array(
//              'allBorders' => array(
//                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
//                'color' => array('rgb' => $GLOBALS["colorBordeTitulo"]),
//                ),
//              ), 
      'alignment' => array(
         'wrap' => true,
         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
    // Comento porque ahora está con fondo blanco!:
//      'fill' => array(
//          'color' => array('rgb' => $GLOBALS["colorFondoTitulo"]),
//          'fillType' => 'solid',
//        ),
      );
  $hoja->getStyle($mensajeTipo)->applyFromArray($styleMensajeTipo);
  ///*********************************************************** FIN formato TIPO CONSULTA ***************************************************
  
  ///**************************************************** INICIO formato BORDES INICIAL Y FINAL **********************************************
  $styleBordeFinal = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoBorde"]),
          'fillType' => 'solid',
      ),
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoSubTitulo"]),
          'size' => 15,
        ),
      'alignment' => array(
       'wrap' => true,
       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
       'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
  );
  ///****************************************************** FIN formato BORDES INICIAL Y FINAL ***********************************************
  
  $rangoFilaBordeInicial = $colId.$filaBordeInicial.":".$colFinal.$filaBordeInicial;
  $hoja->mergeCells($colId.$filaBordeInicial.':'.$colFinal.$filaBordeInicial);
  $hoja->setCellValue($colId.$filaBordeInicial, "NODOS");
  $hoja->getStyle($rangoFilaBordeInicial)->applyFromArray($styleBordeFinal);
  
  ///***************************************************************** INICIO CAMPOS *********************************************************
  // Agrego los títulos:
  $celda0 = $colId.$filaEncabezado;
  $hoja->fromArray($nombreCampos, '""', $celda0);
  ///******************************************************************* FIN CAMPOS **********************************************************
  
  ///***************************************************************** INICIO DATOS **********************************************************  
  $j = $filaEncabezado + 1;

  /// Datos de los campos:
  foreach ($reg as $i => $filita) {
    $fila = array();
        
    foreach ($camposNodos as $indice => $datoCampo ) {
      if ($datoCampo['mostrarExcel'] === 'si'){
        switch ($datoCampo['nombreDB']){
          case 'id':  $datito = $i+1;
                      break;  
          case 'areaMetro': if ($filita[$datoCampo['nombreDB']] === "1"){
                              $datito = "SI";
                            }
                            else {
                              $datito = "NO";
                            }
                            break;          
          default:  $datito = trim($filita[$datoCampo['nombreDB']]);
                    break;
        }
        $fila[] = $datito;
      } /// Fin if datoCampo['mostrar'] === si
    } /// Fin foreach datoCampo
    
    $celda = $colId.$j;
    $hoja->fromArray($fila, '""', $celda);
    $j++;
  }
  $filaFinal = $j - 1;
  ///******************************************************************* FIN DATOS ***********************************************************
  
  ///************************************************************ AGREGO BORDE FINAL *********************************************************
  $bordeFinal = $colId.$j.":".$colFinal.$j;
  $hoja->getStyle($bordeFinal)->applyFromArray($styleBordeFinal);
  ///************************************************************** FIN BORDE FINAL **********************************************************

  /// ******************************************************** INICIO formato GENERAL ********************************************************
  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = $colId.$filaEncabezado.":".$colFinal.$filaFinal;
  /// Defino el formato para las celdas:
  $styleGeneral = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color' => array('rgb' => $GLOBALS["colorBordeRegular"]),
        ),
    ),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ),
    'fill' => array(
      'color' => array('rgb' => $GLOBALS["colorFondoDatos"]),
      'fillType' => 'solid',
    ),
  );
  $hoja->getStyle($rango)->applyFromArray($styleGeneral);
  /// ********************************************************** FIN formato GENERAL *********************************************************
  
  ///********************************************************** INICIO FORMATO CAMPOS ********************************************************
  /// Formato de los títulos:
  $header = $colId.$filaEncabezado.':'.$colFinal.$filaEncabezado;
  $styleHeader = array(
    'fill' => array(
        'color' => array('rgb' => $GLOBALS["colorFondoCampos"]),
        'fillType' => 'solid',
      ),
    'font' => array(
        'bold' => true,
        'color' => array ('rgb' => $GLOBALS["colorTextoCampos"]),
        'size' => 14,
      ),
    'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
  );
  $hoja->getStyle($header)->applyFromArray($styleHeader);
  ///*********************************************************** FIN FORMATO CAMPOS **********************************************************
  
  ///************************************************************* INICIO HIPERVÍNCULO *******************************************************
  $k = $filaEncabezado + 1;
  while ($k <= $filaFinal){
    $valor = $hoja->getCell($colIp.$k)->getValue();
    $hoja->getCell($colIp.$k)->getHyperlink()->setUrl($valor);
    $k++;
  }
  ///*************************************************************** FIN HIPERVÍNCULO ********************************************************
  
  ///*************************************************************** INICIO ALINEACIÓN *******************************************************
  $inicioDatos = $filaEncabezado + 1;
  $colNombre = chr(ord($colId) + 1);
  $colLocalidad = chr(ord($colId) + 3);

  $alIzq = $colNombre.$inicioDatos.':'.$colNombre.$filaFinal;
  $alIzq1 = $colLocalidad.$inicioDatos.':'.$colLocalidad.$filaFinal;
  $alIzq2 = $colIp.$inicioDatos.':'.$colIp.$filaFinal;
  
  $styleIzq = array(
//    'fill' => array(
//        'color' => array('rgb' => $GLOBALS["colorFondoCampos"]),
//        'fillType' => 'solid',
//      ),
//    'font' => array(
//        'bold' => true,
//        'color' => array ('rgb' => $GLOBALS["colorTextoCampos"]),
//        'size' => 14,
//      ),
    'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
      ),
  );

  $hoja->getStyle($alIzq)->applyFromArray($styleIzq);
  $hoja->getStyle($alIzq1)->applyFromArray($styleIzq);
  $hoja->getStyle($alIzq2)->applyFromArray($styleIzq);
  
  ///***************************************************************** FIN ALINEACIÓN ********************************************************
    
  /// ****************************************************** INICIO AUTOAJUSTE COLUMNAS ******************************************************
  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord(''.$colId.''); $col <= ord(''.$colFinal.''); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
  }
  /// ***************************************************** FIN AUTOAJUSTE COLUMNAS **********************************************************
  
  /// ********************************************************** INICIO SEGURIDAD ************************************************************
//  switch ($planilla){
//    case "nada": break;
//    case "misma": if ($zipSeguridad !== 'nada') {
//                    $pwdPlanilla = $pwdZip;
//                  }
//                  break;
//    case "fecha": $pwdPlanilla = $timestamp; 
//                  break;
//    case "random": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    case "manual": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    default: break;
//  } 
//  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
//    ///Agrego protección para la hoja activa:
//    $hoja->getProtection()->setPassword($pwdPlanilla);
//    $hoja->getProtection()->setSheet(true);
//  }
  /// ************************************************************ FIN SEGURIDAD *************************************************************
  
  /// ********************************************************* INICIO GUARDADO **************************************************************
  // Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);
  
  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"].$nombreArchivo;
  $writer->save($salida);
  /// *********************************************************** FIN GUARDADO ***************************************************************
  
  return $nombreArchivo;
}

function generarExcelUsuarios($reg) {
  global $nombreReporte, $tituloReporte, $camposUsuarios;
  
  /// ****************************************************** INICIO GENERAL HOJA DE DATOS ****************************************************
  $spreadsheet = new Spreadsheet();

  $locale = 'es_UY'; 
  $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
  if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

  // Set document properties
  $spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
                               ->setLastModifiedBy("Juan Martín Ortega")
                               ->setTitle("Usuarios")
                               ->setSubject("Datos exportados")
                               ->setDescription("Archivo excel con el resultado de la consulta realizada.")
                               ->setKeywords("usuarios excel php")
                               ->setCategory("Resultado");

  /// Declaro hoja activa:
  $hoja = $spreadsheet->getSheet(0);
  /// ******************************************************** FIN GENERAL HOJA DE DATOS *****************************************************
  
  ///Trabajo con el nombre para la hoja activa debido a la limitante del largo (31 caracteres):
  ///Además, ya se tienen 8 de la fecha a la cual se consultó el stock
  $timestamp = date('dmy_His');
  $timestampCorto = date('dmy');
  
  $nombreReporte1 = $nombreReporte."_".$timestampCorto;
  $hoja->setTitle($nombreReporte1);
  $hoja->getTabColor()->setRGB($GLOBALS["colorTabAlarmas"]);
  
  $colId = 'A';
  $filaBordeInicial = '3';
  $filaEncabezado = '4';
  
  $totalCampos = 0;
  $nombreCampos = array();
  foreach ($camposUsuarios as $ind => $fila ) {
    if ($fila['mostrarExcel'] === 'si'){
      $nombreCampos[] = html_entity_decode($fila['nombreMostrar']);
      $totalCampos++;
    }
  } /// Fin foreach campos visibles
  $colFinal = chr(ord($colId)+$totalCampos-1);
  
  ///******************************************************** INICIO formato TIPO CONSULTA ***************************************************
  $hoja->mergeCells($colId.'1:'.$colFinal.'1');
  $hoja->setCellValue($colId."1", $tituloReporte);

  /// Formato del mensaje con el tipo de consulta:
  $mensajeTipo = $colId.'1:'.$colFinal.'1';
  $styleMensajeTipo = array(
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoTitulo"]),
          'size' => 16,
        ),
//      'borders' => array(
//              'allBorders' => array(
//                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
//                'color' => array('rgb' => $GLOBALS["colorBordeTitulo"]),
//                ),
//              ), 
      'alignment' => array(
         'wrap' => true,
         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
    // Comento porque ahora está con fondo blanco!:
//      'fill' => array(
//          'color' => array('rgb' => $GLOBALS["colorFondoTitulo"]),
//          'fillType' => 'solid',
//        ),
      );
  $hoja->getStyle($mensajeTipo)->applyFromArray($styleMensajeTipo);
  ///*********************************************************** FIN formato TIPO CONSULTA ***************************************************
  
   ///**************************************************** INICIO formato BORDES INICIAL Y FINAL **********************************************
  $styleBordeFinal = array(
      'fill' => array(
          'color' => array ('rgb' => $GLOBALS["colorFondoBorde"]),
          'fillType' => 'solid',
      ),
      'font' => array(
          'bold' => true,
          'underline' => true,
          'color' => array('rgb' => $GLOBALS["colorTextoSubTitulo"]),
          'size' => 15,
        ),
      'alignment' => array(
       'wrap' => true,
       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
       'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
      ),
  );
  ///****************************************************** FIN formato BORDES INICIAL Y FINAL ***********************************************
  
  $rangoFilaBordeInicial = $colId.$filaBordeInicial.":".$colFinal.$filaBordeInicial;
  $hoja->mergeCells($colId.$filaBordeInicial.':'.$colFinal.$filaBordeInicial);
  $hoja->setCellValue($colId.$filaBordeInicial, "USUARIOS");
  $hoja->getStyle($rangoFilaBordeInicial)->applyFromArray($styleBordeFinal);
  
  ///***************************************************************** INICIO CAMPOS *********************************************************
  // Agrego los títulos:
  $celda0 = $colId.$filaEncabezado;
  $hoja->fromArray($nombreCampos, '""', $celda0);
  ///******************************************************************* FIN CAMPOS **********************************************************
  
  ///***************************************************************** INICIO DATOS ********************************************************** 
  $j = $filaEncabezado + 1;

  /// Datos de los campos:
  foreach ($reg as $i => $filita) {
    $fila = array();
        
    foreach ($camposUsuarios as $indice => $datoCampo ) {
      if ($datoCampo['mostrarExcel'] === 'si'){
        switch ($datoCampo['nombreDB']){
          case 'id':  $datito = $i+1;
                      break;
          case 'limiteSelects':          
          case 'tamPagina': if ($filita[$datoCampo['nombreDB']] === null){
                              $datito = "No ingresado";
                            }
                            else {
                              $datito = $filita[$datoCampo['nombreDB']];
                            }
                            break;           
          default:  $datito = trim($filita[$datoCampo['nombreDB']]);
                    break;
        }
        $fila[] = $datito;
      } /// Fin if datoCampo['mostrar'] === si
    } /// Fin foreach datoCampo
   
    $celda = $colId.$j;
    $hoja->fromArray($fila, '""', $celda);
    $j++;
  }
  $filaFinal = $j - 1;
  ///******************************************************************* FIN DATOS ***********************************************************

  ///************************************************************ AGREGO BORDE FINAL *********************************************************
  $bordeFinal = $colId.$j.":".$colFinal.$j;
  $hoja->getStyle($bordeFinal)->applyFromArray($styleBordeFinal);
  ///************************************************************** FIN BORDE FINAL **********************************************************
  
  /// ******************************************************** INICIO formato GENERAL ********************************************************
  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = $colId.$filaEncabezado.":".$colFinal.$filaFinal;
  /// Defino el formato para las celdas:
  $styleGeneral = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color' => array('rgb' => $GLOBALS["colorBordeRegular"]),
        ),
    ),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ),
    'fill' => array(
      'color' => array('rgb' => $GLOBALS["colorFondoDatos"]),
      'fillType' => 'solid',
    ),
  );
  $hoja->getStyle($rango)->applyFromArray($styleGeneral);
  /// ********************************************************** FIN formato GENERAL *********************************************************
  
  ///********************************************************** INICIO FORMATO CAMPOS ********************************************************
  /// Formato de los títulos:
  $header = $colId.$filaEncabezado.':'.$colFinal.$filaEncabezado;
  $styleHeader = array(
    'fill' => array(
        'color' => array('rgb' => $GLOBALS["colorFondoCampos"]),
        'fillType' => 'solid',
      ),
    'font' => array(
        'bold' => true,
        'color' => array ('rgb' => $GLOBALS["colorTextoCampos"]),
        'size' => 14,
      ),
    'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
  );
  $hoja->getStyle($header)->applyFromArray($styleHeader);
  ///*********************************************************** FIN FORMATO CAMPOS **********************************************************
  
  /// ****************************************************** INICIO AUTOAJUSTE COLUMNAS ******************************************************
  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord(''.$colId.''); $col <= ord(''.$colFinal.''); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
  }
  /// ***************************************************** FIN AUTOAJUSTE COLUMNAS **********************************************************
  
  /// ********************************************************** INICIO SEGURIDAD ************************************************************
//  switch ($planilla){
//    case "nada": break;
//    case "misma": if ($zipSeguridad !== 'nada') {
//                    $pwdPlanilla = $pwdZip;
//                  }
//                  break;
//    case "fecha": $pwdPlanilla = $timestamp; 
//                  break;
//    case "random": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    case "manual": $pwdPlanilla = $pwdPlanillaManual;
//                   break;
//    default: break;
//  } 
//  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
//    ///Agrego protección para la hoja activa:
//    $hoja->getProtection()->setPassword($pwdPlanilla);
//    $hoja->getProtection()->setSheet(true);
//  }
  /// ************************************************************ FIN SEGURIDAD *************************************************************
  
  /// ********************************************************* INICIO GUARDADO **************************************************************
  // Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);
  
  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"].$nombreArchivo;
  $writer->save($salida);
  /// *********************************************************** FIN GUARDADO ***************************************************************
  
  return $nombreArchivo;
}