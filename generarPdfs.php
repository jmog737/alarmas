<?php
/**
******************************************************
*  @file generarPdfs.php
*  @brief Archivo con las funciones que generan los archivos PDF.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Noviembre 2017
*
*******************************************************/
require_once("css/colores.php");
require_once("data/mc_table.php");

class PDF extends PDF_MC_Table
  {
  ///Constantes usadas por las funciones de ajuste de las imágenes:
  const DPI_150 = 150;
  const DPI_300 = 300;
  const MM_IN_INCH = 25.4;
  const A4_HEIGHT = 297;
  const A4_WIDTH = 210;
  const LOGO_WIDTH_MM = 50;
  const LOGO_HEIGHT_MM = 20;
  const FOTO_WIDTH_MM = 60;
  const FOTO_HEIGHT_MM = 37.84;
  
  ///Variable usada por las funciones de generación de las marcas de agua:
  var $angle=0;
   
  //Cabecera de página
  function Header()
    {
    global $fecha, $hora, $tituloHeader, $x, $marcaAgua, $textoMarcaAgua, $orientacion;
    
    $anchoPage = $this->GetPageWidth();
    $anchoDia = 20;
    $xLogo = 2;
    $yLogo = 0;
    
    //Agrego logo de EMSA:
    $logo = 'images/logoPDF.jpg';
    if (file_exists($logo)){
      list($nuevoAncho, $nuevoAlto) = $this->resizeToFit($logo, self::LOGO_WIDTH_MM, self::LOGO_HEIGHT_MM);
    }
        
    $anchoTitle = $anchoPage - $anchoDia - $nuevoAncho - 2*$xLogo;
    //echo "nuevo ancho: $nuevoAncho<br>nuevo alto: $nuevoAlto<br>xlogo: $xLogo<br>yLogo: $yLogo<br>";
    //echo "pagina: $ancho<br>ancho logo: ".$anchoLogoPx."<br>alto: ".$altoLogoPx."<br>ancho logo mm: ".$anchoLogoMm."<br>alto mm: ".$altoLogoMm."<br><br>";
    $this->Image($logo, $xLogo, $yLogo, $nuevoAncho, $nuevoAlto);
    $this->setY($yLogo+1);
    $this->setX($nuevoAncho+$xLogo);
    
    $this->SetTextColor(colorHeaderTituloTexto[0], colorHeaderTituloTexto[1], colorHeaderTituloTexto[2]);
    $this->SetFillColor(colorHeaderTituloFondo[0], colorHeaderTituloFondo[1], colorHeaderTituloFondo[2]);
    $this->SetFont('Arial', 'BU', 18);
    
    $this->Cell($anchoTitle, $nuevoAlto-3, strtoupper(utf8_decode($tituloHeader)), 0, 0, 'C', false);
    
    $this->setY($yLogo+1);
    $xFecha = $nuevoAncho+$anchoTitle+$xLogo-1;
    $this->setX($xFecha);
    
    $this->SetFont('Arial');
    $this->SetFontSize(10);
    
    $this->Cell($anchoDia, $nuevoAlto-4, $fecha, 0, 0, 'C', false);
    
    $this->setY($yLogo+10);
    $this->setX($xFecha);
    
    $this->SetFont('Arial');
    $this->SetFontSize(10);   
    
    $this->Cell($anchoDia, 5, $hora, 0, 0, 'C', false);

    ///*************************** AGREGADO DE UNA MARCA DE AGUA: *********************************************
    if ($marcaAgua !== "false") {
      $this->SetTextColor(colorMarcaAgua[0],colorMarcaAgua[1],colorMarcaAgua[2]);
      //Put the watermark
      if ($orientacion === 'P'){
        $this->SetFont('Arial','B',120); 
        $this->RotatedText(25,290,$textoMarcaAgua,56);
      }
      else {
        $this->SetFont('Arial','B',120);
        $this->RotatedText(20,200,$textoMarcaAgua,33);
      }   
    }
    ///************************ FIN AGREGADO DE UNA MARCA DE AGUA *********************************************
    
    
    ///******************************* TEST AGREGADO RECTÁNGULO: **********************************************
    
    ///********************************************************************************************************
    $anchoRect = 0.97*$anchoPage;
    $xRect = round((($anchoPage - $anchoRect)/2), 2);
    $this->Rect($xRect, $yLogo+ self::LOGO_HEIGHT_MM-2, $anchoRect, $this->GetPageHeight()-self::LOGO_HEIGHT_MM-6, 'D');
    ///********************************************************************************************************
    
    ///******************************** FIN AGREGADO RECTÁNGULO: **********************************************
    
    //Dejo el cursor donde debe empezar a escribir:
    $this->Ln(10);
    $this->SetX($x);
  }

  //Pie de página
  function Footer()
    {
    global $hFooter;

    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor(colorFooterTexto[0], colorFooterTexto[1], colorFooterTexto[2]);
    $this->SetFillColor(colorFooterFondo[0], colorFooterFondo[1], colorFooterFondo[2]);
    
    $anchoPagina = $this->GetPageWidth();
    $anchoFooter = 0.97*$anchoPagina;
    $altoFooter = 0.95*$hFooter;
    $margen = round((($anchoPagina - $anchoFooter)/2), 2);
    
    $this->SetLeftMargin($margen);
    $this->SetRightMargin($margen);
        
    $this->setX($margen);
    $this->SetY(-$hFooter);
    $this->Cell($anchoFooter, $altoFooter, 'Pag. '.$this->PageNo(), 0, 0, 'R', true);

    $this->SetX($margen);
    $this->SetY(-$hFooter);
    $this->Cell($anchoFooter, $altoFooter, $_SESSION['usuarioReal'], 0, 0, 'L', false);
  }
    
  function armarTabla(){
    global $tituloReporte, $tituloTabla, $h, $hFooter, $registros, $arrayNodos, $nombreNodo;
    require_once('data/camposAlarmas.php');
    
    $hTitulo = 16;
    $tamPagina = $this->GetPageWidth();
    /// Defino un ancho máximo para el título cosa de no llegar a los extremos:
    $anchoTitulo = 0.80*$tamPagina;
    
    /// Defino un ancho máximo para la tabla cosa de no llegar a los extremos:
    $anchoTabla = 0.9*$tamPagina;
       
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    
    ///***************************************************************** TITULO **************************************************************
    /// Defino el tipo de letra y tamaño para el título pues GetStingWidth calcula el ancho en base a esto:
    $this->SetFont('Courier', 'BU', $hTitulo);

    $xTituloReporte = round((($tamPagina-$anchoTitulo)/2), 2);
    $this->SetX($xTituloReporte);
    
    $nbTitulo = $this->NbLines($anchoTitulo, $tituloReporte);
    $hTituloMulti=$hTitulo/$nbTitulo;

    $this->SetTextColor(colorTituloReporte[0], colorTituloReporte[1], colorTituloReporte[2]);
    

    if ($nbTitulo > 1) {
      $this->MultiCell($anchoTitulo, $hTituloMulti, utf8_decode(html_entity_decode($tituloReporte)),0, 'C', false);
    }
    else {
      $this->MultiCell($anchoTitulo, $hTitulo, utf8_decode(html_entity_decode($tituloReporte)),0, 'C', false);
    }
    $y = $this->GetY();
    ///*************************************************************** FIN TITULO ************************************************************
    
    ///********************************************************** INICIO TABLA ***************************************************************
    $tamTablaCampos = 0;
    foreach ($camposAlarmas as $ind => $fila ) {
      if ($fila['mostrarReporte'] === 'si'){
        $tamTablaCampos += $fila['tam'];
      }
    } /// Fin foreach cálculo del tamaño de la tabla    

    $xTabla = round((($tamPagina-$anchoTabla)/2), 2);
    $this->SetX($xTabla);
    
    //******************************************************** TÍTULO TABLA ******************************************************************
    //Defino color de fondo para el título de la tabla:
    $this->SetFillColor(colorTituloTablaFondo[0], colorTituloTablaFondo[1], colorTituloTablaFondo[2]);
    $this->SetTextColor(colorTituloTablaTexto[0], colorTituloTablaTexto[1], colorTituloTablaTexto[2]);
    $this->SetFont('Courier', 'B');
    ///Agrego el rectángulo con el borde redondeado:
    $this->RoundedRect($xTabla, $y, $anchoTabla, $h, 3.5, '12', 'DF');
    //Escribo el título:
    $this->Cell($anchoTabla, $h, utf8_decode(html_entity_decode($tituloTabla)), 0, 0, 'C', 0);
    $this->Ln();
    //******************************************************  FIN TÍTULO TABLA ***************************************************************
    
    ///******************************************************** CAMPOS TABLA *****************************************************************   
    $this->SetFillColor(colorCamposFondo[0], colorCamposFondo[1], colorCamposFondo[2]);
    $this->SetTextColor(colorCamposTexto[0], colorCamposTexto[1], colorCamposTexto[2]);
    $this->SetFont('Courier', 'B', 10);
    $this->SetX($xTabla);
    foreach ($camposAlarmas as $key => $value) {
      if ($value['mostrarReporte'] === 'si'){
        $tamCampoReal = (($anchoTabla*$value['tam'])/$tamTablaCampos);      
        $this->Cell($tamCampoReal, $h, utf8_decode(html_entity_decode($value['nombreMostrar'])), 'LRBT', 0, 'C', true);
      }
    }
    ///****************************************************** FIN CAMPOS TABLA ***************************************************************
    
    ///********************************************************* COMIENZO DATOS **************************************************************
    $this->SetTextColor(colorRegistrosTexto[0], colorRegistrosTexto[1], colorRegistrosTexto[2]);
    $this->setFillColor(colorRegistrosFondo[0], colorRegistrosFondo[1], colorRegistrosFondo[2]);
    $this->SetFont('Courier', '', 9);
    $fill = 1;
    $fillAnterior = $fill;
    $a = 'C';
    $this->Ln();
    $this->SetX($xTabla);
    if ($nombreNodo === 'TODOS'){
      $nodoAnterior = '';
    }
    $j = 1;
    foreach ($registros as $indice => $fila) {
      /// ************************************************** Detección cambio de nodo ********************************************************
      /// Para el caso en que se consultan TODOS los nodos, detecto el cambio de nodo y agrego subtítulo indicando el  nuevo nodo:
      if (isset($nodoAnterior)&&($nodoAnterior === '')){
        $nodoAnterior = $fila['nodo'];
        $this->SetTextColor(colorSubtituloTablaTexto[0], colorSubtituloTablaTexto[1], colorSubtituloTablaTexto[2]);
        $this->setFillColor(colorSubtituloTablaFondo[0], colorSubtituloTablaFondo[1], colorSubtituloTablaFondo[2]);
        $this->SetFont('Courier', 'B', 12);
        $this->Cell($anchoTabla, $h, trim(utf8_decode(html_entity_decode($arrayNodos[$nodoAnterior]))), 1, 10, 'C', true);
      }
      $nodoActual = $fila['nodo'];
      if (isset($nodoAnterior)&&($nodoActual !== $nodoAnterior)){
        $nodoAnterior = $nodoActual;
        $this->SetFont('Courier', 'B', 12);
        $this->SetTextColor(colorSubtituloTablaTexto[0], colorSubtituloTablaTexto[1], colorSubtituloTablaTexto[2]);
        $this->setFillColor(colorSubtituloTablaFondo[0], colorSubtituloTablaFondo[1], colorSubtituloTablaFondo[2]);
        $this->Cell($anchoTabla, $h, trim(utf8_decode(html_entity_decode($arrayNodos[$nodoAnterior]))), 1, 10, 'C', true);
        $j = 1;
      }
      $this->SetFont('Courier', '', 9); 
      ///********************************************* Fin detección de cambio de nodo *******************************************************
      
      ///************ Calculo el alto de la fila según el dato más largo de los que vayan visibles: ******************************************
      $nb=0;
      $h0 = 0;
      foreach ($camposAlarmas as $ind => $datoCampo){
        if ($datoCampo['mostrarReporte'] === 'si'){
          if ($datoCampo['nombreDB'] !== 'id'){
            $dat = '';
            $tamDat = 0;
            $this->SetFont('Courier', '', 9);
            $dat = trim(utf8_decode($fila[$datoCampo['nombreDB']]));
            $tamDat = $this->GetStringWidth($dat);
            $w1 = (($datoCampo['tam']*$anchoTabla)/$tamTablaCampos);
            $nb=max($nb,$this->NbLines($w1,$dat)); 
          }    
        }
      }
      $h0=$h*$nb;
      ///******************** FIN Cálculo del alto de la fila *******************************************************************************
      
      ///*************************************** ENCABEZADO DE PÁGINA (pageBreak) ***********************************************************
      if($this->GetY()+$h0>$this->PageBreakTrigger){
        $this->AddPage($this->CurOrientation);
        $this->SetAutoPageBreak(true, $hFooter);
        ///****************************************************** TITULO (pageBreak) ********************************************************
        /// Defino el tipo de letra y tamaño para el título pues GetStingWidth calcula el ancho en base a esto:
        $this->SetFont('Courier', 'BU', $hTitulo);
        $this->SetX($xTituloReporte);
        $this->SetTextColor(colorTituloReporte[0], colorTituloReporte[1], colorTituloReporte[2]);

        if ($nbTitulo > 1) {
          $this->MultiCell($anchoTitulo, $hTituloMulti, utf8_decode(html_entity_decode($tituloReporte)),0, 'C', false);
        }
        else {
          $this->MultiCell($anchoTitulo, $hTitulo, utf8_decode(html_entity_decode($tituloReporte)),0, 'C', false);
        }
        $y = $this->GetY();
        ///****************************************************** FIN TITULO (pageBreak) *****************************************************

        ///*********************************************** CONTINÚO TABLA (pageBreak) ********************************************************
        $this->SetX($xTabla);
        ///*********************************************** CAMPOS TABLA (pageBreak) **********************************************************  
        $this->SetFillColor(colorCamposFondo[0], colorCamposFondo[1], colorCamposFondo[2]);
        $this->SetTextColor(colorCamposTexto[0], colorCamposTexto[1], colorCamposTexto[2]);
        $this->SetFont('Courier', 'B', 10);
        $this->SetX($xTabla);
        foreach ($camposAlarmas as $key => $value) {
          if ($value['mostrarReporte'] === 'si'){
            $tamCampoReal = (($anchoTabla*$value['tam'])/$tamTablaCampos);      
            $this->Cell($tamCampoReal, $h, utf8_decode(html_entity_decode($value['nombreMostrar'])), 'LRBT', 0, 'C', true);
          }
        }
        $this->Ln();
        $this->SetX($xTabla);
        $this->SetTextColor(0); 
        ///******************************************** FIN CAMPOS TABLA (pageBreak) *********************************************************
      }
      ///*************************************** FIN ENCABEZADO DE PÁGINA (pageBreak) ********************************************************
      $this->SetTextColor(colorRegistrosTexto[0], colorRegistrosTexto[1], colorRegistrosTexto[2]);
      $this->setFillColor(colorRegistrosFondo[0], colorRegistrosFondo[1], colorRegistrosFondo[2]);
      
      foreach ($camposAlarmas as $ind0 => $campo){
        if ($campo['mostrarReporte'] === 'si'){
          $this->SetFont('Courier', '', 9);
          
          switch ($campo['nombreDB']){
            case 'dia': $temp = explode('-', $fila[$campo['nombreDB']]);
                        $datito = $temp[2].'/'.$temp[1].'/'.$temp[0];
                        break;
            case 'id':  //$datito = $indice + 1;
                        $datito = $j;
                        break;         
            default:  $datito = trim(utf8_decode(html_entity_decode($fila[$campo['nombreDB']])));
                      break;
          }
          
          $anchoCampo = (($campo['tam']*$anchoTabla)/$tamTablaCampos);
          $nb1 = $this->NbLines($anchoCampo, $datito);

          //Save the current position
          $x1=$this->GetX();
          $y=$this->GetY();
      
          if ($campo['nombreDB'] === 'tipoAlarma'){
            $fillAnterior = $fill;
            $fill = true;
            $tipoAlarma = $fila['tipoAlarma'];
            switch ($tipoAlarma){
              case 'MN':  $this->setFillColor(colorAlarmaMNFondo[0], colorAlarmaMNFondo[1], colorAlarmaMNFondo[2]);

                          break;
              case 'CR':  $this->setFillColor(colorAlarmaCRFondo[0], colorAlarmaCRFondo[1], colorAlarmaCRFondo[2]);

                          break;
              case 'MJ':  $this->setFillColor(colorAlarmaMJFondo[0], colorAlarmaMJFondo[1], colorAlarmaMJFondo[2]);

                          break;
              case 'WR':  $this->setFillColor(colorAlarmaWRFondo[0], colorAlarmaWRFondo[1], colorAlarmaWRFondo[2]);

                          break;
              default:  $this->setFillColor(colorRegistrosFondo[0], colorRegistrosFondo[1], colorRegistrosFondo[2]);
                        break;
            }
          }
          else {
            $this->setFillColor(colorRegistrosFondo[0], colorRegistrosFondo[1], colorRegistrosFondo[2]);
          }
          
          $f = ($fill) ? 'F' : '';
          //Draw the border
          $this->Rect($x1,$y,$anchoCampo,$h0, $f);
          $h1 = $h0/$nb1;
          
          //Print the text
          if ($nb1 > 1) {
            $this->MultiCell($anchoCampo, $h1, $datito,1, $a, $fill);
          }
          else {
            $this->MultiCell($anchoCampo, $h0, $datito,1, $a, $fill);
          } 
          if ($campo['nombreDB'] === 'tipoAlarma'){
            $fill = $fillAnterior;
          }
          
          //Put the position to the right of the cell
          $this->SetXY($x1+$anchoCampo,$y);
        }
      }
      
      //Go to the next line
      $this->Ln($h0);
      $this->SetX($xTabla);
      $fill = ($fill === 1) ? 0 : 1;
      $j++;
    }
    
    ///******************************************************* BORDE REDONDEADO DE CIERRE ***************************************************
    $y = $this->GetY();
    $this->SetFillColor(colorTituloTablaFondo[0], colorTituloTablaFondo[1], colorTituloTablaFondo[2]);
    ///Agrego el rectángulo con el borde redondeado:
    $this->RoundedRect($xTabla, $y, $anchoTabla, $h, 3.5, '34', 'DF');
    ///***************************************************** FIN BORDE REDONDEADO DE CIERRE *************************************************
    ///********************************************************* FIN TABLA ******************************************************************
  }

  ///Función auxiliar para redondear los bordes de las tablas.
  ///Está sacada del script: Rounded Rectangle
  function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
    $k = $this->k;
    $hp = $this->h;
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $MyArc = 4/3 * (sqrt(2) - 1);
    $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

    $xc = $x+$w-$r;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
    if (strpos($corners, '2')===false)
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
    else
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

    $xc = $x+$w-$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
    if (strpos($corners, '3')===false)
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

    $xc = $x+$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
    if (strpos($corners, '4')===false)
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

    $xc = $x+$r ;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
    if (strpos($corners, '1')===false)
    {
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
        $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
    }
    else
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
    $this->_out($op);
  }

  ///Función auxiliar para redondear los bordes de las tablas.
  ///Está sacada del script: Rounded Rectangle
  function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
    $h = $this->h;
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
  }
  
  ///Función auxiliar que ajusta el tamaño de la imagen a los parámetros de ancho y alto pasados:
  function resizeToFit($imgFilename, $ancho, $alto) {
    $imageInfo = getimagesize($imgFilename);
    $salida = array();
    if ($imageInfo === FALSE){
      $salida[] = null;
      $salida[] = null;
    }
    else {
      $anchoImgPx = $imageInfo[0];
      $altoImgPx = $imageInfo[1];
      ///Convierto de px a mm (usando los DPI estipulados):
      $anchoImgMm = $anchoImgPx*self::MM_IN_INCH/self::DPI_300;
      $altoImgMm = $altoImgPx*self::MM_IN_INCH/self::DPI_300;

      $widthScale = $ancho / $anchoImgMm;
      $heightScale = $alto / $altoImgMm;
      $scale = min($widthScale, $heightScale);
      $salida[] = round($scale * $anchoImgMm);
      $salida[] = round($scale * $altoImgMm);
    }
    return $salida;       
  }  
    
  ///Función auxiliar usada para generar las marcas de agua:
  ///Sacada del script: PDF_Rotate
  function Rotate($angle,$x=-1,$y=-1)
    {
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)
      {
      $angle*=M_PI/180;
      $c=cos($angle);
      $s=sin($angle);
      $cx=$x*$this->k;
      $cy=($this->h-$y)*$this->k;
      $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
  }
  
  ///Función auxiliar para generar las marcas de agua:
  ///Sacada del script: PDF_Rotate
  function RotatedText($x,$y,$txt,$angle)
    {
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
  }

  ///Función auxiliar usada para generar las marcas de agua:
  ///Sacada del script: PDF_Rotate
  function _endpage()
    {
    if($this->angle!=0)
      {
      $this->angle=0;
      $this->_out('Q');
    }
    parent::_endpage();
  }  
    
}