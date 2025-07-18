<?php
session_start();
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Colored Table
 * @author Ivan
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('..\libraries\TCPDF-main\tcpdf.php');
require "..\db\connection.php";


   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
        header('Location: ../auth/login.php');
        exit();
   }

    $id_usuario = $_SESSION['id_usuario'];

    $sql = $conn->prepare("SELECT titulo,descripcion,fecha_creacion,fecha_vencimiento,prioridad,estado
                          FROM tareas 
                          where usuario_id = ?");
   $sql->execute([$id_usuario]);
   $tareas = $sql->fetchAll(PDO::FETCH_ASSOC);

   $sql2 = $conn->prepare("SELECT nombre,email FROM usuarios Where id = ?");
   $sql2->execute([$id_usuario]);
   $usuario = $sql2->fetch(PDO::FETCH_ASSOC);

// extend TCPF with custom functions
class MYPDF extends TCPDF {



	// Colored table
	public function ColoredTable($header,$data) {
		// Colors, line width and bold font
		$this->SetFillColor(255, 0, 0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128, 0, 0);
		$this->SetLineWidth(0.3);
		$this->SetFont('', 'B');
		// Header
		$w = array(55, 80, 21, 16 , 16);
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
			$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
		}
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = 0;
		foreach($data as $row) {
			$this->Cell($w[0], 6, $row['titulo'], 'LR', 0, 'L', $fill);
			$this->Cell($w[1], 6, $row['descripcion'], 'LR', 0, 'L', $fill);
			$this->Cell($w[2], 6, $row['fecha_vencimiento'], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $row['prioridad'], 'LR', 0, 'R', $fill);
            $this->Cell($w[4], 6, $row['estado'], 'LR', 0, 'R', $fill);
			$this->Ln();
			$fill=!$fill;
		}
		$this->Cell(array_sum($w), 0, '', 'T');
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($usuario['nombre']);
$pdf->SetTitle('Task Flow');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 9);

// add a page
$pdf->AddPage();

// column titles
$header = array('Titulo', 'Descripcion','Vencimiento','Prioridad','Estado');
 
// print colored table
$pdf->ColoredTable($header, $tareas);

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('tareas.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+