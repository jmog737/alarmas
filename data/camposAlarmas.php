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
$camposAlarmas[] = ['nombre'=>'id','nombreMostrar'=>'Id', 'orden'=>0, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'idalarma','nombreMostrar'=>'Id Alarma', 'orden'=>1, 'mostrarListado'=>'no', 'mostrarEditar'=>'no'];
$camposAlarmas[] = ['nombre'=>'dia','nombreMostrar'=>'Fecha', 'orden'=>2, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'hora','nombreMostrar'=>'Hora', 'orden'=>3, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>4, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'compound','nombreMostrar'=>'Compund', 'orden'=>5, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'tipoAID','nombreMostrar'=>'Tipo AID', 'orden'=>6, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'tipoAlarma','nombreMostrar'=>'Tipo de Alarma', 'orden'=>7, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'tipoCondicion','nombreMostrar'=>'Tipo de Condici&oacute;n', 'orden'=>8, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'descripcion','nombreMostrar'=>'Descripci&oacute;n', 'orden'=>10, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'afectacionServicio','nombreMostrar'=>'Afectaci&oacute;n de Servicio', 'orden'=>9, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'ubicacion','nombreMostrar'=>'Ubicaci&oacute;n', 'orden'=>11, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'valorMonitoreado','nombreMostrar'=>'Valor Monitoreado', 'orden'=>12, 'mostrarListado'=>'no', 'mostrarEditar'=>'no'];
$camposAlarmas[] = ['nombre'=>'nivelUmbral','nombreMostrar'=>'Nivel de Umbral', 'orden'=>13, 'mostrarListado'=>'no', 'mostrarEditar'=>'no'];
$camposAlarmas[] = ['nombre'=>'periodo','nombreMostrar'=>'Per&iacute;odo', 'orden'=>14, 'mostrarListado'=>'no', 'mostrarEditar'=>'no'];
$camposAlarmas[] = ['nombre'=>'datos','nombreMostrar'=>'Datos', 'orden'=>15, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'filtroALM','nombreMostrar'=>'Filtro ALM', 'orden'=>16, 'mostrarListado'=>'no', 'mostrarEditar'=>'no'];
$camposAlarmas[] = ['nombre'=>'filtroAID','nombreMostrar'=>'Filtro AID', 'orden'=>17, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'accion','nombreMostrar'=>'Acci&oacute;n', 'orden'=>24, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'usuario','nombreMostrar'=>'Usuario', 'orden'=>19, 'mostrarListado'=>'no', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'nodo','nombreMostrar'=>'Nodo', 'orden'=>20, 'mostrarListado'=>'no', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'archivo','nombreMostrar'=>'Archivo', 'orden'=>21, 'mostrarListado'=>'no', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'estado','nombreMostrar'=>'Estado', 'orden'=>18, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'causa','nombreMostrar'=>'Posible Causa', 'orden'=>22, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];
$camposAlarmas[] = ['nombre'=>'solucion','nombreMostrar'=>'Posible Soluci&oacute;n', 'orden'=>23, 'mostrarListado'=>'si', 'mostrarEditar'=>'si'];

/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function ordenarAlarmas ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($camposAlarmas, 'ordenarAlarmas');

?>