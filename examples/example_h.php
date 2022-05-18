<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$pdf = new \Pdf\Pdf();

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setMbStr(true);
$pdf->setHeaderMargin(10);
$pdf->AddPage();

$pdf->h('标题1');
$pdf->h('标题2', 2);
$pdf->h('标题3', 3);
$pdf->h('标题4', 4);
$pdf->h('标题5', 5, 'C');
$pdf->h('标题6', 6, 'R');
$pdf->h('标题7', 7, 'C');
$pdf->h('标题8', 8);

$pdf->Output(__DIR__ . '/example_h.pdf', 'F');
