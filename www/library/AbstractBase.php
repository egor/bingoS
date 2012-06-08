<?php

if (!defined('INDEX_PHP'))
    die;

define('ABSTRACT_BASE_PHP', 'ABSTRACT_BASE_PHP');

require_once PATH . 'library/Templates.php';

abstract class Main_Abstract_Base {

    protected $_config = null;
    protected $_locale = null;
    protected $_way = null;
    protected $sepWay = '<span class="delimiter">&nbsp;</span>'; // '&nbsp;&raquo; ';
    protected $db = null;
    protected $tpl = null;
    protected $settings = null;
    protected $lookups = null;
    protected $auth = null;
    protected $url = array();
    protected $basePath = '';
    protected $lang = '';
    protected $getParam = array();
    protected $_err = '';
    protected $isUseDbProfiler = true;
    protected $dbProfiler = null;
    protected $cryptKey = 'tEXFVrqY';
    protected $defaultFieldsArray = array();
    protected $dbFieldCharset = "CHARACTER SET cp1251 COLLATE cp1251_general_ci";
    // Acl в сатдии разработки
    protected $acl = array(
        'groups' => array(
            'user' => array(// Значение ключа как в таблице users поле privilege
                'allow' => '*',
                'deny' => array(
                    'admin' => '*' // Контроллер=>*|array(actions ...)
                )
            )
        )
    );
    protected $_catalogOption;
    protected $_catalogAllItems;

    public function __construct(array $config) {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->toArray();
        } else {
            if (!is_array($config)) {
                $config = (array) $config;
            }

            $this->_config = $config;
        }

        if (empty($this->_config)) {
            throw new Exception('Config cannot be empty!');
        }

        $this->_locale = new Zend_Locale('ru_UA');
        Zend_Date::setOptions(array('format_type' => 'php'));
        $this->getRegistry();
        $this->connectionToDatabase();
        $this->loadTemplates();
        $this->loadSettings();
        $this->checkUser();
    }

    protected function checkUser() {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $this->auth = $auth->getIdentity();
        }
    }

    protected function connectionToDatabase() {
        try {
            $database = Zend_Db::factory($this->_config['database']['adapter'], $this->_config['database']['params']);
            $this->dbProfiler = $database->getProfiler($this->isUseDbProfiler);
            $database->getConnection();
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
        } catch (Zend_Exception $e) {
            throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
        }

        $this->db = $database;
    }

    public function getProfiler() {
        return $this->dbProfiler;
    }

    protected function loadTemplates() {
        $templates = new Templates('tpl/');
        $templates->define_dynamic('page', "design.tpl");
        $templates->define_dynamic('null', 'page');

        $templates->define_dynamic('p_header', 'page');
        $templates->define_dynamic('basket_block', 'page');
        $templates->define_dynamic('is_index_page', 'page');
        $templates->define_dynamic('is_no_index_page', 'page');
        $templates->define_dynamic('top_banner_block', 'page');
        $templates->define_dynamic('top_banner_item', 'top_banner_block');

        $templates->define_dynamic('main_logo_text', 'page');
        $templates->define_dynamic('main_logo_url', 'page');

        $templates->define_dynamic('administration', 'page');
        $templates->define_dynamic('user_menu', 'page');
        $templates->define_dynamic('adminjslib', 'page');
        $templates->define_dynamic('horisontal', 'page');
        $templates->define_dynamic('horisontal_bottom', 'page');
        $templates->define_dynamic('horisontal_sep', 'horisontal');

        $templates->define_dynamic('vertical', 'page');
        $templates->define_dynamic('vertical_single', 'vertical');
        $templates->define_dynamic('vertical_complex', 'vertical');
        $templates->define_dynamic('vertical_complex_sub_url', 'vertical_complex');
        $templates->define_dynamic('vertical_complex_sub_active', 'vertical_complex');

        $templates->define_dynamic('catalog_menu', 'page');
        $templates->define_dynamic('catalog_menu_new_goods', 'page');
        $templates->define_dynamic('catalog_menu_hit_goods', 'page');
        $templates->define_dynamic('catalog_menu_action_goods', 'page');

        $templates->define_dynamic('catalog_menu_single', 'catalog_menu');
        $templates->define_dynamic('catalog_menu_complex', 'catalog_menu');
        $templates->define_dynamic('catalog_menu_complex_sub', 'catalog_menu_complex');

        $templates->define_dynamic('breadcrumbs', 'page');

        $templates->parse('IS_INDEX_PAGE', 'null');
        $templates->parse('MAIN_LOGO_TEXT', 'null');

        $catalogOptions = $this->getConfig('catalog');

        $catalogOptionsDb = $this->getCatalogOptions();
        $isShowIndexNewGoods = true;
        $isShowIndexHitsGoods = true;
        $isShowIndexActionsGoods = true;

        $templates->parse('ADMINISTRATION', 'null');
        $templates->parse('USER_MENU', 'null');
        $templates->parse('ADMINJSLIB', 'null');


        $templates->assign('H1_ADMIN_MENU', '');
        $templates->assign('ADMIN_BUTTON_PANEL', '');
        $templates->assign('DIV_H1_ADMIN', '');

        if (isset($catalogOptionsDb['0']['is_show_new']) && $catalogOptionsDb['0']['is_show_new'] == '0') {
            $isShowIndexNewGoods = false;
        }

        if (isset($catalogOptionsDb['0']['is_show_hits']) && $catalogOptionsDb['0']['is_show_hits'] == '0') {
            $indexHitLength = false;
        }

        if (isset($catalogOptionsDb['0']['is_show_actions']) && $catalogOptionsDb['0']['is_show_actions'] == '0') {
            $isShowIndexActionsGoods = false;
        }

        if ($this->getStatus('new') == '0' || !$isShowIndexNewGoods) {
            $templates->parse('CATALOG_MENU_NEW_GOODS', 'null');
        }
        if ($this->getStatus('hit') == '0' || !$isShowIndexHitsGoods) {
            $templates->parse('CATALOG_MENU_HIT_GOODS', 'null');
        }

        if ($this->getStatus('action') == '0' || !$isShowIndexActionsGoods) {
            $templates->parse('CATALOG_MENU_ACTION_GOODS', 'null');
        }

        if ($this->getCatOption('isStore')) {
            $templates->parse('BASKET_BLOCK', '.basket_block');
            $templates->assign('SHOW_SEARCH_TEXT_IF_NO_STORE', '');
        } else {
            $templates->parse('BASKET_BLOCK', 'null');
            $templates->assign('SHOW_SEARCH_TEXT_IF_NO_STORE', '<p><span id="find-text">Поиск</span> <br /><span>Поиск продукции по каталогу</span></p>');
        }

        $socialPageUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $socialPageUrl .= implode('/', $this->url);
        //var_dump($this->url);
        //var_dump($socialPageUrl);
        $templates->assign('SOCIAL_PAGE_URL', $socialPageUrl);
        $templates->assign('ITEMSCOPE', '');

        $this->tpl = $templates;
    }

    protected function loadAdminButtonsTemplate()
    {
        $templates = new Templates('tpl/adm/');
        $templates->define_dynamic('page', "buttons.tpl");
        $templates->define_dynamic('null', 'page');

        $templates->define_dynamic('admin_buttons_add', 'page');
        $templates->define_dynamic('admin_button_add_section', 'admin_buttons_add');
        $templates->define_dynamic('admin_button_add_page', 'admin_buttons_add');
        $templates->define_dynamic('admin_button_add_link', 'admin_buttons_add');

        $templates->define_dynamic('admin_buttons_action', 'page');
        $templates->define_dynamic('button_settings', 'admin_buttons_action');
        $templates->define_dynamic('button_features', 'admin_buttons_action');
        $templates->define_dynamic('button_delete', 'admin_buttons_action');
        $templates->define_dynamic('button_edit', 'admin_buttons_action');


        $templates->define_dynamic('simple_button_edit', 'page');
        $templates->define_dynamic('simple_button_delete', 'page');

        return $templates;
    }

    protected function _isAdmin() {
        if (null === $this->auth) {
            return false;
        }

        if (!isset($this->auth->privilege) || $this->auth->privilege !== 'admin') {
            return false;
        }

        $this->tpl->assign('DIV_H1_ADMIN', '-admin');

        return true;
    }

    protected function _isUser() {
        if (null === $this->auth) {
            return false;
        }

        if (!isset($this->auth->privilege) || $this->auth->privilege !== 'user') {
            return false;
        }

        return true;
    }

    protected function convertDate($date = null, $format = "d.m.Y") {
        if (null === $date || !is_numeric($date)) {
            $date = mktime();
        }

        $d = new Zend_Date($date, false, $this->_locale);

        return $d->toString($format);
    }

    protected function getStatus($status = 'new') {
        return $this->db->fetchOne("SELECT COUNT(id) FROM `catalog` WHERE `status`='$status'");
    }

    protected function getCatOption($key) {
        if (isset($this->_config['catalog'])) {
            return $this->gpm($this->_config['catalog'], $key, false);
        }
        return false;
    }

    protected function getConfig($key = null) {
        if ($key == null) {
            return $this->_config;
        }

        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return false;
    }

    protected function getRegistry() {
        $array = Zend_Registry::get('run');

        if (!is_array($array)) {
            throw new Exception('Unexpected Error: Base variables not defined');
        }

        foreach ($array as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            } else {
                throw new Exception("Variable \"$key\" is not defined on Abstract Class!");
            }
        }
    }

    protected function getVar($key = null, $def = null) {
        if (isset($_POST[$key])) {
            return (isset($_POST[$key]) ? addslashes(trim($_POST[$key])) : $def);
        } elseif (isset($_GET[$key])) {
            return (isset($_GET[$key]) ? addslashes(trim($_GET[$key])) : $def);
        } elseif (isset($_FILES[$key])) {
            return (isset($_FILES[$key]) ? $_FILES[$key] : $def);
        } elseif (isset($_SERVER[$key])) {
            return (isset($_SERVER[$key]) ? addslashes($_SERVER[$key]) : $def);
        } else {

            return $def;
        }
    }

    protected function gp($arr, $name, $def = null) {
        if (isset($arr[$name])) {
            if (is_string($arr[$name])) {
                if (!get_magic_quotes_gpc()) {
                    $arr[$name] = htmlspecialchars(addslashes($arr[$name]));
                }
            }
            return $arr[$name];
        } else {
            return $def;
        }
    }

    protected function gpm($arr, $name, $def = null) {
        if (isset($arr[$name])) {
            return $arr[$name];
        } else {
            return $def;
        }
    }

    protected function viewErr() {
        if ($this->_err != '') {
            $this->tpl->define_dynamic('_err', 'err.tpl');
            $this->tpl->define_dynamic('err', '_err');
            $this->tpl->assign('ERR', $this->_err);
            $this->tpl->parse('CONTENT', 'err');
            //return "<div style='color: red; font: Bold 11px Tahoma; padding: 0 0 35px 0;'>".$this->_err.'</div>';
        }

        return '';
    }

    protected function viewMessage($message) {
        $this->tpl->define_dynamic('_err', 'err.tpl');
        $this->tpl->define_dynamic('mess', '_err');
        $this->tpl->assign('ERR', $message);
        $this->tpl->parse('CONTENT', '.mess');
    }

    public function error404() {
        $error = $this->db->fetchRow("SELECT `body`, `header`, `title`, `keywords`, `description` FROM `system_pages` WHERE `href` = '404' AND `language` = '" . $this->lang . "'");

        if (!$error) {
            throw new Exception('Unexpected Error');
        }

        $this->setMetaTags($error);
        $this->setWay($error['header']);

        $this->tpl->assign('CONTENT', stripslashes($error['body']));

        return true;
    }

    protected function ru2Lat($str) {

        $rus = array('ё', 'ж', 'ц', 'ч', 'ш', 'щ', 'ю', 'я', 'Ё', 'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ю', 'Я', 'Ї', 'ї', 'Є', 'є', 'І', 'і', 'ь', 'Ь', 'Ъ', 'ъ');
        $lat = array('yo', 'zh', 'ts', 'ch', 'sh', 'sh', 'yu', 'ya', 'YO', 'ZH', 'TS', 'CH', 'SH', 'SH', 'YU', 'YA', 'YI', 'yi', 'E', 'e', 'I', 'i', '', '', '', '');
        $prototype = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '-', '_', ' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ':', '/', '.', '?', '&');

        /* if ($type == 'link') {
          array_push($prototype, ':', '/', '.', '?', '&');
          } */

        $str = str_replace($rus, $lat, $str);

        $str = strtr(iconv('utf-8', 'cp1251', $str), iconv('utf-8', 'cp1251', "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхыэ"), "ABVGDEZIJKLMNOPRSTUFH_I_Eabvgdezijklmnoprstufhie");

        $size = strlen($str);

        $temp = "";
        for ($i = 0; $i < $size; $i++) {
            if (in_array($str[$i], $prototype))
                $temp .= $str[$i];
        }

        $str = $temp;

        //$str = str_ireplace(' ', '-', $str);
        $str = preg_replace('/\W/', '-', trim($str));
        if (isset($str[0]) && $str[0] == '-') {
            $str[0] = '/';
        }
        $strLen = (strlen($str) - 1);
        if (isset($str[$strLen]) && $str[$strLen] == '-') {
            $str[$strLen] = '/';
        }
        return (strtolower($str));
    }

    protected function addErr($str = null) {
        if (null !== $str) {
            if ($this->_err != '') {
                $this->_err .= '<br />';
            }

            $this->_err .= $str;
        }
    }

    public function finalise() {
        $this->tpl->prnt();
    }

    protected function _getCatalogAllSection() {
        if (null === $this->_catalogAllItems) {
            $this->_catalogAllItems = $this->db->fetchAll("SELECT `id`, `href`, `name`, `level` , `type`, `artikul` FROM `catalog`");
        }

        return $this->_catalogAllItems;
    }

    // Получение информации о товаре по ссылке или id
    public function dataTreeManager($id, $options = array(), $items = array()) {

        if (!is_numeric($id)) {
            return false;
        }

        $id = (int) $id;

        if ($id <= 0) {
            return false;
        }

        $fields = (isset($options['fields']) ? $options['fields'] : '`id`, `href`, `name`, `level` , `type`, `artikul`');
        $retValues = (isset($options['ret']) ? $options['ret'] : array('name', 'href'));
        $subLevel = (isset($options['sub-level']) ? $options['level'] : array());
        $tableName = (isset($options['table']) ? $options['table'] : '`catalog`');


        if (count($items) <= 0) {
            if (empty($options)) {
                $items = $this->_getCatalogAllSection();
            } else {
                $items = $this->db->fetchAll("SELECT $fields FROM $tableName");
            }
        }

        if (!$items) {
            return false;
        }

        $retArr = array();
        $counter = 0;
        $i = 0;
        $isRun = true;
        $linkArr = array();
        $level = 1;
        $nameArr = array();
        $argId = $id;
        $sectionArtikul = '';

        while (true) {

            if (isset($items[$i]['id']) && $items[$i]['id'] == $id) {
                if ((is_array($retValues) && in_array('href', $retValues)) || $retValues == 'href' || $retValues == 'all') {
                    if ($items[$i]['type'] != 'href') {

                        if (isset($options['level']['href'])) {
                            if ($options['level']['href'] == $level) {
                                $linkArr[] = $items[$i]['href'];
                            }
                        } else {
                            $linkArr[] = $items[$i]['href'];
                        }
                    }
                }

                if ((is_array($retValues) && in_array('data', $retValues)) || $retValues == 'data' || $retValues == 'all') {

                    if (isset($options['level']['data'])) {
                        if ($options['level']['data'] == $level) {
                            $retArr[] = $items[$i];
                        }
                    } else {
                        $retArr[] = $items[$i];
                    }
                }

                if ((is_array($retValues) && in_array('name', $retValues)) || $retValues == 'name' || $retValues == 'all') {

                    if (isset($options['level']['name'])) {
                        if ($options['level']['name'] == $level) {
                            $nameArr[] = $items[$i]['name'];
                        }
                    } else {
                        $nameArr[] = $items[$i]['name'];
                    }
                }


                $id = $items[$i]['level'];


                if ($items[$i]['level'] == 0) {
                    $sectionArtikul = $items[$i]['artikul'];
                    break;
                }

                $level++;
                $i = 0;
            } elseif (isset($items[$i]['id'])) {

                $i++;
            } else {
                break;
            }


            $counter++;
            if ($counter > 1000000) {
                break;
            }
        }

        if (count($nameArr) > 0) {

            $retArr['links'] = (count($linkArr > 0) ? implode('/', array_reverse($linkArr)) : '');
            $retArr['linksArr'] = array_reverse($linkArr);
            $retArr['names'] = array_reverse($nameArr);
            $retArr['sectionArtikul'] = $sectionArtikul;

            return $retArr;
        } elseif (count($retArr) > 0) {
            return $retArr;
        }

        return false;
    }

    protected function isBuyButtomType($goodsId) {
        $goodsSession = new Zend_Session_Namespace('goods');

        return (!isset($goodsSession->array[$goodsId]) || $goodsSession->array[$goodsId]['status'] == 'deleted');
    }

    public function getDb() {
        return $this->db;
    }

    protected function getCatalogList($fields = '*') {
        return $this->db->fetchAll("SELECT $fields FROM `catalog`");
    }

    protected function getCatalogOptions($isReturnFileConfigOnly = false) {

        if ($isReturnFileConfigOnly === true) {
            $retArray = array();

            if (!isset($defaultFields)) {
                include 'config/configCatalog.php';
            }

            if (isset($defaultFields)) {
                $retArray['defaultFields'] = $defaultFields;
            }

            return $retArray;
        }

        if (null === $this->_catalogOption) {
            $options = $this->db->fetchAll("SELECT * FROM `catalog_options`");
            $retArr = array();
            if ($options) {
                foreach ($options as $value) {
                    if (isset($value['section_href']))
                        $retArr[$value['section_href']] = $value;
                }
            }

            $this->_catalogOption = $retArr;
        }

        return $this->_catalogOption;
    }

    protected function getField($index, $name = '', $value = '', $default = '') {

        $fieldsArray = array(
            array('name' => 'varchar', 'title' => 'Простое текстовое поле', 'db' => "varchar(255) DEFAULT '$default'", 'html' => '<input type="text" name="' . $name . '" value="' . $value . '" />'),
            array('name' => 'yes-no', 'title' => 'Логичсоке поле (Да/Нет)', 'db' => "ENUM ('1','2') DEFAULT '" . (empty($default) ? '1' : $default) . "'", 'html' => '<select name="' . $name . '"><option value="1" ' . ($value == '1' || $name == 1 ? 'selected' : '') . '>Да</option><option value="0" ' . ($value == '0' || $name == 0 ? 'selected' : '') . '>Нет</option></select>'),
            array('name' => 'have-not-have', 'title' => 'Логичсоке поле (Есть/Нет)', 'db' => "ENUM ('1','2') DEFAULT '" . (empty($default) ? '1' : $default) . "'", 'html' => '<select name="' . $name . '"><option value="1" ' . ($value == '1' || $name == 1 ? 'selected' : '') . '>Есть</option><option value="0" ' . ($value == '0' || $name == 0 ? 'selected' : '') . '>Нет</option></select>'),
            array('name' => 'mce', 'title' => 'Редактор', 'db' => "TEXT DEFAULT '$default'", 'html' => '<textarea class="mceEditor" rows=25 style="width: 100%;" name="' . $name . '">' . stripslashes($value) . '</textarea>'),
        );


        if ($index == 'db') {

            foreach ($fieldsArray as $field) {
                if ($field['name'] == $name) {
                    if (isset($field['db'])) {
                        return $field['db'];
                    }
                }
            }
        }

        if ($index == 'html') {

            foreach ($fieldsArray as $field) {
                if ($field['name'] == $name) {
                    if (isset($field['html'])) {
                        return $field['html'];
                    }
                }
            }
        }

        if ($index == 'select') {

            $ret = '';
            foreach ($fieldsArray as $field) {
                $ret .= '<option value="' . $field['name'] . '" ' . ($field['name'] == $value ? 'selected' : '') . '>' . $field['title'] . '</option>' . "\n";
            }

            return $ret;
        }

        if (isset($fieldsArray[$index]) && is_int($index)) {
            return $fieldsArray[$index];
        }

        if (is_string($index)) {

            foreach ($fieldsArray as $field) {
                if ($field['name'] == $index) {
                    return $field;
                }
            }
        }


        return false;
    }

    // Возвращает массив или таблицу с полями характеристик раздела
    protected function drowSectionFields($href, $isReturnArray = false, $id = '0') {
        $ret = '';

        $where = '';

        /*
         *
         *
         */

        $row = $this->db->fetchAll("SELECT `id`,`is_default_field`,`name`,`layout`,`sub_group_position`,`title_position`,`title`,`catalog_section_href`,`group`,`sub_group`,`type`, `status`,`status_to_group`,`language` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$href' AND `language`='" . $this->lang . "' ORDER BY `group_position`, `sub_group_position`, `title_position` ");

        $arr = array();
        $groupsId = array();
        $subGroupsId = array();
        $defaultFieldArray = array();

        if ($isReturnArray) {
            if (isset(CatalogDetailFieldsLayout::$right)) {
                foreach (CatalogDetailFieldsLayout::$right as $key => $val) {

                        $arr['A2']['A2'][$val] = array('id' => 'cf_' . $key,
                            'name' => $val,
                            'layout' => 'right',
                            'group' => 'A1',
                            'group_position' => $key,
                            'sub_group' => 'A1',
                            'sub_group_position' => $key,
                            'title_position' => $key,
                            'title' => '[__TITLE__]',
                            'catalog_section_href' => $href,
                            'type' => 'varchar',
                            'status' => 'show',
                            'is_default_field' => 'yes',
                            'language' => 'ru');

                }
            }

            if (isset(CatalogDetailFieldsLayout::$bottom)) {
                foreach (CatalogDetailFieldsLayout::$bottom as $key => $val) {

                    $arr['A2']['A2'][$val] = array('id' => 'cf_' . $key,
                        'name' => $val,
                        'layout' => 'bottom',
                        'group' => 'A1',
                        'group_position' => $key,
                        'sub_group' => 'A1',
                        'sub_group_position' => $key,
                        'title_position' => $key,
                        'title' => '[__TITLE__]',
                        'catalog_section_href' => $href,
                        'type' => 'varchar',
                        'status' => 'show',
                        'is_default_field' => 'yes',
                        'language' => 'ru');
                }
            }
        }

        //print_r($defaultFieldArray);
        //print '---------------';



        if ($row && count($row) > 0) {
            //var_dump($row);

            foreach ($row as $key => $res) {
                $group = $res['group'];
                $groupsId[$res['group']] = $res['id'];
                $subGroupsId[$res['sub_group']] = $res['id'];
                $subGroup = $res['sub_group'];
                if (isset($arr['A2']['A2'][$res['name']])) {
                    $arr['A2']['A2'][$res['name']] = $res;
                } else {
                    $arr[$group][$subGroup][$res['name']] = $res;
                }
            }
        }


        if ($isReturnArray) {
            return $arr;
        }

        if (count($arr) > 0) {

            $ret = "<table border='0'> \n";
            foreach ($arr as $group => $val) {
                $groupTmpName = $group;
                if ($group == 'A1') {
                    $group = 'Основная группа';
                }
                $ret .= "<tr><th style='text-align: center;' colspan='2'>$group</th>

               <th>
               <a href='/admin/changecatsection/editgroup/" . $groupsId[$groupTmpName] . "/$id' title='Редактировать'><img width='12' height='12' alt='Редактировать' src='/img/admin_icons/edit.png'></a>
                  </th>
                  <th>
               <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/changecatsection/deletegroup/" . $groupsId[$groupTmpName] . "/$id\"><img width=\"12\" height=\"12\" alt=\"Удалить\" src=\"/img/admin_icons/delete.png\"></a>
               </th>


            </tr>\n";
                foreach ($val as $subGroup => $val1) {
                    $subGroupTmpName = $subGroup;
                    if ($subGroup == 'A1') {
                        $subGroup = 'Основная подгруппа';
                    }



                    $ret .= "<tr><th colspan='2'>$subGroup</th>
               <th>
               <a href='/admin/changecatsection/editsubgrup/" . $subGroupsId[$subGroupTmpName] . "/$id' title='Редактировать'><img width='12' height='12' alt='Редактировать' src='/img/admin_icons/edit.png'></a>
                  </th>
                  <th>
               <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/changecatsection/editsubgrup/" . $subGroupsId[$subGroupTmpName] . "/$id\"><img width=\"12\" height=\"12\" alt=\"Удалить\" src=\"/img/admin_icons/delete.png\"></a>
               </th>

               </tr>\n";
                    foreach ($val1 as $field) {



                        $type = $this->getField($field['type']);
                        $ret .= "<tr><td>$field[title]</td> <td>" . $type['title'] . "</td>
                  <td>

               <a href='/admin/changecatsection/editfield/$field[id]/$id' title='Редактировать'><img width='12' height='12' alt='Редактировать' src='/img/admin_icons/edit.png'></a>
                  </td>
                  <td>
               <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/changecatsection/deletefield/$field[id]/$id\"><img width=\"12\" height=\"12\" alt=\"Удалить\" src=\"/img/admin_icons/delete.png\"></a>
               </td>
                  </tr>\n";
                    }
                }
            }
            $ret .= "</table>\n";
        }
        return $ret;
    }

    // Проверяет наличие таблицы в базе данных
    protected function isTableExists($tableName) {
        $tablesRow = $this->db->fetchAll("SHOW TABLES");
        if ($tablesRow) {
            foreach ($tablesRow as $tablesRow) {
                list($tmpName, $tableTmpName) = each($tablesRow);
                if ($tableTmpName == $tableName) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function loadSettings() {
        if (null === $this->db) {
            throw new Exception('Database not initialised!');
        }

        $settings = $this->db->fetchAll("SELECT `key`, `value` FROM `settings` WHERE `language` = '" . $this->lang . "'");

        if ($settings) {
            $this->settings = array();

            foreach ($settings as $set) {
                $this->settings[$set['key']] = $set['value'];
            }
        }
    }

    // Вывод списка товаров.
    protected function goodsList(array $items, $isShowEmptyPic = true, $isShowEmptyPrice = true) {
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

                    if ($f == 2) {
                        $liLastClass = 'class="last"';
                    }




                    $imgSrc = 'no-foto/no-foto-200x180.gif';
                    $imgAlt = 'Нет фото';
                    $imgTitle = $imgAlt;


                    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $items[$i]['pic'])) {
                        $imgSrc = 'catalog/small_1/' . $items[$i]['pic'];
                        $imgAlt = $items[$i]['pic_alt'];
                        $imgTitle = $items[$i]['pic_title'];

                        if (empty($imgAlt)) {
                            $imgAlt = $items[$i]['name'];
                        }

                        if (empty($imgTitle)) {
                            $imgTitle = $items[$i]['name'];
                        }
                    } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $items[$i]['artikul'] . '.jpg')) {
                        $imgSrc = 'catalog/small_1/' . $items[$i]['artikul'] . '.jpg';
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
                        $imgType = '<img src="/img/hit.png" class="png" width="124" height="124" alt="" />';
                    }

                    if (isset($items[$i]['status']) && $items[$i]['status'] == 'action') {
                        $imgType = '<img src="/img/akcia.png" class="png" width="124" height="124" alt="" />';
                    }

                    if (isset($items[$i]['status']) && $items[$i]['status'] == 'new') {
                        $imgType = '<img src="/img/new.png" class="png" width="124" height="124" alt="" />';
                    }

                    $adminElementStatus = 'Видимый';

                    if ((isset($items['visibility']) && $items['visibility'] == '0') || (!$isShowEmptyPic && !$isShowEmptyPrice)) {
                        $adminElementStatus = 'Скрытый';
                    }
                    $adminButton = '';

                    if ($this->_isAdmin()) {

                        $adminButton = "<p class=\"plashka-admin-button\">$adminElementStatus
<a onclick=\"return confirm('Вы уверены что хотите удалить?');\" href=\"/admin/deletecatpage/" . $items[$i]['id'] . "\"><img src=\"/img/admin_icons/admin-delete.png\"></a>
<a href=\"/admin/editcatpage/" . $items[$i]['id'] . "\"><img src=\"/img/admin_icons/admin-edit.png\"></a>
</p>";
                    }

                    $catItem1 .= '<li ' . $liLastClass . '><div class="img"><a href="/catalog/' . $itemUrl . '">' . $imgType . '<img src="/img/' . $imgSrc . '" width="200" height="180" alt="' . $imgAlt . '" title="' . $imgTitle . '"/></a></div>
                   <div class= "tit"><a href="/catalog/' . $itemUrl . '" title="' . $items[$i]['name'] . '">' . $items[$i]['name'] . '</a> <p></p></div></li> ';

                    $catItem2 .= '<li ' . $liLastClass . '>';
                    $catItem3 .= '<li ' . $liLastClass . '>';
                    $catItem4 .= '<li ' . $liLastClass . ' id="' . $items[$i]['id'] . '">';

                    if ($this->getCatOption('isStore')) {
                        if (isset($items[$i]['cost_old'])) {
                            if ($items[$i]['cost_old'] > $items[$i]['cost']) {

                                $econom = ($items[$i]['cost_old'] - $items[$i]['cost']);
                                if ($econom > 0) {
                                    $catItem2 .= '<p class="price">Розничная цена:<span>' . number_format($items[$i]['cost_old'], 0, '', ' ') . ' грн.</span></p>
     					 <p class="eco">Экономия:<span>' . number_format($econom, 0, '', ' ') . ' грн.</span></p>';
                                }
                            } else {
                                $catItem2 .= "&nbsp;";
                            }
                        }

                        //$buyButton = '<p class="right"><input type="button" onclick="addToBasket(\'goods_'.$items[$i]['id'].'\', 1);" value="Купить" class="button" /></p>';
                        $buyButton = '<div class="buy" id="by_button_goods_' . $items[$i]['id'] . '"><input type="text" class="goods-input" id="' . $items[$i]['id'] . '" value="1"> шт. <a href="#" class="buy-button" id="' . $items[$i]['id'] . '" > Купить</a></DIV>';

                        if (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$items[$i]['id']])) {
                            $buyButton = '<div class="order"><img width="14" height="13" alt="" src="/img/order.gif"><a href="/basket">Перейти в корзину</a></div>';
                        }

                        if (empty($items[$i]['cost'])) {
                            $items[$i]['cost'] = '0';
                        }

                        if ($items[$i]['availability'] == '0') {
                            $buyButton = '<div class="expected"><p>{EXPECTED_TEXT}</p></div>';
                        } elseif ($items[$i]['availability'] == '2') {
                            $buyButton = '<div class="expected"><p>{EXPECTED_TEXT2}</p></div>';
                        }


                        $catItem3.='<p class="newprice">Наша цена: <span>' . number_format($items[$i]['cost'], 0, '', ' ') . ' грн.</span></p>';
                        $catItem4.= $buyButton . $adminButton;
                    } else {
                        $catItem3 .= "&nbsp;";
                        $catItem4 .= "&nbsp;";
                    }
                    $catItem2 .= "</li>";
                    $catItem3 .= "</li>";
                    $catItem4 .= "</li>";
                }

                $f++;
                $i++;

                if ($f == 3) {
                    $f = 0;
                    $s+=3;
                    $catalogItem .='<ul><li><div class="clear"></div><ul style="margin-top: 20px;">' . $catItem1 . '</ul></li>';
                    $catalogItem .='<li><ul>' . $catItem2 . '</ul></li>';
                    $catalogItem .='<li><ul>' . $catItem3 . '</ul></li>';
                    $catalogItem .='<li><ul>' . $catItem4 . '</ul></li>';

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

            $catalogItem .= '<div style="clear: both; padding-top: 20px;"></div>';
        }

        return $catalogItem;
    }

    protected function _goodsList(array $items)
    {
        $goodsSession = new Zend_Session_Namespace('goods');

        $images         = array();
        $names          = array();
        $priceOld       = array();
        $priceEconomy   = array();
        $priceNew       = array();
        $basket         = array();
        $adminRow       = array();

        $itemUrl = '';
        if (($parenSectionInfo = $this->dataTreeManager($items[0]['id']))) {
            $itemUrl = $parenSectionInfo['links'];
        }
//print_r($items);
        //echo $itemUrl;

        for ($i = 0; $i < count($items); $i++) {
            $item = $items[$i];

            $parentInfo = $this->dataTreeManager($item['id']);

            $imgSrc = 'no-foto/no-foto-200x180.gif';
            $imgAlt = 'Нет фото';
            $imgTitle = $imgAlt;


            if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $item['pic'])) {
                $imgSrc = 'catalog/small_1/' . $item['pic'];
                $imgAlt = $item['pic_alt'];
                $imgTitle = $item['pic_title'];

                if (empty($imgAlt)) {
                    $imgAlt = $item['name'];
                }

                if (empty($imgTitle)) {
                    $imgTitle = $item['name'];
                }
            } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $item['artikul'] . '.jpg')) {
                $imgSrc = 'catalog/small_1/' . $item['artikul'] . '.jpg';
                $imgAlt = $item['pic_alt'];
                $imgTitle = $item['pic_title'];

                if (empty($imgAlt)) {
                    $imgAlt = $item['name'];
                }

                if (empty($imgTitle)) {
                    $imgTitle = $item['name'];
                }
            }

            $lastClass = (($i+1)%3 == 0 ? ' last' : '');

            $imgClassType = '';
            if (isset($item['status']) && $item['status'] == 'hit') {
                $imgClassType = '<span class="p_hit"></span>';
            }
            if (isset($item['status']) && $item['status'] == 'action') {
                $imgClassType = '<span class="p_action"></span>';
            }
            if (isset($item['status']) && $item['status'] == 'new') {
                $imgClassType = '<span class="p_new"></span>';
            }

            $images[] = '<li class="p_item'.$lastClass.'"><a href="/catalog/'.$parentInfo['links'].'">'.$imgClassType.'</span><img src="/img/'.$imgSrc.'" class="p_img" alt="'.$imgAlt.'" title="'.$imgTitle.'" /></a></li>';

            $names[] = '<li class="p_item'.$lastClass.'"><a href="/catalog/'.$parentInfo['links'].'" class="p_name" title="'.$item['name'].'">'.$item['name'].'</a></li>';

            if ($item['cost_old'] > $item['cost']) {
                $priceOld[] = '<li class="p_item'.$lastClass.'"><span>'.number_format($item['cost_old'], 0, '', ' ').' грн.</span>Розничная цена:</li>';
            } else {
                $priceOld[] = '<li class="p_item'.$lastClass.'">&nbsp;</li>';
            }

            $econom = ($item['cost_old'] - $item['cost']);

            if ($econom > 0) {
                $priceEconomy[] = '<li class="p_item'.$lastClass.'"><span>'.number_format($econom, 0, '', ' ').' грн.</span>Экономия:</li>';
            } else {
                $priceEconomy[] = '<li class="p_item'.$lastClass.'">&nbsp;</li>';
            }

            $priceNew[] = '<li class="p_item'.$lastClass.'"><span>'.number_format($item['cost'], 0, '', ' ').' грн.</span>Наша цена:</li>';

            if ($item['availability'] == '0') {
                $basket[] = '<li class="p_item'.$lastClass.'"><span class="p_add_disabled">Ожидается на складе</span></li>';
            } elseif ($item['availability'] == '2') {
                $basket[] = '<li class="p_item'.$lastClass.'"><span class="p_add_disabled">Снят с производства</span></li>';
            } elseif (isset($goodsSession->array['basket']) && isset($goodsSession->array['basket'][$item['id']]) &&
                    isset($goodsSession->array['basket'][$item['id']]['status']) && $goodsSession->array['basket'][$item['id']]['status'] == 'active'
                    ) {
                $basket[] = '<li class="p_item'.$lastClass.'"><a class="p_add" style="display:none;">Купить</a><span class="count" style="display:none;"><input type="text" name="count" value="1" />шт.</span><a class="p_link_basket" href="/basket">Оформить заказ</a></li>';
            } else {
                $basket[] = '<li class="p_item'.$lastClass.' buy" id="'.$item['id'].'"><a class="p_add buy-button" id="'.$item['id'].'">Купить</a><span class="count"><input type="text" class="goods-input" name="count" value="1" id="'.$item['id'].'" />шт.</span><a class="p_link_basket" href="/basket" style="display:none;">Оформить заказ</a></li>';
            }

            if ($this->_isAdmin()) {
                $status = 'Видимый';
                if ((isset($item['visibility']) && $item['visibility'] == '0')) {
                    $status = 'Скрытый';
                }

                $adminRow[] = '<li class="p_item'.$lastClass.'"><span><a href="/admin/editcatpage/' . $item['id'] . '"><img src="/img/admin_icons/admin-edit.png"></a><a onclick="return confirm(\'Вы уверены что хотите удалить?\');" href="/admin/deletecatpage/' . $item['id'] . '"><img src="/img/admin_icons/admin-delete.png"></a></span>&nbsp;&nbsp;'.$status.'</li>';
                //$adminRow[] = "<p class=\"plashka-admin-button\">$adminElementStatus<a onclick=\"return confirm('Вы уверены что хотите удалить?');\" href=\"/admin/deletecatpage/" . $items[$i]['id'] . "\"><img src=\"/img/admin_icons/admin-delete.png\"></a><a href=\"/admin/editcatpage/" . $items[$i]['id'] . "\"><img src=\"/img/admin_icons/admin-edit.png\"></a></p>";
            } else {
                $adminRow[] = '<li class="p_item'.$lastClass.'">&nbsp;</li>';
            }
        }

        $return = '';

        for ($i = 0; $i < count($items); $i += 3) {
            // Собираем картинки
            $return .= '<ul class="p_images">';
            if (isset($images[$i]) && !empty($images[$i])) {
                $return .= $images[$i];
            }
            if (isset($images[$i+1]) && !empty($images[$i+1])) {
                $return .= $images[$i+1];
            }
            if (isset($images[$i+2]) && !empty($images[$i+2])) {
                $return .= $images[$i+2];
            }
            $return .= '</ul>';

            // Собираем названия
            $return .= '<ul class="p_names">';
            if (isset($names[$i]) && !empty($names[$i])) {
                $return .= $names[$i];
            }
            if (isset($names[$i+1]) && !empty($names[$i+1])) {
                $return .= $names[$i+1];
            }
            if (isset($names[$i+2]) && !empty($names[$i+2])) {
                $return .= $names[$i+2];
            }
            $return .= '</ul>';

            // Собираем розничную цену
            $return .= '<ul class="p_specifications price_old">';
            if (isset($priceOld[$i]) && !empty($priceOld[$i])) {
                $return .= $priceOld[$i];
            }
            if (isset($priceOld[$i+1]) && !empty($priceOld[$i+1])) {
                $return .= $priceOld[$i+1];
            }
            if (isset($priceOld[$i+2]) && !empty($priceOld[$i+2])) {
                $return .= $priceOld[$i+2];
            }
            $return .= '</ul>';

            // Собираем экономию
            $return .= '<ul class="p_specifications price_economy">';
            if (isset($priceEconomy[$i]) && !empty($priceEconomy[$i])) {
                $return .= $priceEconomy[$i];
            }
            if (isset($priceEconomy[$i+1]) && !empty($priceEconomy[$i+1])) {
                $return .= $priceEconomy[$i+1];
            }
            if (isset($priceEconomy[$i+2]) && !empty($priceEconomy[$i+2])) {
                $return .= $priceEconomy[$i+2];
            }
            $return .= '</ul>';

            // Собираем нашу цену
            $return .= '<ul class="p_specifications price_new">';
            if (isset($priceNew[$i]) && !empty($priceNew[$i])) {
                $return .= $priceNew[$i];
            }
            if (isset($priceNew[$i+1]) && !empty($priceNew[$i+1])) {
                $return .= $priceNew[$i+1];
            }
            if (isset($priceNew[$i+2]) && !empty($priceNew[$i+2])) {
                $return .= $priceNew[$i+2];
            }
            $return .= '</ul>';

            // Собираем кнопки
            $return .= '<ul class="p_tobusket'.(!$this->_isAdmin() ? ' last' : ' admin').'">';
            if (isset($basket[$i]) && !empty($basket[$i])) {
                $return .= $basket[$i];
            }
            if (isset($basket[$i+1]) && !empty($basket[$i+1])) {
                $return .= $basket[$i+1];
            }
            if (isset($basket[$i+2]) && !empty($basket[$i+2])) {
                $return .= $basket[$i+2];
            }
            $return .= '</ul>';

            // Собираем кнопки админки
            if ($this->_isAdmin() && $adminRow[0] != '') {
                $return .= '<ul class="p_admin_row last">';

                if (isset($adminRow[$i]) && !empty($adminRow[$i])) {
                    $return .= $adminRow[$i];
                }
                if (isset($adminRow[$i+1]) && !empty($adminRow[$i+1])) {
                    $return .= $adminRow[$i+1];
                }
                if (isset($adminRow[$i+2]) && !empty($adminRow[$i+2])) {
                    $return .= $adminRow[$i+2];
                }

                $return .= '</ul>';
            }
        }

        //$return .= '</div>';

        return $return;
    }

    // Проверяет ессть ли пользователь с логином или паролем из _POST
    protected function isUserExists() {
        $email = $this->getVar('email', false);
        $password = $this->getVar('password', false);

        if ($email && $password) {
            if ($password) {
                $password = crypt($password, $this->cryptKey);
            }
            $count = $this->db->fetchOne("SELECT count(`id`) FROM `users` WHERE `login` = '$email' OR `pass`='$password'");

            if (is_numeric($count) && intval($count) > 0) {
                return false;
            }
        }
        return true;
    }

    //получает из _POST капчу и проверяет ее валидность
    protected function isCaptcha() {
        $captchaId = $this->getVar('captcha_id', '');
        $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
        $captchaIterator = $captchaSession->getIterator();
        @$captchaWord = $captchaIterator['word'];

        $captchaInput = $this->getVar('captcha_input', false);

        if (!$captchaWord) {
            $this->addErr('Проверочный код устарел.');
            return false;
        }
        if (!$captchaInput) {
            $this->addErr('Вы не ввели проверочный код');
            return false;
        }
        if ($captchaInput != $captchaWord) {
            $this->addErr('Введите проверочный код повторно.');
            return false;
        }
        return true;
    }

    protected function loadDefaultCatalogsFields() {
        if (is_file('config/configCatalog.php')) {
            require_once 'config/configCatalog.php';

            if (isset($defaultFields) && is_array($defaultFields) && count($defaultFields) > 0) {
                $this->defaultFieldsArray = $defaultFields;
            }
        }
    }

    protected function refererInit($id, $tableName) {
        $url = '/catalog';
        $data = $this->dataTreeManager($id, array('table' => $tableName));
        if ($data) {
            $url .= '/' . $data['links'];
        }
        return $url;
    }

}

?>
