<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file subirArchivo.php
*  @brief Form para cargar el archivo en la base de datos.
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
  $query = "select distinct localidad from nodos order by localidad asc";
  require_once ('data/selectQueryDirecto.php');
  $localidades = $datos['resultado'];
  $totalLocalidades = $datos['rows'];
?>
    <main>
      <div id='main-content' class='container-fluid'>
        <br>  
        <h2>Cargar archivo:</h2>
        
        <form method="POST" name='frmSubir' id='frmSubir' action="cargar.php" enctype="multipart/form-data">
          <table id="estadisticas" name="estadisticas" class="tabla2">
          <caption>Formulario para subir el archivo a la base de datos.</caption>
            <tr>
              <th colspan="2" class="centrado tituloTabla">SUBIR ARCHIVO</th>
            </tr>
            <tr>
              <th>
                Archivo:
              </th>  
              <td>
                <input type="file" name="uploadedFile" id="uploadedFile" accept=".csv" onchange="archivoElegido()"/>
              </td> 
            </tr>
            <tr>
              <th>
                Nodo:
              </th>  
              <td>
                <select name="nodo" id="nodo">
                  <option value="nada">--- Seleccionar NODO ---</option>
                  <?php
                  foreach ($localidades as $i => $valor){
                    $loc = $localidades[$i]['localidad'];
                    echo "<option value='".$loc."'>".$loc."</option>";
                  }
                  ?>
                </select>
              </td> 
            </tr>
            <td colspan="2" class="pieTabla">
              <input type="button" name="btnCargar" id="btnCargar" value="CARGAR" />
            </td>
          </table>  
        </form>
        <br>
      </div>      
    </main>
<?php
  require_once ('footer.php');
?>    
  </body>
</html>

