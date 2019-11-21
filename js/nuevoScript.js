///Ahora las variables se toman de un único lugar que es el archivo config.php
///Las mismas, para que estén accesibles, se agregan a unos input "invisibles" que están en el HEAD (antes de incluir script.js para que estén disponibles).
var duracionSesion = parseInt($("#duracionSesion").val(), 10);
var limiteSeleccion = parseInt($("#limiteSeleccion").val(), 10);
var tamPagina = parseInt($("#tamPagina").val(), 10);
var limiteSelects = parseInt($("#limiteSelects").val(), 10);
var maxTamPagina = parseInt($("#maxTamPagina").val(), 10);
var maxLimiteSelects = parseInt($("#maxLimiteSelects").val(), 10);
var causaMal = 0;
var solucionMal = 0;

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
function validarEntero(numero) {
  if (isNaN(numero)){
    return false;
  } 
  else {
    if (numero % 1 == 0) {
      return true;
    } 
    else {
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
          mostrar = '<select name="hint" id="hint" class="hint custom-select-sm custom-select" size="15">';
        }
        else {
          mostrar = '<select name="hint" id="hint" class="hint custom-select-sm custom-select" multiple size="15">';
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
  var usuario = $("#nombreUsuario").val();
  if ((usuario === ' ')||(usuario === "null")||(usuario === '')){ 
    //alert('¡Debe ingresar el nombre de usuario!');
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('¡Debe ingresar el nombre de usuario!');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("user");
//    $("#nombreUsuario").focus();
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
    //alert('No se seleccionó archivo alguno.\nPor favor verifique!.');
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('No se seleccionó archivo alguno.<br>¡Por favor verifique!.');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("noFile");
    return false;
  }
  else {
    var nodo = $("option:selected", "#nodo").val();
    if (nodo === 'nada'){
      //alert('Hay que seleccionar un nodo.\nPor favor verifique!.');
      $("#tituloAdvertencia").html('ATENCIÓN');
      $("#mensajeAdvertencia").html('Hay que seleccionar un nodo.<br>¡Por favor verifique!.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("noNodo");
//      $("#nodo").focus();
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
      
      var posOcs = nombreNodoCorto.search("#");
      if (posOcs === -1){
        if (extension === 'csv'){
          //alert('Se seleccionó un archivo de OCS (.csv) para un PSS32 ('+nombreNodoCorto+').\n¡Por favor verifique!.');
          $("#tituloAdvertencia").html('ATENCIÓN');
          $("#mensajeAdvertencia").html('Se seleccionó un archivo de OCS (.csv) para un PSS32 ('+nombreNodoCorto+').<br>¡Por favor verifique!.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("noNodo");
//          $("#nodo").focus();
          return false;
        }
      }
      else {
        if (extension === 'xls'){
          //alert('Se seleccionó un archivo de PSS32 (.xls) para un OCS ('+nombreNodoCorto+').\n¡Por favor verifique!.');
          $("#tituloAdvertencia").html('ATENCIÓN');
          $("#mensajeAdvertencia").html('Se seleccionó un archivo de PSS32 (.xls) para un OCS ('+nombreNodoCorto+').<br>¡Por favor verifique!.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("noNodo");
//          $("#nodo").focus();
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
    //alert('La causa NO puede quedar vacía.\nPor favor verifique.');
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('La causa NO puede quedar vacía.<br>¡Por favor verifique!.');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("causaEditarAlarma");
//    $("#causa").focus();
  }
  else {
    if (solucion === ''){
//      alert('La solución NO puede quedar vacía.\nPor favor verifique.');
      $("#tituloAdvertencia").html('ATENCIÓN');
      $("#mensajeAdvertencia").html('La solución NO puede quedar vacía.<br>¡Por favor verifique!.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("solucionEditarAlarma");
//      $("#sln").focus();
    }
    else {
      if ((causa === causaOriginal)&&(solucion === solucionOriginal)){
//        alert('No hubo cambios en los datos de la alarma.\nPor favor verifique.');
        $("#tituloAviso").html('AVISO');
        $("#mensajeAviso").html('No hubo cambios en los datos.<br>¡Por favor verifique!.');
        $("#modalAviso").modal("show");
//        $("#caller").val("sinCambiosEditarAlarma"); 
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
//    alert('El nombre NO puede quedar vacío.\nPor favor verifique.');
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('El nombre NO puede quedar vacío.<br>¡Por favor verifique!.');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("nombreEditarUsuario");
//    $("#nombre").val(nombreOriginal);
//    $("#nombre").focus();
  }
  else {
    if (apellido === ''){
//      alert('El apellido NO puede quedar vacío.\nPor favor verifique.');
      $("#tituloAdvertencia").html('ATENCIÓN');
      $("#mensajeAdvertencia").html('El apellido NO puede quedar vacío.<br>¡Por favor verifique!.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("apellidoEditarUsuario");
//      $("#apellido").val(apellidoOriginal);
//      $("#apellido").focus();
    }
    else {
      if (appUser === ''){
//        alert('El nombre de usuario para la app NO puede quedar vacío.\nPor favor verifique.');
        $("#tituloAdvertencia").html('ATENCIÓN');
        $("#mensajeAdvertencia").html('El nombre de usuario para la app NO puede quedar vacío.<br>¡Por favor verifique!.');
        $("#modalAdvertencia").modal("show");
        $("#caller").val("appUserEditarUsuario");
//        $("#appUser").val(appUserOriginal);
//        $("#appUser").focus();
      }
      else {
        if ((nombre === nombreOriginal)&&(apellido === apellidoOriginal)&&(tamPaginaUser === tamPaginaOriginal)&&(limiteSelectsUser === limiteSelectsOriginal)&&(appUser === appUserOriginal)&&(observaciones === observacionesOriginal)){
//          alert('No hubo cambios en los datos de la alarma.\nPor favor verifique.');
          $("#tituloAviso").html('AVISO');
          $("#mensajeAviso").html('No hubo cambios en los datos.<br>¡Por favor verifique!.');
          $("#modalAviso").modal("show");
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
//                  alert('Ya existe un usuario con esos datos. Por favor verifique.');
                  $("#tituloAviso").html('AVISO');
                  $("#mensajeAviso").html('Ya existe un usuario con esos datos.<br>¡Por favor verifique!.');
                  $("#modalAviso").modal("show");
                  $("#caller").val("usuarioExiste");
//                  $("#nombre").val(nombreOriginal);
//                  $("#apellido").val(apellidoOriginal);
//                  $("#appUser").val(appUserOriginal);
//                  $("#nombre").focus();
                }
                else {
                  $("#frmEditarUsuario").submit();
                }
              });
            }
            else {
//              alert('El tamaño para los selects debe ser un entero positivo y menor a '+maxLimiteSelects);
                $("#tituloAdvertencia").html('ATENCIÓN');
                $("#mensajeAdvertencia").html('El tamaño para los selects debe ser un entero positivo y menor a '+maxLimiteSelects+'.<br>¡Por favor verifique!.');
                $("#modalAdvertencia").modal("show");
                $("#caller").val("limiteSelectsEditarUsuario");
//              $("#limiteSelectsUser").val(limiteSelectsOriginal);
//              $("#limiteSelectsUser").focus();
            }
          }
          else {
//            alert('El tamaño de página debe ser un entero positivo y menor a '+maxTamPagina);
              $("#tituloAdvertencia").html('ATENCIÓN');
              $("#mensajeAdvertencia").html('El tamaño de página debe ser un entero positivo y menor a '+maxTamPagina+'.<br>¡Por favor verifique!.');
              $("#modalAdvertencia").modal("show");
              $("#caller").val("tamPaginaEditarUsuario");
//            $("#tamPaginaUser").val(tamPaginaOriginal);
//            $("#tamPaginaUser").focus();
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
//    alert('El nombre NO puede quedar vacío.\nPor favor verifique.');
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('El nombre NO puede quedar vacío.<br>¡Por favor verifique!.');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("nombreEditarNodo");
//    $("#nombre").val(nombreOriginal);
//    $("#nombre").focus();
  }
  else {
    if (localidad === ''){
//      alert('La localidad NO puede quedar vacía.\nPor favor verifique.');
      $("#tituloAdvertencia").html('ATENCIÓN');
      $("#mensajeAdvertencia").html('La localidad NO puede quedar vacía.<br>¡Por favor verifique!.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("localidadEditarNodo");
//      $("#localidad").val(localidadOriginal);
//      $("#localidad").focus();
    }
    else {
      if (tipo === ''){
//        alert('El tipo de dispositivo NO puede quedar vacío.\nPor favor verifique.');
        $("#tituloAdvertencia").html('ATENCIÓN');
        $("#mensajeAdvertencia").html('El tipo de dispositivo NO puede quedar vacío.<br>¡Por favor verifique!.');
        $("#modalAdvertencia").modal("show");
        $("#caller").val("tipoEditarNodo");
//        $("#tipoNodo").val(tipoOriginal);
//        $("#tipoNodo").focus();
      }
      else {
        if ((areaMetro !== 'SI')&&(areaMetro !== 'si')&&(areaMetro !== 'Si')&&(areaMetro !== 'sI')&&(areaMetro !== 'NO')&&(areaMetro !== 'no')&&(areaMetro !== 'No')&&(areaMetro !== 'nO')&&(areaMetro !== "1")&&(areaMetro !== "0")&&(areaMetro !== 1)&&(areaMetro !== 0)){
//          alert('Área metro debe ser sólo SI o NO. \Por favor verifique.');
          $("#tituloAdvertencia").html('ATENCIÓN');
          $("#mensajeAdvertencia").html('Área metro debe ser sólo SI o NO.<br>¡Por favor verifique!.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("areaMetroEditarNodo");
//          $("#areaMetro").val(areaMetroOriginal);
//          $("#areaMetro").focus();
        }
        else {
          if ((nombre === nombreOriginal)&&(localidad === localidadOriginal)&&(tipo === tipoOriginal)&&(ip === ipOriginal)&&(areaMetro === areaMetroOriginal)&&(observaciones === observacionesOriginal)){
//            alert('No hubo cambios en los datos del nodo.\nPor favor verifique.');
            $("#tituloAviso").html('AVISO');
            $("#mensajeAviso").html('No hubo cambios en los datos.<br>¡Por favor verifique!.');
            $("#modalAviso").modal("show");
          }
          else {
            var url = "data/getJSON.php";
            var query = 'select count(*) from nodos where nombre="'+nombre+'" and localidad="'+localidad+'" and tipo="'+tipo+'"';
            var log = "NO";
            $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
              var resultado = parseInt(request["rows"], 10);
              //alert(resultado+'\n'+nombre+'--'+nombreOriginal+'\n'+localidad+'--'+localidadOriginal+'\n'+tipo+'--'+tipoOriginal);
              if ((resultado > 0)) {
//                alert('Ya existe un nodo con esos datos. Por favor verifique.');
                $("#tituloAviso").html('AVISO');
                $("#mensajeAviso").html('Ya existe un nodo con esos datos.<br>¡Por favor verifique!.');
                $("#modalAviso").modal("show");
                $("#caller").val("nodoExiste");
//                $("#nombre").val(nombreOriginal);
//                $("#localidad").val(localidadOriginal);
//                $("#tipoNodo").val(tipoOriginal);
//                $("#nombre").focus();
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
//                        alert('Debe seleccionar al menos una de las dos fechas. Por favor verifique!.');
                        $("#tituloAdvertencia").html('ATENCIÓN');
                        $("#mensajeAdvertencia").html('Debe seleccionar al menos una de las dos fechas.<br>¡Por favor verifique!.');
                        $("#modalAdvertencia").modal("show");
                        $("#caller").val("fechaIntervalo");
//                        inicioObject.focus();
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
//                          alert('Error. La fecha inicial NO puede ser mayor que la fecha final. Por favor verifique.');
                          $("#tituloAdvertencia").html('ATENCIÓN');
                          $("#mensajeAdvertencia").html('La fecha inicial NO puede ser mayor que la fecha final.<br>¡Por favor verifique!.');
                          $("#modalAdvertencia").modal("show");
                          $("#caller").val("fechaInicioMayor");
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
    /// Se comenta la opción "nada" pues ya no está. Se pasó a TODOS por defecto:
//    if (nodo === 'nada'){
////      alert('Se debe seleccionar un nodo.\nPor favor verifique!.');
//      $("#tituloAdvertencia").html('ATENCIÓN');
//      $("#mensajeAdvertencia").html('Se debe seleccionar un nodo.<br>¡Por favor verifique!.');
//      $("#modalAdvertencia").modal("show");
//      $("#caller").val("nodoConsulta");
////      $("#nodo").focus();
//      return false;
//    }
  }
  else {
    if ((archivo === '')||(archivo === undefined)||(archivo === 'NADA')){
//      alert('Se debe seleccionar un archivo.\nPor favor verifique!.');
      $("#tituloAdvertencia").html('ATENCIÓN');
      $("#mensajeAdvertencia").html('Se debe seleccionar un archivo.<br>¡Por favor verifique!.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("archivoConsulta");
//      $("#fileSearch").focus();
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
        estadoMostrar = '"procesadas"';
      }
      else {
        estadoMostrar = '"sin procesar"';
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
    $(this).height($(this).closest("tr").height()-4);
  });
}
/********** fin resizeTextArea() **********/

/**
 * \brief Función que consulta a la BD los largos de los campos para poder validarlos.
 */
function consultarLargos(){
  var url = "data/getJSON.php";
  var query = "select column_name as campo, character_maximum_length as tam from information_schema.columns where table_name = 'alarmas' and data_type = 'varchar'";
  var log = "NO";
  $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
    var resultado = request["resultado"];
//    resultado.forEach(function mostrar(item, index){
//      alert(index+'---'+item['campo']+'+++'+item['tam']);
//    });
    return resultado;
  });
}
/********** fin consultarLargos() **********/

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
      //alert('La contraseña 1 NO puede estar vacía.\nPor favor verifique.');
      $("#tituloAdvertencia").text('ATENCIÓN');
      $("#mensajeAdvertencia").html('¡La contraseña 1 NO puede estar vacía!.<br>Por favor verifique.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("pwd1Vacio");
//      $("#pw1").focus();
    }
    else {
      if (pw2 === ''){
        //alert('La contraseña 2 NO puede estar vacía.\nPor favor verifique.');
        $("#tituloAdvertencia").text('ATENCIÓN');
        $("#mensajeAdvertencia").html('¡La contraseña 2 NO puede estar vacía!.<br>Por favor verifique.');
        $("#modalAdvertencia").modal("show");
        $("#caller").val("pwd2Vacio");
//        $("#pw2").focus();
      }
      else {
        if (pw1 !== pw2) {
          //alert('Las contraseñas ingresadas NO son iguales.\nPor favor verifique.');
          $("#tituloAdvertencia").text('ATENCIÓN');
          $("#mensajeAdvertencia").html('¡Las contraseñas ingresadas NO son iguales!.<br>Por favor verifique.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("pwdDiferentes");
//          $("#pw1").val('');
//          $("#pw2").val('');
//          $("#pw1").focus();
        }
        else {
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
              //alert('Los datos se modificaron correctamente!.');
              $("#tituloAviso").text('AVISO');
              $("#mensajeAviso").html('¡Los datos se modificaron correctamente!.');
              $("#modalAviso").modal("show");
              $("#modalPwd").modal("hide");
            }
            else {
              //alert('Hubo un problema en la actualización. Por favor verifique.');
              $("#tituloAviso").text('AVISO');
              $("#mensajeAviso").html('¡Hubo un problema en la actualización. Por favor verifique.');
              $("#modalAviso").modal("show");
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
      //alert('El tamaño de la página DEBE ser un entero entre 1 y '+limiteMaximoPagina+'.\nPor favor verifique.');
      $("#tituloAdvertencia").text('ATENCIÓN');
      $("#mensajeAdvertencia").html('El tamaño de la página DEBE ser un entero entre 1 y '+limiteMaximoPagina+'.<br>Por favor verifique.');
      $("#modalAdvertencia").modal("show");
      $("#caller").val("pageSize");
      seguir = false;
//      $("#pageSize").focus();
    }
    else {
          var validarLimiteSelects = validarEntero(limiteSelects);
          if ((limiteSelects <= 0) || (limiteSelects > limiteMaximoSelects) || (limiteSelects === "null") || (!validarLimiteSelects)){
            //alert('El tamaño máximo para los selects DEBE ser un entero entre 1 y '+limiteMaximoSelects+'.\nPor favor verifique.');
            $("#tituloAdvertencia").text('ATENCIÓN');
            $("#mensajeAdvertencia").html('El tamaño máximo para los selects DEBE ser un entero entre 1 y '+limiteMaximoSelects+'.<br>Por favor verifique.');
            $("#modalAdvertencia").modal("show");
            $("#caller").val("selectSize");
            seguir = false;
//            $("#tamSelects").focus();
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
//        alert('No se cambiaron los parámetros dado que todos eran iguales.');
        $("#tituloAviso").text('AVISO');
        $("#mensajeAviso").html('No se cambiaron los parámetros dado que todos eran iguales.<br>¡Por favor verifique!.');
        $("#modalAviso").modal("show");
        $("#caller").val("parametrosIguales");
//        $("#modalParametros").modal("hide");
      }
      else {
        $.getJSON(url, {tamPagina: ""+pageSize+"", tamSelects: ""+limiteSelects+"", log: log}).done(function(request) {
          //alert(request.resultadoDB);
          if (request.resultadoDB === "OK"){
            //alert('Los parametros se actualizaron correctamente en la base de datos!');
            if (cambioPagina && cambioSelects){
//              alert('Todos los parámetros se cambiaron con éxito:\n\nNUEVOS PARÁMETROS:\n---------------------------\nTamaño de página: '+pageSize+'\nTamaño de Selects: '+limiteSelects+'\n---------------------------');
              $("#tituloSuccess").text('ÉXITO');
              $("#mensajeSuccess").html('NUEVOS PARÁMETROS:<br>---------------------------<br>Tamaño de página: '+pageSize+'<br>Tamaño de Selects: '+limiteSelects+'<br>---------------------------');
              $("#modalParametros").modal("hide");
              $("#modalSuccess").modal("show");
              $("#caller").val("todosParametrosBien");
            }
            else {
              if (!cambioPagina && !cambioSelects){
//                alert('No se cambiaron los parámetros.');
                $("#tituloAviso").text('AVISO');
                $("#mensajeAviso").html('No se cambiaron los parámetros.<br>¡Por favor verifique!.');
                $("#modalAviso").modal("show");
                $("#caller").val("parametrosIguales");
              }
              else {
                var mostrar = 'NUEVOS PARÁMETROS:<br>-----------------------------';
                if (cambioPagina){
                  mostrar += '<br># Tamaño de página: '+pageSize;
                }
                if (cambioSelects){
                  mostrar += '<br># Tamaño de Selects: '+limiteSelects;
                }
                mostrar += '<br>-----------------------------';
//                alert(mostrar);
                $("#tituloSuccess").text('ÉXITO');
                $("#mensajeSuccess").html(mostrar);
                $("#modalParametros").modal("hide");
                $("#modalSuccess").modal("show");
                $("#caller").val("parametrosBien");
              }
            }
          }
          else {
//            alert('Hubo un problema al actualizar los datos en la base de datos.\nPor favor inténtelo nuevamente.');
            $("#tituloAviso").text('AVISO');
            $("#mensajeAviso").html('Hubo un problema al actualizar los datos en la base de datos.<br>¡Por favor verifique!.');
            $("#modalAviso").modal("show");
          }
//          $("#modalParametros").modal("hide");
//          location.reload(true);
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

///Disparar funcion cuando algún elemento de la clase agrandar reciba el foco.
///Se usa para resaltar el elemento seleccionado.
$(document).on("focus", ".agrandar", function (){
  $(this).css("font-size", "1.15em");
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

///Disparar función al hacer doble click en alguna parte de la fila (td).
///Esto hace que se cambie el estado del checkbox que selecciona la propia fila.
$(document).on("dblclick", "#tblCargar td, #tblResultado td", function() {
  var miCheckbox = $(this).closest("tr").find(":checkbox");
  var estado = miCheckbox.prop("checked");
  if (estado === true){
    miCheckbox.prop("checked", false);
  }
  else {
    miCheckbox.prop("checked", true);
  }
});
/********* fin on("dblclick", "#tblResultado td", function() *********/

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
  $(this).closest("tr").find(":radio").prop("checked", true);
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
  //$(this).parent().prev().prev().children().prop("checked", true);
  $(this).closest("tr").find(":radio").prop("checked", true);
});
/********** fin on("change", "[name=nodo]", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió algún mes quiere decir que la búsqueda es por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#mes", function (){
 // $(this).parent().prev().prev().children().prop("checked", true);
  $(this).closest("tr").find(":radio").prop("checked", true);
});
/********** fin on("change", "#mes", function () **********/

///Disparar función al cambiar el año elegido como parámetro para la búsqueda.
///Si se eligió algún año quiere decir que la búsqueda por mes/año 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#año", function (){
  //$(this).parent().prev().prev().prev().prev().children().prop("checked", true);
  $(this).closest("tr").find(":radio").prop("checked", true);
});
/********** fin on("change", "#año", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de inicio quiere decir que la búsqueda es por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#inicio", function (){
  $(this).closest("tr").find(":radio").prop("checked", true);
});
/********** fin on("change", "#inicio", function () **********/

///Disparar función al cambiar el mes elegido como parámetro para la búsqueda.
///Si se eligió alguna fecha de fin quiere decir que la búsqueda es por rango (inicio/fin) 
///Lo que hace es seleccionar automáticamente el radio button correspondiente.
$(document).on("change", "#fin", function (){
  //$(this).parent().prev().prev().prev().prev().children().prop("checked", true);
  $(this).closest("tr").find(":radio").prop("checked", true);
});
/********** fin on("change", "#fin", function () **********/

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
       
$(document).on('shown.bs.modal', "#modalAdvertencia", function () {
  $(this).find('button').focus();
});       
       
///Disparar función al cerrarse el modal con mensajes de ADVERTENCIA
///Detecta quien llamó y actúa en consecuencia
$(document).on("hidden.bs.modal", "#modalAdvertencia", function() {
  var caller = $("#caller").val();
  switch (caller){
    case "user":  $("#nombreUsuario").focus();
                  break;
    case "pwd1Vacio": $("#pw1").focus();
                      break;
    case "pwd2Vacio": $("#pw2").focus();
                      break;
    case "pwdDiferentes": $("#pw1").val('');
                          $("#pw2").val('');
                          $("#pw1").focus();   
                          break;
    case "pageSize":  $("#pageSize").focus();
                      break;
    case "selectSize":  $("#tamSelects").focus();
                        break;  
    case "noNodo":$("#nodo").focus();
                  break;
    case "causaEditarAlarma": $("#causa").focus();
                              break;
    case "solucionEditarAlarma":  $("#sln").focus();
                                  break;
    case "nombreEditarUsuario": $("#nombre").val($("#nombreOriginal").val());
                                $("#nombre").focus();
                                break;
    case "apellidoEditarUsuario": $("#apellido").val($("#apellidoOriginal").val());
                                $("#apellido").focus();
                                break;   
    case "appUserEditarUsuario":  $("#appUser").val($("#appUserOriginal").val());
                                  $("#appUser").focus();
                                  break; 
    case "tamPaginaEditarUsuario":  $("#tamPaginaUser").val($("#tamPaginaOriginal").val());
                                    $("#tamPaginaUser").focus();
                                    break;
    case "limiteSelectsEditarUsuario":  $("#limiteSelectsUser").val($("#limiteSelectsOriginal").val());
                                        $("#limiteSelectsUser").focus();
                                        break;  
    case "nombreEditarNodo":$("#nombre").val($("#nombreOriginal").val());
                            $("#nombre").focus();  
                            break;
    case "localidadEditarNodo": $("#localidad").val($("#localidadOriginal").val());
                                $("#localidad").focus();
                                break;
    case "tipoEditarNodo": $("#tipoNodo").val($("#tipoOriginal").val());
                           $("#tipoNodo").focus();
                           break;
    case "areaMetroEditarNodo": $("#areaMetro").val($("#areaMetroOriginal").val());
                                $("#areaMetro").focus(); 
                                break;
    case "fechaIntervalo":  
    case "fechaInicioMayor":  $("#inicio").focus();
                              break;      
    case "nodoConsulta": $("#nodo").focus();
                         break;
    case "archivoConsulta": $("#fileSearch").focus();
                            break;  
    case "sinCausa":  var tr = $(":checked:first").closest('tr');
                      var textarea = $(tr).find('[name=causa]');
                      $(textarea).focus();
                      break;
    case "sinSolucion": var tr = $(":checked:first").closest('tr');
                        var textarea = $(tr).find('[name=solucion]');
                        $(textarea).focus();
                        break;
    case "badExtension":  $("#frmSubir")[0].reset();
                          $("#uploadedFile").focus();
                          break;
    case "nologueado":
    case "timeout":                       
    case "cookie":  window.location.assign("salir.php");
                    break;
    case "exportar": window.close();
                     break; 
    case "largoCausa":  var tr = $(":checked:first").closest('tr');
                        var textarea = $(tr).find('[name=causa]');
                        $(textarea).focus();
                        break;
    case "largoSolucion": var tr = $(":checked:first").closest('tr');
                          var textarea = $(tr).find('[name=solucion]');
                          $(textarea).focus();
                          break;
    case "largoCausaTodos": var tr = $("input[type=checkbox][value="+causaMal+"]").closest('tr');
                            var textarea = $(tr).find('[name=causa]');
                            $(textarea).focus();
                            break;
    case "largoSolucionTodos":  var tr = $("input[type=checkbox][value="+solucionMal+"]").closest('tr');
                                var textarea = $(tr).find('[name=solucion]');
                                $(textarea).focus();
                                break;                      
    default: break;                       
  }
});
/********** fin on("hidden.bs.modal", "#modalAdvertencia", function() **********/   

///Disparar función al cerrarse el modal con mensajes de AVISO
///Detecta quien llamó y actúa en consecuencia
$(document).on("hidden.bs.modal", "#modalAviso", function() {
  var caller = $("#caller").val();
  switch (caller){
    case "user":  $("#nombreUsuario").focus();
                  break;
    case "pwd1Vacio": $("#pw1").focus();
                      break;
    case "usuarioExiste": $("#nombre").val($("#nombreOriginal").val());
                          $("#apellido").val($("#apellidoOriginal").val());
                          $("#appUser").val($("#appUserOriginal").val());
                          $("#nombre").focus();        
                          break;
    case "nodoExiste":  $("#nombre").val($("#nombreOriginal").val());
                        $("#localidad").val($("#localidadOriginal").val());
                        $("#tipoNodo").val($("#tipoOriginal").val());
                        $("#nombre").focus();
                        break;
    case "parametrosIguales": $("#pageSize").focus();
                              break;
    default: break;                       
  }
});
/********** fin on("hidden.bs.modal", "#modalAviso", function() **********/ 

///Disparar función al cerrarse el modal con mensajes de SUCCESS
///Detecta quien llamó y actúa en consecuencia
$(document).on("hidden.bs.modal", "#modalSuccess", function() {
  var caller = $("#caller").val();
  switch (caller){
    case "actualizarBien": 
    case "parametrosBien":
    case "todosParametrosBien": //$("#modalParametros").modal("hide");
                                location.reload(true);
                                break;
    default: break;                       
  }
});
/********** fin on("hidden.bs.modal", "#modalSuccess", function() **********/ 

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

///Disparar función al CAMBIAR OPCIÓN en el form para cargar el archivo.
///La idea es mostrar solo las opciones compatibles según lo elegido.
$(document).on("change", "#uploadedFile", function() {
  var dir = $(this).val();
  var elegido = dir.split('\\');
  var sep = elegido[2].split('.');
  var extension = sep[1];
  if ((extension !== 'csv')&&(extension !== 'xls')){
    $("#tituloAdvertencia").html('ATENCIÓN');
    $("#mensajeAdvertencia").html('Extensión NO válida.<br>¡Por favor verifique!.');
    $("#modalAdvertencia").modal("show");
    $("#caller").val("badExtension");
  }
  else {
    $("#nodo option").each(function(){
      var nombreCorto = $(this).attr('nombreCorto');
      var sep1 = nombreCorto.indexOf('#');
      if (nombreCorto !== 'nada'){
        /// Si da -1 quiere decir que NO está en el string por lo cual es un PSS32
        if (sep1 === -1){
          //alert(nombreCorto+' - NO es OCS');
          if (extension === 'csv'){
            $(this).hide();
          }
          else {
            $(this).show();
          }
        }
        /// Si NO da -1 quiere decir que está en el string por lo cual es un OCS
        else {
          //alert(nombreCorto+' - es OCS');
          if (extension === 'xls'){
            $(this).hide();
          }
          else {
            $(this).show();
          }
        }
      }
    });
  }
});
/********** fin on("change", "#uploadedFile", function() **********/

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
                                var url = "data/getJSON.php";
                                var query = "select column_name as campo, character_maximum_length as tam from information_schema.columns where table_name = 'alarmas' and data_type = 'varchar'";
                                var log = "NO";
                                $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {
                                  var resultado = request["resultado"];
                                  var largosCampos = new Object();
                                  resultado.forEach(function mostrar(item, index){
                                    var campo = item['campo'];
                                    largosCampos[campo] = item['tam'];
                                  });
                                  var tamCausa = largosCampos['causa'];
                                  var tamSolucion = largosCampos['solucion'];
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
                                            $("#tituloAdvertencia").text('ATENCIÓN');
                                            $("#mensajeAdvertencia").html('Si se selecciona más de 1 alarma DEBEN tener la MISMA CAUSA.<br>Por favor verifique.');
                                            $("#modalAdvertencia").modal("show");
                                            $("#caller").val("largoCausa");
                                            sigo = false;
                                            return;
                                          }
                                          else {
                                            causaTemp = item.causa;
                                          } 
                                        }
                                        if (item.solucion !== ''){
                                          if ((solucionTemp !== item.solucion)&&(solucionTemp !== '')){
                                            $("#tituloAdvertencia").text('ATENCIÓN');
                                            $("#mensajeAdvertencia").html('Si se selecciona más de 1 alarma DEBEN tener la MISMA SOLUCIÓN.<br>Por favor verifique.');
                                            $("#modalAdvertencia").modal("show");
                                            $("#caller").val("mismaSolucion");
                                            sigo = false;
                                            return;
                                          }
                                          else {
                                            solucionTemp = item.solucion;
                                          }      
                                        }
                                      }  
                                    });

                                    var tamRealCausa = causaTemp.length;
                                    var tamRealSolucion = solucionTemp.length;
                                    if (causaTemp === ''){
                                      $("#tituloAdvertencia").text('ATENCIÓN');
                                      $("#mensajeAdvertencia").html('La CAUSA no fue ingresada.<br>Por favor verifique.');
                                      $("#modalAdvertencia").modal("show");
                                      $("#caller").val("sinCausa");
                                      sigo = false;
                                    }
                                    if ((tamRealCausa > tamCausa)&&(sigo === true)){
                                      $("#tituloAdvertencia").text('ATENCIÓN');
                                      $("#mensajeAdvertencia").html('La CAUSA tiene un largo de '+tamRealCausa+', mayor al permitido de '+tamCausa+'.<br>Por favor verifique.');
                                      $("#modalAdvertencia").modal("show");
                                      $("#caller").val("sinCausa");
                                      sigo = false;
                                    }
                                    if ((solucionTemp === '')&&(sigo === true)){
                                      $("#tituloAdvertencia").text('ATENCIÓN');
                                      $("#mensajeAdvertencia").html('La SOLUCIÓN no fue ingresada.<br>Por favor verifique.');
                                      $("#modalAdvertencia").modal("show");
                                      $("#caller").val("sinSolucion");
                                      sigo = false;
                                    }
                                    if ((tamRealSolucion > tamSolucion)&&(sigo === true)){
                                      $("#tituloAdvertencia").text('ATENCIÓN');
                                      $("#mensajeAdvertencia").html('La SOLUCIÓN tiene un largo de '+tamRealSolucion+', mayor al permitido de '+tamSolucion+'.<br>Por favor verifique.');
                                      $("#modalAdvertencia").modal("show");
                                      $("#caller").val("sinSolucion");
                                      sigo = false;
                                    }

                                    var query = '';
                                    if (sigo === true){
                                      query = "update alarmas set causa='"+causaTemp+"', solucion='"+solucionTemp+"', estado='Procesada' where (idalarma="+param[0].idalarma;
                                      param.forEach(function agregarAlarma(id){
                                        if (id.idalarma !== param[0].idalarma){
                                          query += ' or idalarma='+id.idalarma;
                                        }
                                      });
                                      query += ")";
                                      var url = "data/updateJSON.php";
                                      var log = "NO";
                                      $.getJSON(url, {query: ""+query+"", log: log}).done(function(resultado) {
                                        if (resultado === "OK") {
                                          $("#tituloSuccess").text('ÉXITO');
                                          $("#mensajeSuccess").html('¡Los datos se modificaron correctamente!.');
                                          $("#modalSuccess").modal("show");
                                          $("#caller").val("actualizarBien");
                                        }
                                        else {
                                          $("#tituloAviso").text('AVISO');
                                          $("#mensajeAviso").html('¡Hubo un problema en la actualización. Por favor verifique.');
                                          $("#modalAviso").modal("show");
                                        }
                                      });
                                    }  
                                  }
                                  else {
                                    $("#tituloAdvertencia").html('ATENCIÓN');
                                    $("#mensajeAdvertencia").html('No se seleccionaron alarmas.<br>¡Por favor verifique!.');
                                    $("#modalAdvertencia").modal("show");                                 
                                  }
                                });
                                break;
    case 'btnActualizarUsuarios': elemento = $("#frmUsuarios"); 
                                  break;
    case 'btnActualizarNodos':  elemento = $("#frmNodos");
                                break;
    default: break;
  }
//  elemento.attr("action", "exportar.php");
//  elemento.attr("target", "_blank");
//  elemento.submit();
});
/********** fin on("click", "#btnActualizar", function() *********/

///Disparar función al hacer SUBMIT del form para actualizar TODOS los datos.
$(document).on("click", "[name=btnActualizarTodo]", function() {
  var query = "update alarmas set ";
  var param = []; 
  var registro;
     
  var url = "data/getJSON.php";
  var query = "select column_name as campo, character_maximum_length as tam from information_schema.columns where table_name = 'alarmas' and data_type = 'varchar'";
  var log = "NO";
  $.getJSON(url, {query: ""+query+"", log: log}).done(function(request) {   
    var resultado = request["resultado"];
    var largosCampos = new Object();
    resultado.forEach(function mostrar(item){
      var campo = item['campo'];
      largosCampos[campo] = item['tam'];
    });
    var tamCausa = largosCampos['causa'];
    var tamSolucion = largosCampos['solucion'];
    
    var sigoChequeando = true;
    var actualizar = true;
    
    $("input[type=checkbox]").each(function(){
      var idal = $(this).val();
      var causa = $("textarea[idalarma="+idal+"][name='causa']").val();
      var solucion = $("textarea[idalarma="+idal+"][name='solucion']").val();
      var tamRealCausa = causa.length;
      var tamRealSolucion = solucion.length;
      
      if (((tamRealCausa > 0)||(tamRealSolucion > 0))&&(actualizar === true)){
        //alert('idalarma: '+idal+'\nreal causa: '+tamRealCausa+'\nreal solucion: '+tamRealSolucion+'\n'+causa+'\n'+solucion);
        if (tamRealCausa > tamCausa){
          causaMal = idal;
          $("#tituloAdvertencia").text('ATENCIÓN');
          $("#mensajeAdvertencia").html('Una CAUSA tiene un largo de '+tamRealCausa+', mayor al permitido de '+tamCausa+'.<br>Por favor verifique.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("largoCausaTodos");
          actualizar = false;
          return;
        }

        if ((tamRealSolucion > tamSolucion)&&(actualizar === true)){
          solucionMal = idal;
          $("#tituloAdvertencia").text('ATENCIÓN');
          $("#mensajeAdvertencia").html('Una SOLUCIÓN tiene un largo de '+tamRealSolucion+', mayor al permitido de '+tamSolucion+'.<br>Por favor verifique.');
          $("#modalAdvertencia").modal("show");
          $("#caller").val("largoSolucionTodos");
          actualizar = false;
          return;
        }
        if (actualizar === true){
          registro = {idalarma: idal, causa: causa, solucion: solucion};
          param.push(registro);
        }
      }
    });
    
    var modificadas = param.length;
    
    var query = '';
    if (actualizar === true){
      if (modificadas > 0){
        query = "update alarmas set estado='Procesada', causa = CASE";
        param.forEach(function agregaridAlarma(id){
          query += ' when (idalarma='+id.idalarma+') then "'+id.causa+'"';  
        });
        query += " END, ";
        query += "solucion = CASE";
        param.forEach(function agregarCausa(id){
          query += ' when (idalarma='+id.idalarma+') then "'+id.solucion+'"';  
        });
        query += " END";
        query += " where idalarma in ("+param[0].idalarma;
        param.forEach(function agregarSln(id){
          if (id.idalarma !== param[0].idalarma){
            query += ', '+id.idalarma;  
          }
        });
        query += ')';
        //alert(query);
        var url = "data/updateJSON.php";
        var log = "NO";
        $.getJSON(url, {query: ""+query+"", log: log}).done(function(resultado) {
          if (resultado === "OK") {
            $("#tituloSuccess").text('ÉXITO');
            $("#mensajeSuccess").html('¡Los datos se AGREGARON/ACTUALIZARON correctamente!.');
            $("#modalSuccess").modal("show");
            $("#caller").val("actualizarBien");
          }
          else {
            $("#tituloAviso").text('AVISO');
            $("#mensajeAviso").html('¡Hubo un problema en la actualización. Por favor verifique.');
            $("#modalAviso").modal("show");
          }
        });
      } 
      else {
        $("#tituloAdvertencia").html('ATENCIÓN');
        $("#mensajeAdvertencia").html('No se ingresaron/actualizaron alarmas.<br>¡Por favor verifique!.');
        $("#modalAdvertencia").modal("show");  
      }
    }
  });   
});
/********** fin on("click", "#btnActualizarTodo", function() *********/

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
  $('html, body').animate({scrollTop:86}, '1000');
  return false;
});
/********** fin on("click", ".arrow-top", function() **********/

/*****************************************************************************************************************************
/// *************************************************** FIN DESPLAZAMIENTO ***************************************************
******************************************************************************************************************************
*/
//}


/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).ready(function(){
//  $('#tblResultado').DataTable({
//    "paging": false,
//    "ordering": false,
//    "sorting": false,
//    "searching": false,
//    "scrollX": true
//  });
//  $('.dataTables_length').addClass('bs-select');
//$('.mdb-select').materialSelect();
});
/********** fin on("ready", todo()) **********/

