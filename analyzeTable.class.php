<?php

/**
 *  AnalyzeTable v1.0
 *
 *  Config:
 *      @self::$TABLE_CAPTION           - шапка таблицы
 *      @self::$TABLE_TR                - tr tag
 *      @self::$TABLE_TD                - ts tag
 *
 *  Usage:
 *       $o = new AnalyzeTable();
 *       $html = $o->getTable($arr);
 *       echo $html;
 *
 *  @dev by Stas Oreshin & Kir Chernenko
 */

class analyzeTable
{
    static $TABLE_CAPTION = '<table width="1200" border="1" align="center" cellpadding="4" cellspacing="0">';
    static $TABLE_TR = '<tr';
    static $TABLE_TD = '<td';
    
    public $maxIn = 0;
    public $result = [];
    public $curLevel = 0;
    
     
    public function getLevel($arr, $sort = [], $level = -1)
    {

        $level++;
        
        if(isset($sort['asc']) && ($sort['asc']==-1 || $sort['asc']>=$level) && is_array($arr))
            ksort($arr);
        if(isset($sort['desc']) && ($sort['desc']==-1 || $sort['desc']>=$level) && is_array($arr))
            krsort($arr);
        
        foreach($arr as $k => $v)
        {
            if(is_array($v))
            {
                list($newLevel, $sortedInner) = $this->getLevel($v, $sort, $level);
                //ksort($sortedInner);
                $arr[$k] = $sortedInner;
                
                if($this->maxIn<$newLevel)
                    $this->maxIn = $newLevel;
            }
            
        }
        
        return [$level, $arr];
    }
    
    private function makeTable($incEl, &$result, $curLevel = 0)
    {
        //Vars
        $tempMiddleRes = [];
        $tempEndpointRes = [];
        $sumRowCount = 0;
        $haveEndpointRec = false;        
        $firstTr = true;
        
        
        foreach($incEl as $k => $v)
        {
            if(!empty($v) && is_array($v))
            {
                if(!$firstTr)
                    $tempMiddleRes[] = self::$TABLE_TR.'>';
                $innerResult = [];
                $innerResult[] = '';
                $innerRowCount = $this->makeTable($v, $innerResult, ($curLevel+1));
                $innerResult[0] = self::$TABLE_TD." rowspan=\"$innerRowCount\">$k</td>
                ";
                
                $tempMiddleRes = array_merge($tempMiddleRes, $innerResult);
                
                $sumRowCount += $innerRowCount;
                if(!$firstTr)
                    $tempMiddleRes[] = '</tr>
                    ';
            }
            else
            {
                if(!$firstTr)
                    $tempEndpointRes[] = self::$TABLE_TR.'>';
                    
                $haveEndpointRec = true;
                $colspan = ($this->maxIn-$curLevel)>0 ? ' colspan="'.($this->maxIn-$curLevel+1).'"' : '';
                if(is_array($v)) $v = '';
                    $tempEndpointRes[] = self::$TABLE_TD."$colspan>[$curLevel] $k => $v</td>
                    ";
                    
                if(!$firstTr)
                    $tempEndpointRes[] = '</tr>
                    ';
                $sumRowCount++;
                
            }
                
            $firstTr = false;
        }
        
        if($haveEndpointRec)
            $tempMiddleRes = array_merge($tempMiddleRes, $tempEndpointRes);
        
        $result = array_merge($result, ($tempMiddleRes));
        
        
        return $sumRowCount;        
    }
    
    public function getTable($arr, $sort = [])
    {
        list($level, $arr) = $this->getLevel($arr, $sort);
        $result = [];
        $this->makeTable($arr, $result, 0);

        $result = self::$TABLE_CAPTION.implode('', ($result)).'</table>';
        return $result;
    }
}

?>