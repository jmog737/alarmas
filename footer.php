<footer class='panel-footer' id='miFooter'>
  <a href='#' class="arrow arrow-bottom"><img border='0'  src="images/arrowDown.png" height="35" width="35" title="BAJAR" /></a>
  <a href='#' class="arrow arrow-top"><img border='0'  src="images/arrowUp.png" height="35" width="35"  title="SUBIR" /></a>
  <p>
    <input id="usuarioSesion" name="usuarioSesion" type="text" value="" style="color: black; display: none">
    <input id="userID" name="userID" type="text" type="text" value="" style="color: black; display: none">
    <input id="timestampSesion" name="timestampSesion" type="text" value="" style="color: black; display: none">
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

<!-- Modal para cambiar la contraseña -->
<div class="modal fade bottom" id="modalPwd" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-danger modal-sm modal-side modal-bottom-left" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cambio PWD: <?php echo $_SESSION["username"]?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="white-text">&times;</span></button>
      </div>
      <div class="modal-body">
        <form>
          <table id="tblModalPwd">
  <!--          <tr>
              <td>Nuevo nombre:</td>
              <td>
                <input type="text" id="nombreUser" name="nombreUser" class="agrandar">
              </td>
            </tr>-->
            <tr>
              <td>NUEVA:</td>
              <td>
                <input type="password" id="pw1" placeholder="Contraseña NUEVA" title="Ingresar la NUEVA contraseña" class="agrandar" autofocus="true" autocomplete="new password">
              </td>
            </tr>
            <tr>
              <td>REPETIR:</td>
              <td>
                <input type="password" id="pw2" placeholder="Contraseña NUEVA" title="Repetir la NUEVA contraseña" class="agrandar" autocomplete="new password">
              </td>
            </tr>
          </table>  
        </form>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" title="Cambiar la contraseña" id="btnModal">Actualizar</button>
        <button type="button" class="btn btn-sm btn-danger btn-outline-danger waves-effect" title="Cerrar ventana SIN modificar la contraseña" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para cambiar la contraseña -->

<!-- Modal para cambiar los parámetros de visualización -->
<div class="modal fade left" id="modalParametros" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-warning modal-sm modal-side modal-bottom-left" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cambio Par&aacute;metros: <?php echo $_SESSION["username"]?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="white-text">&times;</span></button>
      </div>
      <div class="modal-body" style="padding-left: 5px">
        <table id="tblModalParametros">
<!--          <tr>
            <td>Nuevo nombre:</td>
            <td>
              <input type="text" id="nombreUser" name="nombreUser" class="agrandar">
            </td>
          </tr>-->
          <tr>
            <td nowrap>Tam. p&aacute;gina:</td>
            <td>
              <input type="text" id="pageSize" placeholder="NUEVO tamaño de página" title="Ingresar el NUEVO tamaño de p&aacute;gina" class="agrandar" autofocus="true">
            </td>
          </tr>
          <tr>
            <td nowrap>Tam. Selects:</td>
            <td>
              <input type="text" id="tamSelects" placeholder="NUEVO tamaño de SELECTS" title="Ingresar el NUEVO tamaño de los selects" class="agrandar" autofocus="true">
            </td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-warning" title="Cambiar par&acute;metros" id="btnParam">Actualizar</button>
        <button type="button" class="btn btn-sm btn-warning btn-outline-warning waves-effect" title="Cerrar ventana SIN modificar los par&aacute;metros" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para cambiar los parámetros -->
<?php 

require_once("scripts.php");
require_once("alertas.php");
?>
