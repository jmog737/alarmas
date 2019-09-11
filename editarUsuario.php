<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarUsuario.php
*  @brief Archivo que carga los datos del usuario pasado en la url y permite su edición.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
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
  require_once('data/camposUsuarios.php');
  
  if (isset($_GET['us'])){
    $idusuario = base64_decode($_GET['us']);
  }
  if (isset($_GET['o'])){
    $origen = base64_decode($_GET['o']);
    $origenCodif = base64_encode($origen);
  }
  if (isset($_GET['cUser'])){
    $consulta = base64_decode($_GET['cUser']);
    $consultaCodif = $_GET['cUser'];
  }
  if (isset($_GET['pUser'])){
    $param = unserialize(base64_decode($_GET['pUser']));
    $paramCodif = $_GET['pUser'];
  }
  else {
    $param = false;
    $paramCodif = false;
  }

  /// ***************************************** GENERACIÓN NAVEGACIÓN ************************************************************************
  /// Vuelvo a realizar la consulta para poder obtener el array con los índices y así poder generar la navegación:
  $log1 = "NO";
  $datos = json_decode(hacerSelect($consulta, $log1, $param), true);
  
  $keys = array();
  foreach ($datos['resultado'] as $key0 => $fila0 ) {
    $idusuario0 = $fila0['idusuario'];
    $keys[] = $idusuario0;
  } /// Fin foreach datos para sacar los keys
  
  $id = array_search($idusuario, $keys);
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
    echo "Hubo un error.<br>El índice recibido ($idusuario) no está incluido en el array de índices.<br>Por favor verifique.";
  }
  /// ************************************* FIN GENERACIÓN NAVEGACIÓN ************************************************************************

  /// Chequeo si vengo del listado de usuarios para editarla, o de la propia página luego de la edición:
  if (isset($_POST['btnEditarUsuario'])){
    $nombre = htmlentities($_POST['nombre']);
    $apellido = htmlentities($_POST['apellido']);
    $appUser = htmlentities($_POST['appUser']);
    $observaciones = htmlentities($_POST['observaciones']);
    $query = "update usuarios set nombre=?, apellido=?, appUser=?, observaciones=? where idusuario=?";
    $paramUpdate = array($nombre, $apellido, $appUser, $observaciones, $idusuario);
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
  $queryParam = "select idusuario, nombre, apellido, appUser, appPwd, tamPagina, limiteSelects, estado, observaciones from usuarios where idusuario=?";
  $param1 = array($idusuario);
  $log = "NO";
  $datosParam = json_decode(hacerSelect($queryParam, $log, $param1), true);
  $datosMostrar = $datosParam['resultado'][0];

  $nombreOriginal = $datosMostrar['nombre'];
  $apellidoOriginal = $datosMostrar['apellido'];
  $appUserOriginal = $datosMostrar['appUser'];
  $limiteSelectsOriginal = (int)$datosMostrar['limiteSelects'];
  $tamPaginaOriginal = (int)$datosMostrar['tamPagina'];
  $observacionesOriginal = $datosMostrar['observaciones'];
?>
<main>
  <script>
    window.location = '#tituloEditarUsuario';
  </script>
  
  <div id='main-content' class='container-fluid'>
    <?php
    if (isset($_POST['btnEditarUsuario'])){
      echo "<br><h3>".$mensaje."</h3>";
    }
    ?>
    <br>
    <h2 id="tituloEditarAlarma">Datos del Usuario <?php echo $idMostrar?></h2>

    <form id='frmEditarUsuario' name='frmEditarUsuario' method='post'>
      <table name='tblEditarUsuario' id='tblEditarUsuario' class='tabla2'>
        <caption>Formulario para la edici&oacute;n del usuario</caption>
        <tr>
          <th colspan='2' class='tituloTabla'>EDITAR USUARIO</th>
        </tr>
        
        <?php
        $i = 1;
              
        /// Recorro el array con los campos para ver cuales hay que mostrar y cuales no.
        foreach ($camposUsuarios as $key => $value) {   
          if ($camposUsuarios[$key]['mostrarEditar'] === 'si'){
            $indice = $camposUsuarios[$key]['nombreDB'];

            switch ($indice){
              case 'id': break;
        
              case 'nombre': echo "<tr>
                                    <td class='enc'>Nombre</td>
                                    <td>
                                      <input type='text' name='nombre' id='nombre' class='agrandar' rows='5' placeholder='Ingrese el nombre.' value='".$datosMostrar[$indice]."'>
                                      <input name='nombreOriginal' id='nombreOriginal' type='hidden' value='".$nombreOriginal."'>  
                                    </td>
                                  </tr>"; 
                            break;
              case 'apellido': echo "<tr>
                                      <td class='enc'>Apellido</td>
                                      <td>
                                        <input type='text' name='apellido' id='apellido' class='agrandar' rows='5' placeholder='Ingrese el apellido.' value='".$datosMostrar[$indice]."'>
                                        <input name='apellidoOriginal' id='apellidoOriginal' type='hidden' value='".$apellidoOriginal."'>
                                      </td>
                                    </tr>"; 
                                break;  
              case 'tamPagina': echo "<tr>
                                      <td class='enc'>Tama&nacute;o de P&aacute;gina</td>
                                      <td>
                                        <input type='text' name='tamPaginaUser' id='tamPaginaUser' class='agrandar' rows='5' placeholder='Ingrese el tamaño para la p&aacute;gina.' value='".(int)$datosMostrar[$indice]."'>
                                        <input name='tamPaginaOriginal' id='tamPaginaOriginal' type='hidden' value='".$tamPaginaOriginal."'>
                                      </td>
                                    </tr>"; 
                                break; 
              case 'limiteSelects': echo "<tr>
                                      <td class='enc'>Apellido</td>
                                      <td>
                                        <input type='text' name='limiteSelectsUser' id='limiteSelectsUser' class='agrandar' rows='5' placeholder='Ingrese el tama&nacute;o para los selects.' value='".(int)$datosMostrar[$indice]."'>
                                        <input name='limiteSelectsOriginal' id='limiteSelectsOriginal' type='hidden' value='".$limiteSelectsOriginal."'>
                                      </td>
                                    </tr>"; 
                                break; 
              case 'appUser': echo "<tr>
                                      <td class='enc'>Usuario en la App</td>
                                      <td>
                                        <input type='text' name='appUser' id='appUser' class='agrandar' rows='5' placeholder='Ingrese el nombre para el usuario de la app.' value='".$datosMostrar[$indice]."'>
                                        <input name='appUserOriginal' id='appUserOriginal' type='hidden' value='".$appUserOriginal."'>
                                      </td>
                                    </tr>"; 
                                break;  
              case 'observaciones': echo "<tr>
                                      <td class='enc'>Observaciones</td>
                                      <td>
                                        <input type='text' name='observaciones' id='observaciones' class='agrandar' rows='5' placeholder='Ingrese las obsevaciones.' value='".$datosMostrar[$indice]."'>
                                        <input name='observacionesOriginal' id='observacionesOriginal' type='hidden' value='".$observacionesOriginal."'>
                                      </td>
                                    </tr>"; 
                                break;                 
              case 'accion': break;                 
              default: echo "<tr>
                              <td class='enc'>".$camposUsuarios[$key]['nombreMostrar']."</td>
                              <td>".$datosMostrar[$indice]."</td>
                            </tr>";                 
            } /// Fin switch indice       
          } /// Fin if si mostrarEditar 
        } /// Fin foreach camposUsuarios
        ?>
        
        <tr>
          <td colspan='2' class='pieTabla'>
            <input type='submit' class='btn btn-danger' name='btnEditarUsuario' id='btnEditarUsuario' value='EDITAR'>
          </td>
        </tr>
      </table>
    </form>

    <div id="navegacion" class="pagination">
    <?php
      echo "<ul>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir a la primer alarma' href='editarUsuario.php?us=".$primero."&o=".$origenCodif."&cUser=".$consultaCodif."&pUser=".$paramCodif."'>|<  </a></li>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir a la alarma anterior' href='editarUsuario.php?us=".$anterior."&o=".$origenCodif."&cUser=".$consultaCodif."&pUser=".$paramCodif."'>  <<  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir a la siguiente alarma' href='editarUsuario.php?us=".$siguiente."&o=".$origenCodif."&cUser=".$consultaCodif."&pUser=".$paramCodif."'>  >>  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir a la última alarma' href='editarUsuario.php?us=".$ultimo."&o=".$origenCodif."&cUser=".$consultaCodif."&pUser=".$paramCodif."'>  >|</a></li>";
      echo "</ul>";
    ?>
    </div>
    <?php
      if ($origen === "cargar"){
        $volver = "<br><a href='cargar.php?i=1'>Volver al listado de usuarios</a><br><br>";
      }
      else {
        $volver = '<br><a href="javascript:close()">Cerrar y volver al listado</a><br><br>';
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

