<?php
class Address {

    public function getCatalogCityInfo($city) {
        $t = new Obj_DBTable_Citys();
        return $t->getCityAndCountry($city);
    }

}
?>
