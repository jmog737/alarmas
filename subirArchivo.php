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
?>
    <main>
      <div id='main-content' class='container-fluid'>
        <br>  
        <h1>Cargar archivo:</h1>
        
        <form method="POST" name='frmSubir' id='frmSubir' action="cargar.php" enctype="multipart/form-data">
          <table id="estadisticas" name="estadisticas" class="tabla2">
          <caption>Formulario para subir el archivo a la base de datos.</caption>
            <tr>
              <th colspan="2" class="centrado tituloTabla">SUBIR</th>
            </tr>
            <tr>
              <td>
                Archivo:
              </td>  
              <td>
                <input type="file" name="uploadedFile" id="uploadedFile" accept=".csv"/>
              </td> 
            </tr>
            <td colspan="2">
              <input type="button" name="btnCargar" id="btnCargar" value="CARGAR" />
            </td>
            <tr>
            </tr>
          </table>  
        </form>
        
      </div>      
    </main>
<?php
  require_once ('footer.php');
?>    
  </body>
</html>

