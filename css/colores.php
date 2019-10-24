<?php

if (!isset($_SESSION)) {
  //Reanudamos la sesión:
  session_start();
}
/* !
  @file colores.php
  @brief Archivo que contiene las constantes predefinidas con los colores usados para generar los pdfs. \n
  @version v.1.0.
  @author Juan Martín Ortega
 */

//$testColor = array(255, 180, 203);
//$colorHexa = sprintf("#%02x%02x%02x", $testColor[0], $testColor[1], $testColor[2]);echo "testColor: $testColor<br>colorHexa: $colorHexa<br>";
///**************************************************** COLORES PDFs *************************************************************
///Color para la marca de agua:
define("colorMarcaAgua", array(255, 180, 203));

///Color para el título del Header:
define("colorHeaderTituloTexto", array(0, 0, 0));
define("colorHeaderTituloFondo", array(237, 233, 232));

///Color para el texto del Footer:
define("colorFooterTexto", array(0, 0, 0));
///Color para el fondo del Footer:
define("colorFooterFondo", array(255, 255, 255));

///Color para el texto legal en el footer:
define("colorTextoLegal", array(234, 229, 227));

///Color Título del reporte:
define("colorTituloReporte", array(255, 0, 0));

///Color Título de la tabla:
//define("colorTituloTablaTexto", array(255, 255, 255));
//define("colorTituloTablaFondo", array(2, 49, 136));
define("colorTituloTablaTexto", array(255, 255, 255));
//define("colorTituloTablaFondo", array(23, 162, 184));
define("colorTituloTablaFondo", array(69, 169, 85));

///Color Subtítulo:
define("colorSubtitulo", array(134, 144, 144));

///Color SubtítuloTabla:
//define("colorSubtituloTablaTexto", array(255, 0, 0));
//define("colorSubtituloTablaFondo", array(40, 206, 68));
define("colorSubtituloTablaTexto", array(255, 255, 255));
//define("colorSubtituloTablaFondo", array(136, 179, 143));
define("colorSubtituloTablaFondo", array(119, 189, 130));

///Borde redondeado intermedio:
define("colorBordeRedondeado", array(157, 176, 243));

///Color Campos:
//define("colorCamposFondo", array(0, 176, 243));
//define("colorCamposTexto", array(255, 255, 255));
define("colorCamposFondo", array(23, 162, 184));
define("colorCamposTexto", array(255, 255, 0));

///Color Registros:
define("colorRegistrosTexto", array(0, 0, 0));
define("colorRegistrosFondo", array(220, 223, 232));

///Colores para los tipos de alarma:
//define("colorAlarmaMNFondo", array(255, 255, 124));
//define("colorAlarmaMJFondo", array(255, 169, 41));
//define("colorAlarmaCRFondo", array(253, 81, 81));
//define("colorAlarmaWRFondo", array(117, 236, 236));
define("colorAlarmaMNFondo", array(255, 238, 186));
define("colorAlarmaMJFondo", array(255, 187, 85));
define("colorAlarmaCRFondo", array(245, 198, 203));
define("colorAlarmaWRFondo", array(190, 229, 235));
define("colorAlarmaNAFondo", array(159, 182, 133));
define("colorAlarmaNRFondo", array(153, 153, 153));

///Colores para los comentarios:
define("colorComPlastico", array(234, 140, 160));
define("colorComStock", array(4, 255, 20));
define("colorComDiff", array(255, 255, 51));
define("colorComRegular", array(220, 223, 232));

///Colores para el stock:
define("colorStockAlarma1", array(255, 255, 51));
define("colorStockAlarma2", array(231, 56, 67));
define("colorStockRegular", array(113, 236, 113));

///Retiros, Renovaciones, Destrucciones, Consumos, Ingresos, AjusteRetiros, y AjusteIngresos:
define("colorRetiros", array(157, 176, 243));
define("colorRenos", array(157, 176, 243));
define("colorDestrucciones", array(157, 176, 243));
define("colorConsumos", array(75, 90, 243));
define("colorIngresos", array(39, 222, 93));
define("colorAjusteRetiros", array(162, 92, 243));
define("colorAjusteIngresos", array(255, 193, 104));

define("colorPromedio", array(249, 143, 8));

define("colorTotal", array(0, 255, 255));
///********************************************************** FIN COLORES PDFs **************************************************
///****************************************************** COLORES para las GRAFICAS *********************************************
$colorRetirosGrafica = array(17, 17, 204);
$colorRenosGrafica = array(240, 138, 29);
$colorDestruccionesGrafica = array(255, 7, 25);
$colorIngresosGrafica = array(25, 82, 46);
$colorAjusteRetirosGrafica = array(162, 92, 243);
$colorAjusteIngresosGrafica = array(255, 193, 104);
$colorPromedio = array(249, 143, 8);
$colorNombreEjeX = 'white';
$colorNombreEjeY = 'white';
$colorEjeX = 'white';
$colorEjeY = 'white';
$colorFrame = 'red';
$colorBordeRetiros = 'white';
$colorBordeIngresos = 'white';
$colorBordeRenos = 'white';
$colorBordeDestrucciones = 'white';
$colorBordeAjusteRetiros = 'white';
$colorBordeAjusteIngresos = 'white';

$colorTituloLeyenda = 'red';
$colorFondoTituloLeyenda1 = '#ac90d4';
$colorFondoTituloLeyenda2 = '#0ca0d4';

$colorShadowLeyenda = '#e2bd6e@1';
$colorFondoLeyenda = 'white';
$colorTextoLeyenda = 'blue';
$colorBordeLeyenda = 'white';

$colorLeyendaRetiros = '#023184:0.98';
$colorFondoLeyendaRetiros1 = 'navajowhite1';
$colorFondoLeyendaRetiros2 = 'white';

$colorLeyendaRenos = '#023184:0.98';
$colorFondoLeyendaRenos1 = 'navajowhite1';
$colorFondoLeyendaRenos2 = 'white';

$colorLeyendaDestrucciones = '#023184:0.98';
$colorFondoLeyendaDestrucciones1 = 'navajowhite1';
$colorFondoLeyendaDestrucciones2 = 'white';

$colorLeyendaIngresos = '#258246:0.98';
$colorFondoLeyendaIngresos1 = 'navajowhite1';
$colorFondoLeyendaIngresos2 = 'white';

$colorLeyendaAjusteRetiros = '#a25cf3:0.98';
$colorFondoLeyendaAjusteRetiros1 = 'navajowhite1';
$colorFondoLeyendaAjusteRetiros2 = 'white';

$colorLeyendaAjusteIngresos = '#a25cf3:0.98';
$colorFondoLeyendaAjusteIngresos1 = 'navajowhite1';
$colorFondoLeyendaAjusteIngresos2 = 'white';

$colorLeyendaConsumos = 'red:0.98';
$colorFondoLeyendaConsumos1 = 'navajowhite1';
$colorFondoLeyendaConsumos2 = 'white';

$colorGradiente1 = '#02bd6e';
$colorGradiente2 = '#023184:0.98';

///Colores para la gráfica tipo torta (cuando es por producto).
///El orden es: retiros, ingresos, renos, destrucciones, ajuste retiros y ajuste ingresos:
$coloresTorta = array('blue', 'forestgreen', '#ff9600', 'red', '#a25cf3', '#ffc168');
$colorPorcentajes = 'blue';
$colorBackgroundTorta = 'ivory3';
$colorShadowLeyendaPie = '#e2bd6e@1';
$colorTituloLeyendaPie = 'red';
$colorFondoTituloLeyendaPie1 = '#b19dda';
$colorFondoTituloLeyendaPie2 = '#7c90d4';

///***************************************************** FIN COLORES para las GRAFICAS *******************************************
//
///************************************************************* COLORES EXCEL ***************************************************
///NOTA: SOLO ACEPTA EN FORMATO HEXA (Salvo en formato de números)
//$colorTabAlarmas = '023184';
//$colorTabBoveda = '46A743';
$colorTabAlarmas = 'E02309';

//$colorBordeTitulo = '023184';
$colorBordeTitulo = 'ffffff';
//$colorFondoTitulo = sprintf("%02x%02x%02x", colorSubtitulo[0], colorSubtitulo[1], colorSubtitulo[2]);
//$colorFondoTitulo = '238632';//4acba7
//$colorTextoTitulo = 'ffffff';
$colorFondoTitulo = 'ffffff';
$colorTextoTitulo = 'ff0000';

$colorFondoBorde = '45a955';
$colorTextoSubTitulo = 'ffffff';
$colorTextoDatos = '8c8080';
$colorFondoDatos = 'ffeeba';
$colorFondoDatosCeleste = 'bee5eb';

//$colorFondoCampos = 'AEE2FA';
$colorFondoCampos = '17a2b8';
$colorTextoCampos = 'ffff00';
$colorFondoTextoLegal = 'DFDFDF';

$colorTotal = 'ff0000';
$colorFondoTotal = 'f3FF00';

$colorStock = 'Blue';
$colorFondoDefault = 'A9FF96';
//$colorFondoMN = 'ffff7c';
//$colorFondoMJ = 'ffa929';
//$colorFondoCR = 'fd5151';
//$colorFondoWR = '75ecec';
//$colorFondoNA = '9FB685';
//$colorFondoNR = '999999';
//$colorFondoNodo = '28ce44';
//$colorTextoNodo = '071c40';
$colorFondoMN = 'ffeeba';
$colorFondoMJ = 'ffbb55';
$colorFondoCR = 'f5c6cb';
$colorFondoWR = 'bee5eb';
$colorFondoNA = '9FB685';
$colorFondoNR = '999999';

$colorFondoNodo = '77bd82';
$colorTextoNodo = 'ffffff';

$colorComRegular = 'd3d3d3';
$colorComDiff = 'ffff00';
$colorComStock = '38ff1d';
$colorComPlastico = 'FF9999';

//$colorBordeRegular = '023184';
$colorBordeRegular = 'ffffff';
$colorFondoCamposResumen = 'b3a8ac';
$colorBordeResumen = '023184';
$colorCategorias = 'Blue';
$colorConsumos = 'ff0000';
$colorFondoConsumos = 'ffff99';
$colorIngresos = 'ff0000';
$colorFondoIngresos = 'cefdd5';
$colorFondoTotalesCategoria = '888888';
$colorTextoTotalResumen = 'Red';
$colorTextoTotalesCategoria = 'ff0000';
$colorTotalesCategoria = 'Red';
$colorConsumosTotal = 'ff0000';
$colorFondoTotalConsumos = 'feff00';
$colorIngresosTotal = 'ff0000';
$colorFondoTotalIngresos = '00ff11';
$colorFondoFecha = 'A9FF96';
$colorAjustesRetiros = 'ff0000';
$colorAjustesIngresos = 'ff0000';
$colorFondoAjustesRetiros = 'd5baf5';
$colorFondoAjustesIngresos = 'ffe5bf';
$colorAjustesRetirosTotal = 'ff0000';
$colorAjustesIngresosTotal = 'ff0000';
$colorFondoAjustesRetirosTotal = 'a25cf3';
$colorFondoAjustesIngresosTotal = 'ffc168';

///************************************************************ FIN COLORES EXCEL *************************************************
?>
