<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarAlarma.php
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
  require_once('data/pdo.php');
  require_once('data/camposAlarmas.php');
  
  if (isset($_GET['idalarma'])){
    $idalarma = $_GET['idalarma'];
  }
  if (isset($_GET['id'])){
    $ind = $_GET['id'];
  }

  /// Chequeo si vengo del listado de alarmas para editarla, o de la propia página luego de la edición:
  if (isset($_POST['btnEditarAlarma'])){
    $causa = $_POST['causa'];
    $solucion = $_POST['sln'];
    $query = "update alarmas set causa='".$causa."', solucion='".$solucion."', estado='Procesada' where idalarma=".$idalarma."";
    $resultadoInsert = hacerUpdate($query);
    if ($resultadoInsert === 'ERROR'){
      $mensaje = "Hubo un problema al actualizar los datos.<br>Por favor verifique.";
    }
    else {
      $mensaje = "¡Datos editados correctamente!";
    }
  }

  $query = "select * from alarmas where idalarma=$idalarma";
  $datos = hacerSelect($query);
  $datosMostrar = $datos['resultado'][0];
  
  $causaOriginal = $datosMostrar['causa'];
  $solucionOriginal = $datosMostrar['solucion'];
  $temp = explode('-', $datosMostrar['dia']);
  $diaMostrar = $temp[2]."/".$temp[1]."/".$temp[0];
?>
  <main>
    <div id='main-content' class='container-fluid'>
    <?php
    if (isset($_POST['btnEditarAlarma'])){
      echo "<br><h3>".$mensaje."</h3>";
    }
    ?>
    <br>
    <h2>Datos de la alarma:</h2>

    <form id='frmEditarAlarma' name='frmEditarAlarma' method='post'>
      <table name='tblEditarAlarma' id='tblEditarAlarma' class='tabla2'>
        <caption>Formulario para la edici&oacute;n de la alarma</caption>
        <tr>
          <th colspan='2' class='tituloTabla'>EDITAR ALARMA</th>
        </tr>
        
        <?php
          $i = 1;
              
          /// Extraigo tipo de alarma para poder resaltar en consecuencia:
          $tipoAlarma = $datosMostrar['tipoAlarma'];
          switch ($tipoAlarma) {
            case 'CR': $clase = 'alCritica';
                       break;
            case 'MJ': $clase = 'alMajor';
                       break;
            case 'MN': $clase = 'alMinor';
                       break;
            case 'WR': $clase = 'alWarning';
                       break;     
            default: $clase = '';
                     break;
          }
          
          /// Recorro el array con los campos para ver cuales hay que mostrar y cuales no.
          /// Para los que hay que hacerlo, veo si es alguno que requiera un formato especial (la fecha o el tipo de alarma) o no.
          foreach ($camposAlarmas as $key => $value) {   
            if ($camposAlarmas[$key]['mostrarEditar'] === 'si'){
              $indice = $camposAlarmas[$key]['nombre'];

              switch ($indice){
                case 'id': break;
                case 'dia': echo "<tr>
                                    <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                    <td>".$diaMostrar."</td>
                                  </tr>";
                            break;
                case 'causa': echo "<tr>
                                      <td class='enc'>Posible Causa</td>
                                      <td>
                                        <textarea type='text' name='causa' id='causa' class='agrandar' rows='5' placeholder='Ingrese cual puede ser el motivo que puede haber causado la alarma.'>".$datosMostrar[$indice]."</textarea>
                                        <input name='causaOriginal' id='causaOriginal' type='hidden' value='".$causaOriginal."'>  
                                      </td>
                                    </tr>"; 
                              break;
                case 'solucion': echo "<tr>
                                        <td class='enc'>Posible Soluci&oacute;n</td>
                                        <td>
                                          <textarea type='text' name='sln' id='sln' class='agrandar' rows='5' placeholder='Ingrese la posible soluci&oacute;n a la alarma.'>".$datosMostrar[$indice]."</textarea>
                                          <input name='solucionOriginal' id='solucionOriginal' type='hidden' value='".$solucionOriginal."'>
                                        </td>
                                      </tr>"; 
                                  break;
                case 'tipoAlarma': echo "<tr>
                                          <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                          <td class='".$clase."'>".$datosMostrar[$indice]."</td>
                                        </tr>";
                                   break;
                case 'accion': break;                 
                default: echo "<tr>
                                <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                <td>".$datosMostrar[$indice]."</td>
                              </tr>";                 
              }       
            } 
          }
        ?>
        
        
        
        <tr>
          <td colspan='2' class='pieTabla'>
            <input type='submit' name='btnEditarAlarma' id='btnEditarAlarma' value='EDITAR'>
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

