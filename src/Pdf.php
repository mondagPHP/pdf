<?php
declare(strict_types=1);

namespace Pdf;

/**
 * pdf
 */
class Pdf extends \TCPDF
{
    private $isMbStr;

    private $pdfOrientation;
    private $pdfFormat;
    private $pdfEncoding;
    private $pdfUnit;

    //页码样式
    private $footerFormat = [
        'prefix' => '页码：',
        'separator' => ' / ',
        'align' => 'C',
        'font' => [
            'size' => 11,
            'family' => 'ht',
        ],
    ];

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        $this->pdfOrientation = $orientation;
        $this->pdfUnit = $unit;
        $this->pdfFormat = $format;
        $this->pdfEncoding = $encoding;

        $this->AddFont('ht', '', __DIR__ . '/font/ht.php');
        $this->AddFont('tbht_r', '', __DIR__ . '/font/tbht_r.php');

        $this->setFont('tbht_r', '', 10.5);

    }

    /**
     * 设置 mb_*
     *
     * @param boolean $b
     * @return void
     */
    public function setMbStr(bool $b)
    {
        $this->isMbStr = $b;
    }

    /**
     * @param array $footerFormat
     */
    public function setFooterFormat(array $footerFormat): void
    {
        $this->footerFormat = array_merge($this->footer_margin, $footerFormat);
    }

    /**
     * 标题
     * @param string $txt
     * @param int $level 标题等级 1， 2， 3 ...
     * @param string $align 内容定位 'L'： 默认靠左， 'C'： 居中 'R': 靠右
     */
    public function h(string $txt, int $level = 1, string $align = 'L')
    {
        $size = 24;
        $diff = 2;
        $tempFontSize = $this->getFontSize();
        $this->setFontSize($size - $diff * $level);
        $this->Cell(0, 0, $txt, 0, 1, $align);
        $this->setFontSize($size - $diff * $level, $tempFontSize);
    }

    /**
     * 段落
     *
     * @param string $txt
     * @param integer $indent 缩进
     * @param float $lineSpace 行距
     * @param string $align 内容定位 'L'： 默认靠左， 'C'： 居中 'R': 靠右
     * @return void
     */
    public function p(string $txt, int $indent = 4, float $lineSpace = 0.5, string $align = 'L')
    {
        $this->setY($this->GetY());
        $maxW = $this->w - $this->lMargin - $this->rMargin;
        $txt = str_repeat(' ', $indent) . $txt;

        $curStr = '';
        $curW = 0;
        $isWrite = false;
        $type = 0;
        $this->setCellPadding(0);
        //$this->setCellMargins(0, 0 ,0 ,0);
        $this->setCellPaddings(0, 0, 0, $lineSpace);
        for ($i = 0, $txtLen = $this->strLen($txt); $i < $txtLen; $i++) {
            $c = $this->subStr($txt, $i, 1);
            $cl = $this->GetStringWidth($c);
            if ($c === "\n") {
                $isWrite = true;
                $type = 0;
            } elseif ($curW + $cl > $maxW) {
                $isWrite = true;
                $type = 1;
            }
            if ($isWrite) {
                $isWrite = false;
                $stretch = 0;
                if ($maxW - $curW < 2) {
                    $stretch = 4;
                }
                //autoPage
                if ($this->y > ($this->h - $this->FontSize - $lineSpace - $this->bMargin)) {
                    $this->AddPage($this->pdfOrientation, $this->pdfFormat, true);
                }
                $this->Cell(0, 0, $curStr, 0, 1, $align, false, '', $stretch);

                switch ($type) {
                    case 1:
                        $curStr = $c;
                        $curW = $cl;
                        break;
                    default:
                        $curStr = '';
                        $curW = 0;
                        break;
                }
                continue;
            }
            $curStr .= $c;
            $curW += $cl;
        }
        if (! empty($curStr)) {
            $this->Cell(0, 0, $curStr, 0, 1, $align);
        }
    }

    /**
     * 表格
     * @param float|int $w 0：默认除去左右页面边距宽度
     * @param int $columns 表格有多少列
     * @param array $config
     *              <ul>
     *              <li>'columnWidths' => [  //每一列的占比宽度比
     *                  0 => ['width' => 0.1],
     *                  ...
     *              ],</li>
     *              <li>'rowLine' => 2,   //默认值： 1 ，限制每列的行，内容超过 锁限制的行会被截取 货用'...' 代替</li>
     *              <li>'isFixLine' => true, // 默认值：false ，是否每行固定行数，</li>
     *              <li>'tdPadding' => 1.5 //默认值： 0 ，每个单元格子上下左右间距 ，还可以传数组 eg:['B'=>1] 设置下边距 1mm, L:左，T：上，R：右，B：下</li>
     *              <li>'vAlign' => 'T' //默认值'C' 单元格 内容垂直位置， T：垂直居上， 'C' 垂直居中</li>
     *              <li>'format' => 'none' //默认值 'ellipsis' 单元格超出行内容 截断还是省略</li>
     *              </ul>
     * @return \Generator
     */
    public function tableIter(float $w = 0, int $columns = 1, array $config = [])
    {
        $tableWith = $w;
        if ($w == 0) {
            $tableWith = $this->w - $this->lMargin - $this->rMargin;
        }
        $columnWidthArr = [];
        //calculate each td with  :%
        for ($i = 0; $i < $columns; $i ++) {
            if (isset($config['columnWidths'][$i]['width'])) {
                $columnWidthArr[$i] = $tableWith * $config['columnWidths'][$i]['width'];
            }
        }
        $startX = $this->GetX();
        $startY = $this->GetY();
        $tdPadding = $config['tdPadding'] ?? 0.5;
        $tdPaddingL = 0;
        $tdPaddingT = 0;
        $tdPaddingR = 0;
        $tdPaddingB = 0;
        if (is_array($tdPadding)) {
            $tdPaddingL = $tdPadding['L'] ?? $tdPaddingL;
            $tdPaddingT = $tdPadding['T'] ?? $tdPaddingT;
            $tdPaddingR = $tdPadding['R'] ?? $tdPaddingR;
            $tdPaddingB = $tdPadding['B'] ?? $tdPaddingB;
        } else {
            $tdPaddingL = $tdPadding;
            $tdPaddingT = $tdPadding;
            $tdPaddingR = $tdPadding;
            $tdPaddingB = $tdPadding;
        }
        $lineSpace = 0.5;
        $vAlign = $config['vAlign'] ?? 'C';
        $format = $config['format'] ?? 'ellipsis';

        $this->setCellMargins(0, 0, 0, 0);
        $this->setCellPadding(0);

        $configRowLine = $config['rowLine'] ?? 1;
        $configIsFixLine = $config['isFixLine'] ?? false;

        $rows = 0;

        while (true) {
            $sendData = yield;
            $cData = [];
            $maxLine = 0;
            $rowData = $sendData['data'] ?? [];
            $configRowLine = $sendData['rowLine'] ?? $configRowLine;
            $align = $sendData['align'] ?? 'L';
            $border = $sendData['border'] ?? 'LB';
            $currentFormat = $sendData['format'] ?? $format;
            $stretchDiff = $sendData['stretchDiff'] ?? $this->FontSize;

            //spans
            $currentColumnWidthArr = $columnWidthArr;
            if (isset($sendData['spans']) && is_array($sendData['spans'])) {
                $currentColumnWidthArr = $this->tableCalculateSpansColumn($columnWidthArr, $sendData['spans']);
            }
            $columnCount = count($currentColumnWidthArr);

            foreach ($rowData as $k => $data) {
                if (! isset($currentColumnWidthArr[$k])) {
                    continue;
                }
                $maxTdW = $currentColumnWidthArr[$k];
                $lines = $this->tableCalculateTdLines($maxTdW - $tdPaddingL - $tdPaddingR, $data, $configRowLine, $currentFormat);
                if (count($lines) > $maxLine) {
                    $maxLine = count($lines);
                }
                $cData[$k] = $lines;
            }

            $maxLine = $configIsFixLine ? $configRowLine : $maxLine;
            $rowX = $startX;

            $rowH = $maxLine * $this->FontSize + ($maxLine - 1) * $lineSpace + $tdPaddingT + $tdPaddingB;

            //autoPage
            if ($this->y > ($this->h - $rowH - $this->bMargin)) {
                $this->AddPage($this->pdfOrientation, $this->pdfFormat, true);
                $this->setY($this->tMargin, false);
                $startY = $this->GetY();
                $rows = 0;
            }

            foreach ($currentColumnWidthArr as $cIdx => $tdW) {
                $lines = $cData[$cIdx] ?? [];
                $curY = $startY;
                $this->setX($rowX);
                $this->setY($curY, false);

                $cellBorder = 'R';
                if ($rows === 0) {
                    $cellBorder = 'TR';
                }
                $this->Cell($tdW, $rowH, '', $border === 0 ? 0 : $cellBorder);

                $this->setX($rowX + $tdPaddingL);

                if ($vAlign == 'T') {
                    $curY += $tdPaddingT;
                } else {
                    $curY += ($rowH - ($this->FontSize + $lineSpace) * count($lines)) / 2;
                }

                $strW = $tdW - $tdPaddingL - $tdPaddingR;
                foreach ($lines as $lineIdx => $lineTxt) {
                    $this->setX($rowX + $tdPaddingL);
                    if ($lineIdx !== 0) {
                        $curY += $this->FontSize + $lineSpace;
                    }

                    $this->setY($curY, false);
                    $stretch = 0;
                    if ($strW - $this->GetStringWidth($lineTxt) < $stretchDiff) {
                        $stretch = 4;
                    }
                    $this->Cell($strW, $this->FontSize, $lineTxt, 0, 1, $align, 0, '', $stretch);
                }
                $rowX += $tdW;
            }
            $this->setX($startX);
            $this->setY($startY);
            $this->Cell($tableWith, $rowH, '', $border);
            $rows++;
            $startY += $rowH;
            $this->setY($startY);
        }
    }

    public function subStr($s, $start, ?int $offset, string $encode = 'UTF-8')
    {
        if ($this->isMbStr) {
            return mb_substr($s, $start, $offset, $encode);
        }
        return substr($s, $start, $offset);
    }

    public function strLen($s)
    {
        if ($this->isMbStr) {
            return mb_strlen($s);
        }
        return strlen($s);
    }

    private function tableCalculateSpansColumn(array $columnWidthArr, array $spans): array
    {
        $newWidthArr = [];
        $totalCol = count($columnWidthArr);
        $idx = 0;
        foreach ($spans as $span) {
            $w = 0;
            for ($i = 0; $i < (int)$span; $i++) {
                if ($idx === $totalCol) {
                    return $newWidthArr;
                }
                $w += $columnWidthArr[$idx] ?? 0;
                $idx++;
            }
            $newWidthArr[] = $w;
        }
        for (;$idx < $totalCol; $idx++) {
            $newWidthArr[] = $columnWidthArr[$idx];
        }
        return $newWidthArr;
    }

    private function tableCalculateTdLines(float $w, $txt, $limitLines = 1, $format = 'ellipsis'): array
    {
        $txt = (string)$txt;
        $ew = $this->GetStringWidth('...');
        $l = $this->strLen($txt);
        $maxW = $w;

        $lines = [];
        $curW = 0;
        if ($format === 'ellipsis' && $limitLines === 1) {
            $curW = $ew;
        }
        $curS = '';
        for ($i = 0; $i < $l; $i++) {
            $uniChar = $this->subStr($txt, $i, 1);
            $uniLen = $this->GetStringWidth($uniChar) ?: 0;
            if ($curW + $uniLen > $maxW) {
                if (count($lines) === ($limitLines - 1) && $format !== 'fill') {
                    if ($format === 'ellipsis') {
                        $curS .= '...';
                    }
                    array_push($lines, $curS);
                    $curS = '';
                    break;
                } elseif (count($lines) < $limitLines || $format === 'fill') {
                    array_push($lines, $curS);
                    $curW = 0;
                    if ($format === 'ellipsis') {
                        $curW = $ew;
                    }
                    $curS = '';
                } else {
                    break;
                }
            }
            $curW += (float)$uniLen;
            $curS .= $this->subStr($txt, $i, 1);
        }
        if (! empty($curS)) {
            array_push($lines, $curS);
        }
        return $lines;
    }

    /**
     * 自定义页脚
     */
    public function Footer(): void
    {
        $this->SetY(-10);
        $font = $this->footerFormat['font'];
        $this->SetFont($font['family'] ?? 'ht', '', $font['size'] ?? 11);
        $prefix = $this->footerFormat['prefix'] ?? '页码：';
        $separator = $this->footerFormat['separator'] ?? ' / ';
        $align = $this->footerFormat['align'] ?? 'C';
        $this->Cell(0, 0, $prefix . $this->getAliasNumPage() . $separator . $this->getAliasNbPages(), 0, 1, $align);
    }
}
