# xmq
AnalyzeTable<br />
<br />
Реализация трансформирования многомерного массива неопределенной структуры в HTML таблицу<br />
<br />
  Пример использования:<br />
       $o = new AnalyzeTable();<br />
       $html = $o->getTable($arr);<br />
       echo $html;<br />
<br />
  Конфиг:<br />
      @self::$TABLE_CAPTION           - шапка таблицы<br />
      @self::$TABLE_TR                - tr tag<br />
      @self::$TABLE_TD                - ts tag<br />