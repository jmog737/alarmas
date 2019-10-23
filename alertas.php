<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file alertas.php
*  @brief Archivo que contiene todas las alertas a mostrar.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Octubre 2019
*
*******************************************************/
?>
<p>
  <input id="caller" name="caller" type="text" value="" style="color: black; display: none">
</p>
<!-- Modal para mostrar avisos -->
<div class="modal fade top" id="modalAviso" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-info modal-sm modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloAviso"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-left: 5px">
        <p id="mensajeAviso"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-info btn-outline-info waves-effect" title="Cerrar ventana" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para mostrar avisos -->

<!-- Modal para mostrar advertencias -->
<div class="modal fade top" id="modalAdvertencia" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-danger modal-sm modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloAdvertencia"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <p aria-hidden="true" class="white-text">&times;</p>
        </button>
      </div>
      <div class="modal-body" style="padding-left: 5px">
        <p id="mensajeAdvertencia"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger btn-outline-danger waves-effect" title="Cerrar ventana" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para mostrar advertencias -->

<!-- Modal para mostrar success -->
<div class="modal fade top" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-notify modal-success modal-sm modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloSuccess"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="white-text">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-left: 5px">
        <p id="mensajeSuccess" class='text-center'></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success btn-outline-success waves-effect" title="Cerrar ventana" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para mostrar success -->