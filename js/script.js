///Ahora las variables se toman de un único lugar que es el archivo config.php
///Las mismas, para que estén accesibles, se agregan a unos input "invisibles" que están en el HEAD (antes de incluir script.js para que estén disponibles).
var duracionSesion = parseInt($("#duracionSesion").val(), 10);

/**
///  \file script.js
///  \brief Archivo que contiene todas las funciones de Javascript.
///  \author Juan Martín Ortega
///  \version 1.0
///  \date Agosto 2019
*/

/***********************************************************************************************************************
/// ************************************************** FUNCIONES GENÉRICAS *********************************************
************************************************************************************************************************
*/

/**
 * \brief Función que vacía los campos del form frmLogin. 
 *        Se hace para sobre escribir el autocompletado del navegador.
 */
function vaciarFrmLogin(){
  $("#nombreUsuario").val('');
  $("#password").val('');
}
/********** fin vaciarFrmLogin() **********/

/**
 * \brief Función que valida los datos de ingreso (por ahora, solo que se haya ingresado el usuario).
 */
function validarIngreso () {
  var usuario = $("#nombreUsuario").val();
  if ((usuario === ' ')||(usuario === "null")||(usuario === '')){ 
    alert('¡Debe ingresar el nombre de usuario!');
    $("#nombreUsuario").focus();
  }
  else {
    $("#frmLogin").submit();
  }
}
/********** fin validarIngreso() **********/

/**
 * \brief Función que valida el forma para cargar el archivo.
 */
function validarSubmit(){
  var archivoASubir = $("#uploadedFile").val();
  
  if ((archivoASubir === undefined)||(archivoASubir === '')){
    alert('No se seleccionó archivo alguno.\nPor favor verifique!.');
  }
  else {
    $("#frmSubir").submit();
  }
}
/********** fin validarSubmit() **********/

/**
 * \brief Función que chequea las variables de sesión para saber si la misma aún está activa o si ya expiró el tiempo.
 * @param mensaje {String} String con un mensaje opcional usado para debug.
 * @param cookie {String} String que indica si se debe o no actualizar la expiración de la cookie.
 */
function verificarSesion(mensaje, cookie) {
  var xmlhttp = new XMLHttpRequest();
  if (mensaje !== ''){ 
    const dateTime = Date.now();
    const tiempo = Math.floor(dateTime / 1000);
    //alert(mensaje+': '+tiempo);
  }
  else {
    mensaje = 'XXX';
  }
  /*
  onreadystatechange: Defines a function to be called when the readyState property changes.
  readyState property:
    Holds the status of the XMLHttpRequest.
      0: request not initialized 
      1: server connection established
      2: request received 
      3: processing request 
      4: request finished and response is ready
  */
  xmlhttp.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      var myObj1 = JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText);
      var user = '';
      var user_id = '';
      var sesion = '';
      var timestamp = '';
      var oldTime = '';
      var usuarioViejo = 'ERROR';
      var duracionSesion = myObj1.duracion;
      if ($.isEmptyObject(myObj1)){
        user = 'ERROR';
        user_id = 0;
        sesion = 'expirada';
        timestamp = 0;
        oldTime = 0;
        usuarioViejo = 'ERROR';
      }
      else {
        user = myObj1.user;
        user_id = myObj1.user_id;
        sesion = myObj1.sesion;
        timestamp = myObj1.time;
        oldTime = myObj1.oldTime;
        usuarioViejo = myObj1.oldUser;
      };
      var temp = String(timestamp).substr(-3);

      if (sesion === 'expirada'){
        var mostrarSesion = '';
        ///Se comenta siguiente línea usada para las pruebas:
        //var tempSesion = prompt('Ingrese el tiempo deseado para la sesión: \n');   
        var horas = Math.floor( duracionSesion / 3600 );  
        var minutos = Math.floor( (duracionSesion % 3600) / 60 );
        var segs = duracionSesion % 60;
        //Anteponiendo un 0 a los minutos si son menos de 10 
        //minutos = minutos < 10 ? '0' + minutos : minutos;
        //Anteponiendo un 0 a los segundos si son menos de 10 
        //segs = segs < 10 ? '0' + segs : segs;
        if (horas === 0){
          if (minutos === 0){
            mostrarSesion = segs+' segs';
          }
          else {
            if (segs === 0){
              mostrarSesion = minutos+'min';
            }
            else {
              mostrarSesion = minutos+'min '+segs+'segs';
            }
          }
        }
        else {
          if ((minutos === 0)&&(segs === 0)){
            mostrarSesion = horas+'h';
          }
          else {
            if (segs === 0){
              mostrarSesion = horas+'h '+minutos+'min';
            }
            else {
              mostrarSesion = horas+'h '+minutos+'min '+segs+'segs';
            }
          }  
        }
        //alert('Motivo: '+user+'\n'+usuarioViejo.toUpperCase()+":\nTú sesión ha estado inactiva por más de "+mostrarSesion+"\nPor favor, por seguridad, ¡vuelve a loguearte!.\n\ntiempo seteado: "+oldTime+'\nactual: '+temp+'\n\nDuración Sesión: '+duracionSesion+'s\nmensaje: '+mensaje);
        alert(usuarioViejo.toUpperCase()+":\n\nTú sesión ha estado inactiva por más de "+mostrarSesion+"\nPor favor, por seguridad, ¡vuelve a loguearte!.\n\n"+'Motivo: '+user+"\nTiempo seteado: "+oldTime+'\nTiempo actual: '+temp);
        window.location.assign("salir.php");
      }
      else {
        $("#usuarioSesion").val(user);
        $("#userID").val(user_id);
        $("#timestampSesion").val(timestamp);
        $("#main-content").focus();
        //alert('¡Actualicé!\n\nTiempo viejo: '+oldTime+'\nNuevo tiempo: '+temp+'\n\nDuración Sesión: '+duracionSesion+'\nmensaje: '+mensaje+'\n\nsesion: '+sesion+'\nDesde: '+window.location.href);
      }
    }
  };

  xmlhttp.open("GET", "data/estadoSesion.php?c="+cookie+"", true);
  xmlhttp.send();
}
/********** fin verificarSesion(mensaje, cookie) **********/

/***********************************************************************************************************************
/// ************************************************* FUNCIONES USUARIOS ***********************************************
************************************************************************************************************************
*/

/**
 * \brief Función que primero valida la info ingresada, y de ser válida, hace la actualización del pwd del usuario del sistema.
 */
function actualizarUser() {
    verificarSesion('', 's');
    
    var pw1 = $("#pw1").val();
    var pw2 = $("#pw2").val();

    if (pw1 === ''){
      alert('La contraseña 1 NO puede estar vacía.\nPor favor verifique.');
      $("#pw1").focus();
    }
    else {
      if (pw2 === ''){
        alert('La contraseña 2 NO puede estar vacía.\nPor favor verifique.');
        $("#pw2").focus();
      }
      else {
        if (pw1 !== pw2) {
          alert('Las contraseñas ingresadas NO son iguales.\nPor favor verifique.');
          $("#pw1").val('');
          $("#pw2").val('');
          $("#pw1").focus();
        }
        else {
          //alert('hay que actualizar a: '+$("#usuarioSesion").val()+'\nID: '+$("#userID").val());
          /******** COMENTO PARTE DEL USUARIO POR AHORA **********************/
          ///var user = $("#nombreUser").val();
          var iduser = $("#userID").val();
          var url = "data/updateQuery.php";
          var query = 'update usuarios set appPwd=sha1("'+pw1+'") ';
          /*
          if (user !== ''){
            query += ', user="'+user+'" ';
          }
          */
          query += 'where idusuario='+iduser;
          var log = "NO";
          var jsonQuery = JSON.stringify(query);
          //alert(query);
          $.getJSON(url, {query: ""+jsonQuery+"", log: log}).done(function(request) {
            var resultado = request["resultado"];
            if (resultado === "OK") {
              alert('Los datos se modificaron correctamente!.');
              $("#modalPwd").modal("hide");
              //cargarEditarMovimiento(idmov, "main-content");
              //inhabilitarMovimiento();
            }
            else {
              alert('Hubo un problema en la actualización. Por favor verifique.');
            }
            
          });
        }
      }
    }
  //}
}
/********** fin actualizarUser() **********/

/***********************************************************************************************************************
/// *********************************************** FIN FUNCIONES USUARIOS *********************************************
************************************************************************************************************************
**/

/**
\brief Función que se ejecuta al cargar la página.
En la misma se ve primero desde que página se llamó, y en base a eso
se llama a la función correspondiente para cargar lo que corresponda (actividades, referencias, etc.)
Además, en la función también están los handlers para los distintos eventos jquery.
*/
function todo () {
  ///Levanto la url actual: 
  var urlActual = jQuery(location).attr('pathname');
  var parametros = jQuery(location).attr('search');
  var remplaza = /\+|%20/g;
  if (parametros) {
    //parametros = unescape(parametros);
    parametros = parametros.replace(remplaza, " ");
  }
  var res = urlActual.split("/");
  var tam = res.length;
  var dir = res[tam-1];
  
  ///Según en que url esté, es lo que se carga:
  switch (dir) {
    case "index.php": break;
                                     
    default: break;
  }  

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados al RESALTADO de los input.
******************************************************************************************************************************
*/

///Disparar funcion cuando algún elemento de la clase agrandar reciba el foco.
///Se usa para resaltar el elemento seleccionado.
$(document).on("focus", ".agrandar", function (){
  $(this).css("font-size", "1.35em");
  $(this).css("background-color", "#e7f128");
  $(this).css("font-weight", "bolder");
  $(this).css("color", "red");
  $(this).css("height", "100%");
  //$(this).css("max-width", "100%");
  //$(this).parent().prev().prev().children().prop("checked", true);
});
/********** fin on("focus", ".agrandar", function () **********/

///Disparar funcion cuando algún elemento de la clase agrandar pierda el foco.
///Se usa para volver al estado "normal" el elemento que dejó de estar seleccionado.
$(document).on("blur", ".agrandar", function (){
  $(this).css("font-size", "inherit");
  $(this).css("background-color", "#ffffff");
  $(this).css("font-weight", "inherit");
  $(this).css("color", "inherit");
});
/********** fin on("blur", ".agrandar", function () **********/

/*****************************************************************************************************************************
/// ***************************************************** FIN RESALTADO ******************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// Comienzan las funciones para el form de INGRESO.
******************************************************************************************************************************
*/

///Disparar función al hacer enter estando en el elemento nombreUsuario.
///Básicamente, la idea es pasar el foco al elemento password cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#nombreUsuario", function(e) {
  if(e.which === 13) {
    e.preventDefault();
    $("#password").val('');
    $("#password").focus();
  }  
});
/********** fin on("keypress", "#nombreUsuario", function(e) **********/

///Disparar función al hacer enter estando en el elemento password.
///Básicamente, la idea es hacer el submit cosa de ahorrar tiempo en el ingreso.
$(document).on("keypress", "#password", function(e) {
  if(e.which === 13) {
    e.preventDefault();
    validarIngreso();
  }  
});
/********** fin on("keypress", "#password", function(e) **********/

///Disparar función al detectar el submit en el formulario de ingreso.
$(document).on("click", "#login", function(e) {
  e.preventDefault();
  validarIngreso();
});
/********** fin on("click", "#login", function(e) **********/

/*****************************************************************************************************************************
/// Fin de las funciones para el form de INGRESO.
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// **************************************************** INICIO MODAL USARIO *************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer click en el link con el nombre del usuario que está logueado.
///Esto hace que se abra el modal para cambiar la contraseña.
$(document).on("click", "#user", function(){
  verificarSesion('', 's');
  $("#modalPwd").modal("show");
});
/********** fin on("click", "#user", function() **********/

///Disparar función al abrirse el modal para cambiar la contraseña.
///Lo único que hace es limpiar el form para poder ingresar los nuevos datos.
$(document).on("shown.bs.modal", "#modalPwd", function() {
  $("#pw1").val('');
  $("#pw2").val('');
  $("#pw1").attr("autofocus", true);
  $("#pw1").focus();
});
/********** fin on("shown.bs.modal", "#modalPwd", function() **********/

///Disparar función al hacer click en el botón de ACTUALIZAR que está en el MODAL.
///Primero valida que la info ingresada sea válida (pwd no nulos e iguales entre sí), y luego 
///ejecuta la consulta para cambiar la contraseña.
$(document).on("click", "#btnModal", function(){
  actualizarUser();
});
/********** fin on("click", "#btnModal", function() **********/

///Disparar función al hacer ENTER estando en el elemento pw1 del MODAL.
///Esto hace que se pase el foco al siguiente input del MODAL (pw2) cosa de ahorrar tiempo.
$(document).on("keypress", "#pw1", function(e) {
  if(e.which === 13) {
    $("#pw2").focus();
  }  
});
/********** fin on("keypress", "#pw1", function(e) **********/

///Disparar función al hacer ENTER estando en el elemento pw2 del MODAL.
///Esto hace que se llame a la función correspondiente (actualizarUser()) cosa de ahorrar tiempo.
$(document).on("keypress", "#pw2", function(e) {
  if(e.which === 13) {
    actualizarUser();
  }  
});
/********** fin on("keypress", "#pw2", function(e) **********/

/*****************************************************************************************************************************
/// **************************************************** FIN MODAL USARIO ****************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************** INICIO SUBIR ARCHIVO **************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer SUBMIT del form para cargar el archivo.
///La idea es validar que al menos se haya elegido un archivo.
$(document).on("click", "#btnCargar", function() {
  validarSubmit();
});
/********** fin on("click", "#btnCargar", function() **********/

/*****************************************************************************************************************************
/// **************************************************** FIN SUBIR ARCHIVO ***************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************ INICIO MUESTRA ARCHIVO **************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer SUBMIT del form para exportar los datos.
$(document).on("click", "#btnExportar", function() {
  alert('click en exportar');
});
/********** fin on("click", "#btnExportar", function() *********/

/*****************************************************************************************************************************
/// ************************************************** FIN MUESTRA ARCHIVO ***************************************************
******************************************************************************************************************************
*/

}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());
/********** fin on("ready", todo()) **********/