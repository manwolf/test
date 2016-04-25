<?php
require('chinese.php');

$pdf=new PDF_Chinese();
$pdf->AddBig5Font();
$pdf->AddPage();
$pdf->SetFont('Big5','',20);
$pdf->Write(10,"孙广jing  兢兢业业 462dsf*&&……%##&**");
$pdf->Output();
?>
