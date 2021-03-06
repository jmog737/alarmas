<?php

header('content-type:text/css');
 
/******** STYLES *********/
//$colorFondo = '#61122f';
$colorFondo = '#263c25';
/****** FIN STYLES *******/

/******* STYLES1: ********/ 
//$colorFondo = '#d29238';
/****** FIN STYLES1 ******/

/******** STYLES2: *******/ 
//$colorFondo = '#cc514d';
/****** FIN STYLES2 ******/

echo <<<FINCSS
/**
******************************************************
*  @file styles.css
*  @brief Archivo con todas las hojas de estilo usadas en el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Marzo 2017
*
*******************************************************/
* {
  margin: 0px;
  padding: 0px;
  box-sizing: border-box;
  font-family: serif, helvetica, arial;
  text-align: inherit;
}
html {
  min-height: 100%;
  width: 100%;
  position: relative;
  font-size: 16px;
  color: #fff;
  background-color: $colorFondo;
  font-family: sans-serif;
  padding: 0px;
  margin: 0px;
}

body {
  width: 100%;
  font-size: 16px;
  color: #fff;
  background-color: $colorFondo;
  font-family: sans-serif;
  padding: 0px;
  margin: 0px;
  margin-bottom: 110px;
  margin-top: 120px;
}

/****************************** HEADER **************************************/
header {
  background-color: #023184;
  border-radius: 0;
  border: 0;
  width: 100%;
  /*position: absolute;*/
  top: 0;
  margin: 0;
  height: 120px;
  position: fixed;
  z-index: 1;
}

#header-nav {
  padding: 0px;
}
.container {
  width: 100%;
}
.navbar-header {
  
}
.navbar-header button.navbar-toggle, .navbar-header .icon-bar {
  border: 1px solid #ccce8b;
}
.navbar-header button.navbar-toggle {
  clear: both;
  margin-top: -30px;
}
#logo-img {
  background: url('../images/logo.png') no-repeat;
  width: 260px;
  height: 100px;
  margin: 10px 10px 10px 0;
}
.navbar-brand {
  padding-top: 25px;
}
.navbar-brand h1 { /* Page section name */
  font-family: 'Lora', serif;
  color: #ccce8b;
  font-size: 2.1em;
  text-transform: uppercase;
  font-weight: bold;
  text-shadow: 1px 1px 1px #222;
  margin-top: 0;
  margin-bottom: 0;
  line-height: .75;
}
.navbar-brand a:hover, .navbar-brand a:focus {
  text-decoration: none;
}
#drop{
  background-color: #023184;
}

#nav-list a {
  color: #ccce8b;
  background-color: #023184;
}
#nav-list a:hover {
  background: #009999;
}
#nav-list .menu a:hover {
    zoom: 1.5;
}
#nav-list a span {
  font-size: 1.8em;
}
/*************************** END HEADER *************************************/


/**************************** HOME PAGE *************************************/
.container .jumbotron {
  box-shadow: 0 0 50px #3F0C1F;
  border: 2px solid #3F0C1F;
  margin: 0px;
  width: 100%;
  height: 100%;
  /*min-height:100%;*/
}
h2 {
  color:#ffcc66;
  font-weight: bold;
  /*text-decoration: underline;*/
  margin: 20px;
  margin-top: 0px;
}
h3 h4{
  color:#fff;
}

.rango {
  font-style: italic;
}
input[type=checkbox] {
  zoom: 1.4;
}

.derecha {
  float: right;
}
.izquierda {
  float: left;
}

main {
/*  height: 72%;*/
  background-color: #07ff00;
  /*min-height: 515px !important;*/
}

#main-content {
  text-align: center;
  background-color: #ffff99;/* agregardo durante el desarrollo para diferenciar!!!*/
  background-color: $colorFondo;
  margin: auto;
  padding: 0px;
  padding-top: 20px;
}

#fila {
  background-color: $colorFondo;
  /*background-color: #99ffff;*/
  padding: 30px;
  padding-top: 0px;
  margin: 0px;
  height: 100%;
}
#volverActividad, #volverReferencia, #volverUsuario {
  color:#fff;
}
a.detailObject:focus, a.detailObject:hover {
  background-color:#ffff00;
  font-weight: bolder;
  font-size: 1.3em;
}

#selector {
  background-color: $colorFondo;
  /*background-color: #99ff99;/* agregardo durante el desarrollo para diferenciar!!!*/
  position: relative;
  padding: 0px;
  height: 100%;
}
#selector a:hover, a:focus {
  color: #080571;
  text-decoration: underline;
}
#content {
  background-color: $colorFondo;
  /*background-color: #ffcc99; agregardo durante el desarrollo para diferenciar!!!*/
  padding-left: 50px;
  position: relative;
  padding: 0px;
}

/****************************** FOOTER **************************************/
.panel-footer {
  padding-top: 2px;
  padding-bottom: 2px;
  border-top: 0;
  bottom: 0;
  width: 100%;
  background-color: #023184;
 position: absolute;
  /*position: fixed;*/
  height: 110px;
}
.panel-footer div.row {
  text-align: center;
}

#fechaActual{
  color: #ccce8b;
}
/**************************** END FOOTER ************************************/

/************************************ TABS ************************************/
#pills-tab {
  margin-bottom: 0px !important;
  margin-top: -20px !important;
}
#pills-tab li {
  background-color: #c5c5c5;
  padding-bottom: 0;
  margin-left: 1.3px;
  margin-right: 1.3px;
  margin-bottom: 0;
}
.tab-content {
  background-color: #523a43;
  background-color: #40383b;
  height: 100%;
  min-height: 580px !important;
}
hr {
  background-color: white;
  height: 5px;
  width: 50%;
  margin: auto;
}
.tab-pane {
  height: 100%;
}
/********************************* FIN TABS ***********************************/

/*********************************** POPOVERS *********************************/
.popover{
  max-width: 100%;
}
.popover-header{
  background-color: #c12929;
}
.popover-body{
  background-color: #cccccc;
}

/* Estilos para el popover con el historial general.
   El mismo fue "atado" al elemento gralHistory cosa de poder diferenciarlo
*/
#historialGeneral {
  float: center;
}
#gralHistory{
  margin: auto;
}
#gralHistory .popover{
  max-width: 40%;
}
#gralHistory .popover-header {
  background-color: #5ca037;
  color: #ffffff;
  font-size: 1.2em;
  font-weight: bold;
  text-decoration: underline;
}
#gralHistory .popover-body {
  background-color: #f8ffcd;
  font-style: italic;
}
/************************************** FIN POPOVERS **************************/

/************************************* TABLAS *********************************/
.tituloTabla {
  border-radius: 25px 25px 0 0;
  text-align: center;
  font-weight: 800 !important;
}
.pieTabla {
  border-radius: 0 0 25px 25px;
}
.pieTablaIzquierdo {
  border-radius: 0 0 0 25px;
}
.pieTablaDerecho {
  border-radius: 0 0 25px 0;
}

.tituloTablaIzquierdo {
  border-radius: 25px 0 0 0;
}
.tituloTablaDerecho {
  border-radius: 0 25px 0 0;
}

.tabla1 {
  border: white 2px solid;
  background-color: #48403e;
  text-align: center;
  color: #000;
  border-collapse: separate;
  margin: 0 auto;
  max-width: 80%;
}
.tabla1 th {
  background-color: #0066cc;
  text-align: center;
  color: #ffff00;
  border: white 2px solid;
}
.tabla1 tr {
  border: white 2px solid;
}
.tabla1 td {
  border: white 2px solid;
}
.tabla1 td input text{
  width: 100%;
  text-align: center;
}

#ref table, #ref td, #ref th {
  padding: 0px;
  max-width: 70%;
}

.tabla2 {
  border: #c0c0c0 1px solid;
  background-color:#fbffe2;
  text-align: center;
  color: #000;
  border-collapse: separate;
  margin: auto;
  border-radius: 25px;
  max-width: 80% !important;
  padding: 0px;
  max-height: 200px;
  overflow-x: scroll !important;
}
.tabla2 caption {
  display: table-caption;
  text-align: center;
  caption-side: bottom;
  color: #ccce8b;
  font-size: 1.1em;
} 
.tabla2 th {
  background-color: #0066cc;
  background-color: #17a2b8;
  color: #ffff00;
  border: #c0c0c0 1px solid;
  margin: 0px;
  line-height:none;
  max-width: 100%;
  text-align: center;
  padding: 5px !important;
  font-weight: 700;
}
.tabla2 tr {
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  padding: 0px;
}
.tabla2 td {
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  padding: 1px;
  vertical-align: middle;
  /*font-size: 0.9em;*/
}
.tabla2 td input[type="text"] {
  line-height: normal;
  width: 100%;
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  text-align: center;
  padding: 0px;
}
.tabla2 td input[type="checkbox"]{
  text-align: center;
  vertical-align: middle;
  background-color:background;
}
.tabla2 td select {
  line-height: normal;
  width: 100%;
  max-width: 100%;
  border: #c0c0c0 1px solid;
  border-collapse: separate;
  text-align: center;
  padding: 0px;
}
.tabla2 textarea {
	overflow: hidden;
	box-sizing: border-box; 
  border: 0px;
  max-width: 120px;
}

.tabla2 .subTituloTabla1 {
  background-color: #28ce44;
  background-color: #88b38f;
  background-color:#77bd82;
  text-align: center;
/*  color: #071c40;*/
  color: #ffffff;
  font-weight: 800;
  /*font-size: 10pt;*/
}
.tabla2 .subTituloTabla2 {
  background-color:#9fadea;
  background-color: #f5d684;
  text-align: center;
  /*  color: #071c40;*/
  color: #ffffff;
  font-size: 10pt;
  font-weight: 800;
}

.tabla2 .enc {
  background-color:#07ff00;
  background-color: #0066cc;
  color: #ffff00;
  border: #c0c0c0 1px solid;
  margin: 0px;
  line-height:none;
  max-width: 100%;
  text-align: left;
  font-weight: bold;
}

.tabla2 input[type=radio]+label{
  display: inline-block;
  padding: 10px 20px;
  font-size: 12px;
  cursor: pointer;
  border-radius: 10px;
  margin: 0px;
  background-color: grey;
  font-weight:bolder;
  color:#ffffff;
}
.tabla2 input[type=radio].visible:checked+label{
  background-color: #17a2b8; /* info */
  background-color: #ffc107; /* warning */
  background-color: #28a745; /* success */
  background-color: #ff3547; /* danger */
  font-weight:bolder;
  color:#ffffff;
}

/***************************************************************************************************/
/* NUEVOS ESTILOS MDB */
div[name=table-content] {
  width: 99%;
  margin: auto;
  padding: 0px;
}
table.table td{
  padding: 0px;
}
table.table textArea{
  border: 0px;
  min-width: 100px;
  padding: 0px;
  margin: 0px;
}
table.dataTable thead .sorting:after,
table.dataTable thead .sorting:before,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_asc:before,
table.dataTable thead .sorting_asc_disabled:after,
table.dataTable thead .sorting_asc_disabled:before,
table.dataTable thead .sorting_desc:after,
table.dataTable thead .sorting_desc:before,
table.dataTable thead .sorting_desc_disabled:after,
table.dataTable thead .sorting_desc_disabled:before {
bottom: .8em;
}
/***************************************************************************************************/

/******************************* FIN TABLAS ***********************************/

/************************************ ALARMAS *********************************/
.alCritica {
  background-color:#f5c6cb;
}
.alMajor {
  background-color:#ffa929;
  background-color: #ffbb55;
}
.table-hover .alMajor:hover {
  background-color:#ff7609;
}
.alMinor {
  background-color:#ffff7c;
}
.alWarning {
  background-color:#75ecec;
}
.alNotAlarmed {
  background-color:#9FB685;
}
.alNotReported {
  background-color:#999999;
}
.procesada {
  background-color: #85ca5e !important;
}
.sinProcesar {
  background-color:#f7ff00 !important;
}

/****************************** FIN ALARMAS ***********************************/

.btn {
  /*padding: .3rem .75rem !important;*/
  border-radius: 25px 25px 25px 25px;
}

#snapshot {
  padding: 10px;
}
.encabezado {
  color: #ffcc00;
  color: #ccce8b;
  text-decoration: underline;
  font-weight: bolder;
}
.detailActivity {
  background-color: #ffff99;
}
.detailObject {
  /*background-color: #99ff99;*/
}
.detailUser {
  text-align: right;
}
.naranja {
  color: #ecb127;
}

.nomostrar {
  display: none;
}

.usuarioIndex {
  color: #007bff;
  background-color: #ffff00;
  font-weight: bold;
}

.fondoNaranja {
  background-color: #ecb127;
}

.fondoVerde {
  background-color: #276731;
}

.fondoRojo {
  background-color: #c12929;
}

.resaltarDiferencia {
  background-color:#ffff00;
  font-size: 1.2em;
  font-weight: bolder;
  color: red;
}

.resaltarStock {
  background-color: #38ff1d;
  font-size: 1.2em;
  font-weight: bolder;
  color: red;
}

.resaltarPlastico {
  background-color: #ff9999;
  font-size: 1.1em;
  font-weight: bolder;
}

.resaltarComentario {
  background-color: #d3d3d3;
  font-size: 1.1em;
  font-weight: bolder;
}

a.linkHistorialGeneral {
  color: blue;
}
a.linkHistorialProducto {
  color: black;
}
a.linkHistorialGeneral:hover, a.linkHistorialProducto:hover{
  background-color: #ffff00;
  font-size: 1.2em;
}

.transparente {
  background-color:transparent;
}

.fondoSelect {
  /*background-color:#ffffff;*/
  background-color: #ea843b;
  background-color: #ffd8bd;
}

.negrita {
  font-weight: bolder;
}
.negritaGrande {
  font-weight: bolder;
  font-size: 1.25em;
}
.italica {
  font-style: italic;
}
.resaltado {
  background-color:#ffff00;
  background-color: #a3a59d;
  background-color: #9bf79b;
  font-weight: bolder;
  font-size: 1.2em;
  color: red;
}
.resaltado1 {
  background-color:#07ff00;
  font-weight: bolder;
  font-size: 1.6em;
  color: #1400af;
}
.alarma1 {
  background-color:#ffff33;
  font-weight: bolder;
  font-size: 1.4em;
  font-style: italic;
  color: black;
}
.alarma2 {
  background-color:#c12929;
  font-weight: bolder;
  font-size: 1.4em;
  font-style: italic;
  color: white;
}
.centrado {
  text-align: center;
}
.hint {
  max-width: 100px;
  position: relative;
  min-width: 100px;
  overflow:auto;
}
#hint {
  max-width: 100%;
  position: relative;
  overflow-x: scroll;
  overflow-y: auto;
}

.comentHintResaltar {
  background-color:#ffff00;
  font-size: 2.2em;
  font-weight: bolder;
  color: red;
}
.comentHintStock{
  font-size: 2.2em;
  font-weight: bolder;
  color: red;
  background-color: #38ff1d;
}
.comentHintPlastico{
  font-size: 2.2em;
  font-weight: bolder;
  color: red;
  background-color: #ff9999;
}
.comentHint{
  font-size: 2.2em;
  font-weight: bolder;
  color: red;
  background-color: #d3d3d3;
}

.subtotal {
  background-color:#9db0f3;
  font-weight: bolder;
  font-size: 1.4em;
  text-align: right;
}
.totalConsumos {
  background-color:#4b5af3;
  font-weight: bolder;
  font-size: 1.4em;
  text-align: right;
}
.totalIngresos {
  background-color:#27de5d;
  font-weight: bolder;
  font-size: 1.4em;
  text-align: right;
}
.totalAjusteRetiros {
  background-color:#a25cf3;
  font-weight: bolder;
  font-size: 1.4em;
  text-align: right;
}
.totalAjusteIngresos {
  background-color:#ffc168;
  font-weight: bolder;
  font-size: 1.4em;
  text-align: right;
}

/******************************** MODAL **************************************/
/*.tituloModal {
  background-color: #023184;
}
.tblModal {
  color: #000;
}*/

#modalExportar .modal-header {
  background-color: #e44040;
  color: #000;
  font-size: 18px;
  text-align: center;
}
#modalExportar .modal-body {
  background-color: #d2d2d2;
  color: #000;
  font-size: 16px;
  text-align: center;
}
#modalExportar .modal-footer {
  background-color: #d2d2d2;
  color: #000;
  font-size: 14px;
  text-align: center;
}

#modalMovRepetido .modal-header {
  background-color: #e44040;
  background-color: #e00800;
  font-weight: bolder;
  color: #fff;
  font-size: 18px;
  text-align: center;
}
#modalMovRepetido .modal-body {
  background-color: #d2d2d2;
  background-color: #5c8260;
  color: #000;
  font-size: 16px;
}
#modalMovRepetido .modal-footer {
  background-color: #d2d2d2;
  color: #000;
  font-size: 14px;
  text-align: center;
}
#btnModalRepetido {
  /*background-color:#ffff33;*/
  font-weight: bold;
}
#btnModalRepetido:focus, #btnModalRepCerrar:focus{
  border-width: 3px;
  border-color: white;
  background-color:#9e41d0;
}
#modalMovRepetido input[type="text"]:disabled {
  background-color: #e8efb0;
}

#modalCbioFecha .modal-header {
  background-color: #1270e0;
  font-weight: bolder;
  color: #fff;
  font-size: 16px;
  text-align: center;
}
#modalCbioFecha .modal-body {
  background-color: #5c8260;
  color: #000;
  font-size: 1.16em;
}
#modalCbioFecha .modal-body table {
  margin: auto;
  padding: 10px;
}
#modalCbioFecha .modal-body table td, th{
  padding-right: 10px;
  padding-right: 10px;
}
#modalCbioFecha .modal-footer {
  background-color: #d2d2d2;
  color: #000;
  font-size: 14px;
  text-align: center;
}
#mdlFechaActual {
  background-color: #eeff88;  
}
#mdlFechaNueva {
  background-color: #ffb8d7;  
}

/**************************** FIN MODAL **************************************/

/************************* END HOME PAGE *************************************/

/********************************* PAGINAS ***********************************/
.pagination {
  /*height: 36px;*/
  margin: auto;
  width: 80%;
}
.pagination ul {
  border-radius: 3px 3px 3px 3px;
/*    box-shadow: 0 10px 10px rgba(255, 255, 255, 0.2);*/
/*    display: inline-block !important;*/
  margin: auto;
}
.pagination li {
  display: inline !important;
  margin: auto;
}
.pagination a {
  -moz-border-bottom-colors: none;
  -moz-border-image: none;
  -moz-border-left-colors: none;
  -moz-border-right-colors: none;
  -moz-border-top-colors: none;
  border-color: #DDDDDD;
  border-color: #f1643a;
  /*border-color: #fb4f4a;*/
  /*border-style: solid;*/
  /*border-width: 1px 1px 1px 1px;*/
  float: left;
  line-height: 34px;
  padding: 0 10px;
  text-decoration: none;
  background-color: #f1643a;
  margin: 0px 10px 0px 10px;
  color: #000;
}
.pagination a:hover {
  background-color: #f36464;
  border-color: #f36464;
  font-size: 1.5em;
  cursor: pointer;
  color: #ffffff;
}
.pagination a:active {
  color: #999999;
  cursor: default;
}
.pagination li:first-child a{
  border-left-width: 1px;
  border-radius: 30px 0 0 30px;
}
.pagination li:last-child a {
  border-radius: 0 30px 30px 0;
}
.pageActive {
  /*background-color: #f36464;*/
  background-color: #757582 !important;
}

.inhabilitar{
  pointer-events: none;
  cursor: default;
  background-color: #d8d8d8 !important;
  color: #d8d8d8;
}
/******************************** FIN PAGINAS ********************************/

/* Popup container */
.popup {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

/* The actual popup (appears on top) */
.popup .popuptext {
    visibility: hidden;
    width: 160px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 0;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -80px;
}

/* Popup arrow */
.popup .popuptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

/* Toggle this class when clicking on the popup container (hide and show the popup) */
.popup .show {
    visibility: visible;
    -webkit-animation: fadeIn 1s;
    animation: fadeIn 1s
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
    from {opacity: 0;} 
    to {opacity: 1;}
}

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1 ;}
}

.arrow {
  position: fixed;
  text-decoration: none;
  color: #fff;
  
  font-size: 16px;
  padding: 1em;
  display: none;
  opacity: 1;
}

.arrow:hover {
  background-color: rgba(0, 0, 0, 0.4);
}  
  
.arrow-bottom {
  top: 7em;
  right: 0em;      
}
        
.arrow-top {
  bottom: 6em;
  right: 0em;
}  


/************************* MEDIA QUERIES *************************************/

/********** Large devices only (> 1200px) **********/
@media (min-width: 1200px) {
  /* Header */
  .navbar-brand h1 { /* Page section name */
  font-size: 2.8em;
  margin-top: 7px;
  }
  /* End Header */
  
  .container .jumbotron {
    background: url('../images/jumbotron_1200.jpg') no-repeat;
    height: 675px;
  }
}

/********** Medium devices only (992px - 1199px )**********/
@media (min-width: 992px) and (max-width: 1199.98px) {
  /* Header */
  .navbar-brand h1 { /* Page section name */
  font-size: 4.14vw; /* 1vw = 1% of viewport width */
  margin: auto;
  margin-top: 9px;
  }
  /* End Header */

  /* Home Page */
  .container .jumbotron {
    background: url('../images/jumbotron_992.jpg') no-repeat;
    height: 558px;
  }
  /* End Home Page */
}

/********** Small devices only (768px - 991px ) **********/
@media (min-width: 768px) and (max-width: 991.98px) {
  /* Header */
  header {
    height: 82px;
  }
  .navbar-header {
    width: 100%;
  }
  .navbar-brand {
    padding-top: 5px;
    padding-right: 0px;
    height: 62px;
    margin: auto;
  }
  .navbar-brand h1 { /* Page section name */
  font-size: 4.19vw; /* 1vw = 1% of viewport width */
  padding-top: 20px;
  padding-right: 10px;
  margin: auto;
  }
  #logo-img {
    background: url('../images/logoChico.png') no-repeat;
    width: 160px;
    height: 62px;
    margin: 10px 10px 10px 0;
  }
  /* End Header */
 
  /* Home Page */
  .container .jumbotron {
    background: url('../images/jumbotron_768.jpg') no-repeat;
    height: 432px;
  }
  /* End Home Page */
  
  #testimonials {
    font-size: 15px;
  }
  
  #address {
    font-size: 15px;
    padding-left: 0px;
    padding-right: 0px;
  }
}

/********** Extra small devices only (577px - 767px) **********/
@media (min-width: 576px) and (max-width: 767.98px) {
  /* Header */
  header {
    height: 72px;
  }
  .navbar-header {
    width: 100%;
  }
  .navbar-brand {
    padding-top: 15px;
    height: 62px;
    margin-top: 5px
  }
  .navbar-brand h1 { /* Restaurant name */
    padding-top: 0px;
    font-size: 3.08vw; /* 1vw = 1% of viewport width */
  }

  #logo-img {
    background: url('../images/logoChico.png') no-repeat;
    width: 160px;
    height: 62px;
    margin: 0px 10px 0px 0;
  }

  .divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5;
    }
  
  .navbar-header button.navbar-toggle {
    margin-top: -85px;
  }
  #collapsable-nav a { /* Collapsed nav menu text */
    font-size: 0.9em;
  }
  #collapsable-nav a span { /* Collapsed nav menu glyph */
    font-size: 1em;
    margin-right: 5px;
  }
  #nav-list a:hover {
    background: #009999;
  }
  /* End Header */

  /* Footer */
  .panel-footer {
    max-height: 150px;
    font-size: 11px;
  }
  .panel-footer section {
    margin-bottom: 10px;
    text-align: center;
  }
  .panel-footer section:nth-child(3) {
    margin-bottom: 0; /* margin already exists on the whole row */
  }
  .panel-footer section hr {
    width: 100%;
  }
  #address {
    font-size: 11px;
    padding-left: 0px;
    padding-right: 0px;
  }
  /* End Footer */
}


/********** Super extra small devices Only :-) (e.g., iPhone 4) **********/
@media (max-width: 575.98px) {
  /* Header */
  
  .navbar-brand {
    padding-top: 10px;
    height: 100px;
    margin-top: 10px
  }
  .navbar-brand h1 { /* Restaurant name */
    padding-top: 15px;
    font-size: 5.0vw;
  }
/*  #logo-img {
    background: url('../images/logo-emsa150.png') no-repeat;
    width: 150px;
    height: 100px;
    margin: 20px 10px 10px 0;
  }*/
  #collapsable-nav{
    padding-left: 0px;
  }
  #nav-list  {
    font-size: 2.67vw;
    margin: 0px;
  }
  /* End Header */
  hr{
    height: 1px;
    margin: 2px;
    max-width: 75%;
    margin: auto;
  }
  /* Home page */
  .col-xxs-12 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
    width: 100%;
  }
  /* Footer */
  .panel-footer {
    max-height: 150px;
    font-size: 11px;
  }
  .panel-footer section {
    margin-bottom: 10px;
    text-align: center;
  }
  .panel-footer section:nth-child(3) {
    margin-bottom: 0; /* margin already exists on the whole row */
  }
  .panel-footer section hr {
    width: 100%;
  }
  #address {
    font-size: 11px;
    padding-left: 0px;
    padding-right: 0px;
  }
  /* End Footer */
  
}

/************************* END MEDIA QUERIES ********************************/

FINCSS;
?>