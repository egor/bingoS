<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Catalog extends Main_Abstract implements Main_Interface {

    private $activePage = false;
    private $activeHeader = false;
    private $catalogOptions = array();
    private $filter = array(
              // Имя поля => параметры или просто заголовок который будет первым вываливается в списке
              'brand' => 'Бренд',
              'cost' => array(
              'title' => 'Цена',
              'type' => 'range' // Для диапазонных значений
              ),
    );
    private $сountGoodsRange = array(9, 36, 72);

    public function factory() {
        try {
            $database = Zend_Db::factory($this->_config['databaseOld']['adapter'], $this->_config['databaseOld']['params']);
            //$this->dbProfiler = $database->getProfiler($this->isUseDbProfiler);
            $database->getConnection();
        } catch (Zend_Db_Adapter_Exception $e) {
            //throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
            return true;
        } catch (Zend_Exception $e) {
            //throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
            return true;
        }

        $oldArticle = $database->fetchAll("SELECT * FROM `catalog` WHERE `link` = '".end($this->url)."' AND `visibility` = '1' AND `type` = 'page'");

        if ($oldArticle) {
            $newArticle = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `artikul` = '".$oldArticle[0]['artikul']."' AND `type` = 'page'");

            if (!$newArticle) {
                return true;
            }

            $item = $this->dataTreeManager($newArticle['id']);

            if (empty ($item) || !isset ($item['links']) || $item['links'] == '') {
                return true;
            }

            header("HTTP/1.1 301 Moved Permanently");
            header("Location: /catalog/" . $item['links']);
            //var_dump($this->dataTreeManager($newArticle['id']));
            exit();
        }



        return true;
    }

    /*
      Функкция main
      В этой функции происходит загрузка метатегов, определение активного раздела, вывод разделов, подразделов, списка товаров
      в зависимости от выбранного url

     */

    public function main() {

        $this->catalogOptions = $this->getConfig('catalog');

        if (count($this->url) > 0) {
            $level = 0;

            $url = end($this->url);

            // Если url заканчивается словом catalog - это корневой уровень каралога.
            // Если нет - загружаем активную страницу

            if ($url == 'catalog') {

                // Для корня каталога ищется метатеги в таблице page если есть ссылка в горизонтальном меню

                $this->activeHeader = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `href` = 'catalog'");

                if ($this->activeHeader) {
                    $this->setMetaTags($this->activeHeader);
                } else {
                    $meta['title'] = 'Каталог';
                    $meta['keywords'] = $meta['title'];
                    $meta['description'] = $meta['title'];
                    $meta['header'] = $meta['title'];
                    $meta['title'] = $meta['title'];
                    $this->setMetaTags($meta);
                }

                $metaBody = '';

                if (isset($this->activeHeader['body'])) {
                    $metaBody = $this->activeHeader['body'];
                }

                $this->tpl->assign(array('CATALOG_SECTION_BODY' => $metaBody));
            } elseif ($this->loadActivePage($url)) {
                if ($this->activePage) {

                    $level = $this->activePage['id'];
                }
            } else {
                return $this->error404();
            }

            //$this->setMetaTags($this->activePage);

            if ((isset($this->activePage['type']) && $this->activePage['type'] == 'section') || $level == 0) {

                if (is_numeric(($pageLevel = $this->loadSectionsItems($level)))) { // Загрузка разделов. Если в разделе обнаружатся страницы - выводим список страниц
                    $this->loadPagesItems($pageLevel); // Список страниц
                }
            } elseif ($this->activePage['type'] == 'page') { // Подробная информация
                $this->loadDetail();
            }

            return true;
        }

        return false;
    }

    // loadActivePage() - Определяет активную страницу по url.

    protected function loadActivePage($href) {

        return ($this->activePage = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `href` = '$href'"));
    }

    // loadSectionsItems() - Выводит список разделов и подразделов. Если в списке разделов окажутся страницы - вернет уровень.

    protected function loadSectionsItems($id)
    {

        // Постраничный навигатор

        $start = 0;
        $navbar = $navTop = $navBot = '';
        $pagePagen = 1;
        $navTop = '';
        $navBot = '';
        $pagenHref = 'catalog';

        if ($id == 0) {
            $h1AdminButtonOptions = array('section' => array('url' => '/admin/addcatsection/' . $id, 'text' => 'Раздел'),
            //  'page' => array('url' => '/admin/addcatpage/' . $id, 'text' => 'Товар')
            );
        } else {

            if ($this->activePage['level'] == '0') {
                $h1AdminButtonOptions = array('section' => array('url' => '/admin/addcatsection/' . $id, 'text' => 'Раздел'),
                    'page' => array('url' => '/admin/addcatpage/' . $id, 'text' => 'Товар')
                );
            } else {
                $h1AdminButtonOptions = array('page' => array('url' => '/admin/addcatpage/' . $id, 'text' => 'Товар')
                );
            }

        }



        $catalogPageLength = $this->settings['num_catalog'];

        $visibleQuery = '';

        if (!$this->_isAdmin()) {
            $visibleQuery = " AND `visibility` = '1'";
        }

        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `catalog` WHERE `level` = '$id' $visibleQuery");

        if (($dataTree = $this->dataTreeManager($id))) {
            $pagenHref .= '/' . $dataTree['links'];

            // Загрузка паффиндера

            $dataTreeLength = count($dataTree['names']);
            //	$this->setWay('Каталог', '/catalog/');
            if ($dataTreeLength > 0) {
                //var_dump($dataTree);
                for ($i = 0; $i < $dataTreeLength; $i++) {
                    $url = '/catalog';
                    if ($i != ($dataTreeLength - 1)) {
                        $url .= '/' . $dataTree['linksArr'][$i];
                    } else {
                        $url = null;
                    }

                    $this->setWay($dataTree['names'][$i], $url);
                }
            }
        }



        if ($count > 0) {
            if ($count > $catalogPageLength) {
                if (isset($this->getParam['page'])) {
                    $pagePagen = (int) $this->getParam['page'];
                    $start = $catalogPageLength * $pagePagen - $catalogPageLength;

                    if ($start > $count) {
                        $start = 0;
                    }
                }

                $pagenParams = '';

                if (!empty($brandName)) {
                    $pagenParams = "?brand=$brandName";
                }

                $navbar = $this->loadPaginator((int) ceil($count / $catalogPageLength), (int) $pagePagen, $this->basePath . $pagenHref . $pagenParams);

                if ($navbar) {
                    $navTop = '<div class="pager2">' . $navbar . '</div>';
                    $navBot = $navTop;
                }
            }
        }



        $sql = "SELECT *FROM `catalog` WHERE `level` = '$id' $visibleQuery ORDER BY `position` ,`name` LIMIT " . $start . ", " . $this->settings['num_catalog'];

        $sectionItems = $this->db->fetchAll($sql);

        if (isset($sectionItems[0]['type'])) {
            if ($sectionItems[0]['type'] == 'page') {
                return $sectionItems[0]['level'];
            } elseif ($sectionItems[0]['type'] == 'section') {
                unset($h1AdminButtonOptions['page']);
            }
        }


        $this->setH1Admin('/admin/editcatsection/' . $id, '/admin/deletecatsection/' . $id, $h1AdminButtonOptions);

        $this->tpl->define_dynamic('_catalog_section_list_body', 'catalog.tpl');
        $this->tpl->define_dynamic('catalog_section_list_body', '_catalog_section_list_body');
        $this->tpl->define_dynamic('catalog_section_list_empty', 'catalog_section_list_body');
        $this->tpl->define_dynamic('catalog_section_list', 'catalog_section_list_body');
        $this->tpl->define_dynamic('catalog_section_list_image', 'catalog_section_list_body');




        if (count($sectionItems) > 0) {

            if ($this->activePage) {
                //$this->activePage['header'] .= '<p> ' . $this->getAdminAdd('catsection', $this->activePage['id']) . '</p>';
                $this->setMetaTags($this->activePage);
            }

            $this->tpl->parse('CATALOG_SECTION_LIST_EMPTY', 'null');
            $adminClassName = '';
            if ($this->_isAdmin()) {
                $adminClassName = 'plashka-admin-button';
            }

            foreach ($sectionItems as $sectionItem) {

                $url = '/' . $pagenHref . '/' . $sectionItem['href'];


                $body = $sectionItem['body'];


                //$name = (!empty($sectionItem['header1']) ? $sectionItem['header1'] : $sectionItem['name']);

                $name = $sectionItem['name'];

                $isSection = ($sectionItem['level'] == '0');

                $pic = '';

                if (is_file(PATH . 'img/catalog/section/' . $sectionItem['pic'])) {
                    $pic = '<a href="' . $url . '" title="' . $sectionItem['title'] . '"><img class="float_left" src="/img/catalog/section/' . $sectionItem['pic'] . '" width="145" height="107" alt="' . $sectionItem['title'] . '" title="' . $sectionItem['title'] . '"/></a>';
                }


                $this->setAdminCatalogButtons('/admin/editcatsection/' . $sectionItem['id'], '/admin/deletecatsection/' . $sectionItem['id']
                        , '/admin/changecatsection/' . $sectionItem['id']
                        , '/admin/catalogoptions/' . $sectionItem['id']);

                if ($sectionItem['level'] != '0') {

                    $this->setAdminButtons('/admin/editcatsection/' . $sectionItem['id'], '/admin/deletecatsection/' . $sectionItem['id']
                        , '/admin/changecatsection/' . $sectionItem['id']
                        , '/admin/catalogoptions/' . $sectionItem['id']);
                }




                $this->tpl->assign(array(
                    'ADMIM_CLASS_NAME' => $adminClassName, //$this->getAdminEdit('catsection', $sectionItem['id'], true, $isSection, $isSection),
                    'CATALOG_SECTION_LIST_NAME' => $name,
                    'CATALOG_SECTION_LIST_PIC' => $pic,
                    'CATALOG_SECTION_LIST_HREF' => $url,
                    'CATALOG_SECTION_LIST_PREVIEW' => (!empty($sectionItem['preview']) ? $sectionItem['preview'] : '')
                ));

                if (isset($sectionItem['pic']) && is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $sectionItem['pic'])) {
                    $this->tpl->parse('CATALOG_SECTION_LIST_IMAGE', 'catalog_section_list_image');
                } else {
                    $this->tpl->parse('CATALOG_SECTION_LIST_IMAGE', 'null');
                    $pic = '';
                }
                $this->tpl->parse('CATALOG_SECTION_LIST', '.catalog_section_list');
            }
            $this->tpl->assign(array('TOP_NAV_BAR' => $navTop, 'BOTT_NAV_BAR' => $navBot));
        } else {

            if (!$this->activePage) {
                $this->setMetaTags($this->activeHeader);
            } else {
                //  $this->activePage['header'] .= '<p> ' . $this->getAdminAdd('catsection', $this->activePage['id']) . $this->getAdminAdd('catpage', $this->activePage['id']) . '</p>';
                $this->setMetaTags($this->activePage);
            }

            $this->tpl->parse('CATALOG_SECTION_LIST', 'null');
            $this->tpl->parse('CATALOG_SECTION_LIST_BODY', '.catalog_section_list_empty');
            $this->tpl->assign(array('TOP_NAV_BAR' => '', 'BOTT_NAV_BAR' => ''));
        }


        $this->tpl->parse('CONTENT', '.catalog_section_list_body');

        return true;
    }

    protected function loadPagesItems($level) {

        if (!is_numeric($level)) {
            return false;
        }

        if ($level <= 0) {
            return false;
        }

//        $this->activePage['header'] .= '<p> ' . $this->getAdminAdd('catpage', $this->activePage['id']) . '</p>';
        $h1AdminButtonOptions = array('page' => array('url' => '/admin/addcatpage/' . $this->activePage['id'], 'text' => 'Товар'));
        $this->setH1Admin('/admin/editcatsection/' . $this->activePage['id'], '/admin/deletecatsection/' . $this->activePage['id'], $h1AdminButtonOptions);

        $this->setMetaTags($this->activePage);

        $this->tpl->define_dynamic('_catalog', 'catalog.tpl');
        $this->tpl->define_dynamic('catalog', '_catalog');

        $this->tpl->define_dynamic('catalog_items_body', 'catalog');
        $this->tpl->define_dynamic('catalog_brands_items', 'catalog');
        $this->tpl->define_dynamic('catalog_items', 'catalog_items_body');

        $this->tpl->define_dynamic('catalog_items_empty', 'catalog');

        /*$brand = $this->getVar('brand');
        $cost = $this->getVar('cost');
        $numItems = $this->getVar('show-length', $this->settings['num_catalog']);
        
        $navbar = $navTop = $navBot = '';
        
        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }
        
        $paginationUrl = '/' . implode('/', $this->url);
        $getUrl = '';
        
        $selectItem = $this->db->select();
        $selectItem->from('catalog');
        $selectItem->order(array('position', 'name'));
        $selectItem->where("level = '?'", (int) $this->activePage['id']);
        
        $selectCount = $this->db->select();
        $selectCount->from('catalog', array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));
        $selectCount->where("level = '".(int) $this->activePage['id']."'");
        
        if (null !== $brand) {
            $getUrl .= 'brand=' . $_GET['brand'] . '&';
            
            $selectItem->where("brand = '?'", $brand);
            $selectCount->where("brand = '?'", $brand);
        }
        
        if (null !== $cost) {
            $getUrl .= 'cost=' . $_GET['cost'] . '&';
            
            $cost = explode('-', $cost);
            
            if (is_array($cost) && count($cost) == 2 && !empty($cost[1])) {
                $min = (int) $cost[0];
                $max = (int) $cost[1];
                
                if ($min == $max && $min == 0) {
                    return $this->error404();
                }
                
                if ($min > $max) {
                    return $this->error404();
                }
                
                $selectItem->where("cost >= '?'", $min);
                $selectItem->where("cost <= '?'", $max);
                
                $selectCount->where("cost >= '?'", $min);
                $selectCount->where("cost <= '?'", $max);
            }
        }
        
        if ($getUrl) {
            $getUrl = trim($getUrl, '&');
            $getUrl = "?$getUrl";
        }

        $paginationUrl .= $getUrl;

        if (!$this->_isAdmin()) {
            $selectItem->where("visibility = '1'");
            $selectCount->where("visibility = '1'");
        }
//echo $selectItem->__toString();
//echo '<br /><br />';
//echo $selectCount->__toString();
//exit();
        $adapter = new Zend_Paginator_Adapter_DbSelect($selectItem);
        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($numItems);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, $paginationUrl, $getUrl ? '&' : '?');
        
        if ($navbar) {
            $navTop = '<div class="pager_right">' . $navbar . '</div>';
            $navBot = '<div style="clear: both; padding-top: 10px;"></div>' . $navTop;
        }
        
        $countValue = $this->db->fetchAll("SELECT  max(`cost`) as `max`, min(`cost`) as `min`  FROM `catalog` WHERE `level` = '$level'".($this->_isAdmin() ? '' : ' AND `visibility` = "1"')." AND `type` = 'page'  LIMIT 1");

        $minCost = intval($countValue[0]['min']);
        $maxCost = intval($countValue[0]['max']);
        
        //var_dump($paginator);
        //exit();
        
        $this->tpl->assign(array(
            'FILTER' => $this->drowFilter($level, array(
                'range' => array(
                    'min' => $minCost,
                    'max' => $maxCost,
                    ))
            ),
            'CATALOG_ITEM' => $this->goodsList($paginator, true, true), // Определена в AbstractBase.php
            'TOP_NAV_BAR' => $navTop,
            'BOTT_NAV_BAR' => $navBot
        ));
        $this->tpl->parse('CONTENT', '.catalog');
        
        return true;
        */
        
        
        
        // Бренды

        $in = "AND `level` = '" . $this->activePage['id'] . "'";

        // Постраничная навигация

        $visibleQuery = '';

        $isShowEmptyPic = true;
        $isShowEmptyPrice = true;


        if (!$this->_isAdmin()) {
            $catalogOptions = $this->getCatalogOptions();
            $catalogOptions = array_values($catalogOptions);

            if (isset($catalogOptions['0']['is_show_empty_pic']) && $catalogOptions['0']['is_show_empty_pic'] == '0') {
                // $visibleQuery .= " AND `pic` != ''";
                // $isShowEmptyPic = false;
            }

            $catalogOptions = $this->getCatalogOptions();

            if (isset($catalogOptions['0']['is_show_empty_price']) && $catalogOptions['0']['is_show_empty_price'] == '0') {
                //   $visibleQuery .= " AND `cost` > 0 ";
                //  $isShowEmptyPrice = false;
            }
            $visibleQuery .= " AND `visibility` = '1'";
        }

        $start = 0;
        $navbar = $navTop = $navBot = '';
        $pagePagen = 1;
        $filterSql = '';

        if (isset($_GET) && count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (isset($this->filter[$key]) && !empty($value)) {
                    if (isset($this->filter[$key]['type']) && $this->filter[$key]['type'] == 'range') {
                        $rangeArray = explode('-', $value);

                        if (is_array($rangeArray) && count($rangeArray) == 2) {
                            if (empty($rangeArray[0]) && !empty($rangeArray[1])) {
                                $filterSql .= " AND `$key` >= " . $rangeArray[1];
                            } elseif (empty($rangeArray[1]) && !empty($rangeArray[0])) {
                                $filterSql .= " AND `$key` >= " . $rangeArray[0];
                            } elseif (!empty($rangeArray[0]) && !empty($rangeArray[1])) {
                                $filterSql .= " AND (`$key` >= " . $rangeArray[0] . " AND `$key` <= " . $rangeArray[1] . " )";
                            }
                        }
                    } elseif (!empty($value)) {
                        $filterSql .= " AND `$key` LIKE '%$value%' ";
                    }
                }
            }
        }

        $url = '?';
        $pagegUrl = '';

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (isset($this->filter) && is_array($this->filter)) {
                    if (isset($this->filter[$key])) {

                        $url .= $key . '=' . $value . '&';
                        $pagegUrl = $url;
                    }
                }

                if ($key == 'show-length') {
                    $url .= $key . '=' . $value . '&';
                    $pagegUrl = $url;
                }

                if ($key != 'page') {
                    $url .= $key . '=' . $value . '&';
                }
            }
        }

        if (!empty($pagegUrl)) {
            $pagegUrl = "?$pagegUrl";
            $pagegUrl = str_replace('&&', '&', $pagegUrl);
        }
//echo "SELECT COUNT(`id`) FROM `catalog` WHERE `level` = '$level' $visibleQuery AND `type` = 'page' $filterSql LIMIT 1";
        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `catalog` WHERE `level` = '$level' $visibleQuery AND `type` = 'page' $filterSql LIMIT 1");
        $countValue = $this->db->fetchAll("SELECT  max(`cost`) as `max`, min(`cost`) as `min`  FROM `catalog` WHERE `level` = '$level' $visibleQuery AND `type` = 'page'  LIMIT 1");

        $minCost = intval($countValue[0]['min']);
        $maxCost = intval($countValue[0]['max']);



        $catalogPageLength = $this->drowCountGoodsRange($count, $url);
        $limitQuery = "";
        if (is_numeric($catalogPageLength) && $count > 0) {
            if ($count > $catalogPageLength) {
                if (isset($this->getParam['page'])) {
                    $pagePagen = (int) $this->getParam['page'];
                    $start = $catalogPageLength * $pagePagen - $catalogPageLength;
                    $url = $this->mergeUrlArray();

                    if ($start > $count) {
                        $start = 0;
                    }
                }

                $paginationUrl = '/' . implode('/', $this->url);
                
                $getUrl = '';
                
                if (isset($_GET['brand'])) {
                    $getUrl .= 'brand=' . $_GET['brand'] . '&';
                }
                
                if (isset($_GET['cost'])) {
                    $getUrl .= 'cost=' . $_GET['cost'] . '&';
                }
                
                if (isset($_GET['show-length'])) {
                    $getUrl .= 'show-length=' . $_GET['show-length'] . '&';
                }
                
                if ($getUrl) {
                    $getUrl = trim($getUrl, '&');
                    $getUrl = "?$getUrl";
                }
                
                $paginationUrl .= $getUrl;
                
                $navbar = $this->loadPaginator((int) ceil($count / $catalogPageLength), (int) $pagePagen, $url);

                if ($navbar) {
                    $navTop = '<div class="pager2">' . $navbar . '</div>';
                    $navBot = '<div style="clear: both; padding-top: 10px;"></div>' . $navTop;
                }
            }
            $limitQuery = " LIMIT " . $start . ", " . $catalogPageLength;
        } else {
            //$this->tpl->assign('CONTENT', ($index ? '' : $this->getAdminAdd('news')).'{EMPTY_SECTION}');
            //  return true;
        }

        $sql = "SELECT * FROM `catalog` WHERE `level` = '$level' $visibleQuery AND `type` = 'page'  $filterSql ORDER BY `position`, `name` $limitQuery";

        $items = $this->db->fetchAll($sql);

        $this->tpl->assign(array(
            'FILTER' => $this->drowFilter($level, array(
                'range' => array(
                    'min' => $minCost,
                    'max' => $maxCost,
                    ))
            ),
            'CATALOG_ITEM' => $this->goodsList($items, $isShowEmptyPic, $isShowEmptyPrice), // Определена в AbstractBase.php
            'TOP_NAV_BAR' => $navTop,
            'BOTT_NAV_BAR' => $navBot
        ));
        $this->tpl->parse('CONTENT', '.catalog');
    }

    /**
     * Выводит строку "Отображать на странице по: 9 36 72 Все"
     * Возврщает активное занчение из сессии
     * Данные берет из массива сountGoodsRange.
     */
    protected function drowCountGoodsRange($goodsLength, $url='?') {
        $return = $this->settings['num_catalog'];
        $goodsLengthRange = '';

        $goodsLength = intval($goodsLength);
        if (isset($this->сountGoodsRange) && isset($this->сountGoodsRange[0])) {
            if ($goodsLength > $this->сountGoodsRange[0]) {
                $goodsSession = new Zend_Session_Namespace('catalog');
                if (isset($_GET['show-length']) && (is_numeric($_GET['show-length']) || $_GET['show-length'] == 'all')) {
                    $goodsSession->array['list_lenght'] = $_GET['show-length'];
                }

                if (isset($goodsSession->array['list_lenght']) && (is_numeric($goodsSession->array['list_lenght']) || $goodsSession->array['list_lenght'] == 'all')) {
                    $_GET['show-length'] = $goodsSession->array['list_lenght'];
                }

                $goodsLengthRange .= "<span>Отображать на странице по: ";

                foreach ($this->сountGoodsRange as $key => $val) {
                    if ((isset($_GET['show-length']) && ($_GET['show-length'] == $val)) || (!isset($_GET['show-length']) && $key == 0)) {
                        $goodsLengthRange .= "<span>$val</span>";
                        $return = $val;
                    } elseif ($goodsLength >= $val) {
                        $goodsLengthRange .= "<a href=\"" . $url . "show-length=$val\">$val</a>";
                    }
                }

                if (isset($_GET['show-length']) && $_GET['show-length'] == 'all') {
                    $goodsLengthRange .= "<span>Все</span>";
                    $return = 'all';
                    $goodsSession->array['list_lenght'] = 'all';
                } else {
                    $goodsLengthRange .= "<a href=\"?show-length=all\">Все</a>";
                }

                $goodsLengthRange .= "</span>";
            }
        }

        $this->tpl->assign('GOODS_LENGTH_RANGE', $goodsLengthRange);
        return $return;
    }

    protected function drowFilter($level, $options=array()) {
        $ret = '';
        $dataList = array();
        $tmp2 = '';

        if (isset($this->filter) && is_array($this->filter) && count($this->filter) > 0) {

            foreach ($this->filter as $field => $value) {
                $rangeArray = array();
                $valuesArray = array();

                if (is_array($value) && isset($value['type']) && $value['type'] == 'range') {

                    if (isset($options['range']['max']) && isset($options['range']['min'])) {
                        $minCost = intval($options['range']['min']);
                        $maxCost = intval($options['range']['max']);

                        $range = $minCost;

                        if ($maxCost <= 100) {
                            $range = 20;
                        }

                        if ($maxCost >= 1000) {
                            $range = 100;
                        }

                        if ($maxCost >= 10000) {
                            $range = 1000;
                        }

                        if ($maxCost >= 100000) {
                            $range = 10000;
                        }
                        $f = $range;

                        if ($minCost < $maxCost) {

                            for ($minCost, $f, $s = 0; $minCost <= $maxCost; $minCost++, $s++) {
                                $selected = '';
                                if ($s == 0) {
                                    if (($val = $this->getVar($field, false)) !== false && $val == "$minCost") {
                                        $selected = " selected ";
                                    }
                                    $ret .= "<select name='$field'  class='select-filter'>\n
                                <option value='' $selected>Цена</option>\n
                                <option value='$minCost' $selected>от $minCost </option>\n";
                                }

                                if ($minCost == $f) {

                                    $ot = $minCost;
                                    $f+= $range;
                                    $do = ($f - 1);
                                    if ($s > 0) {
                                        if (($val = $this->getVar($field, false)) !== false && $val == "$ot-$do") {
                                            $selected = " selected ";
                                        }
                                        $ret .= "<option value='$ot-$do' $selected>от $ot до $do </option>\n";
                                        $ot -=1;
                                    }
                                }
                            }

                            if ($f >= $maxCost) {
                                $ret .= "<option value='$ot-$maxCost'>от $ot до $maxCost </option>\n";
                            }

                            $ret .= "</select>\n";
                        }
                    }
                } else {



                    $ret .= "<select name='" . $field . "' class='select-filter'>\n";
                    $ret .= "<option value='";
                    $defaultTitle = '';
                    if (is_array($value) && isset($value['title'])) {
                        $ret .= ''; //$value['title'];
                        $defaultTitle = $value['title'];
                    } else {
                        $ret .= ''; //$value;
                        $defaultTitle = $value;
                    }

                    $ret .= "'";

                    if (!($this->getVar($field, false))) {
                        $ret .= " selected ";
                    }
                    $ret .= ">$defaultTitle</option>\n";

                    $dataList = $this->db->fetchAll("SELECT DISTINCT `$field` FROM `catalog` WHERE `level` = '$level'");
                    if ($dataList > 0) {

                        foreach ($dataList as $key => $value1) {
                            if (isset($value1[$field])) {

                                if (!isset($valuesArray[$field]) || !in_array($value1[$field], $valuesArray[$field])) {

                                    $valuesArray[$field][] = $value1[$field];
                                    $ret .= "<option value='$value1[$field]'";

                                    if (($filterValue = $this->getVar($field, false)) && isset($value1[$field]) && ($filterValue == $value1[$field])) {

                                        $ret .= "selected";
                                    }
                                    $ret .= "> $value1[$field] </option>\n";
                                }
                            }
                        }
                    }
                    $ret .= "<select/> \n\n";
                }
            }
        }
        return $ret;
    }

    // Выводит новинки, акции, хиты
    protected function drowGoodsList2Status($header, $status, $pagenUrl='') {

        $this->setMetaTags($header);
        $this->setWay($header);

        $this->tpl->define_dynamic('_catalog', 'catalog.tpl');
        $this->tpl->define_dynamic('catalog', '_catalog');

        $this->tpl->define_dynamic('catalog_items_body', 'catalog');
        $this->tpl->define_dynamic('catalog_brands_items', 'catalog');
        $this->tpl->define_dynamic('catalog_items', 'catalog_items_body');

        $this->tpl->define_dynamic('cataog_items_empty', 'catalog');

        $visibleQuery = '';
        if (!$this->_isAdmin()) {
            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_pic']) && $catalogOptions['0']['is_show_empty_pic'] == '0') {
                $visibleQuery .= " AND `pic` != ''";
            }

            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_price']) && $catalogOptions['0']['is_show_empty_price'] == '0') {
                $visibleQuery .= " AND `cost` > 0 ";
            }
            $visibleQuery .= " AND `visibility` = '1'";
        }


        $start = 0;
        $navbar = $navTop = $navBot = '';
        $pagePagen = 1;

        $catalogPageLength = $this->settings['num_catalog'];
        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `catalog` WHERE `type` = 'page' $visibleQuery  AND `status` = '$status'");

        if ($count > 0) {
            if ($count > $catalogPageLength) {
                if (isset($this->getParam['page'])) {
                    $pagePagen = (int) $this->getParam['page'];
                    $start = $catalogPageLength * $pagePagen - $catalogPageLength;

                    if ($start > $count) {
                        $start = 0;
                    }
                }

                $pagenParams = '';

                if (!empty($brandName)) {
                    $pagenParams = "?brand=$brandName";
                }

                $pagenHref = 'catalog';

                if (isset($this->activePage['href1'])) {
                    $pagenHref .= '/' . $this->activePage['href1'];
                }

                $pagenHref .= '/' . $this->activePage['href'];

                $navbar = $this->loadPaginator((int) ceil($count / $catalogPageLength), (int) $pagePagen, $pagenUrl);

                if ($navbar) {
                    $navTop = '<div class="pager2">' . $navbar . '</div>';
                    $navBot = '<div style="clear: both; padding-top: 10px;"></div>' . $navTop;
                }
            }
        } else {
            //$this->tpl->assign('CONTENT', ($index ? '' : $this->getAdminAdd('news')).'{EMPTY_SECTION}');
            //  return true;
        }

        $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' $visibleQuery  AND `status` = '$status' ORDER BY `position`, `name` LIMIT " . $start . ", " . $this->settings['num_catalog'];
        $items = $this->db->fetchAll($sql);
        $itemsLength = count($items);

        if ($itemsLength > 0) {
            $this->tpl->parse('CATALOG_ITEMS_EMPTY', 'null');
        }



        $this->tpl->assign(array(
            'FILTER' => '',
            'GOODS_LENGTH_RANGE' => '',
            'CATALOG_ITEM' => $this->goodsList($items),
            'TOP_NAV_BAR' => $navTop,
            'BOTT_NAV_BAR' => $navBot
        ));
        $this->tpl->parse('CONTENT', '.catalog');

        return true;
    }

    public function novelty() {
        $catalogOptions = $this->getCatalogOptions();
        if (isset($catalogOptions['0']['is_show_new']) && $catalogOptions['0']['is_show_new'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Новинки', 'new', '/catalog/novelty');
    }

    public function hits() {
        $catalogOptions = $this->getCatalogOptions();
        if (isset($catalogOptions['0']['is_show_hits']) && $catalogOptions['0']['is_show_hits'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Хиты', 'hit', '/catalog/hits');
    }

    public function actions() {
        if (isset($catalogOptions['0']['is_show_actions']) && $catalogOptions['0']['is_show_actions'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Акции', 'action', '/catalog/actions');
    }

    // Рекомендуемые товары
    protected function featuredProducts($level, $where='') {

        $visibleQuery = '';
        if (!$this->_isAdmin()) {
            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_pic']) && $catalogOptions['0']['is_show_empty_pic'] == '0') {
                $visibleQuery .= " AND `pic` != ''";
            }

            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_price']) && $catalogOptions['0']['is_show_empty_price'] == '0') {
                $visibleQuery .= " AND `cost` > 0 ";
            }
            $visibleQuery .= " AND `visibility` = '1'";
        }

        $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' $visibleQuery $where ORDER BY RAND() LIMIT 5";

        $items = $this->db->fetchAll($sql);
        $itemsLength = count($items);

        $this->tpl->assign(array('FEATURED_CATALOG_ITEM' => $this->goodsListFeaturedProductsUsedComplete($items)));
        return ($itemsLength > 0);
    }

    protected function goodsListFeaturedProductsUsedComplete(array $items) {
        $itemsLength = count($items);

        if ($itemsLength > 0) {
            $this->tpl->parse('CATALOG_ITEMS_EMPTY', 'null');
        }

        $catItem1 = '';
        $catItem2 = '';
        $catItem3 = '';
        $catalogItem = '';

        $i = 0;
        $f = 0;
        $s = 0;
        $goodsSession = new Zend_Session_Namespace('goods');

        if ($itemsLength > 0) {

            while (true) {

                if (isset($items[$i])) {
                    $itemUrl = '';

                    if (($parenSectionInfo = $this->dataTreeManager($items[$i]['id']))) {
                        $itemUrl = $parenSectionInfo['links'];
                    }


                    $link = (($items[$i]['type'] != 'link') ? ('/catalog') : ('')) . $parenSectionInfo['links'];
                    //$types = adminCatalogEdit($items[$i]['id']);
                    $name = $items[$i]['name'];
                    //$url = '/catalog/'.$parenSectionInfo[0]['href'];

                    if (!empty($types)) {
                        $types = "$types ";
                    }

                    $liLastClass = '';

                    if ($f == 4) {
                        $liLastClass = 'class="last"';
                    }




                    $imgSrc = 'no-foto/no-foto-122x120.gif';
                    $imgAlt = 'Нет фото';
                    $imgTitle = $imgAlt;

                    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_2/' . $items[$i]['pic'])) {
                        $imgSrc = 'catalog/small_2/' . $items[$i]['pic'];
                        $imgAlt = $items[$i]['pic_alt'];
                        $imgTitle = $items[$i]['pic_title'];

                        if (empty($imgAlt)) {
                            $imgAlt = $items[$i]['name'];
                        }

                        if (empty($imgTitle)) {
                            $imgTitle = $items[$i]['name'];
                        }
                    } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_2/' . $items[$i]['artikul'] . '.jpg')) {
                        $imgSrc = 'catalog/small_2/' . $items[$i]['artikul'] . '.jpg';
                        $imgAlt = $items[$i]['pic_alt'];
                        $imgTitle = $items[$i]['pic_title'];

                        if (empty($imgAlt)) {
                            $imgAlt = $items[$i]['name'];
                        }

                        if (empty($imgTitle)) {
                            $imgTitle = $items[$i]['name'];
                        }
                    }

                    $imgType = '';

                    if (isset($items[$i]['status']) && $items[$i]['status'] == 'hit') {
                        //$imgType = '<img src="/img/hit.png" class="png" width="124" height="124" alt="" />';
                    }

                    if (isset($items[$i]['status']) && $items[$i]['status'] == 'action') {
                        // $imgType = '<img src="/img/akcia.png" class="png" width="124" height="124" alt="" />';
                    }

                    if (isset($items[$i]['status']) && $items[$i]['status'] == 'new') {
                        //$imgType = '<img src="/img/new.png" class="png" width="124" height="124" alt="" />';
                    }

                    $adminButton = ''; //$this->getAdminEdit('catpage', $items[$i]['id']);

                    $catItem1 .= '<li ' . $liLastClass . '><a href="/catalog/' . $itemUrl . '">' . $imgType . '<img src="/img/' . $imgSrc . '" width="122" height="120" alt="' . $imgAlt . '" title="' . $imgTitle . '"/></a>  <p><a href="/catalog/' . $itemUrl . '" title="' . $items[$i]['name'] . '">' . $items[$i]['name'] . '</a> </p></li> ';

                    $catItem2 .= '<li ' . $liLastClass . '>';
                    $catItem3 .= '<li ' . $liLastClass . '>';
                    if ($this->getCatOption('isStore')) {


                        //$buyButton = '<p class="right"><input type="button" onclick="addToBasket(\'goods_'.$items[$i]['id'].'\', 1);" value="Купить" class="button" /></p>';
                        $buyButton = '<p class="buy"><a href="akces_block" class="buy-button" id="' . $items[$i]['id'] . '" > Купить</a> </p>';

                        if (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$items[$i]['id']])) {
                            $buyButton = '<p class="order detail"><img width="14" height="13" alt="" src="/img/order.gif"><a href="/basket" class="a-buy">Перейти в корзину</a></p>';
                        }

                        if (empty($items[$i]['cost'])) {
                            $items[$i]['cost'] = '0';
                        }

                        if ($items[$i]['availability'] == '0') {
                            $buyButton = '<div class="expected"><p>{EXPECTED_TEXT}</p></div>';
                        }

                        $catItem2.='<p class="newprice">Цена: <span>' . number_format($items[$i]['cost'], 0, '', '') . ' грн.</span></p>';
                        $catItem3.= $buyButton;
                    } else {
                        $catItem2 .= "&nbsp;";
                        $catItem3 .= "&nbsp;";
                    }
                    $catItem2 .= "</li>";
                    $catItem3 .= "</li>";
                }

                $f++;
                $i++;

                if ($f == 5) {
                    $f = 0;
                    $s+=5;
                    $catalogItem .='<ul><li><div class="clear"></div><ul style="margin-top: 20px;">' . $catItem1 . '</ul></li>';
                    $catalogItem .='<li><ul>' . $catItem2 . '</ul></li>';
                    $catalogItem .='<li><ul>' . $catItem3 . '</ul></li><div class="clear"></div>';


                    $catItem1 = '';
                    $catItem2 = '';
                    $catItem3 = '';
                }

                if ($s >= $itemsLength) {
                    $catItem1 = '';
                    $catItem2 = '';
                    $catItem3 = '';
                    $catItem4 = '';
                    break;
                }
            }
        }

        return $catalogItem;
    }

    // Комплект
    protected function usedComplete($level, $where='') {

        $visibleQuery = '';

        if (!$this->_isAdmin()) {
            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_pic']) && $catalogOptions['0']['is_show_empty_pic'] == '0') {
                $visibleQuery .= " AND `pic` != ''";
            }

            $catalogOptions = $this->getCatalogOptions();
            if (isset($catalogOptions['0']['is_show_empty_price']) && $catalogOptions['0']['is_show_empty_price'] == '0') {
                $visibleQuery .= " AND `cost` > 0 ";
            }
            $visibleQuery .= " AND `visibility` = '1'";
        }

        $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' $visibleQuery $where ORDER BY `position`, `name` ";
        $items = $this->db->fetchAll($sql);
        $itemsLength = count($items);



        $this->tpl->assign(array('USED_COMPLATE_CATALOG_ITEM' => $this->goodsListFeaturedProductsUsedComplete($items)));
        return ($itemsLength > 0);
    }

    // Прорисовывет картинки товаров с других ракурсов
    protected function drowForeshortening($goodsArtikul) {

        $items = $this->db->fetchAll("SELECT * FROM `catalog_gallery` WHERE `goods_artikul` = '$goodsArtikul'  AND `gallery_type` = 'foreshortening'");
        if ($items) {

            foreach ($items as $item) {
                if (empty($item['title'])) {
                    $item['title'] = $this->activePage['pic_title'];
                }

                if (empty($item['alt'])) {
                    $item['alt'] = $this->activePage['pic_alt'];
                }
                $this->tpl->assign(array(
                    'F_ID' => $item['id'],
                    'F_SRC' => $item['pic'],
                    'F_ALT' => $item['alt'],
                    'F_TITLE' => $item['title']
                ));
                $this->tpl->parse('CATALOG_FORESHORTENING_ITEMS', '.catalog_foreshortening_items');
            }
            $this->tpl->parse('CATALOG_FORESHORTENING_LIST', '.catalog_foreshortening_list');
        }
    }

    // Прорисовывет картинки из минигалереи
    protected function drowMiniGallery($goodsArtikul) {

        $items = $this->db->fetchAll("SELECT * FROM `catalog_gallery` WHERE `goods_artikul` = '$goodsArtikul' AND `gallery_type` = 'gallery' ORDER BY `position`, `pic`");
        if ($items) {

            foreach ($items as $item) {
                if (empty($item['title'])) {
                    $item['title'] = $this->activePage['pic_title'];
                }

                if (empty($item['alt'])) {
                    $item['alt'] = $this->activePage['pic_alt'];
                }

                $this->tpl->assign(array(
                    'G_ID' => $item['id'],
                    'G_SRC' => $item['pic'],
                    'G_ALT' => $item['alt'],
                    'G_TITLE' => $item['title']
                ));
                $this->tpl->parse('CATALOG_GALLERY_ITEMS', '.catalog_gallery_items');
            }
            $this->tpl->parse('CATALOG_GALLERY_LIST', '.catalog_gallery_list');
        }
    }

    protected function loadDetail() {

        $this->tpl->define_dynamic('_catalog_detail_body', 'catalog.tpl');
        $this->tpl->define_dynamic('catalog_detail_body', '_catalog_detail_body');
        $this->tpl->define_dynamic('features_block', 'catalog_detail_body');
        $this->tpl->define_dynamic('complate_block', 'catalog_detail_body');


        if ($this->getCatOption('isForeshortening')) {
            $this->tpl->define_dynamic('catalog_foreshortening_list', 'catalog_detail_body');
            $this->tpl->define_dynamic('catalog_foreshortening_items', 'catalog_foreshortening_list');
            $this->tpl->parse('CATALOG_FORESHORTENING_LIST', 'null');
        }

        if ($this->getCatOption('isMiniGallery')) {
            $this->tpl->define_dynamic('catalog_gallery_list', 'catalog_detail_body');
            $this->tpl->define_dynamic('catalog_gallery_items', 'catalog_gallery_list');
        }


        if ($this->getCatOption('isComments')) {
            $this->tpl->define_dynamic('comments', 'catalog_detail_body');
            $this->tpl->define_dynamic('comments_list', 'comments');
            $this->tpl->define_dynamic('comment_helpful_yes', 'comments_list');
            $this->tpl->define_dynamic('comment_helpful_no', 'comments_list');
            $this->tpl->parse('COMMENT_HELPFUL_YES', 'null');
            $this->tpl->parse('COMMENT_HELPFUL_NO', 'null');
        }

        $this->tpl->define_dynamic('store', 'catalog_detail_body');
        $this->tpl->define_dynamic('catalog_detail_plashka', 'store');

        $this->tpl->define_dynamic('pbutton', 'store');
        $this->tpl->define_dynamic('npbutton', 'store');
        $this->tpl->define_dynamic('availability_button', 'store');
        $this->tpl->parse('AVAILABILITY_BUTTON', 'null');


        $this->tpl->parse('CATALOG_FORESHORTENING_LIST', 'null');
        $this->tpl->parse('CATALOG_GALLERY_LIST', 'null');

        $sectionUrl = '/catalog';
        $sectionUrlText = '';

        $groupName = '';
        $groupUrl = '';

//        $this->activePage['header'] .= '  ' . $this->getAdminEdit('catpage', $this->activePage['id']);

        $this->setH1Admin('/admin/editcatpage/' . $this->activePage['id'], '/admin/deletecatpage/' . $this->activePage['id'], false);

        $commentsLength = $this->db->fetchOne("SELECT count(id) FROM `comments` WHERE `goods_artikul` = '$this->activePage[artikul]'  AND `visible` = '1'");
        if (isset($this->activePage['brand_rus'])) {
            $this->activePage['description'] = str_replace('__BRAND__', $this->activePage['brand'], $this->activePage['description']);
        }

        $this->activePage['description'] = str_replace('__URL__', $_SERVER['HTTP_HOST'], $this->activePage['description']);

        if (isset($this->activePage['brand_rus'])) {
            $this->activePage['description'] = str_replace('__BRAND_RU__', $this->activePage['brand_rus'], $this->activePage['description']);
        }

        $this->activePage['description'] = str_replace('__ARTIСUL__', $this->activePage['artikul'], $this->activePage['description']);
        $this->activePage['description'] = str_replace('__ARTIKUL__', $this->activePage['artikul'], $this->activePage['description']);
        $this->activePage['description'] = str_replace('__COST__', $this->activePage['cost'], $this->activePage['description']);
        $this->activePage['description'] = str_replace('__COMMENTS__', (is_numeric($commentsLength) && intval($commentsLength) > 0 ? "($commentsLength)" : ''), $this->activePage['description']);
        if (isset($this->activePage['guarantee'])) {
            $this->activePage['description'] = str_replace('__GARANTY__', $this->activePage['guarantee'], $this->activePage['description']);
        }
        $this->activePage['description'] = str_replace('__PHONE__', '{GOODS_META_PHONE}', $this->activePage['description']);

        $this->activePage['title'] = str_replace('__URL__', $_SERVER['HTTP_HOST'], $this->activePage['title']);
        if (isset($this->activePage['brand'])) {
            $this->activePage['title'] = str_replace('__BRAND__', $this->activePage['brand'], $this->activePage['title']);
        }

        if (isset($this->activePage['brand_rus'])) {
            $this->activePage['title'] = str_replace('__BRAND_RU__', $this->activePage['brand_rus'], $this->activePage['title']);
        }

        $this->activePage['title'] = str_replace('__ARTIKUL__', $this->activePage['artikul'], $this->activePage['title']);
        $this->activePage['title'] = str_replace('__ARTIСUL__', $this->activePage['artikul'], $this->activePage['title']);
        $this->activePage['title'] = str_replace('__COST__', $this->activePage['cost'], $this->activePage['title']);
        $this->activePage['title'] = str_replace('__COMMENTS__', (is_numeric($commentsLength) && intval($commentsLength) > 0 ? "($commentsLength)" : ''), $this->activePage['title']);
        if (isset($this->activePage['guarantee'])) {
            $this->activePage['title'] = str_replace('__GARANTY__', $this->activePage['guarantee'], $this->activePage['title']);
        }
        $this->activePage['title'] = str_replace('__PHONE__', '{GOODS_META_PHONE}', $this->activePage['title']);



        $this->setMetaTags($this->activePage);

        if (($dataTree = $this->dataTreeManager($this->activePage['id']))) {

            // Загрузка паффиндера

            $dataTreeLength = count($dataTree['names']);

            if ($dataTreeLength > 0) {
                //var_dump($dataTree);
                for ($i = 0; $i < $dataTreeLength; $i++) {
                    $url = '/catalog';
                    if ($i != ($dataTreeLength - 1)) {
                        $url .= '/' . $dataTree['linksArr'][$i];
                        $groupName = $dataTree['names'][$i];
                        $groupUrl = $url;
                    } else {
                        $url = null;
                    }

                    $this->setWay($dataTree['names'][$i], $url);
                }
            }
        }

        // Рейтинг товара
        $ratingGoods = $this->db->fetchRow("SELECT SUM(`points`) as spoints, COUNT(`id`) as `count` FROM `comments` WHERE `points` != '' AND `points` != '0' AND `goods_artikul` = '" . $this->activePage['artikul'] . "'");

        $ratingSumm = null;
        $ratingCount = 0;
        $ratingValue = 0;

        if (isset($ratingGoods['spoints']) && is_numeric($ratingGoods['spoints']) && isset($ratingGoods['count']) && is_numeric($ratingGoods['count'])) {

            $ratingSumm = intval($ratingGoods['spoints']);
            $ratingCount = intval($ratingGoods['count']);
            if ($ratingSumm > 0 && $ratingCount > 0) {
                $ratingValue = ($ratingSumm / $ratingCount);
            }
        }


        $ratingValue = number_format($ratingValue, 1, '.', "");

        list($rCount, $rOst) = explode('.', $ratingValue);
        $rCount = intval($rCount);
        $rOst = intval($rOst);

        $rating = '';
        $isAcive = true;

        for ($i = 1; $i <= 5; $i++) {
            if ($i > $rCount) {
                $isAcive = false;
            }

            $split = '';
            if ($rOst > 0) {
                $split = "{split:$rOst}";
            }


            if (!$isAcive) {
                $rating .= '<div class="star-rating rater-0 star star-rating-applied star-rating-live star-rating-readonly"><a title="2">2</a></div>';
            } else {
                $rating .= '<div class="star-rating rater-1 star  star-rating-applied star-rating-live star-rating-on" ><a title="2">2</a></div>';
            }
        }


        $pic = 'no-foto/no-foto-370x370.gif';
        $catalogDetailImgRealPath = '/img/';
        $catalogDetailImgBigPath = '/img/';

        $imgAlt = 'Нет фото';
        $imgTitle = $imgAlt;
        $loop = '';


        if (is_file(PATH . 'img/catalog/big/' . $this->activePage['pic'])) {
            $pic = $this->activePage['pic'];
            $imgAlt = $this->activePage['pic_alt'];
            $imgTitle = $this->activePage['pic_title'];
            $catalogDetailImgRealPath = '/img/catalog/real/';
            $catalogDetailImgBigPath = '/img/catalog/big/';

            if (empty($imgAlt)) {
                $imgAlt = $this->activePage['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $this->activePage['name'];
            }
            $loop = '<span><a href="/img/catalog/real/' . $this->activePage['pic'] . '" rel="xbox[photo-item]"  title="' . $this->activePage['name'] . '">Увеличить</a></span>';
            //  $loop = '<span><a href="#"  title="Увеличить">Увеличить</a></span>';
        }

        if (is_file(PATH . 'img/catalog/big/' . $this->activePage['artikul'] . '.jpg')) {
            $pic = $this->activePage['artikul'] . '.jpg';
            $imgAlt = $this->activePage['pic_alt'];
            $imgTitle = $this->activePage['pic_title'];
            $catalogDetailImgRealPath = '/img/catalog/real/';
            $catalogDetailImgBigPath = '/img/catalog/big/';

            if (empty($imgAlt)) {
                $imgAlt = $this->activePage['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $this->activePage['name'];
            }
            $loop = '<span><a href="/img/catalog/real/' . $this->activePage['artikul'] . '.jpg' . '" rel="xbox[photo-item]"  title="' . $this->activePage['name'] . '">Увеличить</a></span>';
            //  $loop = '<span><a href="#"  title="Увеличить">Увеличить</a></span>';
        }



        $costInShop = $this->activePage['cost_old'];
        $cost = $this->activePage['cost'];
        $econom = ($costInShop - $cost);
        $availability = 'Есть';

        $featuredProducts = $this->activePage['featured_products'];

        // Комплект

        $usedComplate = $this->activePage['used_complete'];



        if ($this->getCatOption('isUsedComplete') && !empty($usedComplate)) {
            // $usedComplate = str_replace(' ', '', $usedComplate);

            $usedComplate = " AND `artikul` IN('" . str_replace(',', "','", $usedComplate) . "')";
            $usedComplate = preg_replace("/\'\s+|\s+\'/", "'", $usedComplate);

            if (!$this->usedComplete($this->activePage['id'], $usedComplate)) {
                //complate_block
                $this->tpl->parse('COMPLATE_BLOCK', 'null');
                $this->tpl->assign(array('USED_COMPLATE_CATALOG_ITEM' => '', 'USED_COMPLATE_TITLE' => ''));
            } else {

            }
            $this->tpl->assign(array('USED_COMPLATE_TITLE' => '<p>Комплект:</p>'));
        } else {
            $this->tpl->parse('COMPLATE_BLOCK', 'null');
            $this->tpl->assign(array('USED_COMPLATE_CATALOG_ITEM' => '', 'USED_COMPLATE_TITLE' => ''));
        }

        // Рекомендованные товары

        if ($this->getCatOption('isFeatured') && !empty($featuredProducts)) {
            // $featuredProducts = str_replace(' ', '', $featuredProducts);

            $featuredProducts = " AND `artikul` IN('" . str_replace(',', "','", $featuredProducts) . "')";
            $featuredProducts = preg_replace("/\'\s+|\s+\'/", "'", $featuredProducts);

            if (!$this->featuredProducts($this->activePage['id'], $featuredProducts)) {
                $this->tpl->parse('FEATURES_BLOCK', 'null');
                $this->tpl->assign(array('FEATURED_CATALOG_ITEM' => '', 'FEATURED_TITLE' => ''));
            } else {

            }
            $this->tpl->assign(array('FEATURED_TITLE' => '<p>Рекомендованные товары:</p>'));
        } else {
            $this->tpl->parse('FEATURES_BLOCK', 'null');
            $this->tpl->assign(array('FEATURED_CATALOG_ITEM' => '', 'FEATURED_TITLE' => ''));
        }


        $imgType = '';

        if (isset($this->activePage['status']) && $this->activePage['status'] == 'hit') {
            $imgType = '<img src="/img/hit.png" class="png" width="124" height="124" alt="" />';
        }

        if (isset($this->activePage['status']) && $this->activePage['status'] == 'action') {
            $imgType = '<img src="/img/akcia.png" class="png" width="124" height="124" alt="" />';
        }

        if (isset($this->activePage['status']) && $this->activePage['status'] == 'new') {
            $imgType = '<img src="/img/new.png" class="png" width="124" height="124" alt="" />';
        }

        if (empty($costInShop)) {
            $costInShop = 0;
        }

        if (empty($econom)) {
            $econom = 0;
        }

        if (empty($cost)) {
            $cost = 0;
        }

        if ($this->activePage['availability'] == '0') {
            $this->tpl->parse('PBUTTON', 'null');
            $this->tpl->parse('NPBUTTON', 'null');
            $this->tpl->parse('AVAILABILITY_BUTTON', 'availability_button');
            $availability = 'Нет';
        }


        // Характеристики
        $sectionFields = '';
        $rightFields = '';
        $bottomFields = '';
        $groupNamesArray = array();
        $defaultFields = $this->getCatalogOptions(true);
        $isFindBodyField = false;


        if (isset($dataTree['sectionArtikul']) && !empty($dataTree['sectionArtikul'])) {
            $tableName = 'catalog-fields-' . $dataTree['sectionArtikul'];
            if ($this->isTableExists($tableName)) {

                if (($row = $this->drowSectionFields($dataTree['sectionArtikul'], true))) {


                    if (!($sectionFieldsValues = $this->db->fetchRow("SELECT * FROM `$tableName` WHERE `catalog_artikul` = '" . $this->activePage['artikul'] . "' "))) {
                        $sectionFieldsValues = array();
                    }

                    $data = array();
                    $sectionFields = "
                     <p>Характеристики:</p>
                     <table class='secton-fields'>\n";

                    //var_dump($groupNamesArray); die;



                    if (count($row) > 0) {
                        $isColor = true;
                        $group = '';
                        $group1 = '';
                        foreach ($row as $title => $value) {
                            if ($title != 'A2') {
                                $group1 = "<tr><th colspan='2'>$title</th></tr>\n";
                            }



                            if (is_array($value) && count($value) > 0) {

                                $subGroup = '';

                                foreach ($value as $subTitle => $subValue) {
                                    $subGroup1 = '';
                                    if (is_array($subValue) && count($subValue) > 0) {
                                        if ($subTitle != 'A1' && $subTitle != 'A2') {
                                            $subGroup1 .= "<tr bgcolor='#e5eaee'><td colspan='2'>$subTitle</td></tr>\n";
                                        } elseif ($subTitle == 'A1') {
                                            // $subGroup1 .= "<tr bgcolor='#EFEFEF'><td colspan='2'>Дополнительные параметры</td></tr>\n";
                                        }

                                        $fields = '';

                                        foreach ($subValue as $fieldValue) {
                                            $val = '';
                                            if (isset($sectionFieldsValues[$fieldValue['name']])) {
                                                $val = $sectionFieldsValues[$fieldValue['name']];
                                            }

                                            if ($fieldValue['title'] == '[__TITLE__]') {

                                                $tmpTitle = '';
                                                //var_dump($fieldValue);
                                                if (isset($defaultFields['defaultFields'][$fieldValue['name']])) {
                                                    $tmpTitle = $defaultFields['defaultFields'][$fieldValue['name']];
                                                }

                                                if (isset($defaultFields['defaultFields'][$fieldValue['name']]) && is_array($defaultFields['defaultFields'][$fieldValue['name']]) && isset($defaultFields['defaultFields'][$fieldValue['name']]['title'])) {
                                                    $tmpTitle = $defaultFields['defaultFields'][$fieldValue['name']]['title'];
                                                }

                                                $fieldValue['title'] = str_replace('[__TITLE__]', $tmpTitle, $fieldValue['title']);

                                                if (isset($this->activePage[$fieldValue['name']])) {
                                                    $val = $this->activePage[$fieldValue['name']];
                                                }
                                            }

                                            if ($fieldValue['layout'] == 'features_table' && $fieldValue['is_default_field'] == 'no') {

                                                if (!$this->_isAdmin()) {
                                                    if (!(empty($val) || $val == '') && $fieldValue['status'] != 'hidden') {
                                                        $isColor = !$isColor;
                                                        $fields .= "<tr " . ($isColor ? 'class="color"' : '') . "><td>$fieldValue[title]</td><td>$val </td></tr>\n";
                                                    }
                                                } else {

                                                    if ((empty($val) || $val == '')) {
                                                        $val = ' - ';
                                                    }

                                                    if ($fieldValue['status'] == 'hidden') {
                                                        $val .= " (Скрытое поле) ";
                                                    }

                                                    if (is_numeric($val)) {
                                                        $val = substr($val, 0, 4);
                                                    }

                                                    $isColor = !$isColor;
                                                    $fields .= "<tr " . ($isColor ? 'class="color"' : '') . "><td>$fieldValue[title]</td><td>$val </td></tr>\n";
                                                }
                                            }



                                            if ($fieldValue['layout'] == 'right') {

                                                if (empty($val)) {
                                                    if (isset($this->activePage[$fieldValue['name']])) {
                                                        $val = $this->activePage[$fieldValue['name']];
                                                    }
                                                }

                                                if (!empty($fieldValue['title'])) {

                                                    if (is_numeric($val)) {
                                                        $val = substr($val, 0, 4);
                                                    }

                                                    if ($fieldValue['status'] == 'hidden') {
                                                        if ($this->_isAdmin()) {
                                                            if (empty($val)) {
                                                                $val = '-';
                                                            }
                                                            $rightFields .= "<tr><td><strong>$fieldValue[title]: </strong>$val (Скрытое поле)</td></tr>";
                                                        }
                                                    } else {
                                                        $rightFields .= "<tr><td><strong>$fieldValue[title]: </strong>$val</td></tr>";
                                                    }
                                                }
                                            }

                                            if ($fieldValue['layout'] == 'bottom') {
                                                if (is_numeric($val)) {
                                                    $val = substr($val, 0, 4);
                                                }


                                                if (isset($fieldValue['name']) && $fieldValue['name'] == 'body') {
                                                    $isFindBodyField = true;
                                                }

                                                if (empty($val)) {
                                                    if (isset($this->activePage[$fieldValue['name']])) {
                                                        $val = $this->activePage[$fieldValue['name']];
                                                    }
                                                }
                                                if (!empty($fieldValue['title'])) {
                                                    if (is_numeric($val)) {
                                                        $val = substr($val, 0, 4);
                                                    }

                                                    if ($fieldValue['status'] == 'hidden') {
                                                        if ($this->_isAdmin()) {
                                                            if (empty($val)) {
                                                                $val = '-';
                                                            }
                                                            $bottomFields .= "<p>$fieldValue[title]:</p><p style='text-align: justify;'>$val (Скрытое поле)</p><div class=\"clear\"></div>";
                                                        }
                                                    } else {
                                                        $bottomFields .= "<p>$fieldValue[title]:</p><p style='text-align: justify;'>$val</p><div class=\"clear\"></div>";
                                                    }
                                                }
                                            }
                                        }

                                        if (!$this->_isAdmin()) {
                                            if (!empty($fields)) {
                                                $subGroup .= $subGroup1 . $fields;
                                            }
                                        } else {
                                            $subGroup .= $subGroup1 . $fields;
                                        }
                                    }
                                }
                            }



                            if (!$this->_isAdmin()) {
                                if (!empty($subGroup)) {
                                    $group .= $group1 . $subGroup;
                                }
                            } else {
                                $group .= $group1 . $subGroup;

                            }
                        }
                    }

                    if (!empty($group)) {
                        $sectionFields .= $group;
                    }
                    $sectionFields .= "</table>\n";
                }
            }
        }




        // unset($_SESSION['is_tip_helpful']);


        $object3D = '';


        if (isset($this->activePage['id_3d']) && !empty($this->activePage['id_3d'])) {
            $object3D = '<div id="' . $this->activePage['id_3d'] . '" class="3dstudio" title="' . $this->activePage['title_3d'] . '"></div>';
        }

        if (!$isFindBodyField) {
            $bottomFields .= "<p></p><p style='text-align: justify;'> ".$this->activePage['body']."</p><div class=\"clear\"></div>";
        }




        $this->tpl->assign(array(
            'CATALOG_DETAIL_RATING' => $rating,
            'CATALOG_DETAIL_IMG_REAL_PATH' => $catalogDetailImgRealPath,
            'CATALOG_DETAIL_IMG_BIG_PATH' => $catalogDetailImgBigPath,
            'CATALOG_DETAIL_ID' => $this->activePage['id'],
            'CATALOG_DETAIL_SECTION_URL' => $groupUrl,
            'CATALOG_DETAIL_SECTION_TEXT' => $groupName,
            'CATALOG_DETAIL_IMG' => $pic,
            'CATALOG_DETAIL_COST_IN_SHOP' => number_format($costInShop, 0, '', " "),
            'CATALOG_DETAIL_COST_ECONOM' => number_format($econom, 0, '', " "),
            'CATALOG_DETAIL_BRAND' => $this->activePage['brand'],
            'CATALOG_DETAIL_AVAILABILITY' => $availability,
            'CATALOG_DETAIL_PREWIEV' => (!empty($this->activePage['preview']) ? $this->activePage['preview'] : ''),
            'CATALOG_DETAIL_ALT' => (!empty($imgAlt) ? $imgAlt : ''),
            'CATALOG_DETAIL_TITLE' => (!empty($imgTitle) ? $imgTitle : ''),
            'CATALOG_DETAIL_COST' => number_format($cost, 0, ', ', " "),
            'CATALOG_DETAIL_ING_SRC' => $pic,
            'RIGHT_FIELDS' => $rightFields,
            'BOTTOM_FIELDS' => $bottomFields,
            'CATALOG_DETAIL_ING_TYPE' => $imgType,
            'CATALOG_DETAIL_USER_SECTION_FIELDS' => $sectionFields,
            'CATALOG_DETAIL_ING_LOOP' => $loop,
            'CATALOG_DETAIL_BODY1' => (!empty($this->activePage['body']) ? $this->activePage['body'] : ''),
            '3D_OBJECTS' => $object3D
        ));



        if (!$this->getCatOption('isStore')) {
            $this->tpl->parse('STORE', 'null');
        }
        $goodsSession = new Zend_Session_Namespace('goods');


        if (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$this->activePage['id']])) {
            $this->tpl->parse('PBUTTON', 'null');
        } else {
            $this->tpl->parse('NPBUTTON', 'null');
        }

        if ($econom <= 0) {
            $this->tpl->parse('CATALOG_DETAIL_PLASHKA', 'null');
        }


        if ($this->getCatOption('isForeshortening')) {
            $this->drowForeshortening($this->activePage['artikul']);
        }



        if ($this->getCatOption('isMiniGallery')) {
            $this->drowMiniGallery($this->activePage['artikul']);
        }


        if ($this->getCatOption('isComments')) {

            if (!$this->drowComments($this->activePage['artikul'])) {
                $this->tpl->parse('COMMENTS', 'null');
            } else {
                $this->tpl->parse('COMMENTS', '.comments');
            }
        }

        $this->tpl->parse('CONTENT', '.catalog_detail_body');


        return true;
    }

    public function addcomment() {

        if (!empty($_POST) && !$this->_err) {

            $fio = mysql_escape_string($this->getVar('fio', ''));
            $period_of_operation = mysql_escape_string($this->getVar('period_of_operation', ''));
            $dignity = mysql_escape_string($this->getVar('dignity', ''));
            $shortcomings = mysql_escape_string($this->getVar('shortcomings', ''));
            $recommendations = mysql_escape_string($this->getVar('recommendations', ''));
            $conclusion = mysql_escape_string($this->getVar('conclusion', ''));
            $captchaId = $this->getVar('captcha_id', '');
            $captchaInput = $this->getVar('captcha_input', '');
            $points = $this->getVar('star1', '0');
            $artikul = $this->getVar('artikul', '0');


            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
            $captchaIterator = $captchaSession->getIterator();

            @$captchaWord = $captchaIterator['word'];

            if (empty($fio)) {
                $this->addErr('Поле "Имя и фамилия" не должно быть пустым');
            }

            $validate = new Zend_Validate_EmailAddress();


            if (empty($period_of_operation)) {
                $this->addErr('Поле "Период эксплуатации" не должно быть пустым');
            }

            if (empty($dignity)) {
                $this->addErr('Поле "Достоинства" не должно быть пустым');
            }

            if (empty($shortcomings)) {
                $this->addErr('Поле "Недостатки" не должно быть пустым');
            }

            if (empty($recommendations)) {
                $this->addErr('Поле "Рекомендации" не должно быть пустым');
            }

            if (empty($conclusion)) {
                $this->addErr('Поле "Вывод" не должно быть пустым');
            }


            if (!$captchaWord) {
                $this->addErr('Проверочный код устарел. Пожалуйста введите код заново');
            } else {
                if (!$captchaInput) {
                    $this->addErr('Вы не ввели проверочный код');
                } else {
                    if ($captchaInput != $captchaWord)
                        $this->addErr('Ошибка. Введите проверочнй код повторно');
                }
            }

            $data = array(
                'date' => date('Y-m-d'),
                'goods_artikul' => $artikul,
                'fio' => $fio,
                'period_of_operation' => $period_of_operation,
                'dignity' => $dignity,
                'shortcomings' => $shortcomings,
                'recommendations' => $recommendations,
                'conclusion' => $conclusion,
                'points' => $points
            );
            $this->db->delete('comments', "goods_artikul='0'");
            $this->db->insert('comments', $data);
            $referrer = $_SERVER['HTTP_REFERER'];
            $this->tpl->parse('CONTENT', 'null');
            $content = "<h2>{COMMENT_MESSAGE}</h2> <meta http-equiv='refresh' content='2;URL=$referrer#comment-" . $this->db->lastInsertId() . "' />";
            $this->tpl->assign('CONTENT', $content);
            //$this->viewMessage($content);
            return true;
        }
        return false;
    }

    protected function drowComments($goodsArtlkul) {

        $this->tpl->parse('COMMENTS_LIST', 'null');

        $comments = $this->db->fetchAll("SELECT `id`, `fio`, `points`, `period_of_operation`, `dignity`, `shortcomings`, `recommendations`, `conclusion`, `points`, `tip_helpful_yes`,  `tip_helpful_no`, DATE_FORMAT(`date`, '%d/%m/%Y') as `date` FROM `comments` WHERE `goods_artikul` = '$goodsArtlkul' " . (!$this->_isAdmin() ? " AND `visible` = '1' " : '') . ' ORDER BY `id` DESC ');
        $counter = 0;
        if ($comments) {


            if (count($comments) > 0) {
                foreach ($comments as $comm) {
                    $rating = '';
                    $isAcive = true;

                    $pointIntVal = intval($comm['points']);

                    for ($i = 1; $i <= 5; $i++) {
                        if ($i > $pointIntVal) {
                            $isAcive = false;
                        }

                        if (!$isAcive) {
                            $rating .= '<div class="star-rating rater-0 star  star-rating-applied star-rating-live star-rating-readonly"><a title="2">2</a></div>';
                        } else {
                            $rating .= '<div class="star-rating rater-1 star {split:2} star-rating-applied star-rating-live star-rating-on"><a title="2">2</a></div>';
                        }
                    }

                    $descClassName = '';

                    if ($pointIntVal == 0 || $pointIntVal == 1) {
                        $descClassName = 'desc_s';
                    }

                    if ($pointIntVal == 2 || $pointIntVal == 3) {
                        $descClassName = 'desc_m';
                    }


                    $this->tpl->assign(
                            array(
                                'COMMENT_GOODS_ID' => $comm['id'],
                                'COMMENT_LIST_DATE' => $comm['date'],
                                'COMMENT_LIST_ADMIN' => '', // '<p class="right">' . $this->getAdminEdit('comments', $comm['id']) . '</p>',
                                'COMMENT_LIST_CLASS' => ($counter == 0 ? 'class="first"' : ''),
                                'COMMENT_LIST_FIO' => $comm['fio'],
                                'COMMENT_LIST_PERIOD_OF_OPERATION' => $comm['period_of_operation'],
                                'COMMENT_LIST_DIGNITY' => $comm['dignity'],
                                'COMMENT_LIST_SHORTCOMMINGS' => $comm['shortcomings'],
                                'COMMENT_LIST_RECOMENDATIONS' => $comm['recommendations'],
                                'COMMENT_LIST_CONCLUSION' => $comm['conclusion'],
                                'COMMENT_HELPFUL_YES_VAL' => ($comm['tip_helpful_yes'] != '' ? $comm['tip_helpful_yes'] : '0' ),
                                'COMMENT_HELPFUL_NO_VAL' => (!empty($comm['tip_helpful_no']) ? $comm['tip_helpful_no'] : '0'),
                                'COMMENT_LIST_RATING' => $rating,
                                'COMMENT_LIST_DESC_CLASS_NAME' => $descClassName,
                                'CAPTCHA_ERROR' => ''
                            )
                    );


                    if (!isset($_SESSION['is_tip_helpful'][$comm['id']][$goodsArtlkul])) {
                        $this->tpl->parse('COMMENT_HELPFUL_YES', 'comment_helpful_yes');
                        $this->tpl->parse('COMMENT_HELPFUL_NO', 'null');
                    } else {
                        $this->tpl->parse('COMMENT_HELPFUL_YES', 'null');
                        $this->tpl->parse('COMMENT_HELPFUL_NO', 'comment_helpful_no');
                    }

                    $this->tpl->parse('COMMENTS_LIST', '.comments_list');

                    $counter++;
                }
            } else {

            }
        } else {

        }

        $captcha = new Zend_Captcha_Png(array(
                    'name' => 'cptch',
                    'wordLen' => 6,
                    'timeout' => 1800,
                ));

        $fio = mysql_escape_string($this->getVar('fio', ''));
        $period_of_operation = mysql_escape_string($this->getVar('period_of_operation', ''));
        $dignity = mysql_escape_string($this->getVar('dignity', ''));
        $shortcomings = mysql_escape_string($this->getVar('shortcomings', ''));
        $recommendations = mysql_escape_string($this->getVar('recommendations', ''));
        $conclusion = mysql_escape_string($this->getVar('conclusion', ''));
        $captchaId = $this->getVar('captcha_id', '');
        $captchaInput = $this->getVar('captcha_input', '');
        $points = $this->getVar('star1', '0');



        $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
        $captcha->setStartImage('./img/captcha.png');
        $id = $captcha->generate();

        if (!empty($_POST) && $this->_err) {
            $this->viewErr();
        }



        if (!empty($_POST) && !$this->_err) {

            $fio = '';
            $dignity = '';
            $period_of_operation = '';
            $shortcomings = '';
            $recommendations = '';
            $conclusion = '';
        }

        $this->tpl->assign(array(
            'CAPTCHA_ID' => $id,
            'COMMENT_FIO' => $fio,
            'COMMENT_PERIOD_OF_OPERATION' => $period_of_operation,
            'COMMENT_DIGNITY' => $dignity,
            'COMMENT_SHORTCOMMINGS' => $shortcomings,
            'COMMENT_RECOMENDATIONS' => $recommendations,
            'COMMENT_CONCLUSION' => $conclusion,
            'COMMENT_GOODS_ARTIKUL' => $goodsArtlkul,
        ));


        // $this->tpl->parse('CATALOG_DETAIL_BODY', '.comments');
        return ($counter > 0);
    }

}