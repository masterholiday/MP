<?php
class Search {

    private $iventorservicestable;
    private $servicesearchesrable;


    public function findEventors($city, $category, $min, $max, $uid) {
        if (!$this->iventorservicestable) $this->iventorservicestable = new Obj_DBTable_IventorServices();
        return $this->iventorservicestable->findEventors($city, $category, $min, $max, $uid);
    }

    public function addSearch($date, $city, $aname, $user, $iscat, $phone) {
        $t = new Obj_DBTable_Searches();
        $arr = array(
            'Date' => date("Y-m-d", $date),
            'CityId' => intval($city),
            'ActivityName' => $aname,
            'UserId' => $user,
            'searchPhoneNumber' => $phone,
            'SearchDate' => new Zend_Db_Expr('NOW()'),
            'categorySearch' => intval($iscat)
        );
        return $t->insert($arr);
    }

    public function addSearchService($searchID, $catID, $evID, $min, $max, $info, $uid) {
        if (!$this->servicesearchesrable) $this->servicesearchesrable = new Obj_DBTable_ServicesSearches();
        $arr = array(
            'SearchID' => (int)$searchID,
            'CategoriesId' => (int)$catID,
            'EventorID' => (int)$evID,
            'minPrice' => (int)$min,
            'maxPrice' => (int)$max,
            'Info' => $info,
            'UserID' => (int) $uid,
            'SearchDate' => new Zend_Db_Expr('NOW()')
        );
        return $this->servicesearchesrable->insert($arr);
    }

    public function getClientSearchesCount($uid, $type) {
        if ($type == 'complex') {
            $t = new Obj_DBTable_Searches();
            return $t->getClientSearchesCount($uid, $type == 'complex' ? 0 : 1);
        }
        else {
            $t = new Obj_DBTable_Calls();
            return $t->getClientSearchesCount($uid);
        }
    }

    public function getEventorSearchesCount($eid, $type) {
        if ($type == 'complex') {
            $t = new Obj_DBTable_Searches();
            return $t->getEventorSearchesCount($eid, $type == 'complex' ? 0 : 1);
        }
        else {
            $t = new Obj_DBTable_Calls();
            return $t->getEventorSearchesCount($eid);
        }
    }

    public function deleteClientServiceSearch($sid, $cid, $uid) {
        $t = new Obj_DBTable_ServicesSearches();
        $t->update(array('UserDeleted' => 1), 'SearchID = '.intval($sid).' AND CategoriesId = '.intval($cid).' AND UserID = '.intval($uid));
    }

    public function deleteClientSearch($sid, $uid) {
        $t = new Obj_DBTable_Searches();
        $t->update(array('UserDeleted' => 1), 'Id = '.intval($sid).' AND UserId = '.intval($uid));
    }

    public function deleteEventorSearch($sid, $uid) {
        $t = new Obj_DBTable_ServicesSearches();
        $t->update(array('EventorDeleted' => 1), 'Id = '.intval($sid).' AND EventorID = '.intval($uid));
    }

    public function deleteEventorSearch2($sid, $uid) {
        $t = new Obj_DBTable_Calls();
        $t->update(array('EventorDeleted' => 1), 'Id = '.intval($sid).' AND EventorID = '.intval($uid));
    }

    public function deleteClientCatalogSearch($id, $uid) {
        $t = new Obj_DBTable_Calls();
        $t->update(array('UserDeleted' => 1), 'ID = '.intval($id).' AND UserID = '.intval($uid));
    }

    private function getClientCatalogSearches($uid, $page, $limit) {
        $t = new Obj_DBTable_Calls();
        return $t->getClientSearches($uid, $page, $limit);
    }

    private function getEventorCatalogSearches($uid, $page, $limit) {
        $t = new Obj_DBTable_Calls();
        return $t->getEventorSearches($uid, $page, $limit);
    }

    public function getClientSearches($uid, $type, $page, $limit) {
        if ($type != 'complex') {
            return $this->getClientCatalogSearches($uid, $page, $limit);
        }
        $t = new Obj_DBTable_Searches();
        $t2 = new Obj_DBTable_ServicesSearches();
        $list = $t->getClientSearches($uid, $type == 'complex' ? 0 : 1, $page, $limit);
        foreach ($list as $k => $l) {
            $services = $t2->getSearchServices($l->Id);
            $subcatid = 0;
            $list[$k]->services = array();
            foreach ($services as $s) {
                if ($s->SubcategoryID != $subcatid) {
                    $subcatid = $s->SubcategoryID;
                    $list[$k]->services[] = new stdClass();
                    $i = count($list[$k]->services) - 1;
                    $list[$k]->services[$i]->Category = $s->Category;
                    $list[$k]->services[$i]->Subcategory = $s->Subcategory;
                    $list[$k]->services[$i]->SubcategoryID = $s->SubcategoryID;
                    $list[$k]->services[$i]->Date = $l->Date;
                    $list[$k]->services[$i]->City = $l->CityName;
                    $list[$k]->services[$i]->Country = $l->CountryName;
                    $list[$k]->services[$i]->Currency = $l->CountryID == 9908 ? "грн" : "руб";
                    $list[$k]->services[$i]->Min = $s->minPrice;
                    $list[$k]->services[$i]->Max = $s->maxPrice;
                    $list[$k]->services[$i]->ID = $s->Id;
                    $list[$k]->services[$i]->eventors = array();
                }
                if (intval($s->EventorID) < 1) continue;
                $i = count($list[$k]->services) - 1;
                $ev = new stdClass();
                $ev->ID = $s->EventorID;
                $ev->CompanyName = $s->CompanyName;
                $ev->CompanyPhone = $s->CompanyPhone;
                $ev->Website = $s->Website;
                $ev->Image = $s->Image;
                $ev->Description = $s->Description;
                $ev->UserID = $s->EventorUserID;
                $list[$k]->services[$i]->eventors[] = $ev;
            }
        }
        return $list;
    }


    public function getEventorSearches($uid, $type, $page, $limit) {
        if ($type != 'complex') {
            return $this->getEventorCatalogSearches($uid, $page, $limit);
        }
        $t = new Obj_DBTable_Searches();
        $list = $t->getEventorSearches($uid, $type == 'complex' ? 0 : 1, $page, $limit);
        return $list;
    }

    public function getCatalogSearchResultCount($category, $city, $min, $max) {
        if ($category == 0) return 0;
        $t = new Obj_DBTable_IventorServices();
        return $t->getCategorySearchesCount($category, $city, $min, $max);
    }

    public function getCatalogSearchResult($category, $city, $min, $max, $uid, $rows) {
        if ($category == 0) return 0;
        $t = new Obj_DBTable_IventorServices();
        $res = $t->getCategorySearches($category, $city, $min, $max, $uid);
        $result = new stdClass();
        $result->pages = array();
        $page = 0;
        $row = 0;
        $rowfill = 0;
        //print_r($res);die();
        foreach ($res as $r) {
            //$r->CompanyName = mb_substr($r->CompanyName, 0, 35, "utf-8");
            if ($rowfill >= 6) {
                $row++;
                $rowfill = 0;
            }
            if ($row >= $rows) {
                $page++;
                $row = 0;
            }
            if ($r->Premium == 1) {
                $rowfill += 2;
            }
            else {
                $rowfill++;
            }
            $r->page = $page;
            if (!isset($result->pages[$page])) $result->pages[$page] = array();
            $result->pages[$page][] = $r;
        }
        return $result;
    }


    public function getClientStarredCount($uid) {
        $t = new Obj_DBTable_Starred();
        return $t->getUserStarredCount($uid);
    }

    public function getClientStarredResult($uid, $page, $perpage) {
        $t = new Obj_DBTable_Starred();
        return $t->getUserStarred($uid, $page, $perpage);
    }

}
?>
