<?php
class Obj_DBTable_Missing extends Zend_Db_Table_Abstract
{
	protected $_name = 'missing';
	protected $_primary = 'Id';
    private $monthes = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	 
	public function changeDate($d, $id)
	{
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('IventorId = ?', $id)->where('Date = ?', $d);
        if ($select->query()->fetchObject()->c > 0) {
            $this->delete("`Date` = ".$this->_db->quote($d)." AND IventorId = ".intval($id));
        }
        else {
            $this->insert(array("IventorId" => $id, "Date" => $d));
        }
	}

    public function getIventorDates($id) {
        $select = $this->select()->from($this)->where('IventorId = ?', $id)->order('Date')->where('Date >= NOW()');
        return $select->query()->fetchAll(PDO::FETCH_CLASS);
    }



    public function makeNice($dates) {
        if (count($dates) == 0) return "";
        $arr = array();
        foreach ($dates as $d) {
            $t = strtotime($d->Date);
            $y = intval(date("Y", $t));
            $m = intval(date("m", $t));
            $d = intval(date("d", $t));
            if (!isset($arr[$y])) $arr[$y] = array();
            if (!isset($arr[$y][$m])) $arr[$y][$m] = array();
            $arr[$y][$m][$d] = 1;
        }
        $arr2 = array();
        foreach ($arr as $y => $mm) {
            foreach ($mm as $m => $dd) {
                if (!isset($arr2[$y."-".$m])) $arr2[$y."-".$m] = array();
                foreach ($dd as $i => $k) {
                    if (count($arr2[$y."-".$m]) == 0) {
                        $arr2[$y."-".$m][] = $i;
                        continue;
                    }
                    $dds = explode("-", $arr2[$y."-".$m][count($arr2[$y."-".$m]) - 1]);
                    if ($dds[count($dds) - 1] == $i - 1) {
                        $arr2[$y."-".$m][count($arr2[$y."-".$m]) - 1] = $dds[0]."-".$i;
                    }
                    else {
                        $arr2[$y."-".$m][] = $i;
                    }
                }
            }
        }
        $arr3 = array();
        foreach ($arr2 as $ym => $d) {
            $s = implode(", ", $d);
            $s = str_replace("-", " по ", $s);
            $yms = explode("-", $ym);
            $s .= " ".$this->monthes[intval($yms[1])] . " " . $yms[0];
            $arr3[] = "<span>".$s."</span>";
        }
        return implode("", $arr3);
    }

}
