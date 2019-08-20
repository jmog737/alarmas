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
  
  if (isset($_GET['al'])){
    $idalarma = $_GET['al'];
  }
  if (isset($_GET['k'])){
    $keys = unserialize($_GET['k']);
    $id = array_search($idalarma, $keys);
    $totalFilas = count($keys);
    $primero = $keys[0];
    $ultimo = $keys[$totalFilas-1];
    if ($id !== false){
      if ($id === 0){
        $anterior = $keys[$id];
      }
      else {
        $anterior = $keys[$id-1];
      }
      if ($id === ($totalFilas-1)){
        $siguiente = $keys[$totalFilas-1];
      }
      else {
        $siguiente = $keys[$id+1];
      }
    }
    else {
      echo "Hubo un error.<br>El índice recibido ($idalarma) no está incluido en el array de índices.<br>Por favor verifique.";
    }
  }

  //echo "idalarma: ".$idalarma."<br>keys: <br>";var_dump($keys);
  
  /// Chequeo si vengo del listado de alarmas para editarla, o de la propia página luego de la edición:
  if (isset($_POST['btnEditarAlarma'])){
    $causa = htmlentities($_POST['causa']);
    $solucion = htmlentities($_POST['sln']);
    $query = "update alarmas set causa=?, solucion=?, estado='Procesada' where idalarma=?";
    $paramUpdate = array($causa, $solucion, $idalarma);
    $resultadoInsert = hacerUpdate($query, $paramUpdate);
    if ($resultadoInsert === 'ERROR'){
      $mensaje = "Hubo un problema al actualizar los datos.<br>Por favor verifique.";
    }
    else {
      $mensaje = "¡Datos editados correctamente!";
    }
  } /// Fin isset($_POST)

  $query = "select * from alarmas where idalarma=?";
  $param1 = array($idalarma);
  $datos = hacerSelect($query, $param1);
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
          case 'CR': $claseAlarma = 'alCritica';
                     break;
          case 'MJ': $claseAlarma = 'alMajor';
                     break;
          case 'MN': $claseAlarma = 'alMinor';
                     break;
          case 'WR': $claseAlarma = 'alWarning';
                     break;     
          default: $claseAlarma = '';
                   break;
        } /// Fin switch tipoAlarma

        /// Extraigo estado de la alarma para poder resaltar en consecuencia:
        $tipoEstado = $datosMostrar['estado'];
        switch ($tipoEstado) {
          case 'Procesada': $claseEstado = 'procesada';
                            break;
          case 'Sin procesar':  $claseEstado = 'sinProcesar';
                                break;
          default: break;
        } /// Fin switch tipoEstado

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
                                      <textarea type='text' name='causa' id='causa' class='agrandar' rows='5' placeholder='Ingrese el motivo que puede haber causado la alarma.'>".$datosMostrar[$indice]."</textarea>
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
                                        <td class='".$claseAlarma."'>".$datosMostrar[$indice]."</td>
                                      </tr>";
                                 break;
              case 'estado':  echo "<tr>
                                      <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                      <td class='".$claseEstado."'>".$datosMostrar[$indice]."</td>
                                    </tr>";
                              break;                 
              case 'accion': break;                 
              default: echo "<tr>
                              <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                              <td>".$datosMostrar[$indice]."</td>
                            </tr>";                 
            } /// Fin switch indice       
          } /// Fin if si mostrarEditar 
        } /// Fin foreach camposAlarmas
        ?>
        
        <tr>
          <td colspan='2' class='pieTabla'>
            <input type='submit' name='btnEditarAlarma' id='btnEditarAlarma' value='EDITAR'>
          </td>
        </tr>
      </table>
    </form>

    <?php
      echo "<ul>";
      echo "<a title='Ir a la primer alarma' href='editarAlarma.php?al=".$primero."&k=". serialize($keys)."'>|<  </a>";
      echo "<a title='Ir a la alarma anterior' href='editarAlarma.php?al=".$anterior."&k=". serialize($keys)."'>  <<  </a>";
      echo "<a title='Ir a la siguiente alarma' href='editarAlarma.php?al=".$siguiente."&k=". serialize($keys)."'>  >>  </a>";
      echo "<a title='Ir a la última alarma' href='editarAlarma.php?al=".$ultimo."&k=". serialize($keys)."'>  >|</a>";
      echo "</ul>";
      $volver = "<br><a href='cargar.php?i=1'>Volver a las alarmas</a><br><br>";
      echo $volver;
    ?>
  </div>      
</main>      
<?php
require_once ('footer.php');
?>  
</body>
</html>

