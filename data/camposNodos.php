<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file camposUsuarios.php
*  @brief Archivo que se encarga de definir y reordenar los campos a mostrar de la tabla nodos.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
*
*******************************************************/

/// Defino array con todos los campos presentes en la base de datos, su nombre a mostrar en pantalla, el orden a mostrar y si se
/// tienen que mostrar o no.
$camposNodos[] = ['nombreDB'=>'id','nombreMostrar'=>'Id', 'orden'=>0, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>0.8, 'especial'=>'no', 'formato'=>''];///id
$camposNodos[] = ['nombreDB'=>'idnodo','nombreMostrar'=>'Id Nodo', 'orden'=>1, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///idnodo
$camposNodos[] = ['nombreDB'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>2, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.8, 'especial'=>'no', 'formato'=>''];///nombre
$camposNodos[] = ['nombreDB'=>'tipo','nombreMostrar'=>'Tipo', 'orden'=>3, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.6, 'especial'=>'no', 'formato'=>''];///tipo
$camposNodos[] = ['nombreDB'=>'localidad','nombreMostrar'=>'Localidad', 'orden'=>4, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.1, 'especial'=>'no', 'formato'=>''];///localidad
$camposNodos[] = ['nombreDB'=>'areaMetro','nombreMostrar'=>'&Aacute;rea Metro', 'orden'=>5, 'mostrarListado'=>'no', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.6, 'especial'=>'no', 'formato'=>''];///areaMetro
$camposNodos[] = ['nombreDB'=>'ip','nombreMostrar'=>'IP', 'orden'=>6, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.2, 'especial'=>'no', 'formato'=>''];///ip
$camposNodos[] = ['nombreDB'=>'observaciones','nombreMostrar'=>'Observaciones', 'orden'=>7, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.4, 'especial'=>'no', 'formato'=>''];///observaciones
$camposNodos[] = ['nombreDB'=>'accion','nombreMostrar'=>'Acci&oacute;n', 'orden'=>8, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///accion
//
/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function ordenarNodos ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($camposNodos, 'ordenarNodos');

?>