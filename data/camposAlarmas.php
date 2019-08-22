<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file camposAlarmas.php
*  @brief Archivo que se encarga de definir y reordenar los campos a mostrar de la tabla alarmas.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/

/// Defino array con todos los campos presentes en la base de datos, su nombre a mostrar en pantalla, el orden a mostrar y si se
/// tienen que mostrar o no.
$camposAlarmas[] = ['nombreDB'=>'id','nombreMostrar'=>'Id', 'orden'=>0, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'idalarma','nombreMostrar'=>'Id Alarma', 'orden'=>1, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no'];
$camposAlarmas[] = ['nombreDB'=>'dia','nombreMostrar'=>'Fecha de Alarma', 'orden'=>2, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'hora','nombreMostrar'=>'Hora', 'orden'=>3, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>4, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'compound','nombreMostrar'=>'Compund', 'orden'=>5, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'tipoAID','nombreMostrar'=>'Tipo AID', 'orden'=>6, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'tipoAlarma','nombreMostrar'=>'Tipo de Alarma', 'orden'=>7, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'tipoCondicion','nombreMostrar'=>'Tipo de Condici&oacute;n', 'orden'=>8, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'descripcion','nombreMostrar'=>'Descripci&oacute;n', 'orden'=>10, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'afectacionServicio','nombreMostrar'=>'Afectaci&oacute;n de Servicio', 'orden'=>9, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'ubicacion','nombreMostrar'=>'Ubicaci&oacute;n', 'orden'=>11, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'valorMonitoreado','nombreMostrar'=>'Valor Monitoreado', 'orden'=>12, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no'];
$camposAlarmas[] = ['nombreDB'=>'nivelUmbral','nombreMostrar'=>'Nivel de Umbral', 'orden'=>13, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no'];
$camposAlarmas[] = ['nombreDB'=>'periodo','nombreMostrar'=>'Per&iacute;odo', 'orden'=>14, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no'];
$camposAlarmas[] = ['nombreDB'=>'datos','nombreMostrar'=>'Datos', 'orden'=>15, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'filtroALM','nombreMostrar'=>'Filtro ALM', 'orden'=>16, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no'];
$camposAlarmas[] = ['nombreDB'=>'filtroAID','nombreMostrar'=>'Filtro AID', 'orden'=>17, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'accion','nombreMostrar'=>'Acci&oacute;n', 'orden'=>25, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'usuario','nombreMostrar'=>'Usuario Carga', 'orden'=>19, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'nodo','nombreMostrar'=>'Nodo', 'orden'=>20, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'archivo','nombreMostrar'=>'Archivo', 'orden'=>21, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'fechaCarga','nombreMostrar'=>'Fecha de Carga', 'orden'=>22, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'estado','nombreMostrar'=>'Estado', 'orden'=>18, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'causa','nombreMostrar'=>'Posible Causa', 'orden'=>23, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];
$camposAlarmas[] = ['nombreDB'=>'solucion','nombreMostrar'=>'Posible Soluci&oacute;n', 'orden'=>24, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si'];

/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function ordenarAlarmas ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($camposAlarmas, 'ordenarAlarmas');

?>