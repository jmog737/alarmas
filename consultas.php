<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file consultas.php
*  @brief Archivo que se encarga de valiadar y generar las consultas a la base de datos.
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
require_once ('data/config.php');
require_once ('data/pdo.php');

/// Consulta sólo OCSs del área metro:
$query = "select idnodo, nombre, localidad from nodos where tipo!='PSS 32' and areaMetro=true";
$log = false;
$datos = json_decode(hacerSelect($query, $log), true);
$localidades = $datos['resultado'];
$totalLocalidades = $datos['rows'];

/// Consulta usuarios:
$query1 = "select idusuario, nombre, apellido from usuarios where estado='activo' order by apellido, nombre";
$datos1 = json_decode(hacerSelect($query1, $log), true);
$usuarios = $datos1['resultado'];
$totalUsuarios = $datos['rows'];

?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h2>Consultas hist&oacute;ricas:</h2>

      <form method="POST" name='frmConsultas' id='frmConsultas' action="buscar.php">
        <table id="consultas" name="consultas" class="tabla2">
        <caption>Formulario para hacer consultas hist&oacute;ricas a la base de datos.</caption>
          <tr>
            <th colspan="5" class="centrado tituloTabla">CONSULTAS</th>
          </tr>
          <tr>
            <th colspan="5" class="subTituloTabla1">FUENTE</th>
          </tr>
          <tr>
            <td class="fondoVerde"><input type="radio" name="criterio"  checked="checked" title="Elegir el origen a consultar. Seleccionar si se quiere buscar por NODO." value="nodo"></td>
            <th>Nodo:</th>  
            <td colspan="3">
              <select name="nodo" id="nodo" tabindex="1" style="width: 100%" title="Seleccione por favor el nodo del cual consultar.">
                <option value="todos" nombreCorto="todos">--- TODOS LOS NODOS ---</option>
                <?php
                foreach ($localidades as $i => $valor){
                  $loc = $localidades[$i]['localidad'];
                  $nombreCorto = $localidades[$i]['nombre'];
                  $idnodo = $localidades[$i]['idnodo'];
                  echo "<option value='".$idnodo."' nombreCorto='".$nombreCorto."'>".$loc." [".$nombreCorto."]</option>";
                }
                ?>
              </select>
            </td> 
          </tr>
          <tr>
            <td class="fondoVerde"><input type="radio" name="criterio" title="Elegir el origen a consultar. Seleccionar si se quiere buscar por ARCHIVO." value="file"></td>
            <th>Archivo</th>
            <td colspan="3"><input type="text" tabindex="2" placeholder="Elegir archivo" title="Elegir el archivo a consultar." name="fileSearch" id="fileSearch" class="agrandar" size="9" onkeyup="showHint(this.value, '#fileSearch', '')"></td>
          </tr>
          <tr>
            <th colspan="5" class="subTituloTabla1">FECHA</th>
          </tr>
          <tr>
            <td class="fondoNaranja">
              <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quiere buscar por fechas." value="intervalo">
            </td>
            <th>Entre:</th>
            <td>
              <input type="date" name="inicio" id="inicio" tabindex="3" title="Elegir la fecha de inicio. Sólo si se optó por una consulta por fechas." tabindex="6" style="width:100%; text-align: center" min="2019-08-01">
            </td>
            <td>y:</td>
            <td>
              <input type="date" name="fin" id="fin" tabindex="4" title="Elegir la fecha de finalización. Sólo si se optó por una consulta por fechas." tabindex="7" style="width:100%; text-align: center" min="2019-08-01">
            </td>
          </tr>
          <tr>
            <td class="fondoNaranja">
              <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quiere buscar por mes." value="mes">
            </td>
            <th>Mes:</th>
            <td>
              <select id="mes" name="mes" tabindex="5" title="Elegir el mes a buscar. Sólo si se optó por una consulta por mes." tabindex="8" style="width:100%">
                <option value="todos" selected="yes">--TODOS--</option>
                <option value="01">Enero</option>
                <option value="02">Febrero</option>
                <option value="03">Marzo</option>
                <option value="04">Abril</option>
                <option value="05">Mayo</option>
                <option value="06">Junio</option>
                <option value="07">Julio</option>
                <option value="08">Agosto</option>
                <option value="09">Setiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
              </select>
            </td>
            <th>Año:</th>
            <td>
              <select id="año" name="año" tabindex="6" title="Elegir el año. Sólo si se optó por una consulta por mes y/o año." tabindex="9" style="width:100%">
                <option value="2019" selected="yes">2019</option>
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="fondoNaranja">
              <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quieren TODOS los movimientos" value="todos" checked="checked">
            </td>
            <th>TODOS</th>
          </tr>
          <tr>
            <th colspan="5" class="subTituloTabla1">FILTROS</th>
          </tr>
          <tr>
            <th colspan="2">Alarma:</th>  
            <td colspan="3">
              <select name="alarma" id="alarma" tabindex="7" title="Elegir el tipo de alarma.">
                <option value="todas">--- TODAS ---</option>
                <option value="CR" title="Alarma CRITICAL">CR</option>
                <option value="WR" title="Alarma WARNING">WR</option>  
                <option value="MJ" title="Alarma MAJOR">MJ</option>
                <option value="MN" title="Alarma MINOR">MN</option>
                <option value="NA" title="Alarma NOT ALARMED">NA</option>
                <option value="NR" title="Alarma NOT REPORTED">NR</option>
              </select>
            </td> 
          </tr>
<!--          <tr>
            <th colspan="2">Condici&oacute;n:</th>  
            <td colspan="3">
              <select name="condicion" id="condicion" title="Elegir el tipo de condición.">
                <option value="todas">--- TODAS ---</option>
                //<?php
//                foreach ($localidades as $i => $valor){
//                  $loc = $localidades[$i]['localidad'];
//                  $nombreCorto = $localidades[$i]['nombre'];
//                  $idnodo = $localidades[$i]['idnodo'];
//                  echo "<option value='".$loc."' nombreCorto='".$nombreCorto."' idnodo=".$idnodo.">".$nombreCorto." - ".$loc."</option>";
//                }
//                ?>
              </select>
            </td> 
          </tr>-->
          <tr>
            <th colspan="2">Usuario:</th>  
            <td colspan="3">
              <select name="usuarios" id="usuarios" tabindex="8" title="Elegir el usuario.">
                <option value="todos">--- TODOS ---</option>
                <?php
                foreach ($usuarios as $i => $valor1){
                  $apellidoUsuario = $usuarios[$i]['apellido'];
                  $nombreUsuario = $usuarios[$i]['nombre'];
                  $idusuario = $usuarios[$i]['idusuario'];
                  echo "<option value='".$idusuario."'>".$nombreUsuario." ".$apellidoUsuario."</option>";
                }
                ?>
              </select>
            </td> 
          </tr>
<!--          <tr>
            <th colspan="2">Equipo:</th>  
            <td colspan="3">
              <select name="equipo" id="equipo" tabindex="9" title="Elegir el tipo de equipo.">
                <option value="todas">--- TODOS ---</option>
                <option value="pss32" title="PSS 32">PSS 32</option>
                <option value="ocs36" title="OCS 36">OCS 36</option>  
                <option value="ocs64" title="OCS 64">OCS 64</option>
              </select>
            </td> 
          </tr>-->
          <tr>
            <td colspan="5" class="pieTabla">
              <input type="button" class="btn btn-success" tabindex="10" name="buscar" id="buscar" title="Ejecutar la consulta" tabindex="16" value="Consultar" align="center">
            </td>
          </tr>
        </table>
        <input type="hidden" name="query" id="query" value="">
        <input type="hidden" name="param" id="param" value="">
        <input type="hidden" name="offset" id="offset" value="0">
        <input type="hidden" name="mensaje" id="mensaje" value="">
      </form> 
      
    <?php
    $volver = "<a href='subirArchivo.php'>Volver a Inicio</a><br><br>";
    echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>