<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$pdf = new \Pdf\Pdf();

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setMbStr(true);
$pdf->setHeaderMargin(10);
$pdf->AddPage();
$pdf->AddFont('tbht_r', '');

$pdf->setFont('tbht_r', '', 15);

$pdf->p('英文 qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM');
$pdf->p('数字 0123456789');
$pdf->p('符号 `~!@#$%^&*()-=_+[]\{}|;\':",./<>?/*-+');
$pdf->p('符号 ·~！@#￥%……&*（）-=——+【】、「」|；‘：“，。、《》？/*-+');
$pdf->p('方正黑体显示失败   道滘(jiao)    鹓鶵(yuan\'chu)');
$pdf->p('魑魅魍魉(chi\'mei\'wang\'liang)');
$pdf->p('名称、类别、杰 没');

$pdf->Output(__DIR__ . '/font_test.pdf', 'F');
