<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Catalog extends Main_Abstract implements Main_Interface
{

    protected $activePage;
    protected $_catalogItems;
    protected $_filterQueryUrl = array();
    private $filter = array(
        // Имя поля => параметры или просто заголовок который будет первым вываливается в списке
        'brand' => 'Бренд',
        'cost' => array(
            'title' => 'Цена',
            'type' => 'range' // Для диапазонных значений
        ),
    );
    private $сountGoodsRange = array(9, 36, 72);

    public function factory()
    {
        /*    try {
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

          $oldArticle = $database->fetchAll("SELECT * FROM `catalog` WHERE `link` = '" . end($this->url) . "' AND `visibility` = '1' AND `type` = 'page'");

          if ($oldArticle) {
          $newArticle = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `artikul` = '" . $oldArticle[0]['artikul'] . "' AND `type` = 'page'");

          if (!$newArticle) {
          return true;
          }

          $item = $this->dataTreeManager($newArticle['id']);

          if (empty($item) || !isset($item['links']) || $item['links'] == '') {
          return true;
          }

          header("HTTP/1.1 301 Moved Permanently");
          header("Location: /catalog/" . $item['links']);
          exit();
          }
         */
        return true;
    }

    public function main()
    {
        $url = $this->url;

        array_shift($url);

        if (empty($url)) {
            return $this->_viewGroups();
        }

        $queryIN = '"' . implode('", "', $url) . '"';
        //echo "SELECT * FROM `catalog` WHERE href IN ($queryIN) ORDER BY `level`";
        //exit();
        $pages = $this->db->fetchAll("SELECT * FROM `catalog` WHERE `href` IN ($queryIN) ORDER BY `level`");

        if (!$pages || count($pages) != count($url)) {
            return $this->error404();
        }

        if ($pages[0]['level'] != "0") {
            return $this->error404();
        }

        for ($i = 0; $i < count($url); $i++) {
            if ($pages[$i]['href'] != $url[$i]) {
                return $this->error404();
            }
        }

        $item = array_pop($pages);
        $this->_catalogItems = $pages;

        $breadcrumbsUrl = '/catalog';
        foreach ($pages as $page) {
            $breadcrumbsUrl .= '/' . $page['href'];
            $this->setWay($page['name'], $breadcrumbsUrl);
        }

        $this->setMetaTags($item);
        $this->setWay($item['name']);

        if ($item['type'] == 'page') {
            return $this->_viewItemDetail($item);
        }

        $type = $this->db->fetchOne("SELECT `type` FROM `catalog` WHERE `level` = '" . $item['id'] . "' LIMIT 1");

        if (!$type) {
            $this->tpl->assign('CONTENT', '{GOODS_EMPTY}');
            return true;
        }

        switch ($type) {
            case 'section' :
                return $this->_viewGroups($item['id']);
            case 'page' :
                return $this->_viewItemList($item['id']);
            default :
                return $this->error404();
        }
    }

    protected function _viewGroups($level = 0)
    {
        $parentAdminUrls = array(
            'edit' => $level > 0 ? '/admin/editcatsection/' . $level : '/admin/editmetatag/' . $this->_defaultMeta['id'],
            'delete' => $level > 0 ? '/admin/deletecatsection/' . $level : false
        );

        $this->setH1Admin(
                $parentAdminUrls['edit'], $parentAdminUrls['delete'], array(
            'section' => array(
                'url' => '/admin/addcatsection/' . $level,
                'text' => 'Раздел'
            )
                )
        );

        $pageUrl = '/' . implode('/', $this->url);

        $navbar = $navTop = $navBot = '';

        $num_catalog = $this->settings['num_catalog'];

        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }

        $select = $this->db->select();
        $select->from('catalog', array('*'));
        $select->where('level = ?', array($level));
        $select->order(array('position', 'name'));

        if (!$this->_isAdmin()) {
            $select->where('visibility = ?', array('1'));
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);

        $selectCount = clone $select;
        $selectCount->reset(Zend_Db_Select::COLUMNS);
        $selectCount->columns(array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));

        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num_catalog);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, $pageUrl);

        /* if ($navbar) {
          $navTop = '<div class="pager_right">' . $navbar . '</div>';
          $navBot = $navTop;
          } */

        $this->tpl->define_dynamic('section', 'catalog/section.tpl');
        $this->tpl->define_dynamic('section_list', 'section');
        $this->tpl->define_dynamic('section_list_row', 'section_list');
        $this->tpl->define_dynamic('section_list_pic', 'section_list_row');
        $this->tpl->define_dynamic('section_list_row_user', 'section_list_row');
        $this->tpl->define_dynamic('section_list_row_admin', 'section_list_row');
        $this->tpl->define_dynamic('section_empty', 'section');

        $catAdd = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                    array(
                        'ADD_SECTION_URL' => '/admin/addcatsection/' . $level,
                        'ADD_SECTION_TITLE' => 'Добавить раздел',
                        'ADD_PAGE_URL' => '/admin/addcatpage/' . $level,
                        'ADD_PAGE_TITLE' => 'Добавить страницу'
                    )
            );

            if ($level == 0) {
                $templates->parse('ADMIN_BUTTON_ADD_PAGE', 'null');
            } elseif (count($paginator)) {
                $items = $paginator->getCurrentItems();
                if ($items[0]['type'] == 'section') {
                    $templates->parse('ADMIN_BUTTON_ADD_PAGE', 'null');
                } else {
                    $templates->parse('ADMIN_BUTTON_ADD_SECTION', 'null');
                }
            }

            $templates->parse('ADMIN_BUTTON_ADD_LINK', 'null');
            $templates->parse('ADMIN_BUTTONS_ADD', 'admin_buttons_add');

            $catAdd = $templates->prnt_to_var('ADMIN_BUTTONS_ADD');
        }

        $this->tpl->assign('CATALOG_SECTION_LIST_ADMIN', $catAdd);

        if (count($paginator)) {
            foreach ($paginator as $row) {
                $itemUrl = $pageUrl . '/' . $row['href'];

                $pic = '';
                if ($row['pic'] != '' && file_exists('./img/catalog/section/' . $row['pic'])) {
                    $pic = $row['pic'];
                }

                $this->tpl->assign(
                        array(
                            'SECTION_ID' => $row['id'],
                            'SECTION_LIST_SRC_PIC' => $pic,
                            'SECTION_HREF' => $itemUrl,
                            'SECTION_NAME' => $row['name'],
                            'SECTION_PREVIEW' => null === $row['preview'] ? '' : stripslashes($row['preview'])
                        )
                );

                if ($pic == '') {
                    $this->tpl->parse('SECTION_LIST_PIC', 'null');
                }

                $catEdit = '';
                if ($this->_isAdmin()) {
                    $templates->assign(
                            array(
                                'BUTTON_EDIT_URL' => '/admin/editcatsection/' . $row['id'],
                                'BUTTON_EDIT_TITLE' => 'Редактировать раздел',
                                'BUTTON_DELETE_URL' => '/admin/deletecatsection/' . $row['id'],
                                'BUTTON_DELETE_TITLE' => 'Удалить раздел',
                                'BUTTON_FEATURES_URL' => '/admin/changecatsection/' . $row['id'],
                                'BUTTON_FEATURES_TITLE' => 'Характеристика раздела',
                                'BUTTON_SETTINGS_URL' => '/admin/catalogoptions/' . $row['id'],
                                'BUTTON_SETTINGS_TITLE' => 'Настройки раздела'
                            )
                    );

                    //$templates->parse('BUTTON_SETTINGS', 'null');
                    //$templates->parse('BUTTON_FEATURES', 'null');

                    $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

                    $catEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
                }

                $this->tpl->assign('CATALOG_SECTION_ITEM_ADMIN', $catEdit);

                $this->tpl->parse('SECTION_LIST_ROW', '.section_list_row');
            }

            $this->tpl->assign(
                    array(
                        'PAGINATION' => $navbar
                    )
            );

            $this->tpl->parse('CONTENT', '.section_list');
        } else {
            $this->tpl->parse('CONTENT', '.section_empty');
        }

        return true;
    }

    protected function _viewItemList($level = null)
    {
        if (null === $level) {
            return $this->error404();
        }

        $parentAdminUrls = array(
            'edit' => '/admin/editcatsection/' . $level,
            'delete' => '/admin/deletecatsection/' . $level
        );

        $this->setH1Admin(
                $parentAdminUrls['edit'], $parentAdminUrls['delete'], array(
            'section' => array(
                'url' => '/admin/addcatpage/' . $level,
                'text' => 'Товар'
            )
                )
        );

        $pageUrl = '/' . implode('/', $this->url);
        $navbarUrl = '';

        $navbar = $navTop = $navBot = '';

        $num_catalog = $this->getVar('show-length', $this->settings['num_catalog']);
        if ($num_catalog == 'all') {
            $num_catalog = 999999;
        }

        $num_catalog = (int) $num_catalog;
        if ($num_catalog <= 0) {
            return $this->error404();
        }

        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }

        $brand = $this->getVar('brand');
        $cost = $this->getVar('cost');
        $sort = $this->getVar('sort');
        $numItem = $this->getVar('show-length');

        $select = $this->db->select();
        $select->from('catalog', array('*'));
        $select->where('level = ?', array($level));
        $select->order(array(new Zend_Db_Expr('`cost` = 0'), 'position', 'name'));

        if (!$this->_isAdmin()) {
            $select->where('visibility = ?', array('1'));
        }

        if (!empty($brand)) {
            $this->_filterQueryUrl['brand'] = $brand;
            $navbarUrl .= "brand=$brand&";
            $select->where('brand = ?', array("$brand"));
        }

        if (null !== $cost) {
            $this->_filterQueryUrl['cost'] = $cost;
            $navbarUrl .= "cost=$cost&";
            $cost = explode('-', $cost);

            if (isset($cost[1]) && !empty($cost[1])) {
                $select->where('cost >= ?', array((int) $cost[0]));
                $select->where('cost <= ?', array((int) $cost[1]));
            }
        }

        //$select = $select->__toString();

        if (null !== $sort) {
            $this->_filterQueryUrl['sort'] = $sort;

            if ($sort != 'great' && $sort != 'lower') {
                return $this->error404();
            }

            if ($sort == 'great') {
                $orderType = ' DESC';
            } else {
                $orderType = ' ASC';
            }

            $select->reset(Zend_Db_Select::ORDER);
            //$select->order(array('cost' . $orderType, 'position', 'name'));
            $select->order(array(new Zend_Db_Expr('`cost` = 0'), 'cost' . $orderType, 'position', 'name'));
            //$select->order('cost = ?', array('0'));
            //$select->order(array('cost' . $orderType, 'position', 'name'));

            $navbarUrl .= "sort=$sort&";
        }
//echo $select->__toString();
        if (null !== $numItem) {
            $this->_filterQueryUrl['show-length'] = $numItem;
            $navbarUrl .= "show-length=$numItem&";
        }

        if ($navbarUrl) {
            $navbarUrl = $pageUrl . '?' . rtrim($navbarUrl, '&');
        }
//echo $select->__toString();
        $adapter = new Zend_Paginator_Adapter_DbSelect($select);

        $selectCount = clone $select;
        $selectCount->reset(Zend_Db_Select::COLUMNS);
        $selectCount->columns(array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));

        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num_catalog);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, $navbarUrl ? $navbarUrl : $pageUrl, $navbarUrl ? '&' : '?');

        /* if ($navbar) {
          $navTop = '<div class="pager2">' . $navbar . '</div>';
          $navBot = $navTop;
          } */

        //$countValue = $this->db->fetchAll("SELECT  max(`cost`) as `max`, min(`cost`) as `min`  FROM `catalog` WHERE `level` = '$level' " . ($this->_isAdmin() ? '' : 'AND `visibility` = "1"') . " AND `type` = 'page' AND `cost` > '0'  LIMIT 1");
        //$minCost = intval($countValue[0]['min']);
        //$maxCost = intval($countValue[0]['max']);

        $this->tpl->define_dynamic('list', 'catalog/list.tpl');
        $this->tpl->define_dynamic('cat_item_add', 'list');
        $this->tpl->define_dynamic('item_list', 'list');
        $this->tpl->define_dynamic('list_empty', 'list');

        if ($this->_isAdmin()) {
            $this->tpl->assign('CAT_PARENT_ID', $level);

            $this->tpl->parse('CONTENT', '.cat_item_add');
        }

        if (count($paginator)) {
            $items = array();

            foreach ($paginator as $row) {
                $items[] = $row;
            }

            //$this->_drowCountGoodsRange($num_catalog, $navbarUrl);

            $this->tpl->assign(
                    array(
                        'FILTER' => $this->_drowFilter($level, $this->filter),
                        //'range' => array(
                        //'min' => $minCost,
                        //'max' => $maxCost,
                        //))
                        //),
                        'FILTER_SORT' => $this->_sortLinksHTML($pageUrl, $sort),
                        'GOODS_LENGTH_RANGE' => $this->_rangeItemsOnPageHTML($paginator->getTotalItemCount(), $pageUrl, $num_catalog),
                        'CATALOG_ITEM' => $this->_goodsList($items),
                        'PAGINATION' => $navbar
                    )
            );

            $this->tpl->parse('CONTENT', '.item_list');
        } else {
            $this->tpl->parse('CONTENT', '.list_empty');
        }

        return true;
    }

    protected function _viewItemDetail($item = null)
    {
        $this->activePage = $item;

        $this->tpl->assign('ITEMSCOPE', ' itemscope itemtype="http://schema.org/Product"');

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
        
       

        $this->tpl->define_dynamic('store', 'catalog_detail_body');
        $this->tpl->define_dynamic('catalog_detail_plashka', 'store');

        $this->tpl->define_dynamic('pbutton', 'store');
        $this->tpl->define_dynamic('npbutton', 'store');
        $this->tpl->define_dynamic('availability_button', 'store');
        $this->tpl->parse('AVAILABILITY_BUTTON', 'null');

        $this->tpl->define_dynamic('availability_button1', 'store');
        $this->tpl->parse('AVAILABILITY_BUTTON1', 'null');



        $this->tpl->parse('CATALOG_FORESHORTENING_LIST', 'null');
        $this->tpl->parse('CATALOG_GALLERY_LIST', 'null');

        $sectionUrl = '/catalog';
        $sectionUrlText = '';

        $groupName = '';
        $groupUrl = '/catalog';

        foreach ($this->_catalogItems as $row) {
            $groupName = $row['name'];
            $groupUrl .= '/' . $row['href'];
        }

//        $item['header'] .= '  ' . $this->getAdminEdit('catpage', $item['id']);

        $this->setH1Admin('/admin/editcatpage/' . $item['id'], '/admin/deletecatpage/' . $item['id'], false);

        $commentsLength = $this->db->fetchOne("SELECT count(id) FROM `comments` WHERE `goods_artikul` = '$item[artikul]'  AND `visible` = '1'");
        if (isset($item['brand_rus'])) {
            $item['description'] = str_replace('__BRAND__', $item['brand'], $item['description']);
        }

        $item['description'] = str_replace('__URL__', $_SERVER['HTTP_HOST'], $item['description']);

        if (isset($item['brand_rus'])) {
            $item['description'] = str_replace('__BRAND_RU__', $item['brand_rus'], $item['description']);
        }

        $item['description'] = str_replace('__ARTIСUL__', $item['artikul'], $item['description']);
        $item['description'] = str_replace('__ARTIKUL__', $item['artikul'], $item['description']);
        $item['description'] = str_replace('__COST__', $item['cost'], $item['description']);
        $item['description'] = str_replace('__COMMENTS__', (is_numeric($commentsLength) && intval($commentsLength) > 0 ? "($commentsLength)" : ''), $item['description']);
        if (isset($item['guarantee'])) {
            $item['description'] = str_replace('__GARANTY__', $item['guarantee'], $item['description']);
        }
        $item['description'] = str_replace('__PHONE__', '{GOODS_META_PHONE}', $item['description']);

        $item['title'] = str_replace('__URL__', $_SERVER['HTTP_HOST'], $item['title']);
        if (isset($item['brand'])) {
            $item['title'] = str_replace('__BRAND__', $item['brand'], $item['title']);
        }

        if (isset($item['brand_rus'])) {
            $item['title'] = str_replace('__BRAND_RU__', $item['brand_rus'], $item['title']);
        }

        $item['title'] = str_replace('__ARTIKUL__', $item['artikul'], $item['title']);
        $item['title'] = str_replace('__ARTIСUL__', $item['artikul'], $item['title']);
        $item['title'] = str_replace('__COST__', $item['cost'], $item['title']);
        $item['title'] = str_replace('__COMMENTS__', (is_numeric($commentsLength) && intval($commentsLength) > 0 ? "($commentsLength)" : ''), $item['title']);
        if (isset($item['guarantee'])) {
            $item['title'] = str_replace('__GARANTY__', $item['guarantee'], $item['title']);
        }
        $item['title'] = str_replace('__PHONE__', '{GOODS_META_PHONE}', $item['title']);



        $this->setMetaTags($item);

        if (($dataTree = $this->dataTreeManager($item['id']))) {

            // Загрузка паффиндера

            $dataTreeLength = count($dataTree['names']);

            if ($dataTreeLength > 0) {
                //var_dump($dataTree);
                for ($i = 0; $i < $dataTreeLength; $i++) {
                    $url = '/catalog';
                    if ($i != ($dataTreeLength - 1)) {
                        $url .= '/' . $dataTree['linksArr'][$i];
                        //$groupName = $dataTree['names'][$i];
                        //$groupUrl = $url;
                    } else {
                        $url = null;
                    }

                    //$this->setWay($dataTree['names'][$i], $url);
                }
            }
        }
        
        $this->tpl->assign('COMMENTS', '');

        if ($this->getCatOption('isComments')) {
            require_once 'library/Comments.php';
            $commetns1 = new Comments(
                    array(
                        'db'=>$this->db,
                        'tpl'=>$this->tpl,
                        'artikul'=>$this->activePage['artikul'],
                        'header'=>$this->activePage['header'],
                        'goodsId'=>$this->activePage['id'],
                        'url'=>'/catalog/'.$dataTree['links'],
                        'isAdmin'=>$this->_isAdmin(),
                        'commentsLengthInPage'=>$this->settings['num_news']
                    ));
            $commetns1->items();
        }

        // Рейтинг товара
        $ratingGoods = $this->db->fetchRow("SELECT SUM(`points`) as spoints, COUNT(`id`) as `count` FROM `comments` WHERE `visible` = '1' AND `points` != '' AND `points` != '0' AND `goods_artikul` = '" . $item['artikul'] . "'");
      
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
       // $ratingValue = (int) $ratingValue;

        $rating = '';
        $isAcive = true;

   
        $ratingValue = round($ratingValue);
        $rating = "<span class=\"stars s_$ratingValue\"></span>";


        $pic = 'no-foto/no-foto-370x370.gif';
        $catalogDetailImgRealPath = '/img/';
        $catalogDetailImgBigPath = '/img/';

        $imgAlt = 'Нет фото';
        $imgTitle = $imgAlt;
        $loop = '';

       // var_dump(PATH . 'img/catalog/big/' . $item['pic']); die;
        if (is_file(PATH . 'img/catalog/big/' . $item['pic'])) {
            $pic = $item['pic'];
            $imgAlt = $item['pic_alt'];
            $imgTitle = $item['pic_title'];
            $catalogDetailImgRealPath = '/img/catalog/real/';
            $catalogDetailImgBigPath = '/img/catalog/big/';

            if (empty($imgAlt)) {
                $imgAlt = $item['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $item['name'];
            }
            $loop = '<span><a href="/img/catalog/real/' . $item['pic'] . '" rel="xbox[photo-item]" class="show_gallery"  title="' . $item['name'] . '">Увеличить</a></span>';
            //  $loop = '<span><a href="#"  title="Увеличить">Увеличить</a></span>';
        } elseif (is_file(PATH . 'img/catalog/big/' . $item['artikul'] . '.jpg')) {
            $pic = $item['artikul'] . '.jpg';
            $imgAlt = $item['pic_alt'];
            $imgTitle = $item['pic_title'];
            $catalogDetailImgRealPath = '/img/catalog/real/';
            $catalogDetailImgBigPath = '/img/catalog/big/';

            if (empty($imgAlt)) {
                $imgAlt = $item['name'];
            }

            if (empty($imgTitle)) {
                $imgTitle = $item['name'];
            }
            $loop = '<span><a href="/img/catalog/real/' . $item['artikul'] . '.jpg' . '" class="show_gallery" rel="xbox[photo-item]"  title="' . $item['name'] . '">Увеличить</a></span>';
            //  $loop = '<span><a href="#"  title="Увеличить">Увеличить</a></span>';
        }



        $costInShop = $item['cost_old'];
        $cost = $item['cost'];
        $econom = ($costInShop - $cost);
        $availability = 'Есть';

        $featuredProducts = $item['featured_products'];

        // Комплект

        $usedComplate = $item['used_complete'];



        if ($this->getCatOption('isUsedComplete') && !empty($usedComplate)) {
            // $usedComplate = str_replace(' ', '', $usedComplate);

            $usedComplate = " AND `artikul` IN('" . str_replace(',', "','", $usedComplate) . "')";
            $usedComplate = preg_replace("/\'\s+|\s+\'/", "'", $usedComplate);

            if (!$this->usedComplete($item['id'], $usedComplate)) {
                //complate_block
                $this->tpl->parse('COMPLATE_BLOCK', 'null');
                $this->tpl->assign(array('USED_COMPLATE_CATALOG_ITEM' => '', 'USED_COMPLATE_TITLE' => ''));
            } else {
                
            }
            $this->tpl->assign(array('USED_COMPLATE_TITLE' => '<b class="title">Комплект:</b>'));
        } else {
            $this->tpl->parse('COMPLATE_BLOCK', 'null');
            $this->tpl->assign(array('USED_COMPLATE_CATALOG_ITEM' => '', 'USED_COMPLATE_TITLE' => ''));
        }

        // Рекомендованные товары

        if ($this->getCatOption('isFeatured') && !empty($featuredProducts)) {
            // $featuredProducts = str_replace(' ', '', $featuredProducts);

            $featuredProducts = " AND `artikul` IN('" . str_replace(',', "','", $featuredProducts) . "')";
            $featuredProducts = preg_replace("/\'\s+|\s+\'/", "'", $featuredProducts);

            if (!$this->featuredProducts($item['id'], $featuredProducts)) {
                $this->tpl->parse('FEATURES_BLOCK', 'null');
                $this->tpl->assign(array('FEATURED_CATALOG_ITEM' => '', 'FEATURED_TITLE' => ''));
            } else {
                
            }
            $this->tpl->assign(array('FEATURED_TITLE' => '<b class="title">Рекомендованные товары:</b>'));
        } else {
            $this->tpl->parse('FEATURES_BLOCK', 'null');
            $this->tpl->assign(array('FEATURED_CATALOG_ITEM' => '', 'FEATURED_TITLE' => ''));
        }


        $imgType = '';

        if (isset($item['status']) && $item['status'] == 'hit') {
            $imgType = '<span class="hit">&nbsp;</span>';
        }

        if (isset($item['status']) && $item['status'] == 'action') {
            $imgType = '<span class="action">&nbsp;</span>';
        }

        if (isset($item['status']) && $item['status'] == 'new') {
            $imgType = '<span class="new">&nbsp;</span>';
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

        if ($item['availability'] == '0') {
            $this->tpl->parse('PBUTTON', 'null');
            $this->tpl->parse('NPBUTTON', 'null');
            $this->tpl->parse('AVAILABILITY_BUTTON', 'availability_button');
        } elseif ($item['availability'] == '2') {
            $this->tpl->parse('PBUTTON', 'null');
            $this->tpl->parse('NPBUTTON', 'null');
            $this->tpl->parse('AVAILABILITY_BUTTON1', 'availability_button1');
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


                    if (!($sectionFieldsValues = $this->db->fetchRow("SELECT * FROM `$tableName` WHERE `catalog_artikul` = '" . $item['artikul'] . "' "))) {
                        $sectionFieldsValues = array();
                    }

                    $data = array();
                    $sectionFields = "
                     
                    	<b class=\"title\">Характеристики $item[header]</b>
                     <table class='secton-fields'>\n";

                    //var_dump($groupNamesArray); die;



                    if (count($row) > 0) {
                        $isColor = true;
                        $group = '';
                        $group1 = '';
                        foreach ($row as $title => $value) {
                            if ($title != 'A2') {
                                if (!empty($title)) {
                                    $group1 = "<tr class='l'><td colspan='2'> $title</td></tr>\n";
                                } else {
                                    $group1 = "<tr class='l'><td colspan='2'> Общие характеристики</td></tr>\n";
                                }
                                
                            }

                            if (is_array($value) && count($value) > 0) {

                                $subGroup = '';


                                foreach ($value as $subTitle => $subValue) {
                                    $subGroup1 = '';
                                    if (is_array($subValue) && count($subValue) > 0) {
                                        if ($subTitle != 'A1' && $subTitle != 'A2') {
                                            $subGroup1 .= "<tr bgcolor='#e5eaee'><td colspan='2'>$subTitle</td></tr>\n";
                                        } elseif ($subTitle == 'A1') {
                                             //$subGroup1 .= "<tr class='l'><td colspan='2'>Общие характеристики</td></tr>\n";
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

                                                if (isset($item[$fieldValue['name']])) {
                                                    $val = $item[$fieldValue['name']];
                                                }
                                            }

                                            if ($fieldValue['layout'] == 'features_table' && $fieldValue['is_default_field'] == 'no') {

                                                if (!$this->_isAdmin()) {
                                                    if (!(empty($val) || $val == '') && $fieldValue['status'] != 'hidden') {
                                                        $isColor = !$isColor;
                                                        $fields .= "<tr " . ($isColor ? 'class="g"' : '') . "><td class='n'>$fieldValue[title]</td><td>$val </td></tr>\n";
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
                                                    $fields .= "<tr " . ($isColor ? 'class="g"' : '') . "><td class='n'>$fieldValue[title]</td><td>$val </td></tr>\n";
                                                }
                                            }



                                            if ($fieldValue['layout'] == 'right') {
                                                
                                           

                                                if (empty($val)) {
                                                    if (isset($item[$fieldValue['name']])) {
                                                        $val = $item[$fieldValue['name']];
                                                    }
                                                }



                                                if (!empty($fieldValue['title']) ) {
                                                    
                                                    if ($fieldValue['title'] == 'Краткое описание') {
                                                        $fieldValue['title'] = '';
                                                    }

                                                    if (is_numeric($val)) {
                                                        $val = substr($val, 0, 4);
                                                    }

                                                    if ($fieldValue['status'] == 'hidden') {
                                                        if ($this->_isAdmin()) {

                                                            if (empty($val)) {
                                                                $val = '-';
                                                            }
                                                            $rightFields .= "<tr><td><strong>".(!empty($fieldValue['title']) ? ":" : '').$fieldValue['title']." </strong>$val (Скрытое поле)</td></tr>";
                                                        }
                                                    } else {

                                                        if (empty($val)) {

                                                            if ($this->_isAdmin()) {

                                                                $rightFields .= "<tr><td><strong>".$fieldValue['title'].(!empty($fieldValue['title']) ? ":" : '')."  </strong>-</td></tr>";
                                                            }
                                                        } else {
                                                            $rightFields .= "<tr><td><strong>".$fieldValue['title'].(!empty($fieldValue['title']) ? ":" : '')." </strong>$val</td></tr>";
                                                        }
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
                                                    if (isset($item[$fieldValue['name']])) {
                                                        $val = $item[$fieldValue['name']];
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
                                                            if ($fieldValue['title'] == 'Подробное описание') {
                                                                $bottomFields .= "<h4 class=\"title_description\">Описание (Скрытое поле)<</h4>$val";
                                                            } else {
                                                                $bottomFields .= "<p>$fieldValue[title]:</p><p style='text-align: justify;'>$val (Скрытое поле)</p><div class=\"clear\"></div>";
                                                            }
                                                        }
                                                    } else {
                                                        if ($fieldValue['title'] == 'Подробное описание') {
                                                            $bottomFields .= "<h4 class=\"title_description\">Описание</h4>$val";
                                                        } else {
                                                            $bottomFields .= "<p>$fieldValue[title]:</p><p style='text-align: justify;'>$val</p><div class=\"clear\"></div>";
                                                        }
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


        if (isset($item['id_3d']) && !empty($item['id_3d'])) {
            $object3D = '<div id="' . $item['id_3d'] . '" class="3dstudio" title="' . $item['title_3d'] . '"></div>';
        }

        if (!$isFindBodyField) {
            $bottomFields .= "<p></p><p style='text-align: justify;'> " . $item['body'] . "</p><div class=\"clear\"></div>";
        }


        

        $this->tpl->assign(array(
            'CATALOG_DETAIL_RATING' => $rating,
            'CATALOG_DETAIL_IMG_REAL_PATH' => $catalogDetailImgRealPath,
            'CATALOG_DETAIL_IMG_BIG_PATH' => $catalogDetailImgBigPath,
            'CATALOG_DETAIL_ID' => $item['id'],
            'CATALOG_DETAIL_SECTION_URL' => $groupUrl,
            'CATALOG_DETAIL_SECTION_TEXT' => $groupName,
            'CATALOG_DETAIL_IMG' => $pic,
            'CATALOG_DETAIL_COST_IN_SHOP' => number_format($costInShop, 0, '', " "),
            'CATALOG_DETAIL_COST_ECONOM' => number_format($econom, 0, '', " "),
            'CATALOG_DETAIL_BRAND' => $item['brand'],
            'CATALOG_DETAIL_AVAILABILITY' => $availability,
            'CATALOG_DETAIL_PREWIEV' => (!empty($item['preview']) ? $item['preview'] : ''),
            'CATALOG_DETAIL_ALT' => (!empty($imgAlt) ? $imgAlt : ''),
            'CATALOG_DETAIL_TITLE' => (!empty($imgTitle) ? $imgTitle : ''),
            'CATALOG_DETAIL_COST' => number_format($cost, 0, ', ', " "),
            'CATALOG_DETAIL_ING_SRC' => $pic,
            'RIGHT_FIELDS' => $rightFields,
            'BOTTOM_FIELDS' => $bottomFields,
            'CATALOG_DETAIL_ING_TYPE' => $imgType,
            'CATALOG_DETAIL_USER_SECTION_FIELDS' => $sectionFields,
            'CATALOG_DETAIL_ING_LOOP' => $loop,
            'CATALOG_DETAIL_BODY1' => (!empty($item['body']) ? $item['body'] : ''),
            '3D_OBJECTS' => $object3D
        ));



        if (!$this->getCatOption('isStore')) {
            $this->tpl->parse('STORE', 'null');
        }
        $goodsSession = new Zend_Session_Namespace('goods');
//var_dump($goodsSession->array['basket']); die;
//var_dump($goodsSession->array['basket'][$item['id']]['status']); die;
        if (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$item['id']]) && 
                isset($goodsSession->array['basket'][$item['id']]['status']) && $goodsSession->array['basket'][$item['id']]['status'] == 'active') {
            $this->tpl->parse('PBUTTON', 'null');
        } else {
            $this->tpl->parse('NPBUTTON', 'null');
        }

        if ($econom <= 0) {
            $this->tpl->parse('CATALOG_DETAIL_PLASHKA', 'null');
        }


        if ($this->getCatOption('isForeshortening')) {
            $this->drowForeshortening($item['artikul']);
        }



        if ($this->getCatOption('isMiniGallery')) {
            $this->drowMiniGallery($item['artikul']);
        }


        if ($this->getCatOption('isComments')) {

          
        }

        $this->tpl->parse('CONTENT', '.catalog_detail_body');


        return true;
    }

    protected function _sortLinksHTML($pageUrl, $sort = null)
    {
        $query = $this->_filterQueryUrl;

        if (isset($query['sort'])) {
            unset($query['sort']);
        }

        $queryUrl = '';
        $symbol = '?';

        foreach ($query as $key => $value) {
            $queryUrl .= $key . "=" . $value . "&";
        }

        if ($queryUrl) {
            $queryUrl = '?' . rtrim($queryUrl, '&');
            $symbol = '&';
        }

        if (null === $sort) {
            return '<span class="arrow">▼ </span><a href="' . $pageUrl . $queryUrl . $symbol . 'sort=great">по убыванию</a> / <a href="' . $pageUrl . $queryUrl . $symbol . 'sort=lower">по возрастанию</a><span class="arrow"> ▲</span>';
        }

        if ($sort == 'great') {
            return '<span class="arrow">▼ </span><span>по убыванию</span> / <a href="' . $pageUrl . $queryUrl . $symbol . 'sort=lower">по возрастанию</a><span class="arrow"> ▲</span>';
        }

        if ($sort == 'lower') {
            return '<span class="arrow">▼ </span><a href="' . $pageUrl . $queryUrl . $symbol . 'sort=great">по убыванию</a> / <span>по возрастанию</span><span class="arrow"> ▲</span>';
        }

        return '';
    }

    protected function _rangeItemsOnPageHTML($numAllItems, $pageUrl, $numOnPage)
    {
        if ($numAllItems <= $this->сountGoodsRange[0]) {
            return '';
        }

        $query = $this->_filterQueryUrl;

        if (isset($query['show-length'])) {
            unset($query['show-length']);
        }

        $queryUrl = '';
        $symbol = '?';

        foreach ($query as $key => $value) {
            $queryUrl .= $key . "=" . $value . "&";
        }

        if ($queryUrl) {
            $queryUrl = '?' . rtrim($queryUrl, '&');
            $symbol = '&';
        }

        $html = '<span class="page-range">Отображать на странице по: ';

        foreach ($this->сountGoodsRange as $key => $val) {
            if ($numOnPage == $val) {
                $html .= "<span>$val</span>";
            } else {
                $html .= "<a href=\"" . $pageUrl . $queryUrl . $symbol . "show-length=$val\">$val</a>";
            }
        }

        if ($numOnPage == 999999) {
            $html .= "<span>Все</span>";
        } else {
            $html .= "<a href=\"" . $pageUrl . $queryUrl . $symbol . "show-length=all\">Все</a>";
        }

        $html .= '</span>';

        return $html;
    }

    /**
     * Выводит строку "Отображать на странице по: 9 36 72 Все"
     * Возврщает активное занчение из сессии
     * Данные берет из массива сountGoodsRange.
     */
    protected function _drowCountGoodsRange($goodsLength, $url = '?')
    {
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

                $goodsLengthRange .= "<span class=\"page-range\">Отображать на странице по: ";

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

        return $goodsLengthRange;

        $this->tpl->assign('GOODS_LENGTH_RANGE', $goodsLengthRange);
        return $return;
    }

    protected function _drowFilter($level, $options = array())
    {
        
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
                                    $ret .= "<select name='$field'  class='brands'>\n
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
    protected function drowGoodsList2Status($header, $status, $pagenUrl = '')
    {

        $this->setMetaTags($header);
        $this->setWay($header);

        /* $this->tpl->define_dynamic('_catalog', 'catalog.tpl');
          $this->tpl->define_dynamic('catalog', '_catalog');

          $this->tpl->define_dynamic('catalog_items_body', 'catalog');
          $this->tpl->define_dynamic('catalog_brands_items', 'catalog');
          $this->tpl->define_dynamic('catalog_items', 'catalog_items_body');

          $this->tpl->define_dynamic('cataog_items_empty', 'catalog'); */

        $num_catalog = (int) $this->settings['num_catalog'];
        if ($num_catalog <= 0) {
            return $this->error404();
        }

        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }

        $select = $this->db->select();
        $select->from('catalog', array('*'));
        $select->where('status = ?', array($status));
        $select->order(array(new Zend_Db_Expr('`cost` = 0'), 'position', 'name'));

        if (!$this->_isAdmin()) {
            $select->where('visibility = ?', array('1'));
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);

        $selectCount = clone $select;
        $selectCount->reset(Zend_Db_Select::COLUMNS);
        $selectCount->columns(array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));

        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num_catalog);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, $pagenUrl);

        $this->tpl->define_dynamic('list', 'catalog/list.tpl');
        $this->tpl->define_dynamic('item_list', 'list');
        $this->tpl->define_dynamic('list_empty', 'list');

        if (count($paginator)) {
            $items = array();

            foreach ($paginator as $row) {
                $items[] = $row;
            }

            //$this->_drowCountGoodsRange($num_catalog, $navbarUrl);

            $this->tpl->assign(
                    array(
                        //'FILTER' => $this->_drowFilter($level, array(
                        //'range' => array(
                        //'min' => $minCost,
                        //'max' => $maxCost,
                        //))
                        //),
                        //'FILTER_SORT' => $this->_sortLinksHTML($pagenUrl, $sort),
                        'FILTER' =>'',
                        'GOODS_LENGTH_RANGE' => $this->_rangeItemsOnPageHTML($paginator->getTotalItemCount(), $pagenUrl, $num_catalog),
                        'CATALOG_ITEM' => $this->_goodsList($items),
                        'PAGINATION' => $navbar
                    )
            );

            $this->tpl->parse('CONTENT', '.item_list');
        } else {
            $this->tpl->parse('CONTENT', '.list_empty');
        }

        return true;
        /*




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
            'CATALOG_ITEM' => $this->_goodsList($items),
            'TOP_NAV_BAR' => $navTop,
            'BOTT_NAV_BAR' => $navBot
        ));
        $this->tpl->parse('CONTENT', '.catalog');

        return true;*/
    }

    public function novelty()
    {
        $catalogOptions = $this->getCatalogOptions();
        if (isset($catalogOptions['0']['is_show_new']) && $catalogOptions['0']['is_show_new'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Новинки', 'new', '/catalog/novelty');
    }

    public function hits()
    {
        $catalogOptions = $this->getCatalogOptions();
        if (isset($catalogOptions['0']['is_show_hits']) && $catalogOptions['0']['is_show_hits'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Хиты', 'hit', '/catalog/hits');
    }

    public function actions()
    {
        $catalogOptions = $this->getCatalogOptions();
        if (isset($catalogOptions['0']['is_show_actions']) && $catalogOptions['0']['is_show_actions'] == '0') {
            return false;
        }
        return $this->drowGoodsList2Status('Акции', 'action', '/catalog/actions');
    }

    // Рекомендуемые товары
    protected function featuredProducts($level, $where = '')
    {

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

    protected function goodsListFeaturedProductsUsedComplete(array $items)
    {
        $itemsLength = count($items);

        if ($itemsLength > 0) {
            $this->tpl->parse('CATALOG_ITEMS_EMPTY', 'null');
        }

        $catItem1 = '';
        $catItem2 = '';
        $catItem3 = '';
        $catItem4 = '';
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
                        $liLastClass = 'last';
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

                    $catItem1 .= '<li class="p_item ' . $liLastClass . '"><a href="/catalog/' . $itemUrl . '">' . $imgType . '<img class="p_img" src="/img/' . $imgSrc . '" width="122" height="120" alt="' . $imgAlt . '" title="' . $imgTitle . '"/></a> </li> ';

                    $catItem2 .= '<li class="p_item ' . $liLastClass . '">';
                    $catItem3 .= '<li class="p_item ' . $liLastClass . '">';
                    $catItem4 .= '<li class="p_item ' . $liLastClass . '">';
                    if ($this->getCatOption('isStore')) {


                        //$buyButton = '<p class="right"><input type="button" onclick="addToBasket(\'goods_'.$items[$i]['id'].'\', 1);" value="Купить" class="button" /></p>';
                        $buyButton = '<a href="akces_block" class="p_add buy-button"  id="' . $items[$i]['id'] . '" >Купить</a>';

                        if (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$items[$i]['id']]) && 
                                isset($goodsSession->array['basket'][$items[$i]['id']]['status']) && $goodsSession->array['basket'][$items[$i]['id']]['status'] == 'active') {
                            $buyButton = '<a href="/basket" class="p_link_basket">Оформить заказ </a>';
                            //$buyButton = '<p class="order detail"><img width="14" height="13" alt="" src="/img/order.gif" class="img-no-border"><a href="/basket" class="a-buy">Перейти в корзину</a></p>';
                        }

                        if (empty($items[$i]['cost'])) {
                            $items[$i]['cost'] = '0';
                        }

                        if ($items[$i]['availability'] == '0') {
                            $buyButton = '<span class="p_add_disabled">{EXPECTED_TEXT}</span>';
                        } elseif ($items[$i]['availability'] == '2') {
                            $buyButton = '<span class="p_add_disabled">{EXPECTED_TEXT2}</span>';
                        }

                       // $catItem2.='<p class="newprice">Цена: <span>' . number_format($items[$i]['cost'], 0, '', '') . ' грн.</span></p>';
                        $catItem2.='<a class="p_name" href="/catalog/' . $itemUrl . '">'. $items[$i]['header'].'</a>';
                        $catItem3.= '<span class="price">' . number_format($items[$i]['cost'], 0, '', '') . ' грн.</span>';
                        $catItem4.= $buyButton;
                    } else {
                        $catItem2 .= "&nbsp;";
                        $catItem3 .= "&nbsp;";
                        $catItem4 .= "&nbsp;";
                    }
                    $catItem2 .= "</li>";
                    $catItem3 .= "</li>";
                    $catItem4 .= "</li>";
                }

                $f++;
                $i++;

                if ($f == 5) {
                    $f = 0;
                    $s+=5;
                    $catalogItem .='<ul class="p_images">' . $catItem1 . '</ul>';
                    $catalogItem .='<ul class="p_names">' . $catItem2 . '</ul>';
                    $catalogItem .='<ul class="p_specifications">' . $catItem3 . '</ul>';
                    $catalogItem .='<ul class="p_tobusket">' . $catItem4 . '</ul>';


                    $catItem1 = '';
                    $catItem2 = '';
                    $catItem3 = '';
                    $catItem4 = '';
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
    protected function usedComplete($level, $where = '')
    {

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
    protected function drowForeshortening($goodsArtikul)
    {

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
    protected function drowMiniGallery($goodsArtikul)
    {

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

  

}