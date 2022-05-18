# pdf

    该类继承 tcpdf， 又进一步提供了一些便捷的方法， 不需要html模板 和繁锁的代码计算
    字体默认采用songti,大小10.5

## 标题

参考 examples/example_h.php

`h(string $txt, int $level = 1, string $align = 'L')`

## 段落

参考 examples/example_p.php

`p(string $txt, int $indent = 4, float $lineSpace = 0.5, string $align = 'L')`

## 表格

参考 examples/example_table.php

`tableIter(float $w = 0, int $columns = 1, array $config = [])`

返回的是一个 `yied` 迭代器
通过 `$table->send([...])` 传递每行数据
```
    $table->send([
        'data' => [...]  //行数据
        'rowLine' => 2,  //行限制 可选
        'spans' => [8],    //行单元格合并 可选
        'align' => 'C',     //文本水平位置 可选 L C R
        'border' => 'LRT',  //定制化显示行边框 可选 LTRB  
    ])
```
