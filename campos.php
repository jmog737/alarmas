<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file campos.php
*  @brief Archivo que se encarga de definir y reordenar los campos a mostrar.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/

/// Defino array con todos los campos presentes en la base de datos, su nombre a mostrar en pantalla, el orden a mostrar y si se
/// tienen que mostrar o no.
$campos[] = ['nombre'=>'id','nombreMostrar'=>'Id', 'orden'=>1, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'dia','nombreMostrar'=>'Fecha', 'orden'=>2, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'hora','nombreMostrar'=>'Hora', 'orden'=>3, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'nombre','nombreMostrar'=>'Nombre', 'orden'=>4, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'compound','nombreMostrar'=>'Compund', 'orden'=>5, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'tipoAID','nombreMostrar'=>'Tipo AID', 'orden'=>6, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'tipoAlarma','nombreMostrar'=>'Tipo de Alarma', 'orden'=>7, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'tipoCondicion','nombreMostrar'=>'Tipo de Condición', 'orden'=>8, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'descripcion','nombreMostrar'=>'Descripción', 'orden'=>10, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'afectacionServicio','nombreMostrar'=>'Afectación de Servicio', 'orden'=>9, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'ubicacion','nombreMostrar'=>'Ubicación', 'orden'=>11, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'valorMonitoreado','nombreMostrar'=>'Valor Monitoreado', 'orden'=>12, 'mostrar'=>'no'];
$campos[] = ['nombre'=>'nivelUmbral','nombreMostrar'=>'Nivel de Umbral', 'orden'=>13, 'mostrar'=>'no'];
$campos[] = ['nombre'=>'periodo','nombreMostrar'=>'Período', 'orden'=>14, 'mostrar'=>'no'];
$campos[] = ['nombre'=>'datos','nombreMostrar'=>'Datos', 'orden'=>15, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'filtroALM','nombreMostrar'=>'Filtro ALM', 'orden'=>16, 'mostrar'=>'no'];
$campos[] = ['nombre'=>'filtroAID','nombreMostrar'=>'Filtro AID', 'orden'=>17, 'mostrar'=>'si'];
$campos[] = ['nombre'=>'accion','nombreMostrar'=>'Acción', 'orden'=>18, 'mostrar'=>'si'];

/// Función auxiliar para ordenar el array anterior según el campo 'orden':
function sort_by_orden ($a, $b) {
    return $a['orden'] - $b['orden'];
}
/// Re ordeno el array según el campo 'orden':
usort($campos, 'sort_by_orden');

?>