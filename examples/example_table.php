<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$pdf = new \Pdf\Pdf();

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setAutoPageBreak(false, 10);

$pdf->setMbStr(true);
$pdf->setHeaderMargin(10);
$pdf->AddPage();
$pdf->AddFont('tbht_r', '');

$pdf->setFont('tbht_r', '', 10.5);

$pdf->setFontSize(8);

//有边框表格
$table1 = $pdf->tableIter(100, 2, [
    'columnWidths' => [
        0 => ['width' => 0.7],
        1 => ['width' => 0.3],

    ],
    'rowLine' => 2,
    'tdPadding' => 1
]);
$table1->send([
    'data' => ['商品名称', '单价'],
    'rowLine' => 1,
    'align' => 'C',
]);
$table1Data = [
    ['身略号sdf身略号身略号身略号身略号身略号身略号身略号身略号sdf身略号身略号身略号身略号身略号身略号身略号', '10000'],
    ['用户签名', '10000']
];
foreach ($table1Data as $row) {
    $table1->send([
        'data' => $row,
        'rowLine' => 2,
    ]);
}


$pdf->Ln(6);

//有边框表格
$pdfTableIter = $pdf->tableIter(0, 8, [
    'columnWidths' => [
        0 => ['width' => 0.1],
        1 => ['width' => 0.3],
        2 => ['width' => 0.1],
        3 => ['width' => 0.1],
        4 => ['width' => 0.1],
        5 => ['width' => 0.1],
        6 => ['width' => 0.1],
        7 => ['width' => 0.1],
    ],
    'rowLine' => 2,
    'isFixLine' => true,
    'tdPadding' => 1.5
]);
$pdfTableIter->send([
    'data' => ['序号', '商品名称', '规格型号', '单位', '数量', '单价', '金额', '备注'],
    'rowLine' => 1,
    'align' => 'C',
]);
$tableData = [
    [1, 'Hello world Hello world Hello world Hello world Hello world ', '240g', 'g', '10000', 1, '10000', ''],
    [2, '2[34sjl省略, 删节号, 省略符号, 身略号-j lslfjlssjsldfjlsjflslfsjlfls', '240g', 'g', '10000', 1, '10000', ''],
    [3, 'Hello省略, 删节号, 省略符号, 身略号sdf身略号身略号身略号身略号身略号身略号身略号', '240g', 'g', '10000', 1, '10000', ''],
    [4, '身略号sdf身略号身略号身略号身略号身略号身略号身略号身略号sdf身略号身略号身略号身略号身略号身略号身略号', '240g', 'g', '10000', 1, '10000', ''],
    [6, '用户签名', '240g', 'g', '10000', 1, '10000', '']
];
foreach ($tableData as $row) {
    $pdfTableIter->send([
        'data' => $row,
        'rowLine' => 2
    ]);
}
$pdfTableIter->send([
    'data' => ['以无更多数据'],
    'rowLine' => 2,
    'spans' => [8],
    'align' => 'C',
]);
$pdfTableIter->send([
    'data' => ['span1', 'span2'],
    'rowLine' => 2,
    'spans' => [6, 2],
    'align' => 'C',
]);

//无边框表格 内容定位
$pdf->Ln(5);
$table2 = $pdf->tableIter(0, 3, [
    'columnWidths' => [
        0 => ['width' => 0.073],
        1 => ['width' => 0.577],
        2 => ['width' => 0.35],
    ],
    'tdPadding' => [
        'L' => 0,
        'R' => 1,
        'T' => 0.5,
        'B' => 0.5
    ],
    'vAlign' => 'T',
    'format' => 'none'
]);
$table2->send([
    'data' => ['购货方名称（全称）: aaaaaazaaaaaa', '送货单号: DG202205161234'],
    'spans' => [2, 1],
    'border' => 0
]);
$table2->send([
    'data' => ['收货地址: ', '复读机啊看风景扣篮大赛荆防颗粒复读机啊看风景扣篮大复读机啊看风景扣篮复读机啊看风景扣篮大赛荆防颗粒复读机啊看风景扣篮大复读机啊看风景扣篮', '订 单 号: Y202204281234'],
    'rowLine' => 2,
    'border' => 0
]);
$table2->send([
    'data' => ['收货人电话: 测试测试 13113113111', '日    期: 2022年05月16日'],
    'spans' => [2, 1],
    'border' => 0
]);

$pdf->Ln(2);

//autoPage
$table3 = $pdf->tableIter(0, 3, [
    'columnWidths' => [
        ['width' => 0.1],
        ['width' => 0.5],
        ['width' => 0.4]],
    'rowLine' => 3
]);
for($i = 0; $i < 35; $i++) {
    $table3->send([
        'data' => [$i, str_repeat('数据', rand(1, 20)), str_repeat('数据', rand(1, 20))],
    ]);
}


$pdf->Output(__DIR__ . '/example_table.pdf', 'F');