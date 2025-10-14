<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$pdf = new \Pdf\Pdf();

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setMbStr(true);
$pdf->setHeaderMargin(10);
$pdf->AddPage();
$pdf->setFont($pdf->getDefaultFont(), '', 10.5);

$pdf->p('英文 qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM');
$pdf->p('数字 0123456789');
$pdf->p('符号 `~!@#$%^&*()-=_+[]\{}|;\':",./<>?/*-+');
$pdf->p('符号 ·~！@#￥%……&*（）-=——+【】、「」|；‘：“，。、《》？/*-+');
$pdf->p('方正黑体显示失败   道滘(jiao)    鹓鶵(yuan\'chu)');
$pdf->p('魑魅魍魉(chi\'mei\'wang\'liang)');
$pdf->p('名称、类别、杰 没');
$pdf->p('tbht_r字体显示失败 阳江市三⼈⾏冷冻⾷品有限公司');

$pdf->Output(__DIR__ . '/font_test.pdf', 'F');
