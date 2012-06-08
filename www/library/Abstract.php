<?php

require_once PATH . 'library/AbstractBase.php';
define('ABSTRACT_PHP', 'ABSTRACT_PHP');

abstract class Main_Abstract extends Main_Abstract_Base {

    private $wayLinkCounter = 0;
    private $adminButtons = array();
    protected $_defaultMeta;

    public function __construct(array $config) {

        parent::__construct($config);

        $this->tpl->assign(array('SERVER_NAME' => $_SERVER['SERVER_NAME']));

        $this->loadBasket();

        $this->loadLookups();
        $this->loadMetaTags();

        $this->checkUser();

        //$this->loadAdminMenu();
        $this->loadHorisontalMenu();
        //$this->loadVerticalMenu();
        $this->loadCatalogMenu();

        $this->_isAuthUser();

        if (!$this->getCatOption('isStore')) {
            $this->tpl->parse('SHOW_ORDER', 'null');
        }

        if (isset($this->auth->privilege)) {
            if ($this->auth->privilege == 'admin') {
                $this->loadAdminMenu();
            }

            if ($this->auth->privilege == 'user') {
                $this->loadUserMenu();
            }
        }
    }

    protected function loadBasket() {
        $this->tpl->define_dynamic('basket', 'basket_block.tpl');
        $this->tpl->define_dynamic('basket_empty', 'basket');
        $this->tpl->define_dynamic('basket_full', 'basket');

        $goodsSession = new Zend_Session_Namespace('goods');

        if (!isset($goodsSession->array['basket']['totalCount']) || $goodsSession->array['basket']['totalCount'] <= 0) {
            $this->tpl->assign('BASKET_STATUS_CLASS', '');
            $this->tpl->parse('BASKET_BLOCK_CONTENT', 'basket_empty');
            /*$this->tpl->assign(
                    array(
                        'EMPTY_BASKET_ID_STATUS' => 'show',
                        'BASKET_ID_STATUS' => 'hide',
                        'BASKET_TOTAL_COUNT' => '',
                        'BASKET_TOTAL_SUMM' => '',
                        'BASKET_TOTAL_COUNT_TEXT' => ''
                    )
            );*/
        } else {
            $basketTotalSumm = 'товар';

            if ($goodsSession->array['basket']['totalCount'] >= 2 && $goodsSession->array['basket']['totalCount'] < 5) {
                $basketTotalSumm = 'товара';
            }

            if ($goodsSession->array['basket']['totalCount'] >= 5) {
                $basketTotalSumm = 'товаров';
            }

            $this->tpl->assign(
                    array(
                        'EMPTY_BASKET_ID_STATUS' => 'hide',
                        'BASKET_ID_STATUS' => 'show',
                        'BASKET_TOTAL_COUNT_TEXT' => $basketTotalSumm,
                        'BASKET_TOTAL_COUNT' => $goodsSession->array['basket']['totalCount'],
                        'BASKET_TOTAL_SUMM' => number_format($goodsSession->array['basket']['totalSumm'], 0, '', ' ')
                    )
            );

            $this->tpl->assign('BASKET_STATUS_CLASS', ' active');
            $this->tpl->parse('BASKET_BLOCK_CONTENT', 'basket_full');
        }
    }

    private function _isAuthUser() {

        if (null === $this->auth) {
            $this->tpl->assign(
                    array('ENTER_SITE_URL' => '/registration',
                        'ENTER_SITE_TITLE' => 'Зарегистрироваться',
                        'ENTER_SITE_URL2' => '/enter',
                        'ENTER_SITE_URL2_ID' => 'id="enter"',
                        'ENTER_SITE_URL2_TITLE' => 'Войти')
            );
        }

        if (isset($this->auth->privilege)) {
            $this->tpl->assign(
                    array('ENTER_SITE_URL' => '/user/profile',
                        'ENTER_SITE_TITLE' => (isset($this->auth->name) && !empty($this->auth->name) ? $this->auth->name : 'Пользователь'),
                        'ENTER_SITE_URL2' => '/logout',
                        'ENTER_SITE_URL2_ID' => '',
                        'ENTER_SITE_URL2_TITLE' => 'Выйти')
            );
        }
    }

    private function loadAdminMenu() {
        $this->tpl->parse('USER_MENU', 'null');
        //$this->setWay('Администрирование');
        if ($this->_isAdmin()) {
            $this->tpl->parse('ADMINISTRATION', 'administration');
            $this->tpl->parse('ADMINJSLIB', 'adminjslib');
        }
    }

    protected function loadUserMenu() {
        $this->tpl->parse('ADMINISTRATION', 'null');
        $this->tpl->parse('ADMINJSLIB', 'null');
        $this->tpl->parse('USER_MENU', '.user_menu');
    }

    protected function setH1Admin($editUrl, $deleteUrl, $addArray) {
        //$this->tpl->assign('H1_ADMIN_MENU', 'wwww');

        $this->tpl->define_dynamic('_h1_admin_panel', 'adm/h1_admin_panel.tpl');
        $this->tpl->define_dynamic('h1_admin_panel', '_h1_admin_panel');
        $this->tpl->define_dynamic('h1_admin_add', 'h1_admin_panel');
        $this->tpl->define_dynamic('h1_admin_delete', 'h1_admin_panel');
        $this->tpl->define_dynamic('h1_admin_edit', 'h1_admin_panel');
        $this->tpl->define_dynamic('h1_admin_add_simple', 'h1_admin_panel');
        $this->tpl->define_dynamic('h1_admin_add_section', 'h1_admin_add');
        $this->tpl->define_dynamic('h1_admin_add_page', 'h1_admin_add');
        $this->tpl->define_dynamic('h1_admin_add_link', 'h1_admin_add');

        $this->tpl->parse('H1_ADMIN_ADD_SIMPLE', 'null');



        if (!$this->_isAdmin()) {
            $this->tpl->parse('H1_ADMIN_PANEL', 'null');
            return true;
        } else {


            if (!$addArray) {
                $this->tpl->parse('H1_ADMIN_ADD', 'null');
            } elseif (is_array($addArray)) {

                $isShowAddButtonSection = true;
                $isShowAddButtonPage = true;
                $isShowAddButtonLink = true;

                if (!isset($addArray['section']['url']) || !isset($addArray['section']['text'])) {
                    $this->tpl->parse('H1_ADMIN_ADD_SECTION', 'null');
                    $isShowAddButtonSection = false;
                } else {
                    $this->tpl->assign(array(
                        'H1_ADMIN_ADD_SECTION_URL' => $addArray['section']['url'],
                        'H1_ADMIN_ADD_SECTION_TEXT' => $addArray['section']['text']
                    ));
                }

                if (!isset($addArray['page']['url']) || !isset($addArray['page']['text'])) {
                    $this->tpl->parse('H1_ADMIN_ADD_PAGE', 'null');
                    $isShowAddButtonPage = false;
                } else {
                    $this->tpl->assign(array(
                        'H1_ADMIN_ADD_PAGE_URL' => $addArray['page']['url'],
                        'H1_ADMIN_ADD_PAGE_TEXT' => $addArray['page']['text']
                    ));
                }

                if (!isset($addArray['link']['url']) || !isset($addArray['link']['text'])) {
                    $this->tpl->parse('H1_ADMIN_ADD_LINK', 'null');
                    $isShowAddButtonLink = false;
                } else {
                    $this->tpl->assign(array(
                        'H1_ADMIN_ADD_LINK_URL' => $addArray['link']['url'],
                        'H1_ADMIN_ADD_LINK_TEXT' => $addArray['link']['text']
                    ));
                }

                if (!$isShowAddButtonSection && !$isShowAddButtonPage && !$isShowAddButtonLink) {
                    $this->tpl->parse('H1_ADMIN_ADD', 'null');
                }
            } else {
                 $this->tpl->parse('H1_ADMIN_ADD', 'null');
                 $this->tpl->assign('H1_ADMIN_ADD_SECTION_URL', $addArray);
                 $this->tpl->parse('H1_ADMIN_ADD_SIMPLE', '.h1_admin_add_simple');
            }



            $this->tpl->assign(array(
                'H1_ADMIN_DELETE_HREF' => $deleteUrl,
                'H1_ADMIN_EDIT_HREF' => $editUrl
            ));

            if (!$editUrl) {
                $this->tpl->parse('H1_ADMIN_EDIT', 'null');
            }

            if (!$deleteUrl) {
                $this->tpl->parse('H1_ADMIN_DELETE', 'null');
            }

            $this->tpl->parse('H1_ADMIN_MENU', '.h1_admin_panel');

        }
    }

    protected function setH1AdminSimple($addSection, $addPage, $addLink) {


           if ($this->_isAdmin()) {

            $this->tpl->define_dynamic('_admin_panel_simple', 'adm/admin_panel_simple.tpl');
            $this->tpl->define_dynamic('admin_panel_simple', '_admin_panel_simple');
            $this->tpl->define_dynamic('h1_admin_add_section', 'admin_panel_simple');
            $this->tpl->define_dynamic('h1_admin_add_page', 'admin_panel_simple');
            $this->tpl->define_dynamic('h1_admin_add_link', 'admin_panel_simple');

            if (!$addSection) {
                $this->tpl->parse('H1_ADMIN_ADD_SECTION', 'null');
            } else {
                $this->tpl->assign('H1_ADMIN_ADD_SECTION_URL', $addSection);
            }

            if (!$addPage) {
                $this->tpl->parse('H1_ADMIN_ADD_PAGE', 'null');
            } else {
                $this->tpl->assign('H1_ADMIN_ADD_PAGE_URL', $addPage);
            }

            if (!$addLink) {
                $this->tpl->parse('H1_ADMIN_ADD_LINK', 'null');
            } else {
                $this->tpl->assign('H1_ADMIN_ADD_LINK_URL', $addLink);
            }


            $this->tpl->parse('H1_ADMIN_MENU', '.admin_panel_simple');


        } else {
            $this->tpl->parse('ADMIN_PANEL_SIMPLE', 'null');
        }
    }

    protected function setAdminButtons($editUrl, $deleteUrl) {

        if ($this->_isAdmin()) {

            //$this->tpl->assign('H1_ADMIN_MENU', 'wwww');
            $this->tpl->define_dynamic('_admin_button_panel', 'adm/admin_button_panel.tpl');
            $this->tpl->define_dynamic('admin_button_panel', '_admin_button_panel');
            $this->tpl->define_dynamic('admin_button_add', 'admin_button_panel');


            $this->tpl->assign(array(
                'ADMIN_BUTTON_DELETE_HREF' => $deleteUrl,
                'ADMIN_BUTTON_EDIT_HREF' => $editUrl
            ));

            $this->tpl->parse('ADMIN_BUTTON_PANEL', 'admin_button_panel');
        } else {
            $this->tpl->parse('ADMIN_BUTTON_PANEL', 'null');
        }
    }

    protected function setAdminCatalogButtons($editUrl, $deleteUrl, $sectionFieldsUrl, $sectionOptionsUrl) {

        if ($this->_isAdmin()) {

            //$this->tpl->assign('H1_ADMIN_MENU', 'wwww');
            $this->tpl->define_dynamic('_admin_button_panel', 'adm/admin_catalog_button_panel.tpl');
            $this->tpl->define_dynamic('admin_button_panel', '_admin_button_panel');
            $this->tpl->define_dynamic('admin_button_add', 'admin_button_panel');


            $this->tpl->assign(array(
                'ADMIN_BUTTON_DELETE_HREF' => $deleteUrl,
                'ADMIN_BUTTON_EDIT_HREF' => $editUrl,
                'ADMIN_BUTTON_FIEIDS_EDIT_HREF'=>$sectionFieldsUrl,
                'ADMIN_BUTTON_OPTIONS_HREF'=>$sectionOptionsUrl
            ));

            $this->tpl->parse('ADMIN_BUTTON_PANEL', 'admin_button_panel');
        } else {
            $this->tpl->parse('ADMIN_BUTTON_PANEL', 'null');
        }
    }

    private function loadLookups() {
        $lookups = $this->db->fetchAll("SELECT * FROM `lookups` WHERE `language` = '" . $this->lang . "' ORDER BY `position`");

        if ($lookups) {
            $this->lookups = $lookups;
            foreach ($lookups as $item) {
                //$this->lookups[$item['key']] = $item['value'];
                $this->tpl->assign(strtoupper($item['key']), stripslashes($item['value']));

                if ($item['key'] == 'FIRST_WAY') {
                    $this->setWay($item['value'], 'http://' . $_SERVER['HTTP_HOST'] . '/');
                }
            }
        }
    }

    private function loadMetaTags() {
        $meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `href` = '" . $this->url[0] . "' AND `language` = '" . $this->lang . "'");

        if ($meta) {
            $this->setMetaTags($meta);

            if (!isset($this->url[1])) {
                $this->setWay(stripslashes($meta['header']));
                $this->tpl->assign('CONTENT', stripslashes($meta['body']));
            } else {
                $this->setWay(stripslashes($meta['header']), $this->basePath . $meta['href']);
            }

            $this->_defaultMeta = $meta;
        }
    }

    private function loadHorisontalMenu() {
        $menu = $this->db->fetchAll("SELECT `href`, `header`, `type` FROM `page` WHERE `level` = '0' AND `menu` = 'horisontal' AND `language` = '" . $this->lang . "' AND `visibility` = '1' ORDER BY `position`, `header`");

        if ($menu) {
            $size = sizeof($menu);

            for ($i = 0; $i < $size; $i++) {
                $className = '';
                if ($i == 0) {
                    $className = "class='first'";
                }

                if ($i == ($size - 1)) {
                    $className = "class='last'";
                }


                $this->tpl->assign(
                        array(
                            'MENU_HREF' => $menu[$i]['type'] === 'link' ? $menu[$i]['href'] : $this->basePath . $menu[$i]['href'],
                            'MENU_NAME' => stripslashes($menu[$i]['header']),
                            'H_MENU_CLASS_NAME' => $className
                        )
                );

                if ($i < $size - 1) {
                    $this->tpl->parse('HORISONTAL_SEP', 'horisontal_sep');
                } else {
                    $this->tpl->parse('HORISONTAL_SEP', 'null');
                }

                $this->tpl->parse('HORISONTAL_BOTTOM', '.horisontal_bottom');
                $this->tpl->parse('HORISONTAL', '.horisontal');
            }
        } else {
            $this->tpl->parse('HORISONTAL', 'null');
        }
    }

    protected function loadBanners($isIndexOnly = false) {


        $showAs = '';
        if ($isIndexOnly) {
            $showAs = " OR `show_as`='index'";
        }

        $banners = $this->db->fetchAll("SELECT * FROM `banners` WHERE `show_as`='all' $showAs  AND `show_as` != 'hide' ORDER BY `layout`, `position`, `name`");

        if ($banners) {
            $topBanners = array();

            //$topLeft = '';
            //$topRight = '';

            $left = '';
            $bottom = '';

            $isShowTop = true;


            foreach ($banners as $banner) {

                $isShowTop = ($banner['layout'] == 'top' && $banner['show_as'] == 'hide');

                if ($banner['type'] == 'img') {

                    if ($banner['layout'] == 'top') {
                        $topBanners[] = "<a href='$banner[href]' title='$banner[title]'  target=\"_blank\"><img src='/img/bnrs/top/$banner[pic]' width='318' height='116' alt='$banner[alt]' title='$banner[title]' /></a>";
                    }

                    /*if ($banner['layout'] == 'top' && !empty($topLeft)) {
                        $topRight = "<a href='$banner[href]' title='$banner[title]'  target=\"_blank\"><img src='/img/bnrs/top/$banner[pic]' width='468' height='60' alt='$banner[alt]' title='$banner[title]' /></a>";
                    }

                    if ($banner['layout'] == 'top' && empty($topLeft)) {
                        $topLeft = "<a href='$banner[href]' title='$banner[title]'  target=\"_blank\"><img src='/img/bnrs/top/$banner[pic]' width='468' height='60' alt='$banner[alt]' title='$banner[title]' /></a>";
                    }*/


                    $ifLeftImg = true;
                    if ($banner['layout'] == 'left') {
                        $left .= "<a href='$banner[href]' title='$banner[title]'  target=\"_blank\"><img src='/img/bnrs/left/$banner[pic]' alt='$banner[alt]' title='$banner[title]'  /></a>";
                    }

                    if ($banner['layout'] == 'bottom') {
                        $bottom .= "<a href='$banner[href]' title='$banner[title]'  target=\"_blank\"><img src='/img/bnrs/bottom/$banner[pic]' alt='$banner[alt]' title='$banner[title]' width='88' height='31' /></a> ";
                    }
                }

                if ($banner['type'] == 'swf') {
                    if ($banner['layout'] == 'top') {
                        $topBanners[] = (!empty($banner['href']) ? " <a href=\"$banner[href]\"  title='$banner[title]' target=\"_blank\">" : '') . "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\"  width=\"318\" height=\"116\"> <param name=\"movie\" value=\"/img/bnrs/top/$banner[pic]\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\">  <embed src=\"/img/bnrs/top/$banner[pic]\" quality=\"high\" type=\"application/x-shockwave-flash\"  WMODE=\"transparent\" width=\"318\" height=\"116\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" allowScriptAccess=\"always\" /></object>" . (!empty($banner['href']) ? " </a>" : '');
                    }

                    /*if ($banner['layout'] == 'top' && !empty($topLeft)) {
                        $topRight = (!empty($banner['href']) ? " <a href=\"$banner[href]\"  title='$banner[title]' target=\"_blank\">" : '') . "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\"  width=\"468\" height=\"60\"> <param name=\"movie\" value=\"/img/bnrs/top/$banner[pic]\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\">  <embed src=\"/img/bnrs/top/$banner[pic]\" quality=\"high\" type=\"application/x-shockwave-flash\"  WMODE=\"transparent\" width=\"468\" height=\"60\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" allowScriptAccess=\"always\" /></object>" . (!empty($banner['href']) ? " </a>" : '');
                    }

                    if ($banner['layout'] == 'top' && empty($topLeft)) {
                        $topLeft = (!empty($banner['href']) ? " <a href=\"$banner[href]\"  title='$banner[title]' target=\"_blank\">" : '') . "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\"  width=\"468\" height=\"60\"> <param name=\"movie\" value=\"/img/bnrs/top/$banner[pic]\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\">  <embed src=\"/img/bnrs/top/$banner[pic]\" quality=\"high\" type=\"application/x-shockwave-flash\"  WMODE=\"transparent\" width=\"468\" height=\"60\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" allowScriptAccess=\"always\" /></object>" . (!empty($banner['href']) ? " </a>" : '');
                    }*/


                    if ($banner['layout'] == 'left') {
                        $left .= (!empty($banner['href']) ? " <a href=\"$banner[href]\"  title='$banner[title]' target=\"_blank\">" : '') . "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" > <param name=\"movie\" value=\"/img/bnrs/left/$banner[pic]\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\">  <embed src=\"/img/bnrs/left/$banner[pic]\" quality=\"high\" type=\"application/x-shockwave-flash\"  WMODE=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" allowScriptAccess=\"always\" /></object>" . (!empty($banner['href']) ? " </a>" : '');
                    }

                    if ($banner['layout'] == 'bottom') {
                        $top .= (!empty($banner['href']) ? " <a href=\"$banner[href]\"  title='$banner[title]' target=\"_blank\">" : '') . "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\"  width=\"88\" height=\"31\"> <param name=\"movie\" value=\"/img/bnrs/bottom/$banner[pic]\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\">  <embed src=\"/img/bnrs/bottom/$banner[pic]\" quality=\"high\" type=\"application/x-shockwave-flash\"  WMODE=\"transparent\" width=\"88\" height=\"31\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" allowScriptAccess=\"always\" /></object>" . (!empty($banner['href']) ? " </a>" : '');
                    }
                }

                if ($banner['type'] == 'html') {
                    if ($banner['layout'] == 'top') {
                        $topBanners[] = $banner['html_code'];
                    }

                    /*if ($banner['layout'] == 'top' && !empty($topLeft)) {
                        $topRight = $banner['html_code'];
                    }

                    if ($banner['layout'] == 'top') {
                        $topLeft = $banner['html_code'];
                    }*/


                    if ($banner['layout'] == 'left') {
                        $left .= $banner['html_code'];
                    }

                    if ($banner['layout'] == 'bottom') {
                        $bottom .= '' . stripcslashes($banner['html_code']) . '';
                    }
                }
            }

            if (!empty($topBanners)) {
                $count = count($topBanners);

                if ($count > 3) {
                    $count = 3;
                }

                for ($i = 0; $i < $count; $i++) {
                    $this->tpl->assign(
                        array(
                            'BANNER_MIDDLE' => $i == 1 ? ' middle' : '',
                            'BANNER_CODE'   => $topBanners[$i]
                        )
                    );

                    $this->tpl->parse('TOP_BANNER_ITEM', '.top_banner_item');
                }
            } else {
                $this->tpl->parse('TOP_BANNER_BLOCK', 'null');
            }

            //$this->tpl->assign(array('TOP_BANNER_LEFT' => (!empty($topLeft) ? stripcslashes($topLeft) : '')));
            //$this->tpl->assign(array('TOP_BANNER_RIGHT' => (!empty($topRight) ? stripcslashes($topRight) : '')));
            $this->tpl->assign(array('LEFT_BANNER' => (!empty($left) ? stripcslashes($left) : '')));
            $this->tpl->assign(array('PARTNERS' => (!empty($topLeft) ? stripcslashes($bottom) : '')));



            /*if (!empty($topRight)) {
                $this->tpl->assign(array('TOP_BANNER_RIGHT' => stripcslashes($topRight)));
            }

            if (!empty($left)) {
                $this->tpl->assign(array('LEFT_BANNER' => stripcslashes($left)));
            }

            if ((empty($topLeft) && empty($topRight)) || $isShowTop) {
                $this->tpl->parse('TOP_BANNER_BLOCK', 'null');
            }*/



            if (!empty($bottom)) {
                $this->tpl->assign(array('PARTNERS' => stripcslashes($bottom)));
            }
        }
    }

    private function loadVerticalMenu() {
        $menu = $this->db->fetchAll("SELECT `id`, `href`, `header`, `type` FROM `page` WHERE `level` = '0' AND `menu` = 'vertical' AND `language` = '" . $this->lang . "' ORDER BY `position`, `header`");

        if ($menu) {
            foreach ($menu as $m) {
                $this->tpl->parse('VERTICAL_COMPLEX', 'null');
                $this->tpl->parse('VERTICAL_COMPLEX_SUB', 'null');

                if ($m['type'] == 'section') {
                    $sub = $this->db->fetchAll("SELECT `href`, `header`, `type` FROM `page` WHERE `level` = '" . $m['id'] . "' ORDER BY `position`, `header`");
                    if ($sub) {
                        foreach ($sub as $s) {
                            $url = $s['href'];
                            if ($s['type'] !== 'link') {
                                $url = $this->basePath . $m['href'] . '/' . $url;
                            }

                            $this->tpl->assign(
                                    array(
                                        'MENU_HREF' => $url,
                                        'MENU_NAME' => stripslashes($s['header'])
                                    )
                            );

                            $this->tpl->parse('VERTICAL_COMPLEX_SUB', '.vertical_complex_sub');
                        }
                    } else {
                        $this->tpl->parse('VERTICAL_COMPLEX_SUB', 'null');
                    }
                }

                $this->tpl->assign(
                        array(
                            'MENU_HREF' => $m['type'] === 'link' ? $m['href'] : $this->basePath . $m['href'],
                            'MENU_NAME' => stripslashes($m['header'])
                        )
                );

                if ($m['type'] == 'section' && $sub) {
                    $this->tpl->parse('VERTICAL_COMPLEX', '.vertical_complex');
                    $this->tpl->parse('VERTICAL_SINGLE', 'null');
                } else {
                    $this->tpl->parse('VERTICAL_COMPLEX', 'null');
                    $this->tpl->parse('VERTICAL_SINGLE', 'vertical_single');
                }

                $this->tpl->parse('VERTICAL', '.vertical');
            }
        } else {
            $this->tpl->parse('VERTICAL', 'null');
        }
    }

    private function loadCatalogMenu() {

        $visibleQuery = '';

        $catalogOptions = array();

        if (!$this->_isAdmin()) {
            $visibleQuery = " AND `visibility` = '1'";
            $catalogOptions = $this->getCatalogOptions();
        }

        $menu = $this->db->fetchAll("SELECT `id`, `href`, `name`, `type`, `artikul` FROM `catalog` WHERE `level` = '0' $visibleQuery  AND   `language` = '" . $this->lang . "' ORDER BY `position` ,`name`");

        if ($menu) {
            foreach ($menu as $m) {
                $this->tpl->parse('CATALOG_MENU_COMPLEX', 'null');
                $this->tpl->parse('VERTICAL_COMPLEX_SUB_URL', 'null');
                $this->tpl->parse('VERTICAL_COMPLEX_SUB_ACTIVE', 'null');

                if ($m['type'] == 'section') {
                    $visibleQuery = '';

                    if (!$this->_isAdmin()) {
                        $visibleQuery = " AND `visibility` = '1'";
                        $visibleQuery1 = '';

                        if (isset($catalogOptions['0']['is_show_empty_pic']) && $catalogOptions['0']['is_show_empty_pic'] == '0' ||
                                isset($catalogOptions[$m['artikul']]['is_show_empty_pic']) && $catalogOptions[$m['artikul']]['is_show_empty_pic'] == '0'
                        ) {
                            $visibleQuery1 .= " AND `pic` != ''";
                        }
                        $visibleQuery .= $visibleQuery1;
                        $visibleQuery2 = '';

                        if (isset($catalogOptions['0']['is_show_empty_price']) && $catalogOptions['0']['is_show_empty_price'] == '0' ||
                                isset($catalogOptions[$m['artikul']]['is_show_empty_price']) && $catalogOptions[$m['artikul']]['is_show_empty_price'] == '0') {
                            $visibleQuery2 .= " AND `cost` > 0 ";
                        }

                        $visibleQuery .= $visibleQuery2;
                    }

                    if (in_array($m['href'], $this->url)) {

                        $sub = $this->db->fetchAll("SELECT `id`, `href`, `name`, `type` FROM `catalog` WHERE `level` = '" . $m['id'] . "' $visibleQuery ORDER BY `position` ,`name`");
                        if ($sub) {
                            foreach ($sub as $s) {
                                $url = $s['href'];
                                if ($s['type'] !== 'link') {
                                    $url = $this->basePath . $m['href'] . '/' . $url;
                                }

                                $subMenuActive = '';
                                if (isset ($this->url[2]) && !empty ($this->url[2])) {
                                    if ($this->url[0] == 'catalog') {
                                        if ($this->url[1] == $m['href']) {
                                            if ($this->url[2] == $s['href']) {
                                                $subMenuActive = 'active';
                                            }
                                        }
                                    }
                                }

                                if (!$subMenuActive) {
                                    $this->tpl->assign('CATALOG_MENU_COMPLEX_ELEMENT', '<li><a href="/catalog'.$url.'" title="'.stripslashes($s['name']).'">'.stripslashes($s['name']).'</a></li>');
                                } else {
                                    $this->tpl->assign('CATALOG_MENU_COMPLEX_ELEMENT', '<li class="active"><b>'.stripslashes($s['name']).'</b></li>');
                                }

                                $this->tpl->assign(
                                        array(
                                            'CATALOG_MENU_HREF' => $url,
                                            'CATALOG_MENU_NAME' => stripslashes($s['name']),
                                            'CATALOG_SUBMENU_ACTIVE' => $subMenuActive
                                        )
                                );

                                $this->tpl->parse('CATALOG_MENU_COMPLEX_SUB', '.catalog_menu_complex_sub');
                            }
                        } else {
                            $this->tpl->parse('CATALOG_MENU_COMPLEX_SUB', 'null');
                        }
                    }
                }

                $menuActive = '';
                if (isset ($this->url[1]) && !empty ($this->url[1])) {
                    if ($this->url[0] == 'catalog') {
                        if ($this->url[1] == $m['href']) {
                            $menuActive = 'active';
                        }
                    }
                }

                $this->tpl->assign(
                        array(
                            'CATALOG_MENU_HREF' => $m['type'] === 'link' ? $m['href'] : $this->basePath . $m['href'],
                            'CATALOG_MENU_NAME' => stripslashes($m['name']),
                            'CATALOG_MENU_ACTIVE' => $menuActive
                        )
                );

                if (isset($sub) && $m['type'] == 'section' && $sub) {
                    $this->tpl->parse('CATALOG_MENU_COMPLEX', '.catalog_menu_complex');
                    $this->tpl->parse('CATALOG_MENU_SINGLE', 'null');
                } else {
                    $this->tpl->parse('CATALOG_MENU_COMPLEX', 'null');
                    $this->tpl->parse('CATALOG_MENU_SINGLE', 'catalog_menu_single');
                }

                $this->tpl->parse('CATALOG_MENU', '.catalog_menu');
            }
        } else {
            $this->tpl->parse('CATALOG_MENU', 'null');
        }
    }

    protected function setMetaTags($meta = null) {
        if (null === $meta) {
            return true;
        }

        if (is_array($meta) && isset($meta['title'])) {
            $this->tpl->assign(
                    array(
                        'TITLE' => stripslashes($meta['title']),
                        'KEYWORDS' => isset($meta['keywords']) ? stripslashes($meta['keywords']) : stripslashes($meta['title']),
                        'DESCRIPTION' => isset($meta['description']) ? stripslashes($meta['description']) : stripslashes($meta['title']),
                        'HEADER' => isset($meta['header']) ? stripslashes($meta['header']) : stripslashes($meta['title'])
                    )
            );
        } else {
            $this->tpl->assign(
                    array(
                        'TITLE' => $meta,
                        'KEYWORDS' => $meta,
                        'DESCRIPTION' => $meta,
                        'HEADER' => $meta
                    )
            );
        }
    }

    protected function setWay($title = null, $url = null) {
        if (null === $title) {
            return true;
        }

        $sep = ($this->_way) ? $this->sepWay : '';

        $formatUrl  = '<a href="%s" class="link">%s</a>';
        $formatText = '<span class="current">%s</span>';



        if (null === $url) {
            //$this->_way .= $sep . '<li>' . stripslashes($title) . '</li>';
            $this->_way .= $sep . sprintf($formatText, stripslashes($title));
        } else {
            $this->_way .= $sep . sprintf($formatUrl, $url, stripslashes($title));
            //$this->_way .= ( $sep . '<li' . ($this->wayLinkCounter == 0 ? ' class="first"' : '') . '><a href="' . $url . '">' . stripslashes($title) . '</a></li>');
        }
        //$this->wayLinkCounter++;
    }

    protected function generateWayFromUri() {
        if (sizeof($this->url) < 2) {
            return true;
        }

        $arrayUrl = $this->url;
        array_pop($arrayUrl);

        $in = '';

        foreach ($arrayUrl as $uri) {
            $in .= '"' . $uri . '",';
        }

        $in = substr($in, 0, -1);

        if (!$in) {
            return true;
        }

        $pages = $this->db->fetchAll("SELECT * FROM `page` WHERE `language` = '" . $this->lang . "' AND `href` IN ($in)");

        if (sizeof($pages) != sizeof($this->url) - 1) {
            return $this->error404();
        }

        $href = $this->basePath;

        foreach ($pages as $page) {
            $href .= $page['href'] . '/';
            $this->setWay(stripslashes($page['header']), $href);
        }
    }

    protected function _loadPaginator(Zend_Paginator $paginator, $baseUrl = '', $delimiter = '?')
    {
        $pages = get_object_vars($paginator->getPages('Sliding'));

        if (!$pages['pageCount'] || $pages['pageCount'] <= 1) {
            return '';
        }

        $baseUrl = rtrim($baseUrl, '/');

        $navbar = '<div class="paginator">';

        if (isset ($pages['previous'])) {
            $navbar .= '<a href="' . $baseUrl . '">«</a>';
            $navbar .= '<a href="' . $baseUrl . ($pages['first'] == $pages['previous'] ? '' : $delimiter . 'page=' . $pages['previous']) . '" class="an">&lsaquo;</a>';
        }

        foreach ($pages['pagesInRange'] as $page) {
            if ((int) $page == $pages['current']) {
                $navbar .= '<b>' . $page . '</b>';
            } else {
                $url = $baseUrl;
                if ($page > 1) {
                    $url .= $delimiter . 'page=' . $page;
                }

                $navbar .= '<a href="' . $url . '">' . $page . '</a>';
            }
        }

        if (isset ($pages['next'])) {
            $navbar .= '<a href="' . $baseUrl . $delimiter . "page=" . $pages['next'] . '" class="an">&rsaquo;</a>';
            $navbar .= '<a href="' . $baseUrl . $delimiter . "page=" . $pages['last'] . '">»</a>';
        }

        $navbar .= "<span class=\"count\">".$pages['firstItemNumber']." - ".$pages['lastItemNumber']." из ".$pages['totalItemCount']."</span>";

        $navbar .= "</div>";

        return $navbar;
    }

    protected function loadPaginator($allPages = null, $pages = null, $url = null) {
        if (null === $allPages || null === $pages || null === $url) {
            return '';
        }

        if ($pages > $allPages) {
            $pages = 1;
        }

        $symbol = (preg_match("/\?/", $url) ? '&' : '?');

        $add = $del = 0;
        $start = $pages - 10;


        if ($start < 1) {
            $add = abs($start);
            $start = 1;
        }

        $end = $pages + 10 + $add;

        if ($end > $allPages) {
            $del = $end - $allPages;
            $end = $allPages;
            $start = ($start - $del > 0) ? $start - $del : 1;
        }

        $navbar = '<div class="pager2">';

        if ($pages > 1) {
            $navbar .= '<a href="' . $url . '">Первая</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $navbar .= '<a href="' . $url . ((($pages - 1) == 1) ? ("") : ($symbol . "page=" . ($pages - 1))) . '" class="an">&laquo;&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($pages == $i) {
                $navbar .= '<span>' . $i . '</span>';
            } elseif ($i > 1) {
                $navbar .= '<a href="' . $url . $symbol . "page=" . $i . '">' . $i . '</a>';
            } else {
                $navbar .= '<a href="' . $url . '">' . $i . '</a>';
            }
        }

        if ($pages < $allPages) {
            $navbar .= '<a href="' . $url . $symbol . "page=" . ($pages + 1) . '" class="an">&raquo;&raquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $navbar .= '<a href="' . $url . $symbol . "page=" . $allPages . '">Последняя</a>';
        }

        $navbar .= "</div>";

        return $navbar;
    }

    protected function mergeUrlArray() {
        $url = $this->basePath;

        foreach ($this->url as $uri) {
            $url .= $uri . '/';
        }

        return $url;
    }

    public function finalise() {
        $this->tpl->assign('WAY', $this->_way);
        $this->tpl->assign('BASE_PATH', $this->basePath);
        $this->tpl->parse('PAGE', 'page');
        $this->tpl->prnt();
    }

    public function beforeAction($isRun = false) {
        if ($isRun) {
            $this->loadBanners();
        }
    }



}