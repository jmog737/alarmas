<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file camposUsuarios.php
*  @brief Archivo que se encarga de definir y reordenar los campos a mostrar de la tabla usuarios.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
*
*******************************************************/

/// Defino array con todos los campos presentes en la base de datos, su nombre a mostrar en pantalla, el orden a mostrar y si se
/// tienen que mostrar o no.
$camposUsuarios[] = ['nombreDB'=>'id','nombreMostrar'=>'Id', 'orden'=>0, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>0.8, 'especial'=>'no', 'formato'=>''];///id
$camposUsuarios[] = ['nombreDB'=>'idusuario','nombreMostrar'=>'Id Usuario', 'orden'=>1, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///idusuario
$camposUsuarios[] = ['nombreDB'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>2, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.2, 'especial'=>'no', 'formato'=>''];///nombre
$camposUsuarios[] = ['nombreDB'=>'apellido','nombreMostrar'=>'Apellido', 'orden'=>3, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.2, 'especial'=>'no', 'formato'=>''];///apellido
$camposUsuarios[] = ['nombreDB'=>'appUser','nombreMostrar'=>'appUser', 'orden'=>4, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.1, 'especial'=>'no', 'formato'=>''];///appUser
$camposUsuarios[] = ['nombreDB'=>'appPwd','nombreMostrar'=>'appPwd', 'orden'=>5, 'mostrarListado'=>'no', 'mostrarEditar'=>'no', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1.6, 'especial'=>'no', 'formato'=>''];///appPwd
$camposUsuarios[] = ['nombreDB'=>'tamPagina','nombreMostrar'=>'Tamaño de P&aacute;gina', 'orden'=>6, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2, 'especial'=>'no', 'formato'=>''];///tamPagina
$camposUsuarios[] = ['nombreDB'=>'limiteSelects','nombreMostrar'=>'L&iacute;mite Selects', 'orden'=>7, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2, 'especial'=>'no', 'formato'=>''];///limiteSelects
$camposUsuarios[] = ['nombreDB'=>'estado','nombreMostrar'=>'Estado', 'orden'=>8, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>1.2, 'especial'=>'no', 'formato'=>''];///estado
$camposUsuarios[] = ['nombreDB'=>'observaciones','nombreMostrar'=>'Observaciones', 'orden'=>9, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'si', 'mostrarExcel'=>'si', 'tam'=>2.4, 'especial'=>'no', 'formato'=>''];///observaciones
$camposUsuarios[] = ['nombreDB'=>'accion','nombreMostrar'=>'Acci&oacute;n', 'orden'=>10, 'mostrarListado'=>'si', 'mostrarEditar'=>'si', 'mostrarReporte'=>'no', 'mostrarExcel'=>'no', 'tam'=>1, 'especial'=>'no', 'formato'=>''];///accion
//
/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function ordenarUsuarios ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($camposUsuarios, 'ordenarUsuarios');

?>