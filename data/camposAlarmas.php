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
$camposAlarmas[] = ['nombreDB'=>'id','nombreMostrar'=>'Id', 'orden'=>0, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>0.9, 'especial'=>'no', 'formato'=>''];///id
$camposAlarmas[] = ['nombreDB'=>'idalarma','nombreMostrar'=>'Id Alarma', 'orden'=>1, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///idalarma
$camposAlarmas[] = ['nombreDB'=>'dia','nombreMostrar'=>'Fecha', 'orden'=>2, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.0, 'especial'=>'no', 'formato'=>''];///fecha
$camposAlarmas[] = ['nombreDB'=>'hora','nombreMostrar'=>'Hora', 'orden'=>3, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.7, 'especial'=>'no', 'formato'=>''];///hora
$camposAlarmas[] = ['nombreDB'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>4, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.7, 'especial'=>'no', 'formato'=>''];///nombre
$camposAlarmas[] = ['nombreDB'=>'compound','nombreMostrar'=>'Compound', 'orden'=>5, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.7, 'especial'=>'no', 'formato'=>''];///compound
$camposAlarmas[] = ['nombreDB'=>'tipoAID','nombreMostrar'=>'AID(ASAP)', 'orden'=>7, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.9, 'especial'=>'no', 'formato'=>''];///tipo aid
$camposAlarmas[] = ['nombreDB'=>'tipoAlarma','nombreMostrar'=>'Alarma', 'orden'=>6, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.4, 'especial'=>'no', 'formato'=>''];///tipo alarma
$camposAlarmas[] = ['nombreDB'=>'tipoCondicion','nombreMostrar'=>'Condici&oacute;n', 'orden'=>8, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2, 'especial'=>'no', 'formato'=>''];///tipo condicion
$camposAlarmas[] = ['nombreDB'=>'descripcion','nombreMostrar'=>'Descripci&oacute;n', 'orden'=>10, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.4, 'especial'=>'no', 'formato'=>''];///descripcion
$camposAlarmas[] = ['nombreDB'=>'afectacionServicio','nombreMostrar'=>'Afectaci&oacute;n', 'orden'=>9, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.3, 'especial'=>'no', 'formato'=>''];///afectacion
$camposAlarmas[] = ['nombreDB'=>'ubicacion','nombreMostrar'=>'Ubicaci&oacute;n', 'orden'=>11, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1.6, 'especial'=>'no', 'formato'=>''];///ubicacion
$camposAlarmas[] = ['nombreDB'=>'valorMonitoreado','nombreMostrar'=>'Valor Monitoreado', 'orden'=>12, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///valor monitoreado
$camposAlarmas[] = ['nombreDB'=>'nivelUmbral','nombreMostrar'=>'Nivel de Umbral', 'orden'=>13, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///nivel umbral
$camposAlarmas[] = ['nombreDB'=>'periodo','nombreMostrar'=>'Per&iacute;odo', 'orden'=>14, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///periodo
$camposAlarmas[] = ['nombreDB'=>'datos','nombreMostrar'=>'Datos', 'orden'=>15, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///datos
$camposAlarmas[] = ['nombreDB'=>'filtroALM','nombreMostrar'=>'Filtro ALM', 'orden'=>16, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///filtro ALM
$camposAlarmas[] = ['nombreDB'=>'filtroAID','nombreMostrar'=>'Filtro AID', 'orden'=>17, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///filtro AID
$camposAlarmas[] = ['nombreDB'=>'accion','nombreMostrar'=>'Acci&oacute;n', 'orden'=>25, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///accion
$camposAlarmas[] = ['nombreDB'=>'usuario','nombreMostrar'=>'Usuario Carga', 'orden'=>19, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///usuario
$camposAlarmas[] = ['nombreDB'=>'nodo','nombreMostrar'=>'Nodo', 'orden'=>20, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'sno', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///nodo
$camposAlarmas[] = ['nombreDB'=>'archivo','nombreMostrar'=>'Archivo', 'orden'=>21, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///archivo
$camposAlarmas[] = ['nombreDB'=>'fechaCarga','nombreMostrar'=>'Fecha de Carga', 'orden'=>22, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///fecha carga
$camposAlarmas[] = ['nombreDB'=>'estado','nombreMostrar'=>'Estado', 'orden'=>18, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1.8, 'especial'=>'no', 'formato'=>''];///estado
$camposAlarmas[] = ['nombreDB'=>'causa','nombreMostrar'=>'Causa', 'orden'=>23, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.8, 'especial'=>'no', 'formato'=>''];///causa
$camposAlarmas[] = ['nombreDB'=>'solucion','nombreMostrar'=>'Soluci&oacute;n', 'orden'=>24, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>3, 'especial'=>'no', 'formato'=>''];///solucion

/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function ordenarAlarmas ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($camposAlarmas, 'ordenarAlarmas');

?>