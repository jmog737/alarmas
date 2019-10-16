<footer class='panel-footer' id='miFooter'>
  <a href='#' class="arrow arrow-bottom"><img border='0'  src="images/arrowDown.png" height="35" width="35" title="BAJAR" /></a>
  <a href='#' class="arrow arrow-top"><img border='0'  src="images/arrowUp.png" height="35" width="35"  title="SUBIR" /></a>
  <p>
    <input id="usuarioSesion" name="usuarioSesion" type="text" value="" style="color: black; display: none">
    <input id="userID" name="userID" type="text" value="" style="color: black; display: none">
    <input id="timestampSesion" name="timestampSesion" type="text" value="" style="color: black; display: none">
    <input id="nombreGrafica" name="nombreGrafica" type="text" value="<?php echo $_SESSION["nombreGrafica"]?>" style="color: black; display: none">
  </p>
  
  <div class='container-fluid'>
    <div class='row'>
      <section id='hours' class='col-sm-4'>
        <hr class='d-block d-sm-none'>
        <?php
          $hoy = date("d/m/Y");
          if (!empty($_SESSION['user_id']))
            {
        ?>
            Usuario:
            <font class='naranja'>
            <?php
              // Confirm the successful log-in
              echo "<a href='#modalPwd' title='Cambiar contraseña de acceso' class='naranja' id='user'>".strtoupper($_SESSION['username'])."</a> ";
              if ( isset($_SESSION['success']) ) {
                echo('<span style="color:white;margin:0">'.$_SESSION['success']."</span>");
                unset($_SESSION['success']);
              }
              echo  "<br>"
                . "<a href='#modalParametros' title='Cambiar los parámetros' class='naranja' id='param'>--- Cambiar Par&aacute;metros ---</a>";
            ?>
            </font>
            <br>
            <span id='fechaActual'>
            <?php echo $hoy." --- "; ?>
            </span>

            <font><a title="Salir del programa" href="salir.php">Salir</a></font>
            <br>
          <?php        
          }
          else {
          ?>
            <br>
            Usuario:
            <font class='naranja'>NO logueado</font>
            <br>
            <span id='fechaActual'>
          <?php
            echo $hoy;
          ?>
            </span>  
          <?php    
          }  
          ?>  
      </section>

      <section id='address' class='col-sm-4 d-none d-sm-block'>
        Vilardeb&oacute; 1500, Montevideo, Uruguay
        <br>xxxxxxxx / 22002065 / xxxxxxxx
        <br>Horario: xx:xx - xx:xx
      </section>

      <section id='testimonials' class='col-sm-4'>
        <hr class='d-block d-sm-none'>
        v.1.0<br>
        &copy; Copyright xxxxxxxxxxxx 2019
        <hr class='d-block d-sm-none'>
      </section>

    </div>
  </div>
</footer>
<?php 
require_once("scripts.php");
?>
