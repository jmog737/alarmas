<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarAlarmas.php
*  @brief Archivo que carga los datos de la alarma pasada en la url y permite la edición de su causa y su posible solución.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/
?>

<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
?>
  <body>
<?php
  require_once ('header.php');
  
  /// Recupero el id de la alarma:
  $idalarma = $_GET['id'];

  $query = "select * from alarmasdwdm where idalarma=$idalarma";
  require_once('data/selectQueryDirecto.php');
  $datosAlarma = $datos['resultado'][0];
  
  $temp = explode('-', $datosAlarma['dia']);
  $diaMostrar = $temp[2]."/".$temp[1]."/".$temp[0];
?>
  <main>
    <div id='main-content' class='container-fluid'>
    <br>
    <h2>Datos de la alarma:</h2>

    <form id='frmEditarAlarma' name='frmEditarAlarma' action='self' method='post'>
      <table name='tblEditarAlarma' id='tblEditarAlarma' class='tabla2'>
        <caption>Formulario para la edici&oacute;n de la alarma</caption>
        <tr>
          <th colspan='2' class='tituloTabla'>EDITAR ALARMA</th>
        </tr>
        <tr>
          <td class='enc'>Fecha</td>
          <td><?php echo $diaMostrar;?></td>
        </tr>
        <tr>
          <td class='enc'>Hora</td>
          <td><?php echo $datosAlarma['hora'];?></td>
        </tr>
        <tr>
          <td class='enc'>Nombre</td>
          <td><?php echo $datosAlarma['nombre'];?></td>
        </tr>
        <tr>
          <td class='enc'>Compound</td>
          <td><?php echo $datosAlarma['compound'];?></td>
        </tr>
        <tr>
          <td class='enc'>Tipo AID</td>
          <td><?php echo $datosAlarma['tipoAID'];?></td>
        </tr>
        <tr>
          <td class='enc'>Tipo de Alarma</td>
          <td><?php echo $datosAlarma['tipoAlarma'];?></td>
        </tr>
        <tr>
          <td class='enc'>Tipo de Condici&oacute;</td>
          <td><?php echo $datosAlarma['tipoCondicion'];?></td>
        </tr>
        <tr>
          <td class='enc'>Afectaci&oacute;n de Servicio</td>
          <td><?php echo $datosAlarma['afectacionServicio'];?></td>
        </tr>
        <tr>
          <td class='enc'>Descripci&oacute;n</td>
          <td><?php echo $datosAlarma['descripcion'];?></td>
        </tr>
        <tr>
          <td class='enc'>Ubicaci&oacute;n</td>
          <td><?php echo $datosAlarma['ubicacion'];?></td>
        </tr>
        <tr>
          <td class='enc'>Datos</td>
          <td><?php echo $datosAlarma['datos'];?></td>
        </tr>
        <tr>
          <td class='enc'>Filtro AID</td>
          <td><?php echo $datosAlarma['filtroAID'];?></td>
        </tr>
        <tr>
          <td class='enc'>Posible Causa</td>
          <td>
            <textarea type='text' name='causa' id='causa' class='agrandar' rows='5' placeholder='Ingrese cual puede ser el motivo que puede haber causado la alarma.'></textarea>
          </td>
        </tr>
        <tr>
          <td class='enc'>Posible Soluci&oacute;n</td>
          <td>
            <textarea type='text' name='sln' id='sln' class='agrandar' rows='5' placeholder='Ingrese la posible soluci&oacute;n a la alarma.'></textarea>
          </td>
        </tr>
        <tr>
          <td colspan='2' class='pieTabla'>
            <input type='button' name='btnEditarAlarma' id='btnEditarAlarma' value='EDITAR'>
          </td>
        </tr>
      </table>
    </form>

    <?php
      $volver = "<br><a href='cargar.php?origen=al'>Volver a las alarmas</a><br><br>";
      echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>

