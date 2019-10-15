///Ahora las variables se toman de un único lugar que es el archivo config.php
///Las mismas, para que estén accesibles, se agregan a unos input "invisibles" que están en el HEAD (antes de incluir script.js para que estén disponibles).
var duracionSesion = parseInt($("#duracionSesion").val(), 10);
var limiteSeleccion = parseInt($("#limiteSeleccion").val(), 10);
var tamPagina = parseInt($("#tamPagina").val(), 10);
var limiteSelects = parseInt($("#limiteSelects").val(), 10);
var maxTamPagina = parseInt($("#maxTamPagina").val(), 10);
var maxLimiteSelects = parseInt($("#maxLimiteSelects").val(), 10);

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
  \brief Función que valida que el parámetro pasado sea un entero.
  @param numero Dato a validar.                  
*/
function validarEntero(numero) {//alert(valor);
  if (isNaN(numero)){
    //alert ("Ups... " + numero + " no es un número.");
    return false;
  } 
  else {
    if (numero % 1 == 0) {
      //alert ("Es un numero entero");
      return true;
    } 
    else {
      //alert ("Es un numero decimal");
      return false;
    }
  }
}
/********** fin validarEntero(valor) **********/

/**
 * 
 * @param {String} str String con la cadena de texto a buscar como parte del archivo.
 * @param {String} id String con el id del campo luego del cual se tienen que agregar los datos.
 * @param {String} seleccionado String que indica, si es que es disitinto de nulo, el archivo seleccionado.
 * \brief Función que muestra las sugerencias de los archivos disponibles.
 */
function showHint(str, id, seleccionado) {
  if (str.length === 0) { 
    //$("#hint").remove();
    $("[name='hint']").remove();
    $("#fileSearch").val("");
    return;
  } 
  else {
    var query = "select distinct archivo from alarmas where (alarmas.archivo like '%"+str+"%' or alarmas.fechaCarga like '%"+str+"%') order by alarmas.fechaCarga desc, alarmas.archivo asc";

    if (seleccionado !== ''){
      var archivosTemp = seleccionado.split(',');
    }
    var url = "data/getJSON.php";
    var log = "NO";
    $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
      var sugerencias = request.resultado;
      var totalSugerencias = parseInt(request.rows, 10);
      $("[name='hint']").remove();
      
      var mostrar = '';
      var unico = '';
      if (totalSugerencias >= 1) {
        if ((parseInt($("#productoGrafica").length, 10) > 0)||(parseInt($("#producto").length, 10) > 0)){
          mostrar = '<select name="hint" id="hint" class="hint" size="15">';
        }
        else {
          mostrar = '<select name="hint" id="hint" class="hint" multiple size="15">';
        }
        if (totalSugerencias > 1) {
          mostrar += '<option value="NADA" name="NADA">--Seleccionar--</option>';
        }
        for (var i in sugerencias) {
          if (totalSugerencias === 1){
            unico = sugerencias[i]["archivo"];
          }          
          if (seleccionado !== ''){
            var sel = "";
//            for (var k in archivosTemp){
//              var selEntero = parseInt(archivosTemp[k], 10);
//              if (parseInt(sugerencias[i]["idprod"], 10) === selEntero) {
//                sel = 'selected="yes"';
//              }
//            }
          }
         
          var resaltarOption = 'class="fondoSelect"';
          //mostrar += '<option value="'+sugerencias[i]["idprod"]+'" name="'+snapshot+'" stock='+sugerencias[i]["stock"]+' alarma='+sugerencias[i]["alarma"]+' '+sel+ '>[' + sugerencias[i]["entidad"]+'] '+sugerencias[i]["nombre_plastico"] + ' {' +bin+'} --'+ codigo_emsa +'--</option>';
          mostrar += '<option value="'+sugerencias[i]["archivo"]+'"'+resaltarOption+'>' + sugerencias[i]["archivo"] + '</option>';
        }
        mostrar += '</select>';
      }
      else {
        mostrar = '<p name="hint" value="">No se encontraron sugerencias!</p>';
      }
      $(id).after(mostrar);
      
      /// Agregado a pedido de Diego para que se abra el select automáticamente:
      var length = parseInt($('#hint> option').length, 10);
      if (length > limiteSelects) {
        length = limiteSelects+1;
      }
      else {
        length++;
      }
      if (length > totalSugerencias){
        length = totalSugerencias + 2;
      }
      //open dropdown
      $("#hint").attr('size',length);
      
      if (seleccionado !== ''){
        $("#hint").focus();
      }
      else {
        $("#fileSearch").focus();    
      }
      
      if (totalSugerencias === 1){
        ///Comentado por ahora pues Diego prefiere que NO salte de forma automática:
        //$("#comentarios").focus();
        $("#hint option[value='"+unico+"'] ").attr("selected", true);
        //$("#cantidad").focus();
      }      
    });
  }
}
/********** fin showHint(str, id, seleccionado) **********/

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
  verificarSesion('', 's');
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
 * \brief Función que valida el form para cargar el archivo.
 */
function validarSubmitCargar(){
  verificarSesion('', 's');
  var archivoASubir = $("#uploadedFile").val();
  
  if ((archivoASubir === undefined)||(archivoASubir === '')){
    alert('No se seleccionó archivo alguno.\nPor favor verifique!.');
    return false;
  }
  else {
    var nodo = $("option:selected", "#nodo").val();
    if (nodo === 'nada'){
      alert('Hay que seleccionar un nodo.\nPor favor verifique!.');
      $("#nodo").focus();
      return false;
    }
    else {
      var nombreNodoCorto = $("option:selected", "#nodo").attr("nombreCorto");
      var temp1 = archivoASubir.split(".");
      //var archivo1 = temp1[0];
      var extension = temp1[1];
      //var archivo2 = archivo1.split("\\");
      //var tam = archivo2.length;
      //var nombreArchivo = archivo2[tam-1];
      
      //alert('nombreCorto: '+nombreNodoCorto+'\nnodo: '+$("#nodo").val()+'\narchivo: '+nombreArchivo+'\nextension: '+ extension);
      
      var posOcs = nombreNodoCorto.search("#");
      if (posOcs === -1){
        if (extension === 'csv'){
          alert('Se seleccionó un archivo de OCS (.csv) para un PSS32 ('+nombreNodoCorto+').\n¡Por favor verifique!.');
          $("#nodo").focus();
          return false;
        }
      }
      else {
        if (extension === 'xls'){
          alert('Se seleccionó un archivo de PSS32 (.xls) para un OCS ('+nombreNodoCorto+').\n¡Por favor verifique!.');
          $("#nodo").focus();
          return false;
        }
      }
      
      /// Extraigo la info de los atributos del option pues NO se pasan en el POST
      /// En base a las mismas modifico el valor del option para poder pasarlos por el POST
      var idnodo = $("option:selected", "#nodo").attr("idnodo"); 
      $("option:selected", "#nodo").val(nodo+'---'+nombreNodoCorto+'---'+idnodo);
      $("#frmSubir").submit();
      
      /// Comento por ahora la validación para que no se repita el archivo:
//      if (nombreSinOCS === nombreArchivo){
//        //alert('Los nombres coinciden:\nnodo: '+nombreSinOCS+'\narchivo: '+nombreArchivo);
//        $("#frmSubir").submit();
//      }
//      else {
//        alert('El archivo seleccionado NO coincide con el nodo elegido:\nNodo: '+nombreSinOCS+'\nArchivo: '+nombreArchivo+'\nPor favor verifique.');
//        return false;
//      }
    }
  }
}
/********** fin validarSubmitCargar() **********/

/**
 * \brief Función que valida el form para cargar el archivo de un PSS32.
 */
function validarSubmitCargarPSS32(){
  verificarSesion('', 's');
  var archivoASubir = $("#uploadedFilePSS32").val();
  
  if ((archivoASubir === undefined)||(archivoASubir === '')){
    alert('No se seleccionó archivo alguno.\nPor favor verifique!.');
    return false;
  }
  else {
    if ($("#nodo").val() === 'nada'){
      alert('Hay que seleccionar un nodo.\nPor favor verifique!.');
      $("#nodo").focus();
      return false;
    }
    else {
      var nombreNodoCorto = $("option:selected", "#nodo").attr("nombreCorto");
      var temp = nombreNodoCorto.split("#");
      var nombreSinOCS = temp[0];
      
      /// Extraigo la info de los atributos del option pues NO se pasan en el POST
      /// En base a las mismas modifico el valor del option para poder pasarlos por el POST
      var idnodo = $("option:selected", "#nodo").attr("idnodo");
      var nodo = $("option:selected", "#nodo").val();
      $("option:selected", "#nodo").val(nodo+'---'+nombreNodoCorto+'---'+idnodo);
      
      var temp1 = archivoASubir.split(".");
      var archivo1 = temp1[0];
      var archivo2 = archivo1.split("\\");
      var tam = archivo2.length;
      var nombreArchivo = archivo2[tam-1];
      $("#frmSubirPSS32").submit();
    }
  }
}
/********** fin validarSubmitCargarPSS32() **********/

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

/**
 * \brief Función que valida el form para editar una alarma.
 */
function validarEditarAlarma(){
  verificarSesion('', 's');
  var seguir = false;
  var causa = $("#causa").val();
  var solucion = $("#sln").val();
  var solucionOriginal = $("#solucionOriginal").val();
  var causaOriginal = $("#causaOriginal").val();
  //alert('causa: '+causa+' --- causaOriginal: '+causaOriginal+'\nsolucion: '+solucion+' --- Solucion Original: '+solucionOriginal);
  if (causa === ''){
    alert('La causa NO puede quedar vacía.\nPor favor verifique.');
    $("#causa").focus();
  }
  else {
    if (solucion === ''){
      alert('La solución NO puede quedar vacía.\nPor favor verifique.');
      $("#sln").focus();
    }
    else {
      if ((causa === causaOriginal)&&(solucion === solucionOriginal)){
        alert('No hubo cambios en los datos de la alarma.\nPor favor verifique.');
      }
      else {
        seguir = true;
      }
    }
  }
  return seguir;
}
/********** fin validarEditarAlarma() **********/

/**
 * \brief Función que valida el form para editar un usuario.
 */
function validarEditarUsuario(){
  verificarSesion('', 's');
  var seguir = false;
  var nombre = $("#nombre").val();
  var apellido = $("#apellido").val();
  var appUser = $("#appUser").val();
  var tamPaginaUser = parseInt($("#tamPaginaUser").val(), 10);
  var limiteSelectsUser = parseInt($("#limiteSelectsUser").val(), 10);
  var observaciones = $("#observaciones").val();
  
  var nombreOriginal = $("#nombreOriginal").val();
  var apellidoOriginal = $("#apellidoOriginal").val();
  var appUserOriginal = $("#appUserOriginal").val();
  var tamPaginaOriginal = parseInt($("#tamPaginaOriginal").val(), 10);
  var limiteSelectsOriginal = parseInt($("#limiteSelectsOriginal").val(), 10);
  var observacionesOriginal = $("#observacionesOriginal").val();
  //alert('nombre: '+nombre+' --- Original: '+nombreOriginal+'\napellido: '+apellido+' --- Original: '+apellidoOriginal+'\nappUser: '+appUser+' --- Original: '+appUserOriginal+'\ntamPagina: '+tamPaginaUser+' --- Original: '+tamPaginaOriginal+'\nselects: '+limiteSelectsUser+' --- Original: '+limiteSelectsOriginal+'\nobservaciones: '+observaciones+' --- Original: '+observacionesOriginal);
  if (nombre === ''){
    alert('El nombre NO puede quedar vacío.\nPor favor verifique.');
    $("#nombre").val(nombreOriginal);
    $("#nombre").focus();
  }
  else {
    if (apellido === ''){
      alert('El apellido NO puede quedar vacío.\nPor favor verifique.');
      $("#apellido").val(apellidoOriginal);
      $("#apellido").focus();
    }
    else {
      if (appUser === ''){
        alert('El nombre de usuario para la app NO puede quedar vacío.\nPor favor verifique.');
        $("#appUser").val(appUserOriginal);
        $("#appUser").focus();
      }
      else {
        if ((nombre === nombreOriginal)&&(apellido === apellidoOriginal)&&(tamPaginaUser === tamPaginaOriginal)&&(limiteSelectsUser === limiteSelectsOriginal)&&(appUser === appUserOriginal)&&(observaciones === observacionesOriginal)){
          alert('No hubo cambios en los datos de la alarma.\nPor favor verifique.');
        }
        else {
          var validarTamaño = validarEntero(tamPaginaUser);
          if ((validarTamaño)&(tamPaginaUser > 0)&(tamPaginaUser <= maxTamPagina)||($("#tamPaginaUser").val() === 'No ingresado')){
            var validarSelect = validarEntero(limiteSelectsUser);
            if ((validarSelect)&&(limiteSelectsUser > 0)&(limiteSelectsUser <= maxLimiteSelects)||($("#limiteSelectsUser").val() === 'No ingresado')){
              var url = "data/getJSON.php";
              var query = 'select count(*) from usuarios where nombre="'+nombre+'" and apellido="'+apellido+'" and appUser="'+appUser+'"';
              var log = "NO";
              $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
                var resultado = parseInt(request["rows"], 10);
                if ((resultado > 0)&&(nombre !== nombreOriginal)&&(apellido !== apellidoOriginal)&&(appUser !== appUserOriginal)) {
                  alert('Ya existe un usuario con esos datos. Por favor verifique.');
                  $("#nombre").val(nombreOriginal);
                  $("#apellido").val(apellidoOriginal);
                  $("#appUser").val(appUserOriginal);
                  $("#nombre").focus();
                }
                else {
                  $("#frmEditarUsuario").submit();
                }
              });
            }
            else {
              alert('El tamaño para los selects debe ser un entero positivo y menor a '+maxLimiteSelects);
              $("#limiteSelectsUser").val(limiteSelectsOriginal);
              $("#limiteSelectsUser").focus();
            }
          }
          else {
            alert('El tamaño de página debe ser un entero positivo y menor a '+maxTamPagina);
            $("#tamPaginaUser").val(tamPaginaOriginal);
            $("#tamPaginaUser").focus();
          }
        }
      }   
    }
  }
  return seguir;
}
/********** fin validarEditarUsuario() **********/

/**
 * \brief Función que valida el form para editar un nodo.
 */
function validarEditarNodo(){
  verificarSesion('', 's');
  var seguir = false;
  var nombre = $("#nombre").val();
  var localidad = $("#localidad").val();
  var ip = $("#ip").val();
  var tipo = $("#tipoNodo").val();
  var areaMetro = $("#areaMetro").val();
  var observaciones = $("#observaciones").val();
  
  var nombreOriginal = $("#nombreOriginal").val();
  var localidadOriginal = $("#localidadOriginal").val();
  var ipOriginal = $("#ipOriginal").val();
  var tipoOriginal = $("#tipoOriginal").val();
  var areaMetroOriginal = $("#areaMetroOriginal").val();
  var observacionesOriginal = $("#observacionesOriginal").val();
  //alert('nombre: '+nombre+' --- Original: '+nombreOriginal+'\napellido: '+localidad+' --- Original: '+localidadOriginal+'\nip: '+ip+' --- Original: '+ipOriginal+'\ntipo: '+tipo+' --- Original: '+tipoOriginal+'\narea Metro: '+areaMetro+' --- Original: '+areaMetroOriginal+'\nobservaciones: '+observaciones+' --- Original: '+observacionesOriginal);
  if (nombre === ''){
    alert('El nombre NO puede quedar vacío.\nPor favor verifique.');
    $("#nombre").val(nombreOriginal);
    $("#nombre").focus();
  }
  else {
    if (localidad === ''){
      alert('La localidad NO puede quedar vacía.\nPor favor verifique.');
      $("#localidad").val(localidadOriginal);
      $("#localidad").focus();
    }
    else {
      if (tipo === ''){
        alert('El tipo de dispositivo NO puede quedar vacío.\nPor favor verifique.');
        $("#tipoNodo").val(tipoOriginal);
        $("#tipoNodo").focus();
      }
      else {
        if ((areaMetro !== 'SI')&&(areaMetro !== 'si')&&(areaMetro !== 'Si')&&(areaMetro !== 'sI')&&(areaMetro !== 'NO')&&(areaMetro !== 'no')&&(areaMetro !== 'No')&&(areaMetro !== 'nO')&&(areaMetro !== "1")&&(areaMetro !== "0")&&(areaMetro !== 1)&&(areaMetro !== 0)){
          alert('Área metro debe ser sólo SI o NO. \Por favor verifique.');
          $("#areaMetro").val(areaMetroOriginal);
          $("#areaMetro").focus();
        }
        else {
          if ((nombre === nombreOriginal)&&(localidad === localidadOriginal)&&(tipo === tipoOriginal)&&(ip === ipOriginal)&&(areaMetro === areaMetroOriginal)&&(observaciones === observacionesOriginal)){
            alert('No hubo cambios en los datos del nodo.\nPor favor verifique.');
          }
          else {
            var url = "data/getJSON.php";
            var query = 'select count(*) from nodos where nombre="'+nombre+'" and localidad="'+localidad+'" and tipo="'+tipo+'"';
            var log = "NO";
            $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
              var resultado = parseInt(request["rows"], 10);
              //alert(resultado+'\n'+nombre+'--'+nombreOriginal+'\n'+localidad+'--'+localidadOriginal+'\n'+tipo+'--'+tipoOriginal);
              if ((resultado > 0)) {
                alert('Ya existe un nodo con esos datos. Por favor verifique.');
                $("#nombre").val(nombreOriginal);
                $("#localidad").val(localidadOriginal);
                $("#tipoNodo").val(tipoOriginal);
                $("#nombre").focus();
              }
              else {
                $("#frmEditarNodo").submit();
              }
            });
          }
        }
      }   
    }
  }
  return seguir;
}
/********** fin validarEditarNodo() **********/

/**
  \brief Función que valida el rango de fechas pasado.
  @param rango {String} Cadena que indica que tipo de período se quiere. Esto es por mes, entre 2 fechas o todos.
  @param rango {Object} Objeto con la fecha de inicio del período en caso se quiera un rango entre fechas.
  @param rango {Object} Objeto con la fecha de fin del período en caso se quiera un rango entre fechas.
  @param rango {Object} Objeto con el mes de inicio del período en caso se quiera consultar por mes.
  @param rango {Object} Objeto con el año de inicio del período en caso se quiera consultar por mes.
*/
function validarFecha(rango, campoFecha, inicioObject, finObject, mesObject, añoObject){
  var validado = true;
  var inicio = inicioObject.val();
  var fin = finObject.val();
  var mes = mesObject.val();
  var año = añoObject.val();
  var hoy = new Date();
  var diaHoy = hoy.getDate();
  var mesHoy = hoy.getMonth()+1;
  if (diaHoy < 10) 
    {
    diaHoy = '0'+diaHoy;
  }                     
  if (mesHoy < 10) 
    {
    mesHoy = '0'+mesHoy;
  }
  var hoyFecha = hoy.getFullYear()+'-'+mesHoy+'-'+diaHoy;
  var hoyMostrar = diaHoy+'/'+mesHoy+'/'+hoy.getFullYear();
  var hourTemp = hoy.getHours();
  var minTemp = hoy.getMinutes();
  var secTemp = hoy.getSeconds();
  if (hourTemp < 10) 
    {
    hourTemp = '0'+hourTemp;
  } 
  if (minTemp < 10) 
    {
    minTemp = '0'+minTemp;
  } 
  if (secTemp < 10) 
    {
    secTemp = '0'+secTemp;
  }
  
  var horaMostrar = hourTemp+':'+minTemp;
  var mensajeFecha = '';
  var rangoFecha = null;
  var d1 = '';
  var d2 = ''; 

  switch (rango) {
    case 'intervalo': ///Comienzo la validación de las fechas:  
                      if ((inicio === '') && (fin === '')) 
                        {
                        alert('Debe seleccionar al menos una de las dos fechas. Por favor verifique!.');
                        inicioObject.focus();
                        validado = false;
                        //return false;
                      }
                      else 
                        {
                        if (inicio === '') 
                          {
                          inicio = inicioObject.attr("min");
                        }
                        if ((fin === '') || (fin > hoyFecha))
                          {
                          fin = hoyFecha;
                        }

                        if (inicio>fin) 
                          {
                          alert('Error. La fecha inicial NO puede ser mayor que la fecha final. Por favor verifique.');
                          validado = false;
                          //return false;
                        }
                        else 
                          {
                          validado = true;  
                          if (inicio === fin){
                            var diaTemp = inicio.split('-');
                            var diaMostrar = diaTemp[2]+"/"+diaTemp[1]+"/"+diaTemp[0];
                            rangoFecha = "("+campoFecha+" ='"+inicio+"')";
                            if (campoFecha === 'dia'){
                              mensajeFecha = "del día: "+diaMostrar;
                            }
                            else {
                              mensajeFecha = "cargadas el día: "+diaMostrar;
                            }
                          }
                          else {
                            var inicioTemp = inicio.split('-');
                            var inicioMostrar = inicioTemp[2]+"/"+inicioTemp[1]+"/"+inicioTemp[0];
                            var finTemp = fin.split('-');
                            var finMostrar = finTemp[2]+"/"+finTemp[1]+"/"+finTemp[0];
                            rangoFecha = "("+campoFecha+" >='"+inicio+"') and ("+campoFecha+" <='"+fin+"')";
                            if (campoFecha === 'dia'){
                              mensajeFecha = "entre las fechas: "+inicioMostrar+" y "+finMostrar;
                            }
                            else {
                              mensajeFecha = "cargadas entre las fechas: "+inicioMostrar+" y "+finMostrar;
                            }
                          }
                        }
                      } /// FIN validación de las fechas intervalo.
                      
                      d1 = inicio;
                      d2 = fin;
                      break;
    case 'mes': if (mes === 'todos') {
                  inicio = año+"-01-01";
                  fin = año+"-12-31";
                  fin = hoyFecha;
                  if (campoFecha === 'dia'){
                    mensajeFecha = "del año "+año;
                  }
                  else {
                    mensajeFecha = "cargadas en el año "+año;
                  }
                }
                else {
                  inicio = año+"-"+mes+"-01";
                  var añoFin = parseInt(año, 10);
                  var mesSiguiente = parseInt(mes, 10) + 1;
                  if (mesSiguiente === 13) {
                    mesSiguiente = 1;
                    añoFin = parseInt(año, 10) + 1;
                  }
                  if (mesSiguiente < 10) 
                    {
                    mesSiguiente = '0'+mesSiguiente;
                  }
                  fin = añoFin+"-"+mesSiguiente+"-01";
                  var mesMostrar = '';
                  switch (mes) {
                    case '01': mesMostrar = "Enero";
                               break;
                    case '02': mesMostrar = "Febrero";
                               break;
                    case '03': mesMostrar = "Marzo";
                               break;
                    case '04': mesMostrar = "Abril";
                               break;
                    case '05': mesMostrar = "Mayo";
                               break;
                    case '06': mesMostrar = "Junio";
                               break;
                    case '07': mesMostrar = "Julio";
                               break;
                    case '08': mesMostrar = "Agosto";
                               break;
                    case '09': mesMostrar = "Setiembre";
                               break;
                    case '10': mesMostrar = "Octubre";
                               break;
                    case '11': mesMostrar = "Noviembre";
                               break;
                    case '12': mesMostrar = "Diciembre";
                               break;
                    default: break;         
                  }
                  if (campoFecha === 'dia'){
                    mensajeFecha = "del mes de "+mesMostrar+" de "+año;
                  }
                  else {
                    mensajeFecha = "cargadas en el mes de "+mesMostrar+" de "+año;
                  }
                }
                validado = true;
                rangoFecha = "("+campoFecha+" >='"+inicio+"') and ("+campoFecha+" <'"+fin+"')";
                d1 = mes;
                d2 = año;
                break;
    case 'todos': break;
    default: break;
  }
  var datos = new Array();
  datos['validado'] = validado;
  datos['rango'] = rangoFecha;
  datos['inicio'] = inicio;
  datos['fin'] = fin;
  datos['mensaje'] = mensajeFecha;
  return datos;
}
/********** fin validarFecha(rango, inicioObject, finObject, mesObject, añoObject) **********/

/**
 * \brief Función que valida el form para cargar el archivo.
 */
function validarBusqueda(){
  verificarSesion('', 's');
  
  ///Recupero los parámetros de la consulta:
  var criterio = $('input:radio[name=criterio]:checked').val();
  var nodo = $("#nodo option:selected").val();
  var nodoNombre = $("#nodo option:selected").text();
  var archivo = $("#hint option:selected").val();
  var radioFecha = $('input:radio[name=criterioFecha]:checked').val();
  var inicio = $("#inicio");
  var fin = $("#fin");
  var mes = $("#mes");
  var año = $("#año");
  var origenFecha = $('input:radio[name=origenFecha]:checked').val();
  var tipoAlarma = $("#alarma").find('option:selected').val( );
  var usuario = $("#usuarios option:selected").val();
  var usuarioNombre = $("#usuarios option:selected").text();
  var nombreAlarma = $("#nameSearch").val();
  var conditionAlarma = $("#conditionSearch").val();
  var aidAlarma = $("#aidSearch").val();
  var estado = $("#estado option:selected").val();
  
  if (criterio === 'nodo'){
    if (nodo === 'nada'){
      alert('Se debe seleccionar un nodo.\nPor favor verifique!.');
      $("#nodo").focus();
      return false;
    }
  }
  else {
    if ((archivo === '')||(archivo === undefined)||(archivo === 'NADA')){
      alert('Se debe seleccionar un archivo.\nPor favor verifique!.');
      $("#hint").focus();
      return false;
    }
  }
  
  var mensaje = 'Alarmas';
  var validado = true;
  var rangoFecha = null;
  var campoFecha = '';
  
  if (origenFecha === 'alarma'){
    campoFecha = 'dia';
  }
  else {
    campoFecha = 'fechaCarga';
  }
  if (radioFecha !== 'todos'){
    var resultado= validarFecha(radioFecha, campoFecha, inicio, fin, mes, año);
    validado = resultado['validado'];
    rangoFecha = resultado['rango'];
    var inicioValidado = resultado['inicio'];
    var finValidado = resultado['fin'];
    var mensajeFecha = resultado['mensaje'];
  }
  
  if (validado){
    var query = "select * from alarmas ";
    var ordenar = ' order by dia desc, hora desc, idalarma';
    var param = '';
    if ((criterio === 'nodo')&&(nodo !== 'todos')){
      query += "where nodo=?";
      param += nodo;
      mensaje += " del nodo "+nodoNombre;
    }
    if (criterio === 'file') {
      query += "where archivo=?";
      param += archivo;
      mensaje += " cargadas del archivo "+archivo;
    }
    if (param === ''){
      param = "TODOS";
    }
    
    if (rangoFecha !== null){
      if ((criterio === 'nodo')&&(nodo === 'todos')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      //query += rangoFecha;
      if (radioFecha === 'mes'){
        query += "("+campoFecha+" >= ?) and ("+campoFecha+" < ?)";
        param += "&"+inicioValidado+"&"+finValidado;
      }
      else {
        if (inicioValidado === finValidado){
          query += campoFecha+"=?";
          param += "&"+inicioValidado+"&FIN";
        }
        else {
          query += "("+campoFecha+" >= ?) and ("+campoFecha+" <= ?)";
          param += "&"+inicioValidado+"&"+finValidado;
        }
      }
      if (mensajeFecha !== ''){
        mensaje += " "+mensajeFecha;
      }
    }
    else {
      param += "&INICIO&FIN";
    }
    
    if (tipoAlarma !== 'todas'){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)){
        query += "where ";
      }
      else {
        query += " and ";
      }
      //query += "tipoAlarma='"+tipoAlarma+"'";
      query += "tipoAlarma=?";
      param += "&"+tipoAlarma;
      mensaje += " del tipo "+tipoAlarma;
    }
    else {
      //mensaje += " de todos los tipos";
      param += "&TIPO";
    }
    
    if (usuario !== 'todos'){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)&&(tipoAlarma === 'todas')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      //query += "usuario="+usuario;
      query += "usuario=?";
      param += "&"+usuario;
      mensaje += " del usuario "+usuarioNombre;
    }
    else {
      //mensaje += " de todos los usuarios";
      param += "&USUARIO";
    }
    
    if (nombreAlarma !== ''){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)&&(tipoAlarma === 'todas')&&(usuario === 'todos')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      query += "nombre like ?";
      param += "&%"+nombreAlarma+"%";
      mensaje += ' con el nombre "'+nombreAlarma.toUpperCase()+'"';
    }
    else {
      param += "&NOMBRE";
    }
    
    if (conditionAlarma !== ''){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)&&(tipoAlarma === 'todas')&&(usuario === 'todos')&&(nombreAlarma === '')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      query += "tipoCondicion like ?";
      param += "&%"+conditionAlarma+"%";
      mensaje += ' con la condición "'+conditionAlarma.toUpperCase()+'"';
    }
    else {
      param += "&CONDITION";
    }
    
    if (aidAlarma !== ''){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)&&(tipoAlarma === 'todas')&&(usuario === 'todos')&&(nombreAlarma === '')&&(conditionAlarma === '')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      query += "tipoAID like ?";
      param += "&%"+aidAlarma+"%";
      mensaje += ' con el AID "'+aidAlarma.toUpperCase()+'"';
    }
    else {
      param += "&AID";
    }
    
    if (estado !== 'Todos'){
      if ((criterio === 'nodo')&&(nodo === 'todos')&&(rangoFecha === null)&&(tipoAlarma === 'todas')&&(usuario === 'todos')&&(nombreAlarma === '')&&(conditionAlarma === '')&&(aidAlarma === '')){
        query += "where ";
      }
      else {
        query += " and ";
      }
      query += "estado=?";
      param += "&"+estado;
      var mensajeTemp = mensaje.split('Alarmas ');
      var estadoMostrar = '';
      if (estado === 'Procesada'){
        estadoMostrar = 'procesadas';
      }
      else {
        estadoMostrar = 'sin procesar';
      }
      mensaje = "Alarmas "+estadoMostrar+' '+mensajeTemp[1];
    }
    else {
      param += "&ESTADO";
    }
    
    if (mensaje === 'Alarmas'){
      mensaje = "Todas las alarmas";
    }
    
    if ((criterio === 'nodo')&&(nodo === 'todos')){
      ordenar = ' order by nodo, dia desc, hora desc';
    }
    query += ordenar;
//alert(query+'\n'+param);
    $("#query").val(query);
    $("#param").val(param);
    $("#mensaje").val(mensaje);
    $("#frmConsultas").submit();
  }
  else {
    return false;  
  }
}
/********** fin validarBusqueda() **********/

/**
 * \brief Función que acomoda la altura de los textareas según el td padre.
 */
function resizeTextArea() {
  
  $("textarea").each(function(){
    $(this).height($(this).closest("td").height()-4);
  });
  alert('antes');
  $("#tblResultado").DataTable();
  alert('en medio');
  $('.dataTables_length').addClass('bs-select');
  alert('despúes');
}
/********** fin resizeTextArea() **********/

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
          var url = "data/updateJSON.php";
          var query = 'update usuarios set appPwd=sha1("'+pw1+'") ';
          /*
          if (user !== ''){
            query += ', user="'+user+'" ';
          }
          */
          query += 'where idusuario='+iduser;
          var log = "NO";

          $.getJSON(url, {query: ""+query+"", log: log}).done(function(resultado) {
            //var resultado = request["resultado"];
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

/**
 * \brief Función que primero valida la info ingresada, y de ser válida, hace la actualización de los parámetros del usuario.
 */
function actualizarParametros()  {
    verificarSesion('', 's');
    
    ///Recupero parámetros pasados por el usuario:
    var pageSize = $("#pageSize").val();
    var limiteSelects = $("#tamSelects").val();
    
    var limiteMaximoPagina = 1000;
    var limiteMaximoSelects = 50;
    
    ///Valido que sean válidos:
    var validarPage = validarEntero(pageSize);
    var seguir = true;
    if ((pageSize <= 0) || (pageSize > limiteMaximoPagina) || (pageSize === "null") || (!validarPage)){
      alert('El tamaño de la página DEBE ser un entero entre 1 y '+limiteMaximoPagina+'.\nPor favor verifique.');
      seguir = false;
      $("#pageSize").focus();
    }
    else {
          var validarLimiteSelects = validarEntero(limiteSelects);
          if ((limiteSelects <= 0) || (limiteSelects > limiteMaximoSelects) || (limiteSelects === "null") || (!validarLimiteSelects)){
            alert('El tamaño máximo para los selects DEBE ser un entero entre 1 y '+limiteMaximoSelects+'.\nPor favor verifique.');
            seguir = false;
            $("#tamSelects").focus();
          } 
    }///***************** FIN validación **************

    if (seguir) {
      var url = "data/updateParametros.php";
      var log = "NO";
      
      pageSize = parseInt(pageSize, 10);
      limiteSelects = parseInt(limiteSelects, 10);
      var paginaVieja = parseInt($("#tamPagina").val(), 10);
      var limiteViejoSelects = parseInt($("#limiteSelects").val(), 10);
      
      var cambioPagina = true;
      var cambioSelects = true;
        
      if (paginaVieja === pageSize){
        cambioPagina = false;
        pageSize = -1;
      }
      
      if (limiteViejoSelects === limiteSelects){
        cambioSelects = false;
        limiteSelects = -1;
      }
            
      //alert('Valores a cambiar:\nPagina: '+pageSize+'\nHistorial General: '+limiteHistorialGeneral+'\nHistorial Producto: '+limiteHistorialProducto);
      
      if (!cambioPagina && !cambioSelects){
        alert('No se cambiaron los parámetros dado que todos eran iguales.');
        $("#modalParametros").modal("hide");
      }
      else {
        $.getJSON(url, {tamPagina: ""+pageSize+"", tamSelects: ""+limiteSelects+"", log: log}).done(function(request) {
          //alert(request.resultadoDB);
          if (request.resultadoDB === "OK"){
            //alert('Los parametros se actualizaron correctamente en la base de datos!');
            if (cambioPagina && cambioSelects){
              alert('Todos los parámetros se cambiaron con éxito:\n\nNUEVOS PARÁMETROS:\n---------------------------\nTamaño de página: '+pageSize+'\nTamaño de Selects: '+limiteSelects+'\n---------------------------');
            }
            else {
              if (!cambioPagina && !cambioSelects){
                alert('No se cambiaron los parámetros.');
              }
              else {
                var mostrar = '-------- NUEVOS PARÁMETROS: --------';
                if (cambioPagina){
                  mostrar += '\n# Tamaño de página: '+pageSize;
                }
                if (cambioSelects){
                  mostrar += '\n# Tamaño de Selects: '+limiteSelects;
                }
                mostrar += '\n--------------------------------------------';
                alert(mostrar);
              }
            }
          }
          else {
            alert('Hubo un problema al actualizar los datos en la base de datos.\nPor favor inténtelo nuevamente.');
          }
          $("#modalParametros").modal("hide");
          location.reload(true);
        });
      }///************ FIN ELSE TODOS IGUALES ****
    }///************ FIN IF SEGUIR ***************
  //}///************* FIN IF SESION ****************
}
/********** fin actualizarParametros() **********/

/**
 * \brief Función que ejecuta la actualización del registro pasada.
 */
function actualizarRegistro()  {
  verificarSesion('', 's');
      
}
/********** fin actualizarRegistro() **********/

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
  //$(this).css("height", "100%");
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
/// Comienzan las funciones para el form de CONSULTAS.
******************************************************************************************************************************
*/

///Disparar función al hacer click en el botón de BUSCAR del from de CONSULTAS.
///Esto hace que se llame a la función que valida la consulta y luego hace el submit.
$(document).on("click", "#buscar", function(){
  validarBusqueda();
});

///Disparar función al hacer click en alguno de los links con las PÁGINAS de los resultados.
///Básicamente arma la consulta para mostrar la pagina solicitada y llama a la función para ejecutarla.
$(document).on("click", ".paginate", function (){
  var page = parseInt($(this).attr('data'), 10);
  var id = $(this).attr("name");
  
  ///Vuelvo a definir una variable local tamPagina para actualizar el valor que ya tiene.
  ///Esto es para que tome el último valor en caso de que se haya modificado desde el modal (que no cambia hasta recargar la página).
  var tamPagina = parseInt($("#tamPagina").val(), 10);
  var offset = (page-1)*tamPagina;
  $("#offset").val(offset);
  $("#page").val(page);
  var frame = '';
  var action = '';
  switch (id){
    case 'cargar':  frame = "#frmCargar";
                    action = "cargar.php";
                    break;
    case 'buscar':  frame = "#frmResultado";
                    action = "buscar.php";
                    break;
    case 'nodos': frame = "#frmNodos";
                  action = "nodos.php";
                  break;  
    case 'usuarios':  frame = "#frmUsuarios";
                      action = "usuarios.php";
                      break; 
    default: break;                
  }
  $(frame).attr("action", action);
  $(frame).removeAttr("target");
  $(frame).submit();
});
/********** fin on("click", ".paginate", function () **********/

///Disparar funcion al cambiar el elemento elegido en el select con las sugerencias para los archivos.
///Cambia el color de fondo para resaltarlo
$(document).on("change focusin", "#hint", function (){
  //verificarSesion('', 's');
  /// Selecciono radio button correspondiente:
  $(this).parent().prev().prev().children().prop("checked", true);
});
/********** fin on("change focusin", "#hint", function () **********/

///Disparar función al hacer doble click en alguna opción del select #hint.
///Esto hace que se llame a la función que valida la consulta y luego hace el submit.
$(document).on("dblclick", "select[name=hint] option", function() {
  validarBusqueda();
});
/********* fin on("dblclick", "select[name=hint] option", function() *********/

///Disparar función al cambiar la entidad elegida en el select NODO. 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "[name=nodo]", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});
/********** fin on("change", "[name=nodo]", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió algún mes quiere decir que la búsqueda es por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#mes", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});
/********** fin on("change", "#mes", function () **********/

///Disparar función al cambiar el año elegido como parámetro para la búsqueda.
///Si se eligió algún año quiere decir que la búsqueda por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#año", function (){
  $(this).parent().prev().prev().prev().prev().children().prop("checked", true);
});
/********** fin on("change", "#año", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de inicio quiere decir que la búsqueda es por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#inicio", function (){
  $(this).parent().prev().prev().children().prop("checked", true);
});
/********** fin on("change", "#inicio", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de fin quiere decir que la búsqueda es por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#fin", function (){
  $(this).parent().prev().prev().prev().prev().children().prop("checked", true);
});
/********** fin on("change", "#fin", function () **********/

/********** fin on("change", "#mes", function () **********/


/*****************************************************************************************************************************
/// Fin de las funciones para el form de CONSULTAS.
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
/// **************************************************** INICIO MODAL PARÁMETROS *********************************************
******************************************************************************************************************************
*/

///Disparar función al hacer click en el link que dice PARAMETROS debajo del usuario logueado
///Esto hace que se abra el modal para cambiar los parámetros.
$(document).on("click", "#param", function(){
  verificarSesion('', 's');
  $("#modalParametros").modal("show");
});
/********** fin on("click", "#param", function() **********/

///Disparar función al abrirse el modal para cambiar los parámetros.
///Lo único que hace es limpiar el form para poder ingresar los nuevos datos.
$(document).on("shown.bs.modal", "#modalParametros", function() {
  $("#pageSize").val($("#tamPagina").val());
  $("#tamSelects").val($("#limiteSelects").val());
  $("#pageSize").attr("autofocus", true);
  $("#pageSize").focus();
});
/********** fin on("shown.bs.modal", "#modalParametros", function() **********/

///Disparar función al hacer click en el botón de ACTUALIZAR que está en el MODAL.
///Llama a la función que se encarga de actualizar los parámetros.
$(document).on("click", "#btnParam", function(){
  actualizarParametros();
});
/********** fin on("click", "#btnParam", function() **********/

///Disparar función al hacer ENTER estando en el elemento pageSize del MODAL.
///Esto hace que se pase el foco al siguiente input del MODAL (tamSelects) cosa de ahorrar tiempo.
$(document).on("keypress", "#pageSize", function(e) {
  if(e.which === 13) {
    $("#tamSelects").focus();
  }  
});
/********** fin on("keypress", "#pageSize", function(e) **********/

///Disparar función al hacer ENTER estando en el elemento tamSelects del MODAL.
///Esto hace que se llame a la función correspondiente (actualizarParametros()) cosa de ahorrar tiempo.
$(document).on("keypress", "#tamSelects", function(e) {
  if(e.which === 13) {
    actualizarParametros();
  }  
});
/********** fin on("keypress", "#tamSelects", function(e) **********/

/*****************************************************************************************************************************
/// **************************************************** FIN MODAL PARÁMETROS ************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************** INICIO SUBIR ARCHIVO **************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer SUBMIT del form para cargar el archivo.
///La idea es validar que al menos se haya elegido un archivo.
$(document).on("click", "#btnCargar", function() {
  validarSubmitCargar();
});
/********** fin on("click", "#btnCargar", function() **********/

/*****************************************************************************************************************************
/// **************************************************** FIN SUBIR ARCHIVO ***************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************ INICIO MUESTRA ALARMAS **************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer SUBMIT del form para exportar los datos.
$(document).on("click", "[name=btnExportar]", function() {
  /// Por el momento comento la validación para casos que se quiera estén todas las alarmas procesadas:
//  $('td[name=estado]').each(function(){
//    if ($(this).text() === 'Sin procesar'){
//      var id = $(this).parent().find("td:first").html(); 
//      alert('Aún hay alarmas sin procesar:\nId: '+id);
//      return false;
//    }
//  });
  var id = $(this).attr("id");
  var elemento = '';
  switch (id){
    case 'btnExportarCargar': elemento = $("#frmCargar");
                              break;
    case 'btnExportarBuscar': elemento = $("#frmResultado");
                              break;
    case 'btnExportarUsuarios': elemento = $("#frmUsuarios"); 
                                break;
    case 'btnExportarNodos':  elemento = $("#frmNodos");
                              break;
    default: break;
  }
  elemento.attr("action", "exportar.php");
  elemento.attr("target", "_blank");
  elemento.submit();
});
/********** fin on("click", "#btnExportar", function() *********/

///Disparar función al hacer SUBMIT del form para actualizar los datos.
$(document).on("click", "[name=btnActualizar]", function() {
  /// Por el momento comento la validación para casos que se quiera estén todas las alarmas procesadas:
//  $('td[name=estado]').each(function(){
//    if ($(this).text() === 'Sin procesar'){
//      var id = $(this).parent().find("td:first").html(); 
//      alert('Aún hay alarmas sin procesar:\nId: '+id);
//      return false;
//    }
//  });
  var id = $(this).attr("id");
  var elemento = '';
  var query = "update alarmas set ";
  switch (id){
    case 'btnActualizarCargar': if (id === 'btnActualizarCargar') {
                                  elemento = $("#frmCargar");
                                }
                                else {
                                  elemento = $("#frmResultado");
                                }
                                
    case 'btnActualizarBuscar': var param = []; 
                                var registro;
                                $("input[type=checkbox]:checked").each(function(){
                                  var idal = $(this).val();
                                  var causa = $("textarea[idalarma="+idal+"][name='causa']").val();
                                  if (causa === undefined){
                                    causa = '';
                                  }
                                  var solucion = $("textarea[idalarma="+idal+"][name='solucion']").val();
                                  if (solucion === undefined){
                                    solucion = '';
                                  }
                                  registro = {idalarma: idal, causa: causa, solucion: solucion};
                                  param.push(registro);
                                });
                                var modificadas = param.length;
                                if (modificadas > 0){
                                  var causaTemp = param[0]['causa'];
                                  var solucionTemp = param[0]['solucion'];
                                  var sigo = true;
                                  param.forEach(function callback(item){
                                    if (sigo === true){
                                      if (item.causa !== ''){
                                        if ((causaTemp !== item.causa)&&(causaTemp !== '')){
                                          alert('Si se selecciona más de 1 alarma DEBEN tener la misma causa');
                                          sigo = false;
                                          return;
                                        }
                                        else {
                                          causaTemp = item.causa;
                                        } 
                                      }
                                      if (item.solucion !== ''){
                                        if ((solucionTemp !== item.solucion)&&(solucionTemp !== '')){
                                          alert('Si se selecciona más de 1 alarma DEBEN tener la misma solución');
                                          sigo = false;
                                          return;
                                        }
                                        else {
                                          solucionTemp = item.solucion;
                                        }  
                                      }
                                    }  
                                  });
                                  if (causaTemp === ''){
                                    alert('La Causa no fue ingresada.');
                                    sigo = false;
                                  }
                                  if ((solucionTemp === '')&&(sigo === true)){
                                    alert('La solución no fue ingresada.');
                                    sigo = false;
                                  }
                                  var query = '';
                                  if (sigo === true){
                                    query = "update alarmas set causa='"+causaTemp+"', solucion='"+solucionTemp+"', estado='Procesada' where (idalarma="+param[0].idalarma;
                                    param.forEach(function agregarAlarma(id){
                                      if (id.idalarma !== param[0].idalarma){
                                        query += ' or idalarma='+id.idalarma;
                                      }
                                    })
                                    query += ")";
                                    var url = "data/updateJSON.php";
                                    var log = "NO";
                                    $.getJSON(url, {query: ""+query+"", log: log}).done(function(resultado) {
                                      if (resultado === "OK") {
                                        alert('Los datos se modificaron correctamente!.');
                                        location.reload();
                                      }
                                      else {
                                        alert('Hubo un problema en la actualización. Por favor verifique.');
                                      }
                                    });
                                  }  
                                }
                                else {
                                  alert('No se han seleccionado alarmas.\nPor favor verifique.');
                                }
                                break;
    case 'btnActualizarUsuarios': elemento = $("#frmUsuarios"); 
                                  break;
    case 'btnActualizarNodos':  elemento = $("#frmNodos");
                                break;
    default: break;
  }
  //alert(elemento.attr('name'));
//  elemento.attr("action", "exportar.php");
//  elemento.attr("target", "_blank");
//  elemento.submit();
});
/********** fin on("click", "#btnActualizar", function() *********/

/*****************************************************************************************************************************
/// ************************************************** FIN MUESTRA ALARMAS ***************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************** INICIO EDITAR ALARMA **************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer SUBMIT del form para editar una alarma.
$(document).on("submit", "#frmEditarAlarma", function() {
  var continuar = validarEditarAlarma();
  if (continuar){
    return true;
  }
  else {
    return false;
  }
});
/********** fin on("click", "#btnEditarAlarma", function() *********/

/*****************************************************************************************************************************
/// **************************************************** FIN EDITAR ALARMA ***************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// ************************************************** INICIO EDITAR USUARIO *************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer CLICK del form para editar un usuario.
$(document).on("click", "#btnEditarUsuario", function(e) {
  e.preventDefault();
  validarEditarUsuario();
});
/********** fin on("click", "#btnEditarUsuario", function() *********/

/*****************************************************************************************************************************
/// **************************************************** FIN EDITAR USUARIO **************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// *************************************************** INICIO EDITAR NODO ***************************************************
******************************************************************************************************************************
*/

///Disparar función al hacer CLICK del form para editar un nodo.
$(document).on("click", "#btnEditarNodo", function(e) {
  e.preventDefault();
  validarEditarNodo();
});
/********** fin on("click", "#btnEditarNodo", function() *********/

/*****************************************************************************************************************************
/// ***************************************************** FIN EDITAR NODO ****************************************************
******************************************************************************************************************************
*/

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan el DESPLAZAMIENTO dentro de la página
******************************************************************************************************************************
*/

///Función que muestra/oculta las flechas para subir y bajar la página según el scroll:
$(window).scroll(function() {
//alert('en el scroll');
  if ($(this).scrollTop() > 80) {
    $('.arrow').fadeIn(50);
  } else {
    $('.arrow').fadeOut(400);
  }
});
/********** fin scroll(function() **********/

///Función que desplaza el foco hacia el final de la página:
$(document).on("click", ".arrow-bottom", function() {
  //event.preventDefault();
  $('html, body').animate({scrollTop:$(document).height()}, '1000');
        return false;
});
/********** fin on("click", ".arrow-bottom", function() **********/

///Función que desplaza el foco hacia el comienzo de la página:
$(document).on("click", ".arrow-top", function() {
  //event.preventDefault();
  $('html, body').animate({scrollTop:136}, '1000');
  return false;
});
/********** fin on("click", ".arrow-top", function() **********/

/*****************************************************************************************************************************
/// *************************************************** FIN DESPLAZAMIENTO ***************************************************
******************************************************************************************************************************
*/

}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());
/********** fin on("ready", todo()) **********/