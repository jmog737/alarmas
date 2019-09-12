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
    $idalarma = base64_decode($_GET['al']);
  }
  if (isset($_GET['o'])){
    $origen = base64_decode($_GET['o']);
    $origenCodif = base64_encode($origen);
  }
  if (isset($_GET['cAlarm'])){echo "isset get";
    $consulta = base64_decode($_GET['cAlarm']);
    $consultaCodif = $_GET['cAlarm'];
  }
  if (isset($_GET['pAlarm'])){
    $param = unserialize(base64_decode($_GET['pAlarm']));
    $paramCodif = $_GET['pAlarm'];
  }
echo $consulta;
  /// ***************************************** GENERACIÓN NAVEGACIÓN ************************************************************************
  /// Vuelvo a realizar la consulta para poder obtener el array con los índices y así poder generar la navegación:
  $log1 = "NO";
  $datos = json_decode(hacerSelect($consulta, $log1, $param), true);
  
  $keys = array();
  foreach ($datos['resultado'] as $key0 => $fila0 ) {
    $idalarma0 = $fila0['idalarma'];
    $keys[] = $idalarma0;
  } /// Fin foreach datos para sacar los keys
  
  $id = array_search($idalarma, $keys);
  $idMostrar = $id + 1;
  $totalFilas = count($keys);
  $primero = base64_encode($keys[0]);
  $ultimo = base64_encode($keys[$totalFilas-1]);
  $inhabilitarPrimero = '';
  $inhabilitarUltimo = '';

  if ($id !== false){
    if ($id === 0){
      $anterior = base64_encode($keys[$id]);
      $inhabilitarPrimero = 'inhabilitar';
    }
    else {
      $anterior = base64_encode($keys[$id-1]);
    }
    if ($id === ($totalFilas-1)){
      $siguiente = base64_encode($keys[$totalFilas-1]);
      $inhabilitarUltimo = 'inhabilitar';
    }
    else {
      $siguiente = base64_encode($keys[$id+1]);
    }
  }
  else {
    echo "Hubo un error.<br>El índice recibido ($idalarma) no está incluido en el array de índices.<br>Por favor verifique.";
  }
  /// ************************************* FIN GENERACIÓN NAVEGACIÓN ************************************************************************

  /// Chequeo si vengo del listado de alarmas para editarla, o de la propia página luego de la edición:
  if (isset($_POST['btnEditarAlarma'])){echo "<br>post seteado<br>";
    $causa = htmlentities($_POST['causa']);
    $solucion = htmlentities($_POST['sln']);
    $query = "update alarmas set causa=?, solucion=?, estado='Procesada' where idalarma=?";
    $paramUpdate = array($causa, $solucion, $idalarma);
    $log = "SI";
    $resultadoInsert = json_decode(hacerUpdate($query, $log, $paramUpdate), true);
    if ($resultadoInsert === 'ERROR'){
      $mensaje = "Hubo un problema al actualizar los datos.<br>Por favor verifique.";
    }
    else {
      $mensaje = "¡Datos editados correctamente!";
    }
  } /// Fin isset($_POST)

  /// Luego de la posible actualización, consulto los datos del registro en cuestión: 
  //$queryParam = "select nodos.localidad as localidad, usuarios.nombre, usuarios.apellido from alarmas inner join nodos on alarmas.nodo=nodos.idnodo inner join usuarios on alarmas.usuario=usuarios.idusuario where alarmas.idalarma=?";
  //$queryParam = "select nodos.localidad as localidad from alarmas inner join nodos on alarmas.nodo=nodos.idnodo where alarmas.idalarma=?";
  $queryParam = "select alarmas.*, nodos.idnodo, nodos.nombre as nombreNodo, nodos.localidad from alarmas inner join nodos on alarmas.nodo=nodos.idnodo where alarmas.idalarma=?";
  $param1 = array($idalarma);
  $log = "NO";
  $datosParam = json_decode(hacerSelect($queryParam, $log, $param1), true);
  $datosMostrar = $datosParam['resultado'][0];

  $usuarioMostrar = $_SESSION['usuarioReal'];
  $localidad = $datosMostrar['localidad'];
  
  $causaOriginal = $datosMostrar['causa'];
  $solucionOriginal = $datosMostrar['solucion'];
  $temp = explode('-', $datosMostrar['dia']);
  $diaMostrar = $temp[2]."/".$temp[1]."/".$temp[0];
  $temp1 = explode('-', $datosMostrar['fechaCarga']);
  $diaMostrar1 = $temp1[2]."/".$temp1[1]."/".$temp1[0];
?>
<main>
  <script>
    window.location = '#tituloEditarAlarma';
  </script>
  
  <div id='main-content' class='container-fluid'>
    <?php
    if (isset($_POST['btnEditarAlarma'])){
      echo "<br><h3>".$mensaje."</h3>";
    }
    ?>
    <br>
    <h2 id="tituloEditarAlarma">Datos de la alarma <?php echo $idMostrar?></h2>

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
            $indice = $camposAlarmas[$key]['nombreDB'];

            switch ($indice){
              case 'id': break;
              case 'dia': echo "<tr>
                                  <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                  <td>".$diaMostrar."</td>
                                </tr>";
                          break;
              case 'fechaCarga':  echo "<tr>
                                          <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                          <td>".$diaMostrar1."</td>
                                        </tr>";
                                  break;          
              case 'causa': echo "<tr>
                                    <td class='enc'>Posible Causa</td>
                                    <td>
                                      <textarea type='text' name='causa' id='causa' class='agrandar' rows='5' placeholder='Ingrese el motivo probable.'>".$datosMostrar[$indice]."</textarea>
                                      <input name='causaOriginal' id='causaOriginal' type='hidden' value='".$causaOriginal."'>  
                                    </td>
                                  </tr>"; 
                            break;
              case 'solucion': echo "<tr>
                                      <td class='enc'>Posible Soluci&oacute;n</td>
                                      <td>
                                        <textarea type='text' name='sln' id='sln' class='agrandar' rows='5' placeholder='Ingrese la posible soluci&oacute;n.'>".$datosMostrar[$indice]."</textarea>
                                        <input name='solucionOriginal' id='solucionOriginal' type='hidden' value='".$solucionOriginal."'>
                                      </td>
                                    </tr>"; 
                                break;
              case 'usuario': echo "<tr>
                                      <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                      <td>".$usuarioMostrar."</td>
                                    </tr>";
                              break;  
              case 'nodo':  echo "<tr>
                                    <td class='enc'>".$camposAlarmas[$key]['nombreMostrar']."</td>
                                    <td>".$localidad."</td>
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
            <input type='submit' class='btn btn-danger' name='btnEditarAlarma' id='btnEditarAlarma' value='EDITAR'>
          </td>
        </tr>
      </table>
    </form>

    <div id="navegacion" class="pagination">
    <?php
      echo "<ul>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir a la primer alarma' href='editarAlarma.php?al=".$primero."&o=".$origenCodif."&cAlarm=".$consultaCodif."&pAlarm=".$paramCodif."'>|<  </a></li>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir a la alarma anterior' href='editarAlarma.php?al=".$anterior."&o=".$origenCodif."&cAlarm=".$consultaCodif."&pAlarm=".$paramCodif."'>  <<  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir a la siguiente alarma' href='editarAlarma.php?al=".$siguiente."&o=".$origenCodif."&cAlarm=".$consultaCodif."&pAlarm=".$paramCodif."'>  >>  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir a la última alarma' href='editarAlarma.php?al=".$ultimo."&o=".$origenCodif."&cAlarm=".$consultaCodif."&pAlarm=".$paramCodif."'>  >|</a></li>";
      echo "</ul>";
    ?>
    </div>
    <?php
      if ($origen === "cargar"){
        $volver = "<br><a href='cargar.php?i=1'>Volver a las alarmas</a><br><br>";
      }
      else {
        $volver = '<br><a href="javascript:close()">Cerrar y volver al resultado</a><br><br>';
      }
      echo $volver;
    ?>
    <br>
  </div>      
</main>      
<?php
require_once ('footer.php');
?>  
</body>
</html>

