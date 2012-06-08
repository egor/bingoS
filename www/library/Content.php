<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Content extends Main_Abstract implements Main_Interface {

    public function factory() {
        return true;
    }

    public function main()
   {
        $href = end($this->url);

        $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `language` = '" . $this->lang . "' AND `href` = '" . $href . "'");

        if (!$page) {
            return $this->error404();
        }

        $this->generateWayFromUri();

        $this->setWay($page['header'], $page['type'] == 'section' ? $this->mergeUrlArray() : null);

        //$page['header'] = $this->getAdminEdit('page', $page['id']) . $page['header'];
        $h1AdminButtonOptions = array(
            'section'   => array('url'=>'/admin/addsection/'.$page['id'], 'text'=>'Раздел'),
            'page'      => array('url'=>'/admin/addpage/'.$page['id'], 'text'=>'Страницу'),
            'link'      => array('url'=>'/admin/addlink/'.$page['id'], 'text'=>'Ссылку'),
        );

        if (($isChildrenSection = $this->db->fetchOne("SELECT `type` FROM `page` WHERE `level`='$page[id]' LIMIT 1"))) {
            if ($isChildrenSection == 'section') {
                unset($h1AdminButtonOptions['page']);
            }

            if ($isChildrenSection == 'page') {
                unset($h1AdminButtonOptions['section']);
            }
        }

        if ($page['type'] == 'page') {
            $h1AdminButtonOptions = false;
        }

        $this->setH1Admin('/admin/editpage/'.$page['id'], '/admin/deletepage/'.$page['id'], $h1AdminButtonOptions );

        $this->setMetaTags($page);

        $pagesEdit = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                array(
                    'BUTTON_EDIT_URL'       => '/admin/editpage/' . $page['id'],
                    'BUTTON_EDIT_TITLE'     => 'Редактировать элемент',
                    'BUTTON_DELETE_URL'     => '/admin/deletepage/' . $page['id'],
                    'BUTTON_DELETE_TITLE'   => 'Удалить элемент'
                )
            );

            $templates->parse('BUTTON_SETTINGS', 'null');
            $templates->parse('BUTTON_FEATURES', 'null');

            $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

            $pagesEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
        }

        $this->tpl->assign(array('CONTENT' => $pagesEdit .'<div class="text">'. stripslashes($page['body']).'</div>'));

        if ($page['type'] == 'section') {
            $this->loadSectionPage((int) $page['id']);
        }

        return true;
    }

    private function loadSectionPage($id = null)
    {
        if (null === $id) {
            return true;
        }

        $url = $this->mergeUrlArray();

        $navbar = $navTop = $navBot = '';

        $num_news = $this->settings['num_news'];

        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }

        $selectItem = $this->db->select();
        $selectItem->from('page');
        $selectItem->where('level = "?"', $id);
        $selectItem->order(array('position', 'date DESC'));

        $selectCount = $this->db->select();
        $selectCount->from('page', array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));
        $selectCount->where('level = "?"', $id);

        if (!$this->_isAdmin()) {
            $selectItem->where('visibility = "1"');
            $selectCount->where('visibility = "1"');
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($selectItem);
        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num_news);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, $url);

        $this->tpl->define_dynamic('_section', 'pages.tpl');
        $this->tpl->define_dynamic('section', '_section');
        $this->tpl->define_dynamic('section_row', 'section');
        $this->tpl->define_dynamic('section_admin', 'section_row');

        $pagesAdd = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                array(
                    'ADD_SECTION_URL'      => '/admin/addsection/' . $id,
                    'ADD_SECTION_TITLE'    => 'Добавить раздел',
                    'ADD_PAGE_URL'      => '/admin/addpage/' . $id,
                    'ADD_PAGE_TITLE'    => 'Добавить страницу',
                    'ADD_LINK_URL'      => '/admin/addlink/' . $id,
                    'ADD_LINK_TITLE'    => 'Добавить ссылку'
                )
            );

            $templates->parse('ADMIN_BUTTONS_ADD', 'admin_buttons_add');

            $pagesAdd = $templates->prnt_to_var('ADMIN_BUTTONS_ADD');
        }

        $this->tpl->assign('PAGES_LIST_ADMIN', $pagesAdd);

        foreach ($paginator as $row) {
            $this->tpl->assign(
                array(
                    'PAGE_ID'           => $row['id'],
                    'PAGE_ADRESS'       => $url . $row['href'],
                    'PAGE_HEADER'       => stripslashes($row['header']),
                    'PAGE_PREVIEW'      => stripslashes($row['preview'])
                )
            );

            $pagesEdit = '';
            if ($this->_isAdmin()) {
                $templates->assign(
                    array(
                        'BUTTON_EDIT_URL'       => '/admin/editpage/' . $row['id'],
                        'BUTTON_EDIT_TITLE'     => 'Редактировать элемент',
                        'BUTTON_DELETE_URL'     => '/admin/deletepage/' . $row['id'],
                        'BUTTON_DELETE_TITLE'   => 'Удалить элемент'
                    )
                );

                $templates->parse('BUTTON_SETTINGS', 'null');
                $templates->parse('BUTTON_FEATURES', 'null');

                $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

                $pagesEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
            }

            $this->tpl->assign('PAGES_ITEM_ADMIN', $pagesEdit);

            $this->tpl->parse('SECTION_ROW', '.section_row');
        }

        $this->tpl->assign(
            array(
                'PAGINATION' => $navbar
            )
        );

        $this->tpl->parse('CONTENT', '.section');

        return true;




        $start = 0;
        $navbar = $navTop = $navBot = '';
        $page = 1;

        $num_pages = $this->settings['num_page_items'];

        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `page` WHERE `language` = '" . $this->lang . "' AND `level` = '" . $id . "'");

        if ($count > 0) {
            if ($count > $num_pages) {
                if (isset($this->getParam['page'])) {
                    $page = (int) $this->getParam['page'];
                    $start = $num_pages * $page - $num_pages;

                    if ($start > $count) {
                        $start = 0;
                    }
                }

                $navbar = $this->loadPaginator((int) ceil($count / $num_pages), (int) $page, $url);

                if ($navbar) {
                    $navTop = '<div class="pager_right">' . $navbar . '</div>';
                    $navBot = $navTop;
                }
            }

            $this->tpl->define_dynamic('_section', 'pages.tpl');
            $this->tpl->define_dynamic('section', '_section');
            $this->tpl->define_dynamic('section_row', 'section');

            $pages = $this->db->fetchAll("SELECT * FROM `page` WHERE `language` = '" . $this->lang . "' AND `level` = '" . $id . "' ORDER BY `position`, `header` LIMIT $start, $num_pages");

            $adminClassName = '';

            if ($this->_isAdmin()) {
                $adminClassName = 'plashka-admin-button';
            }

            foreach ($pages as $page) {
                $this->setAdminButtons('/admin/editpage/'.$page['id'], '/admin/deletepage/'.$page['id']);

                $this->tpl->assign(
                    array(
                        'ADMIM_CLASS_NAME' =>$adminClassName,// $this->getAdminEdit('page', $page['id']),
                        'PAGE_ADRESS' => $url . $page['href'],
                        'PAGE_HEADER' => stripslashes($page['header']),
                        'PAGE_PREVIEW' => stripslashes($page['preview'])
                    )
                );

                $this->tpl->parse('SECTION_ROW', '.section_row');
            }

            $this->tpl->assign(
                array(
                    'PAGES_TOP' => $navTop,
                    'PAGES_BOTTOM' => $navBot
                )
            );

            $this->tpl->parse('CONTENT', '.section');
        }
    }

   public function index() {
       $this->tpl->parse('IS_NO_INDEX_PAGE', 'null');
       $this->tpl->parse('IS_INDEX_PAGE', 'is_index_page');

       $this->tpl->parse('MAIN_LOGO_URL', 'null');
       $this->tpl->parse('MAIN_LOGO_TEXT', 'main_logo_text');

       $this->tpl->parse('BREADCRUMBS', 'null');

      $this->loadBanners(true);
      $index = $this->db->fetchRow("SELECT `title`, `keywords`, `description`, `header`, `body` FROM `page` WHERE `href` = 'mainpage' AND `language` = '" . $this->lang . "'");

      if (!$index) {
         return $this->error404();
      }

      $this->setMetaTags($index);
      $this->setWay($index['header']);

      $this->tpl->parse('P_HEADER', 'null');

      $this->tpl->define_dynamic('main', 'main.tpl');
      $this->tpl->define_dynamic('main_news', 'main');
      $this->tpl->define_dynamic('main_index', 'main');

      $this->tpl->define_dynamic('main_new_goods', 'main');
      $this->tpl->define_dynamic('main_hit_goods', 'main');
      $this->tpl->define_dynamic('main_actions_goods', 'main');



      $catalogOptions = $this->getCatalogOptions();
      $isShowIndexNewGoods = true;
      $indexNewGoodsLength = 3;
      $isShowIndexHitsGoods = true;
      $indexHitLength = 3;
      $isShowIndexActionsGoods = true;
      $indexActionLength = 3;


      if (isset($catalogOptions['0']['is_show_new']) && $catalogOptions['0']['is_show_new'] == '0') {
         $isShowIndexNewGoods = false;
      }

      if (isset($catalogOptions['0']['new_index_length']) && intval($catalogOptions['0']['new_index_length']) > '0') {
         $indexNewGoodsLength = $catalogOptions['0']['new_index_length'];
      }

      if (isset($catalogOptions['0']['is_show_hits']) && $catalogOptions['0']['is_show_hits'] == '0') {
         $indexHitLength = false;
      }

      if (isset($catalogOptions['0']['hits_index_length']) && intval($catalogOptions['0']['hits_index_length']) > '0') {
         $indexHitLength = $catalogOptions['0']['hits_index_length'];
      }

      if (isset($catalogOptions['0']['is_show_actions']) && $catalogOptions['0']['is_show_actions'] == '0') {
         $isShowIndexActionsGoods = false;
      }

      if (isset($catalogOptions['0']['action_index_length']) && intval($catalogOptions['0']['action_index_length']) > '0') {
         $indexActionLength = $catalogOptions['0']['action_index_length'];
      }

      if ($isShowIndexNewGoods) {
         $this->indexNewGoods($indexNewGoodsLength);
      }

      if ($isShowIndexHitsGoods) {
         $this->indexHitsGoods($indexHitLength);
      }

      if ($isShowIndexActionsGoods) {
         $this->indexActionsGoods($indexActionLength);
      }

      //$this->topNews();
      //$this->indexNews();
      $this->newsTop();
      $this->newsIndex();

      //$this->indexArtikle();

      $this->tpl->assign(
              array(
                  'INDEX_HEADER' => stripslashes($index['header']),
                  'INDEX_BODY' => stripslashes($index['body']),
              )
      );
      $this->tpl->parse('CONTENT', '.main_index');

      return true;
   }

   protected function indexNewGoods($limit = 9) {

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

      $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' AND `status`='new' $visibleQuery  ORDER BY RAND() LIMIT $limit";

      $items = $this->db->fetchAll($sql);
      if ($items) {
        $this->tpl->assign(array('NEW_CATALOG_ITEMS' => $this->_goodsList($items)));
        $this->tpl->parse('CONTENT', '.main_new_goods');
      }

   }

   protected function indexHitsGoods($limit = 9) {

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

      $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' AND `status`='hit' $visibleQuery ORDER BY RAND() LIMIT $limit";

      $items = $this->db->fetchAll($sql);
      if ($items) {
        $this->tpl->assign(array('HIT_CATALOG_ITEMS' => $this->_goodsList($items)));
        $this->tpl->parse('CONTENT', '.main_hit_goods');
      }

   }

   protected function indexActionsGoods($limit = 9) {

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

      $sql = "SELECT * FROM `catalog` WHERE `type` = 'page' AND `status`='action' $visibleQuery  ORDER BY RAND() LIMIT $limit";
      $items = $this->db->fetchAll($sql);
      if ($items) {
        $this->tpl->assign(array('ACT_CATALOG_ITEMS' => $this->_goodsList($items)));
        $this->tpl->parse('CONTENT', '.main_actions_goods');
      }

   }

   private function indexNews() {
      $news = $this->db->fetchAll("SELECT * FROM `news` WHERE `visibility` = '1'  ORDER BY `top` DESC, `date`  DESC , `header`  LIMIT 0, " . $this->settings['num_lastnews']);

      if ($news) {
         $this->tpl->define_dynamic('_news', 'news.tpl');
         $this->tpl->define_dynamic('news', '_news');
         $this->tpl->define_dynamic('news_item', 'news');

         foreach ($news as $item) {
            $pic = '';
            if ($item['pic'] != '' && file_exists('./img/news/' . $item['pic'])) {
               $pic = '<a href="/news/' . $item['href'] . '" title="' . $item['header'] . '"><img src="/img/news/' . $item['pic'] . '" alt="' . $item['header'] . '" width="137" height="102" align="left" style="margin-right: 10px;"></a>';
            }

            $this->tpl->assign(
                    array(
                        'ADM_EDIT' => '',
                        'DATE' => $this->convertDate($item['date']),
                        'NEWS_ADRESS' => $item['href'],
                        'NEWS_HEADER' => stripslashes($item['header']),
                        'PIC' => $pic,
                        'NEWS_PREVIEW' => stripslashes($item['preview'])
                    )
            );

            $this->tpl->parse('NEWS_ITEM', '.news_item');
         }

         $this->tpl->assign(
                 array(
                     'PAGES_TOP' => '',
                     'PAGES_BOTTOM' => ''
                 )
         );

         $this->tpl->parse('CONTENT', '.main_news');
         //$this->tpl->parse('NEWS_INDEX_SEP', '.news_index_sep');

         $this->tpl->parse('CONTENT', '.news');
      }
   }

   private function indexArtikle() {
      $num_pages = $this->settings['num_page_items'];

      $this->tpl->define_dynamic('_section', 'pages.tpl');
      $this->tpl->define_dynamic('section', '_section');
      $this->tpl->define_dynamic('section_row', 'section');

      $pages = $this->db->fetchAll("SELECT * FROM `page` WHERE `top` = '1' AND `type` = 'page' ORDER BY RAND() LIMIT 0, " . $num_pages);

      if (sizeof($pages) > 0) {
         $groups = $this->db->fetchAll("
                SELECT `id`, `header`, `href`, `level`
                FROM `page`
                WHERE `type` = 'section' AND `language` = '" . $this->lang . "'
            ");

         foreach ($pages as $item) {
            $this->tpl->assign(
                    array(
                        'PAGE_ADM' => '',
                        'PAGE_ADRESS' => $this->getHref($groups, $page['level']) . $page['href'],
                        'PAGE_HEADER' => stripslashes($page['header']),
                        'PAGE_PREVIEW' => stripslashes($page['preview'])
                    )
            );

            $this->tpl->parse('SECTION_ROW', '.section_row');
         }

         $this->tpl->parse('CONTENT', '.section');
      }
   }

   private function topNews() {
      $news = $this->db->fetchAll("SELECT * FROM `news` WHERE `top` = '1' ORDER BY `date` DESC");

      $this->tpl->define_dynamic('_news', 'news.tpl');
      $this->tpl->define_dynamic('news_top', '_news');
      $this->tpl->define_dynamic('news_top_item', 'news_top');

      if (sizeof($news) > 0) {
         foreach ($news as $item) {
            $pic = '';
            if ($item['pic'] != '' && file_exists('./img/news/' . $item['pic'])) {
               $pic = '<div class="nz_i"><a href="/news/' . $item['href'] . '" title="' . $item['header'] . '"><img src="/img/news/' . $item['pic'] . '" width="143px" height="107px" alt="' . $item['header'] . '" /></a></div>';
            }

            $this->tpl->assign(
                    array(
                        'NEWS_ADRESS' => $item['href'],
                        'NEWS_HEADER' => stripslashes($item['header']),
                        'PIC' => $pic,
                        'NEWS_PREVIEW' => stripslashes($item['preview'])
                    )
            );

            $this->tpl->parse('NEWS_TOP_ITEM', '.news_top_item');
         }

         $this->tpl->parse('CONTENT', '.news_top');
      }
   }

    public function news()
    {
        if (!isset ($this->url[1]) || empty ($this->url[1])) {
            return $this->newsList();
        }

        if (isset ($this->url[2]) && !empty ($this->url[2])) {
            return $this->error404();
        }

        return $this->newsDetail($this->url[1]);
    }

    protected function newsList()
    {
        $this->tpl->define_dynamic('_news', 'news.tpl');
        $this->tpl->define_dynamic('news', '_news');
        $this->tpl->define_dynamic('news_admin_buttons', 'news');
        $this->tpl->define_dynamic('news_item', 'news');
        $this->tpl->define_dynamic('news_admin', 'news_item');
        $this->tpl->define_dynamic('news_item_pic', 'news_item');

        $newsAdd = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                array(
                    'ADD_PAGE_URL'      => '/admin/addnews',
                    'ADD_PAGE_TITLE'    => 'Добавить новость'
                )
            );

            $templates->parse('ADMIN_BUTTON_ADD_SECTION', 'null');
            $templates->parse('ADMIN_BUTTON_ADD_LINK', 'null');

            $templates->parse('ADMIN_BUTTONS_ADD', 'admin_buttons_add');

            $newsAdd = $templates->prnt_to_var('ADMIN_BUTTONS_ADD');
        }

        $this->tpl->assign('NEWS_LIST_ADMIN', $newsAdd);

        $navbar = '';

        $num_news = $this->settings['num_news'];

        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }

        $selectItem = $this->db->select();
        $selectItem->from('news');
        $selectItem->order(array('date DESC', 'header'));

        $selectCount = $this->db->select();
        $selectCount->from('news', array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));

        if (!$this->_isAdmin()) {
            $selectItem->where('visibility = "1"');
            $selectCount->where('visibility = "1"');
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($selectItem);
        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($num_news);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, '/news/');

        if (count($paginator)) {
            $adminClassName = 'plashka-button';

            if ($this->_isAdmin()) {
                $adminClassName = 'plashka-admin-button';
            }

            foreach ($paginator as $row) {
                if ($row['pic'] != '' && file_exists('./img/news/' . $row['pic'])) {
                    $this->tpl->assign('NEWS_ITEM_SRC_PIC', '/img/news/' . $row['pic']);
                    $this->tpl->parse('NEWS_ITEM_PIC', 'news_item_pic');
                } else {
                    $this->tpl->parse('NEWS_ITEM_PIC', 'null');
                }

                $this->setAdminButtons('/admin/editnews/'.$row['id'], '/admin/deletenews/'.$row['id']);

                $this->tpl->assign(
                    array(
                        'NEWS_ID'           => $row['id'],
                        'ADMIM_CLASS_NAME'  => $adminClassName,
                        'DATE'              => $this->convertDate($row['date']),
                        'NEWS_ADRESS'       => $row['href'],
                        'NEWS_HEADER'       => stripslashes($row['header']),
                        'NEWS_PREVIEW'      => stripslashes($row['preview'])
                    )
                );

                $newsEdit = '';
                if ($this->_isAdmin()) {
                    $templates->assign(
                        array(
                            'BUTTON_EDIT_URL'       => '/admin/editnews/' . $row['id'],
                            'BUTTON_EDIT_TITLE'     => 'Редактировать новость',
                            'BUTTON_DELETE_URL'     => '/admin/deletenews/' . $row['id'],
                            'BUTTON_DELETE_TITLE'   => 'Удалить новость'
                        )
                    );

                    $templates->parse('BUTTON_SETTINGS', 'null');
                    $templates->parse('BUTTON_FEATURES', 'null');

                    $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

                    $newsEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
                }

                $this->tpl->assign('NEWS_ITEM_ADMIN', $newsEdit);

                $this->tpl->parse('NEWS_ITEM', '.news_item');
            }

            $this->setH1Admin(false, false, '/admin/addnews/' );

            $this->tpl->assign(
                array(
                    'PAGINATION' => $navbar,
                    'NEWS_BLOCK_CLASS' => 'posts'
                )
            );

            $this->tpl->parse('CONTENT', '.news');
        } else {
            $this->setH1Admin(false, false, '/admin/addnews/' );
            $this->tpl->assign('CONTENT',  '{EMPTY_SECTION}');
        }

        return true;
    }

    protected function newsDetail($url = null)
    {
        if (null === $url) {
            return $this->error404();
        }

        $this->tpl->define_dynamic('_news', 'news.tpl');
        $this->tpl->define_dynamic('news', '_news');
        $this->tpl->define_dynamic('news_detail', 'news');
        $this->tpl->define_dynamic('news_detail_admin', 'news_detail');

        $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `language` = '" . $this->lang . "' AND `href` = '$url'");

        if (!$news) {
            return $this->error404();
        }

        $date = $this->convertDate($news['date']);

        $this->setWay($date . ' / ' . $news['header']);

        $header = $news['header'];
        //$news['header'] = $this->getAdminEdit('news', $news['id']) . '<span>' . $date . '</span> / ' . $news['header'];

        $this->setH1Admin('/admin/editnews/'.$news['id'], '/admin/deletenews/'.$news['id'], false);

        $this->setMetaTags($news);
        $pic = '';

        if (is_file(PATH . 'img/news/' . $news['pic'])) {
            $pic = '<img src="/img/news/' . $news['pic'] . '" class="page_image" alt="' . $header . '" title="' . $header . '" />';
            //$pic = '<img class="news_image" src="/img/news/' . $news['pic'] . '" width="137" height="102" alt="' . $header . '" title="' . $header . '" />';
        }

        $this->tpl->assign(
            array(
                'NEWS_ID'       => $news['id'],
                'NEWS_BODY'     => stripslashes($news['body']),
                'NEWS_DATE'     => '<span>' . $date . '</span>',
                'NEWS_TITLE'    => $header,
                'NEWS_PREVEW'   => stripslashes($news['preview']),
                'PIC'           => $pic
            )
        );

        $newsEdit = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                array(
                    'BUTTON_EDIT_URL'       => '/admin/editnews/' . $news['id'],
                    'BUTTON_EDIT_TITLE'     => 'Редактировать новость',
                    'BUTTON_DELETE_URL'     => '/admin/deletenews/' . $news['id'],
                    'BUTTON_DELETE_TITLE'   => 'Удалить новость'
                )
            );

            $templates->parse('BUTTON_SETTINGS', 'null');
            $templates->parse('BUTTON_FEATURES', 'null');

            $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

            $newsEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
        }

        $this->tpl->assign('NEWS_DETAIL_ADMIN', $newsEdit);

        $this->tpl->parse('CONTENT', 'news_detail');

        return true;
    }

    protected function newsIndex()
    {
        $news = $this->db->fetchAll("SELECT * FROM `news` WHERE `visibility` = '1' AND `top` = '0' ORDER BY `date` DESC , `header` LIMIT 0, " . $this->settings['num_lastnews']);

        if ($news) {
            $this->tpl->define_dynamic('_news', 'news.tpl');
            $this->tpl->define_dynamic('news', '_news');
            $this->tpl->define_dynamic('news_item', 'news');
            $this->tpl->define_dynamic('news_item_pic', 'news_item');
            $this->tpl->parse('NEWS_ITEM', 'null');

            $this->tpl->assign('NEWS_LIST_ADMIN', '');

            foreach ($news as $item) {
                if ($item['pic'] != '' && file_exists('./img/news/' . $item['pic'])) {
                    $this->tpl->assign('NEWS_ITEM_SRC_PIC', '/img/news/' . $item['pic']);
                    $this->tpl->parse('NEWS_ITEM_PIC', 'news_item_pic');
                } else {
                    $this->tpl->parse('NEWS_ITEM_PIC', 'null');
                }

                $this->tpl->assign(
                    array(
                        'NEWS_ID'       => $item['id'],
                        'DATE'          => $this->convertDate($item['date']),
                        'NEWS_ADRESS'   => $item['href'],
                        'NEWS_HEADER'   => stripslashes($item['header']),
                        'NEWS_PREVIEW'  => stripslashes($item['preview'])
                    )
                );

                $this->tpl->assign('NEWS_ITEM_ADMIN', '');

                $this->tpl->parse('NEWS_ITEM', '.news_item');
            }

            $this->tpl->assign(
                array(
                    'PAGINATION' => '',
                    'NEWS_BLOCK_CLASS' => 'block_posts'
                )
            );

            $this->tpl->parse('CONTENT', '.main_news');

            $this->tpl->parse('CONTENT', '.news');
        }
    }

    protected function newsTop()
    {
        $news = $this->db->fetchAll("SELECT * FROM `news` WHERE `visibility` = '1' AND `top` = '1' ORDER BY `date` DESC , `header` LIMIT 0, " . $this->settings['num_lastnews']);

        if ($news) {
            $this->tpl->define_dynamic('_news', 'news.tpl');
            $this->tpl->define_dynamic('news', '_news');
            $this->tpl->define_dynamic('news_item', 'news');
            $this->tpl->define_dynamic('news_item_pic', 'news_item');
            $this->tpl->parse('NEWS_ITEM', 'null');

            $this->tpl->assign('NEWS_LIST_ADMIN', '');

            foreach ($news as $item) {
                if ($item['pic'] != '' && file_exists('./img/news/' . $item['pic'])) {
                    $this->tpl->assign('NEWS_ITEM_SRC_PIC', '/img/news/' . $item['pic']);
                    $this->tpl->parse('NEWS_ITEM_PIC', 'news_item_pic');
                } else {
                    $this->tpl->parse('NEWS_ITEM_PIC', 'null');
                }

                $this->tpl->assign(
                    array(
                        'NEWS_ID'       => $item['id'],
                        'DATE'          => $this->convertDate($item['date']),
                        'NEWS_ADRESS'   => $item['href'],
                        'NEWS_HEADER'   => stripslashes($item['header']),
                        'NEWS_PREVIEW'  => stripslashes($item['preview'])
                    )
                );

                $this->tpl->assign('NEWS_ITEM_ADMIN', '');

                $this->tpl->parse('NEWS_ITEM', '.news_item');
            }

            $this->tpl->assign(
                array(
                    'PAGINATION' => '',
                    'NEWS_BLOCK_CLASS' => 'block_posts'
                )
            );

            $this->tpl->parse('CONTENT', '.main_news');

            $this->tpl->parse('CONTENT', '.news');
        }
    }

   public function _news($index = false) {
      $this->tpl->define_dynamic('_news', 'news.tpl');
      $this->tpl->define_dynamic('news', '_news');
      $this->tpl->define_dynamic('news_item', 'news');
      $this->tpl->define_dynamic('news_detail', '_news');

      if (!isset($this->url[1])) {
         $start = 0;
         $navbar = $navTop = $navBot = '';
         $page = 1;

         if ($index) {
            $num_news = $this->settings['num_lastnews'];
         } else {
            $num_news = $this->settings['num_news'];

            $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `news` WHERE `language` = '" . $this->lang . "'");

            if ($count > 0) {
               if ($count > $num_news) {
                  if (isset($this->getParam['page'])) {
                     $page = (int) $this->getParam['page'];
                     $start = $num_news * $page - $num_news;

                     if ($start > $count) {
                        $start = 0;
                     }
                  }

                  $navbar = $this->loadPaginator((int) ceil($count / $num_news), (int) $page, $this->basePath . 'news/');

                  if ($navbar) {
                     $navTop = '<div class="pager_right">' . $navbar . '</div>';
                     $navBot = $navTop;
                  }
               }
            } else {
                $this->setH1Admin(false, false, '/admin/addnews/' );
               $this->tpl->assign('CONTENT',  '{EMPTY_SECTION}');
               return true;
            }
         }



         $news = $this->db->fetchAll("SELECT * FROM `news` WHERE `language` = '" . $this->lang . "'" . ($this->_isAdmin() ? "" : " AND `visibility` = '1'") . "" . ($index ? ' AND `top` = "0"' : '') . " ORDER BY `date` DESC LIMIT " . $start . ", " . $this->settings['num_news']);

         $adminClassName = '';

         if ($this->_isAdmin()) {
            $adminClassName = 'plashka-admin-button';
         }

         foreach ($news as $item) {
            $pic = '';
            if ($item['pic'] != '' && file_exists('./img/news/' . $item['pic'])) {
               $pic = '<a href="/news/' . $item['href'] . '" title="' . $item['header'] . '"><img src="/img/news/' . $item['pic'] . '" alt="' . $item['header'] . '" alt="' . $item['title'] . '"  width="150" height="114" align="left" style="margin-right: 10px;" /></a>';
            }

            $this->setAdminButtons('/admin/editnews/'.$item['id'], '/admin/deletenews/'.$item['id']);

            $this->tpl->assign(
                    array(
                        'ADMIM_CLASS_NAME' =>$adminClassName,
                        'DATE' => $this->convertDate($item['date']),
                        'NEWS_ADRESS' => $item['href'],
                        'NEWS_HEADER' => stripslashes($item['header']),
                        'PIC' => $pic,
                        'NEWS_PREVIEW' => stripslashes($item['preview'])
                    )
            );

            $this->tpl->parse('NEWS_ITEM', '.news_item');
         }

         $header = '';
         if ($index) {
            $header = $this->db->fetchOne('SELECT `header` FROM `meta_tags` WHERE `href` = "news" AND `language` = "' . $this->lang . '"');
         }

         $this->setH1Admin(false, false, '/admin/addnews/' );

         $this->tpl->assign(
                 array(
                     'PAGES_TOP' => $navTop,
                     'PAGES_BOTTOM' => $navBot,
                     'NEWS_HEADER' => $header,
                     'CONTENT' => ''
                 )
         );

         if (@$count > 0) {
            if ($index) {
               $this->tpl->parse('CONTENT', '.main_news');
            }

            $this->tpl->parse('CONTENT', '.news');
         }
      } elseif (!isset($this->url[2])) {
         $href = end($this->url);

         $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `language` = '" . $this->lang . "' AND `href` = '$href'");

         if (!$news) {
            return $this->error404();
         }

         $date = $this->convertDate($news['date']);

         $this->setWay($date . ' / ' . $news['header']);

         $header = $news['header'];
         //$news['header'] = $this->getAdminEdit('news', $news['id']) . '<span>' . $date . '</span> / ' . $news['header'];

           $this->setH1Admin('/admin/editnews/'.$news['id'], '/admin/deletenews/'.$news['id'], false);

         $this->setMetaTags($news);
         $pic = '';

         if (is_file(PATH . 'img/news/big/' . $news['pic'])) {
            $pic = '<img src="/img/news/big/' . $news['pic'] . '" width="170" height="124" alt="' . $header . '" title="' . $header . '" />';
         }

         $this->tpl->assign(
                 array(
                     'NEWS_BODY' => stripslashes($news['body']),
                     'NEWS_DATE' => '<span>' . $date . '</span>',
                     'NEWS_TITLE' => $header,
                     'NEWS_PREVEW' => stripslashes($news['preview']),
                     'PIC' => $pic
                 )
         );

         $this->tpl->parse('CONTENT', 'news_detail');
      } else {
         return $this->error404();
      }

      return true;
   }

   public function kontakti() {
        $this->setMetaTags("Контакты");
      $this->setWay("Контакты");
      $this->tpl->define_dynamic('feedback', 'feedback.tpl');

      $admin_email = $this->settings['admin_email'];

      $fio = $this->getVar('fio', '');
      $email = $this->getVar('email', '');
      $body = $this->getVar('body', '');
      $captchaId = $this->getVar('captcha_id', '');
      $captchaInput = $this->getVar('captcha_input', '');

      // Переменные для сообщений об ошиках и название класса ошибок

      $fioErrorMessage = '';
      $fioErrorClassName = '';

      $emailErrorMessage = '';
      $emailErrorClassName = '';

      $bodyErrorMessage = '';
      $bodyErrorClassName = '';

      $captchaErrorMessage = '';
      $captchaErrorClassName = '';

      if (!empty($_POST)) {
         if (empty($fio)) {
             $this->addErr('{EMPTY_FIO}');
             $fioErrorMessage = '{EMPTY_FIO}';
             $fioErrorClassName = 'error';
         }


         $validate = new Zend_Validate_EmailAddress();
         if (!$validate->isValid($email)) {
            $emailErrorMessage =  '{WRONG_EMAIL}';
            $emailErrorClassName = 'error';

            $this->addErr('{WRONG_EMAIL}');
         }

         if (!$body) {
            $this->addErr('{EMPTY_TEXT}');
            $bodyErrorClassName = 'error';
            $bodyErrorMessage = '{EMPTY_TEXT}';
         }

         $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
         $captchaIterator = $captchaSession->getIterator();
         @$captchaWord = $captchaIterator['word'];


         if (!$captchaWord)
            $captchaErrorMessage = 'Проверочный код устарел. Пожалуйста введите код заново';
         else {
            if (!$captchaInput)
               $captchaErrorMessage = 'Вы не ввели проверочный код';
            else {
               if ($captchaInput != $captchaWord)
                  $captchaErrorMessage = 'Ошибка. Введите проверочнй код повторно';
            }
         }
      }

      if (!empty($captchaErrorMessage)) {
          $this->addErr($captchaErrorMessage);
          $captchaErrorClassName = 'error';
      }



      if (!empty($_POST) && !$this->_err) {
         $body = "Ф.И.О.: $fio <br>E-Mail: $email <br>Сообщение:<br>$body";
         $subject = "Сообщение с сайта http://" . $_SERVER['HTTP_HOST'] . '/';

         $mail = new Zend_Mail('utf-8');
         $mail->setBodyHtml($body);
         $mail->setFrom('webmaster@' . $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST']);
         //$mail->addTo($admin_email, 'Administrator');
         $mail->setSubject($subject);
         //$mail->send();

         $admin_emails = explode(',', $this->settings['admin_email']);

        foreach ($admin_emails as $email) {
            $mail->addTo(trim($email), 'Администратор');
            $mail->send();
            $mail->clearRecipients();
        }

         $this->tpl->assign('CONTENT', '{MESSAGE_SENT}');
      }

      $this->tpl->assign(array(
         'EMAIL_ERROR_MESSAGE'=>$emailErrorMessage,
         'EMAIL_ERROR_CLASS_NAME'=>$emailErrorClassName,

        'FIO_ERROR_MESSAGE'=>$fioErrorMessage,
        'FIO_ERROR_CLASS_NAME'=>$fioErrorClassName,

        'BODY_ERROR_MESSAGE'=>$bodyErrorMessage,
        'BODY_ERROR_CLASS_NAME'=>$bodyErrorClassName,

        'CAPTCHA_ERROR_MESAGE' =>$captchaErrorMessage,
        'CAPTCHA_ERROR_CLASS_NAME'=>$captchaErrorClassName
      ));


      if (empty($_POST) || $this->_err) {
         $captcha = new Zend_Captcha_Png(array(
                     'name' => 'cptch',
                     'wordLen' => 6,
                     'timeout' => 1800,
                 ));
         $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
         $captcha->setStartImage('./img/captcha.png');
         $id = $captcha->generate();
         $kontakti = $this->db->fetchRow("SELECT * FROM `page` WHERE `href` = 'kontakti'");

        $this->setMetaTags($kontakti);
        $this->tpl->assign(array('KONTAKTI_BODY' => (!empty($kontakti) ) ? $kontakti['body'] : ''));

         $this->tpl->assign(
                 array(
                     'FIO' => $fio,
                     'EMAIL' => $email,
                     'BODY' => $body,
                     'CAPTCHA_ID' => $id,

                 )
         );
         $this->tpl->parse('CONTENT', '.feedback');
      }

      return true;
   }

   public function feedback() {
      $this->setMetaTags("Контакты");
      $this->setWay("Контакты");

      $this->tpl->define_dynamic('feedback', 'feedback.tpl');

      $admin_email = $this->settings['admin_email'];

      $fio = $this->getVar('fio', '');
      $email = $this->getVar('email', '');
      $body = $this->getVar('body', '');
      $captchaId = $this->getVar('captcha_id', '');
      $captchaInput = $this->getVar('captcha_input', '');

      if (!empty($_POST)) {
         if (!$fio)
            $this->addErr('{EMPTY_FIO}');

         $validate = new Zend_Validate_EmailAddress();
         if (!$validate->isValid($email))
            $this->addErr('{WRONG_EMAIL}');

         if (!$body)
            $this->addErr('{EMPTY_TEXT}');

         $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
         $captchaIterator = $captchaSession->getIterator();
         @$captchaWord = $captchaIterator['word'];

         if (!$captchaWord)
            $this->addErr('Проверочный код устарел. Пожалуйста введите код заново');
         else {
            if (!$captchaInput)
               $this->addErr('Вы не ввели проверочный код');
            else {
               if ($captchaInput != $captchaWord)
                  $this->addErr('Ошибка. Введите проверочнй код повторно');
            }
         }
      }

      if (!empty($_POST) && !$this->_err) {
         $body = "Ф.И.О.: $fio <br>E-Mail: $email <br>Сообщение:<br>$body";
         $subject = "Сообщение с сайта http://" . $_SERVER['HTTP_HOST'] . '/';

         $mail = new Zend_Mail('utf8');
         $mail->setBodyHtml($body);
         $mail->setFrom('webmaster@' . $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST']);
         //$mail->addTo($admin_email, 'Administrator');
         $mail->setSubject($subject);
         //$mail->send();

         $admin_emails = explode(',', $this->settings['admin_email']);

        foreach ($admin_emails as $email) {
            $mail->addTo(trim($email), 'Администратор');
            $mail->send();
            $mail->clearRecipients();
        }

         $this->tpl->assign('CONTENT', '{MESSAGE_SENT}');
      }

      if ($this->_err) {
         $this->viewErr();
      }


      //kontakti

      if (empty($_POST) || $this->_err) {
         $captcha = new Zend_Captcha_Png(array(
                     'name' => 'cptch',
                     'wordLen' => 6,
                     'timeout' => 1800,
                 ));
         $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
         $captcha->setStartImage('./img/captcha.png');
         $id = $captcha->generate();

         $this->tpl->assign(
                 array(
                     'FIO' => $fio,
                     'EMAIL' => $email,
                     'BODY' => $body,
                     'CAPTCHA_ID' => $id
                 )
         );
         $this->tpl->parse('CONTENT', '.feedback');
      }


      return true;
   }

   public function sitemap() {
      $elems_h = $this->db->fetchAll('SELECT * FROM `page` WHERE `level` = "0" AND `menu` = "horisontal" AND `visibility` = "1" AND `language` = "' . $this->lang . '" ORDER BY `position`, `header`');

      $elems_v = $this->db->fetchAll('SELECT * FROM `page` WHERE `level` = "0" AND `menu` = "vertical" AND `visibility` = "1" AND `language` = "' . $this->lang . '" ORDER BY `position`, `header`');

      $map = '<div class="text">';
      $map .= '<ul>';
      $map .= '<li><a href="' . $this->basePath . '" title="{FIRST_WAY}">{FIRST_WAY}</a><ul>';

      $isCatalog = false;

      foreach ($elems_h as $elem) {
         $map .= "<li><a href='" . (($elem['type'] != 'link') ? $this->basePath : '') . $elem['href'] . "' title='" . $elem['header'] . "'>" . $elem['header'] . "</a>";

         if ($elem['href'] == '/catalog') {
            $isCatalog = true;
            $catalog = $this->db->fetchAll("SELECT `header`, `href`, `id` FROM `catalog` WHERE `level` = '0' ");
            if ($catalog) {
               $map .= '<ul>';
               foreach ($catalog as $cat) {
                  $map .= "<li><a href='" . $this->basePath . 'catalog/' . $cat['href'] . "' title='" . $cat['header'] . "'>" . $cat['header'] . "</a>";
                  $catalog1 = $this->db->fetchAll("SELECT `header`, `href`, `name` FROM `catalog` WHERE `level` = '$cat[id]' ");
                  if ($catalog1) {
                     $map .= '<ul>';
                     foreach ($catalog1 as $cat1) {
                        $map .= "<li><a href='" . $this->basePath . 'catalog/' . $cat['href'] . '/' . $cat1['href'] . "' title='" . $cat1['header'] . "'>" . $cat1['name'] . "</a></li>";
                     }
                     $map .= '</ul>';
                  }
                  $map .= "</li>";
               }
               $map .= '</ul>';
            }
         }

         if ($elem['type'] == 'section') {
            $sub = $this->db->fetchAll("SELECT * FROM `page` WHERE `level` = '" . $elem['id'] . "'");

            if ($sub) {
               $map .= "<ul>";

               foreach ($sub as $s) {

                  $url = ($s['type'] == 'link') ? $s['href'] : $this->basePath . $elem['href'] . '/' . $s['href'];
                  $map .= '<li><a href="' . $url . (($s['type'] == 'section') ? ('/') : ('')) . '" title="' . $s['header'] . '">' . $s['header'] . '</a></li>';
               }
               $map .= "</ul></li>";
            }
         }
         $map .= "</li>";
      }

      foreach ($elems_v as $elem) {
         $map .= "<li><a href='" . (($elem['type'] != 'link') ? $this->basePath : '') . $elem['href'] . "' title='" . $elem['header'] . "'>" . $elem['header'] . "</a>";

         if ($elem['type'] == 'section') {
            $sub = $this->db->fetchAll("SELECT * FROM `page` WHERE `level` = '" . $elem['id'] . "'");

            if ($sub) {
               $map .= "<ul>";

               foreach ($sub as $s) {

                  if (isset($s['href']) && isset($elem['href']) && isset($url)) {
                     $map .= '<li><a href="' . $url . (($s['type'] == 'section') ? ('/') : ('')) . '" title="' . $s['header'] . '">' . $s['header'] . '</a></li>';
                  }
               }
               $map .= "</ul></li>";
            }
         }
         $map .= "</li>";
      }

      $map .= "</ul></li></ul>";

      if (!$isCatalog) {
         $map .= "<ul class=\"sitemap\"><li><a href='/catalog' title='{CATTITLE}'>{CATTITLE}</a>";
         $catalog = $this->db->fetchAll("SELECT `header`, `href`, `id` FROM `catalog` WHERE `level` = '0' ");
         if ($catalog) {
            $map .= '<ul>';
            foreach ($catalog as $cat) {
               $map .= "<li><a href='" . $this->basePath . 'catalog/' . $cat['href'] . "' title='" . $cat['header'] . "'>" . $cat['header'] . "</a>";
               $catalog1 = $this->db->fetchAll("SELECT `header`, `href`, `name` FROM `catalog` WHERE `level` = '$cat[id]' ");
               if ($catalog1) {
                  $map .= '<ul>';
                  foreach ($catalog1 as $cat1) {
                     $map .= "<li><a href='" . $this->basePath . 'catalog/' . $cat['href'] . '/' . $cat1['href'] . "' title='" . $cat1['header'] . "'>" . $cat1['name'] . "</a></li>";
                  }
                  $map .= '</ul>';
               }
               $map .= "</li>";
            }
            $map .= '</ul>';
         }
      }

      $map .= '</div>';

      $this->tpl->assign(array('CONTENT' => $map));

      return true;
   }

   public function enter() {
      $this->tpl->define_dynamic('_enter', 'users.tpl');
      $this->tpl->define_dynamic('enter', '_enter');

      $email = $this->getVar('login');
      $password = $this->getVar('password');

      if (empty($email) && empty($password)) {
          $this->viewMessage('<script type="text/javascript">jQuery(function(){jQuery( "#dialog" ).dialog("open");});</script>');
          return true;
      }

      if (!empty($_POST)) {
         if (null === $email) {
            $this->addErr('Введите Логин');
         }

         if (null === $password) {
            $this->addErr('Введите Пароль');
         }
      }

      if (!empty($_POST) && !$this->_err) {
         $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
         $authAdapter->setTableName('users');
         $authAdapter->setIdentityColumn('login');
         $authAdapter->setCredentialColumn('pass');

         $authAdapter->setIdentity($email);
         $authAdapter->setCredential(crypt($password, $this->cryptKey));

         $auth = Zend_Auth::getInstance();
         $result = $auth->authenticate($authAdapter);

         if ($result->isValid()) {
            $data = $authAdapter->getResultRowObject(null, 'pass');
            //var_dump($data);
            $auth->getStorage()->write($data);

            $this->viewMessage('Здравствуйте, ' . Zend_Auth::getInstance()->getIdentity()->name . '!<meta http-equiv="refresh" content="1;URL=' . $this->basePath . '">');
         } else {
            $this->addErr('Логин или Пароль введен неверно');
         }
      }

      if ($this->_err) {
         $this->viewErr();
      }

      if (empty($_POST) || !$this->_err) {
        // $this->tpl->parse('CONTENT', '.enter');
          $this->viewMessage('<script type="text/javascript">jQuery(function(){jQuery( "#dialog" ).dialog("open");});</script>');
      }

      return true;
   }

   public function logout() {
      $this->tpl->assign(
              array(
                  'TITLE' => 'Завершение сеанса пользователя',
                  'KEYWORDS' => '',
                  'DESCRIPTION' => '',
                  'HEADER' => 'Завершение сеанса пользователя'
              )
      );

      Zend_Auth::getInstance()->clearIdentity();
      $this->viewMessage('<meta http-equiv="refresh" content="1;URL=' . $this->basePath . '">');

      return true;
   }

   private function getCountSearch($query = null, $table = null) {
      if (null === $query || null === $table) {
         return 'die';
      }

      $select = $this->db->select();
      $select->from(
              array($table), array('c' => 'COUNT(`id`)')
      );

      if ($table == 'page') {
         $select->where("type <> 'link'");
      }

      $select->where("header LIKE ('%$query%')");
      $select->orWhere("preview LIKE ('%$query%')");
      $select->orWhere("body LIKE ('%$query%')");

      $stmt = $this->db->query($select);
      $result = $stmt->fetchAll();

      return $result[0]['c'];
   }

   private function getResultSearch($query = null, $table = null) {
      if (null === $query || null === $table) {
         return 'die';
      }

      $select = $this->db->select();
      $select->from(
              array($table)
      );

      if ($table == 'page') {
         $select->where("type <> 'link'");
      }

      $select->where("header LIKE ('%$query%')");
      $select->orWhere("preview LIKE ('%$query%')");
      $select->orWhere("body LIKE ('%$query%')");

      $stmt = $this->db->query($select);
      $result = $stmt->fetchAll();

      return $result;
   }

   private function getHref($groups = null, $level = null) {
      if (null === $groups || null === $level) {
         return '';
      }

      $url = '/';

      while ($level != 0) {
         foreach ($groups as $group) {
            if ($group['id'] == $level) {
               $url = '/' . $group['href'] . $url;
               $level = $group['level'];
            }
         }
      }

      return $this->basePath . substr($url, 1);
   }

   private function getPreview($preview = null, $query = null) {
      return str_replace($query, '<span class="light">' . $query . '</span>', $preview);
   }

}