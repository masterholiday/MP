<?php
class Catalog {

    public function getShortList() {
        $t = new Obj_DBTable_ServiceCategories();
        $ar = $t->getShortList();
        if (count($ar) < 1) return array();
        $arr = array();
        $i = 0;
        $curID = 0;
        foreach ($ar as $v) {

            if ($curID != $v->CatID) {
                $curID = $v->CatID;
                $arr[] = new stdClass();
                $i = count($arr) - 1;
                $arr[$i]->ID = $v->CatID;
                $arr[$i]->Category = $v->CategoryName;
				//$arr[$i]->Alias = $v->alias;
                $arr[$i]->subcategories = array();
                $arr[$i]->total = 0;
            }
            $arr[$i]->subcategories[] = new stdClass();
            $j = count($arr[$i]->subcategories) - 1;
            $arr[$i]->subcategories[$j]->ID = $v->SubID;
            $arr[$i]->subcategories[$j]->Category = $v->SubcategoryName;
			
			$arr[$i]->subcategories[$j]->Alias = $t->getAlias($v->SubID);
            $arr[$i]->subcategories[$j]->total = (int) $v->total;
            $arr[$i]->total += $v->total;
        }
        return $arr;
    }

    public function getCategorySeo($id, $city) {
        $t = new Obj_DBTable_ServiceCategories();
        return $t->getCategorySeo($id, $city);
    }


}
?>
