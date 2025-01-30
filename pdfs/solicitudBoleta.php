<?php

use function PHPSTORM_META\type;

require_once('./tcpdf_include.php');
require_once('./myPdf.php');
include __DIR__ . '/../getData/getData.php';
require_once __DIR__ . '/../validaciones/validaciones.php';
require_once __DIR__ . '/../funcionesExtras/funcionesExtras.php';
require_once __DIR__ . '/../modelo/conexion.php';

// recivimos por metodo get el id  de la licencia y la descencriptamos, pero verificamos que exista y que no este vacia 

// tpp es tipo de presentacion del promedio
if (isset($_GET['boleta'])  && !empty($_GET['boleta']) && isset($_GET['tpp'])) {
    $idboleta = $_GET['boleta'];
    $tpp = $_GET['tpp'];
    $tpp = (int) $tpp;

    $idUrl = FuncionesExtras::decodificacionUrl($_GET['boleta']);
    $boleta = Validaciones::desencriptar($idUrl);
    $boleta = (int) $boleta;

    // echo $boleta;
    if (is_numeric($boleta)) {

      // mandamos a llamar al metodo para obtener la informacion de la boleta es especifico

       $datosBoleta = getData::getCalificaciones(" id_boleta = '$boleta' ");
       $datosBoleta= FuncionesExtras::generarArregloCalificaciones($datosBoleta);
       $datosBoleta= $datosBoleta[0];

       $turnoMatutino=$datosBoleta['turno'] != "MATUTINO" ? '<img width="15px" src="./img/checkVacio.png" alt="">' : '<img width="15px" src="./img/checkActivo.png" alt="">';
       $turnoVespertino=$datosBoleta['turno'] != "VESPERTINO" ? '<img width="15px" src="./img/checkVacio.png" alt="">' : '<img width="15px" src="./img/checkActivo.png" alt="">';

      //  var_dump($datosBoleta['calificacionesPrimaria']);
       $calificacionesPrimaria= $datosBoleta['calificacionesPrimaria'];
       $calificacionesSecundaria= $datosBoleta['calificacionesSecundaria'];
      //  contamos el numero de materias que se tomaran en cuenta para la calificacion, en caso de que sea indigena omitir la materia lengua que habla
      $contador=0;
      $sumatoria=0;

      for ($i=0; $i <count($calificacionesPrimaria) ; $i++) { 
        if($calificacionesPrimaria[$i]['nombre_materia'] != 'LENGUA QUE HABLA')
        $contador +=1;
        $sumatoria+=$calificacionesPrimaria[$i]['calificacion'];
      }
      $promedio = $contador > 0 ? $sumatoria / $contador : $calificacionesSecundaria['calificacionFinal'];

      if ($tpp == 1 && gettype($tpp) == 'integer') {
          // Redondear hacia arriba o hacia abajo según el decimal
          $promedio = ($promedio - floor($promedio) >= 0.5) ? ceil($promedio) : floor($promedio);
      } else {
          // Redondear a un punto decimal
          $promedio = round($promedio, 1);
      }
      

      

        $fechaActual = FuncionesExtras::formatearFecha(date('Y-m-d'));
        $fechaSeparada=explode('DE',$fechaActual);

        /**
         * 
         * 
         * Creates an example PDF TEST document using TCPDF
         * @package com.tecnick.tcpdf
         * @abstract TCPDF - Example: Custom Header and Footer
         * @author Nicola Asuni
         * @since 2008-03-04
         */

        // Include the main TCPDF library (search for installation path).
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Departamento de Estadistica');
        $pdf->SetTitle('Constancia de solicitud');
        $pdf->SetSubject('Constancia de solicitud');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font


        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('times', '', 12);

        // add a page
        $pdf->AddPage('P', "LETTER");

        $pdf->writeHTMLCell(0, 0, 30, 16, "SOLICITUD DE DUPLICADOS Y SERVICIOS", 0, 1, 0, true, 'C', true);
        $fecha = '
<table >
    <tr>
        <td style="font-size:10pt ;width:377px"></td>
        <td style="font-size:10pt ;width:60px">FECHA:</td>
        <td style="font-size:10pt ;width:250px; border-bottom:1px solid #444; text-align:center">'.$fechaActual.'</td>
        
    </tr>
</table>
';

        // Escribir el contenido HTML en el PDF
        $pdf->writeHTMLCell(0, 0, 0, 23, $fecha, 0, 1, 0, true, 'L', true);

        $pdf->Image('./img/checkSimple.png', 195, 135, 4, 4, '', '', '', false, 300, '', false, false, 0, false, false, false);
        $pdf->Image('./img/checkSimple.png', 195, 165, 4, 4, '', '', '', false, 300, '', false, false, 0, false, false, false);
        $pdf->Image('./img/checkSimple.png', 146, 198, 4, 4, '', '', '', false, 300, '', false, false, 0, false, false, false);
        $pdf->Image('./img/checkSimple.png', 146, 235, 4, 4, '', '', '', false, 300, '', false, false, 0, false, false, false);


        // set some text to print
        $html = '


<table border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td style="font-size:8.7pt; width:660px">
    PRESENTAR LOS REQUISITOS INDISPENSABLES SEGUN LAS NORMAS DE CONTROL ESCOLAR TITULO VII - CERTIFICACION, PUNTO 7.8 (7.8 1-7 . 11)
    </td>
  </tr>

  <tr>
    <td style="font-size:10pt; font-style:italic; font-weight:bold">
    REQUISITOS: 
    </td>
  </tr>
  <tr>
 <td style="font-size:10pt;">
<ul>
<li style="list-style-type: disc;" >Copia de la CURP del alumno solicitante</li>
<li style="list-style-type: disc;" >Copia de la Credencial de Elector del interesado o quien  tramita</li>
<li style="list-style-type: disc;" >Fotografia del certificado o Kardex que sra proporcionado por la Escuala Secundaria (Opcional)</li>
<li style="list-style-type: disc;" >Realizar el pago por la expedicion de acuerdo a la siguiente tabla</li>
</ul>
 </td>
  </tr>
</table>




<table style=" border-collapse: collapse;">
<tr>
  <td style="text-transform:capitalize; ;width:270px; font-weight: bold; text-align: center; border: 1px solid #444 ; font-size: 11pt;">' . ucwords("nivel básico") . '</td>
  <td style="width:50px ;font-weight: bold; text-align: center; border: 1px solid #444 ; font-size: 11pt;">' . ucwords("precio") . '</td>
  <td style="font-weight: bold; text-align: center;  width: 20px;"></td>
  <td style="width:270px;font-weight: bold; text-align: center; border: 1px solid #444 ; font-size: 11pt;">nivel media superior</td>
  <td style="width:50px ;font-weight: bold; text-align: center; border: 1px solid #444 ; font-size: 11pt;">' . ucwords("precio") . '</td>
</tr>
<tr>
  <td style=" width:270px; text-align:center; border: 1px solid #444 ; font-weight: bold;font-size: 9pt;">' . ucwords("preescolar, primaria, secundaria") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;" rowspan="3">$97.71</td>
  <td style="text-align:center;  width: 20px;"></td>
  <td style="width:270px;text-align:center; border: 1px solid #444 ; font-size: 9pt;">' . ucwords("duplicado de  certificacion de prparatoria") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">$ 304.00</td>
</tr>
<tr>
  <td style=" width:270px; text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">' . ucwords("boleta") . ' (1)(2)(3)(4)(5)(6)</td>
  <!-- <td style="text-align:center;  width: 20px;"></td> -->
  <td style="text-align:center;  width: 20px;"></td>
  <td style="text-align:center; border: 1px solid #444 ;font-weight: bold; font-size: 11pt;" colspan="2">' . ucwords("nivel superior") . '</td>
  <!-- <td style="text-align:center;  width: 20px;"></td> -->
</tr>
<tr>
  <td style=" width:270px; text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">' . ucwords("educacion basica para adulto primaria y secundaria") . '</td>
  <!-- <td style="text-align:center;  width: 20px;"></td> -->
  <td style="text-align:center;  width: 20px;"></td>
  <td style=" width:270px;text-align:center; border: 1px solid #444 ; font-size: 9pt;">' . ucwords("duplicado de certificados para UPN, CAM Y normal experimental") . '</td>
  <td style="text-align:center; border: 1px solid #444 ;font-weight: bold; font-size: 9pt;">$ 304.00</td>
</tr>
<tr>
  <td style=" width:270px; text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">' . ucwords("escuelas desaparecidas") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold;font-size: 9pt;">$ 206.28</td>
  <td style="text-align:center;  width: 20px;"></td>
  <td style="width:270px;text-align:center; border: 1px solid #444 ; font-size: 9pt;">' . ucwords("impresion de titulo electronico de UPN") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">$ 508.00</td>
</tr>
<tr>
  <td style=" width:270px; text-align:center; border: 1px solid #444 ; font-weight: bold;font-size: 9pt;">' . ucwords("expedicion de constancias de estudios") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold;font-size: 9pt;">$304.00</td>
  <td style="text-align:center;  width: 20px;"></td>
  <td style="width:270px;text-align:center; border: 1px solid #444 ;font-size: 9pt;">' . ucwords("duplicado de certificacion de postgrados") . '</td>
  <td style="text-align:center; border: 1px solid #444 ; font-weight: bold; font-size: 9pt;">$ 304.00</td>
</tr>
</table>

<table cellspacing="3" cellpadding="3">
  <tr>
    <td style="font-size: 10pt; width: 52%;">' . strtoupper("ceba-cedex") . ' &nbsp;&nbsp; <img width="15px" src="./img/checkVacio.png" alt=""> </td>
    <td style="font-size: 7pt; width: 48%;">
      *De acuerdo a la ley de ingresos del estado de Nayarit parael ejercicio fiscal 2019 Publicado en el  diario oficial de la federacion  seccion 5 de los servicios que presta la secretaria de educacion art.30 - II inciso G 
    </td>
  </tr>
</table>



<table>
<tr>
<td style=" text-decoration: underline; font-weight: bold; font-size:10pt">
   ' . strtoupper("datos personales:") . '
</td>
</tr>
</table>
  
  <table border="0" style="  border-collapse: collapse; border: 1px solid #444;" cellpadding="1" cellspacing="5">
    <tr style="border-top:1px solid #444">
      <td style="font-size:9pt; width:150px; font-weight:bold">' . strtoupper("nombre del alumno") . '</td>
      <td colspan="5"  style="font-size:9pt; border-bottom: 1px solid #444;width:450px; text-align:center;"> '.$datosBoleta['nombre']. " ". $datosBoleta['apellido_paterno']. " ".$datosBoleta['apellido_materno']. " ".'  </td>
    </tr>
    <tr>
      <td style="font-size:9pt; width:200px; font-weight:bold">' . strtoupper("domicilio particular actual") . '</td>
      <td colspan="5"  style="font-size:9pt; border-bottom: 1px solid #444; width:400px; text-align:center; "> '.$datosBoleta['domicilio_particular'].' </td>

    </tr>
    <tr>
      <td style="font-size:9pt; width:70px; font-weight:bold" >' . strtoupper("municipio") . '</td>
      <td style="font-size:9pt; border-bottom: 1px solid #444;width:140px; text-align:center;"> '.$datosBoleta['municipio_dom'].' </td>
      <td style="font-size:9pt; width:80px; font-weight:bold">' . strtoupper("localidad") . '</td>
      <td style="font-size:9pt; border-bottom: 1px solid #444;width:140px; text-align:center">'.$datosBoleta['localidad_dom'].'</td>
      <td style="font-size:9pt; width:60px; font-weight:bold">' . strtoupper("tel. cel") . '</td>
      <td style="font-size:9pt; border-bottom: 1px solid #444;width:111px; text-align:center">'.$datosBoleta['telefono'].'</td>
    </tr>
        <tr>
      <td></td>

    </tr>
    
  </table>



<table>
<tr>
<td style=" text-decoration: underline; font-weight: bold; font-size:10pt">
   ' . strtoupper("datos escolar:") . '
</td>
</tr>
</table>

  <table border="0" style="  border-collapse: collapse; border: 1px solid #444;" cellpadding="1" cellspacing="5">
    <tr style="border-top:1px solid #444">
      <td style="font-size:9pt; width:150px; font-weight:bold">' . strtoupper("nombre de la escuela") . '</td>
      <td colspan="5"  style="font-size:9pt; border-bottom: 1px solid #444;width:450px; text-align:center;">'.$datosBoleta['nombre_cct'].'</td>
    </tr>
    <tr>
      <td style="font-size:9pt; width:150px; font-weight:bold">' . strtoupper("clave de la escuela") . '</td>
      <td colspan="5"  style="font-size:9pt; border-bottom: 1px solid #444; width:450px; text-align:center;">'.$datosBoleta['clave_centro_trabajo'].'</td>

    </tr>
    <tr>
  <td></td>
  </tr>
  
    <tr>
      <td style="font-size:9pt; width:50px; font-weight:bold" >' . strtoupper("turno:") . '</td>
      <td style="font-size:9pt; width:100px">' . strtoupper("matutino") . ' &nbsp;&nbsp;'.$turnoMatutino.'</td>
      <td style="font-size:9pt; width:100px">' . strtoupper("vespertino") . ' &nbsp;&nbsp; '.$turnoVespertino.' </td>
      <td style="font-size:9pt; width:45px">' . strtoupper("grupo") . ' </td>
      <td style="border: 1px solid #444; width:30px; font-size:9pt; text-align:center">B</td>
      <td style="width:20px"></td>

      <td style="font-size:9pt; width:100px">' . strtoupper("ciclo escolar") . '</td>
      <td style="font-size:9pt; border-bottom: 1px solid #444;width:150px; text-align:center">'.$datosBoleta['ciclo'].'</td>
    </tr>
      
      </table>






      <table>
<tr>
<td style=" text-decoration: underline; font-weight: bold; font-size:10pt">
   ' . strtoupper("datos para uso exclusivo del departamento de registro y certificacion escolar:") . '
</td>
</tr>
</table>
<br>

<table    cellpadding="1" cellspacing="0"  >
  <tr>
    <td style="width:450px;">


      <table   cellpadding="" style="border:1px solid #444" >
      <tr>
      <td style="font-size:4px"></td>
      </tr>
 <tr>
        <td style="font-size:9pt; text-align:center; border-bottom:1px solid #444; width:210px;">'.$datosBoleta['clave_centro_trabajo'].'</td>
        <td style="width:20px; "></td>
        <td style="font-size:9pt; text-align:center; border-bottom:1px solid #444; width:210px;">'.$datosBoleta['ciclo'].'</td>
        </tr>
        <tr>
        <td style="font-size:9pt; text-align:center;  width:210px;">'.strtoupper("clave de la escuela").'</td>
        <td style="width:20px; "></td>
        <td style="font-size:9pt; text-align:center;  width:210px;">'.strtoupper("ciclo escolar").'</td>
        </tr>

        <tr>
        <td colspan="3" style="font-size:9pt; text-align:center;">'.$datosBoleta['nombre']. " ". $datosBoleta['apellido_paterno']. " ".$datosBoleta['apellido_materno']. " ".' </td>
        </tr>
        <tr>
        <td colspan="3" style="font-size:9pt; text-align:center; border-top:1px solid #444">'.strtoupper("nombre como viene en el expediente").'</td>
        </tr>

          <tr>
      <td style="font-size:2px"></td>
      </tr>


              <tr>
        <td style="font-size:9pt; text-align:center; border-bottom:1px solid #444">'.$promedio.'</td>
        <td style="width:20px; "></td>
        <td style="font-size:9pt; text-align:center; border-bottom:1px solid #444">'.$datosBoleta['folio'].'</td>
        </tr>
        <tr>
        <td style="font-size:9pt; text-align:center;">'.strtoupper("promedio").'</td>
        <td style="width:20px; "></td>
        <td style="font-size:9pt; text-align:center;">'.strtoupper("folio original").'</td>
        </tr>

          <tr>
      <td style="font-size:2px"></td>
      </tr>


       <tr>
        <td colspan="3" style="font-size:9pt; text-align:center;">'.$datosBoleta['nombre_cct'].'</td>
        </tr>

        <tr>
        <td colspan="3" style="font-size:9pt; text-align:center; border-top:1px solid #444">'.strtoupper("nombre de la escuela").'</td>
        </tr>

         <tr>
      <td style="font-size:7px"></td>
      </tr>

           <tr style=""> 
          <td style="width:60px; text-align:center; font-size:9pt"></td>
          <td style="width:61px; text-align:center; font-size:9pt"></td>
          <td style="width:60px; text-align:center; font-size:9pt"></td>
          <td style="width:100px; text-align:center; font-size:9pt"></td>
          <td style="width:90px; text-align:center; font-size:9pt"></td>
          <td style="width:50px; text-align:center; font-size:9pt"></td>
        </tr>


      <tr style=""> 
          <td style="width:40px; text-align:center; border-top:1px solid #444; ">dia</td>
          <td style="width:81px; text-align:center; border-top:1px solid #444; "></td>
          <td style="width:40px; text-align:center; border-top:1px solid #444; ">mes</td>
          <td style="width:120px; text-align:center; border-top:1px solid #444; "></td>
          <td style="width:110px; text-align:center; border-top:1px solid #444; ">año terminacion</td>
          <td style="width:50px; text-align:center; border-top:1px solid #444; "></td>
        </tr>
                <tr>
          <td style="width:79.9px">siie  <img width="15px" src="./img/checkVacio.png" alt=""></td>
          <td style="width:79.9px">rexa  <img width="15px" src="./img/checkVacio.png" alt=""></td>
          <td style="width:79.9px">syrcer  <img width="15px" src="./img/checkVacio.png" alt=""></td>
          <td style="width:79.9px">r3  <img width="15px" src="./img/checkVacio.png" alt=""></td>
          <td style="width:79.9px">rel  <img width="15px" src="./img/checkVacio.png" alt=""></td>
          <td style="width:40px"></td>
        </tr>

        

      </table>


    </td>
    <td style="width:210px;">
      <table  cellpadding="4" style="border: 1px solid #444">
      <tr><td style="text-align:center; font-size:12pt;"></td></tr>
        <tr><td style="text-align:center; font-size:9pt; border-top:1px solid #444; ">'.strtoupper("valido").'</td></tr>
      <tr><td style="text-align:center; font-size:12pt;"></td></tr>
        <tr><td style="text-align:center; font-size:9pt; border-top:2px solid #444; ">'.strtoupper("recibi tramite").'</td></tr>
      <tr><td style="text-align:center; font-size:12pt;"></td></tr>
        <tr><td style="text-align:center; font-size:9pt; border-top:1px solid #444; ">'.strtoupper("nombre").'</td></tr>
      <tr><td style="text-align:center; font-size:12pt;"></td></tr>
      <tr><td style="text-align:center; font-size:17pt;"></td></tr>
        <tr><td style="text-align:center; font-size:9pt; border-top:1px solid #444; ">'.strtoupper("firma").'</td></tr>
      </table>
    </td>
  </tr>
</table>







';

        $pdf->setCellPadding(0.3);



        // print a block of text using Write()
        $pdf->writeHTML($html, true, false, true, false, '');

        // $pdf->AddPage('P',"LETTER");

        // $pdf->writeHTML($resumenSolicitud, true, false, true, false, '');
        // ---------------------------------------------------------

        //Close and output PDF document
        ob_end_clean();
        $pdf->Output('SOLICITUD DE DUPLICADOS Y SERVICIOS.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+

    } else {
        echo "<h1>Debe de seleccionar una licencia valida</h1>";
    }
} else {
    echo "<h1>Debe de solicitar la impresion de la constancia</h1>";
}
