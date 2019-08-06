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
        <form method="POST" action="cargar.php" enctype="multipart/form-data">
          <input type="hidden" name="MAX_FILE_SIZE" value="8192" />
          <input type="file" name="uploadedFile" /><br>
          <input type="submit" name="btnCargar" value="CARGAR" />
        </form>
      </div>      
    </main>        
  </body>
</html>

