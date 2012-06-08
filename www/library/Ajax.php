<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Abstract.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Interface.php';
if (!defined('ABSTRACT_BASE_PHP')) {
    die('err');
}

class Ajax extends Main_Abstract_Base implements Main_Interface
{

    //put your code here


    public function main()
    {
        return $this->error404();
    }

    public function factory()
    {
        return true;
    }

    public function capcha()
    {
        $captcha = new Zend_Captcha_Png(array(
                    'name' => 'cptch',
                    'wordLen' => 6,
                    'timeout' => 1800,
                ));
        $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
        $captcha->setStartImage('./img/captcha.png');
        die($captcha->generate());
    }

    public function registrateTest()
    {
        $ret = 'true';
        if (!$this->isUserExists()) {
            $ret = 'false';
        }
        die($ret);
    }

    public function testCaptcha()
    {
        $err = 'true';
        if (!$this->isCaptcha()) {
            $err = $this->_err;
        }
        die($err);
    }

    public function loadselectedsections()
    {
        if ($this->_isAdmin()) {



            if (($row = $this->db->fetchAll("SELECT `name`, `artikul` FROM `catalog` WHERE `level` = '0' ORDER BY `position`, `name`"))) {
                die(json_encode($row));
            }
        }
        die;
    }

    public function loadsectiontpl($section)
    {

        $row['goods_length'] = array(
            'goods_length_length' => 'Длина',
            'goods_length_width' => 'Ширина',
            'goods_length_height' => 'Высота',
        );

        $row['pack_length'] = array(
            'pack_length_length' => 'Длина',
            'pack_length_width' => 'Ширина',
            'pack_length_height' => 'Высота',
        );

        $row['body'] = array(
            'body_color' => 'Цвет'
        );

        $row['use'] = array(
            'use_hunter' => 'Охота',
            'use_fishing' => 'Рыбалка',
            'use_tourism' => 'Туризм'
        );
        $row['weight'] = array(
            'weight_goods_kg' => 'Вес (кг.)',
            'weight_goods_g' => 'Вес (г.)',
            'weight_goods_kg' => 'Вес в упаковке(кг.)',
            'weight_goods_g' => 'Вес в упаковке(г.)',
        );

        if (isset($row[$section])) {
            die(json_encode($row[$section]));
        }

        die;
    }

    public function showcatsections()
    {
        if ($this->_isAdmin()) {

            if (($artikul = $this->getVar('artikul', false))) {

                if (($isLoadTemplate = $this->getVar('isLoadTemplate', false)) && $isLoadTemplate == 'yes') {

                    $this->loadsectiontpl(str_replace('use_tempalte_', '', $artikul));
                }

                if (($row = $this->db->fetchAll("SELECT DISTINCT `title` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$artikul'"))) {

                    die(json_encode($row));
                }
            }
        }

        die();
    }

    public function node()
    {
        if ($this->_isAdmin()) {
            if (($operation = $this->getVar('operation', false))) {
                switch ($operation) {
                    case('create_node') : {
                            $this->createNode($this->getVar('section_artikul', false), $this->getVar('title', false), $this->getVar('type', false), $this->getVar('layout', false));
                            break;
                        } case('remove_node') : {
                            $this->removeNode($this->getVar('id', false), $this->getVar('type', false));
                            break;
                        } case ('rename_node') : {
                            $this->renameNode($this->getVar('id', false), $this->getVar('title', false), $this->getVar('type', false));
                            break;
                        } case ('hide_node') : {
                            $this->hideNode($this->getVar('id', false), $this->getVar('type', false));
                            break;
                        } case ('get_children') : {

                            $this->getChildrenNode($this->getVar('artikul', false), $this->getVar('id', false), $this->getVar('params', false), $this->getVar('rel', false));
                            break;
                        } case ('move_node') : {
                            $this->moveNode($this->getVar('artikul', false), $this->getVar('id', false), $this->getVar('position', false), $this->getVar('type', false), $this->getVar('new_parent', false));
                            break;
                        }
                }
            }
        }
        die;
    }

    private function removeNode($id, $type)
    {
        if (!$id || !$type || !is_numeric($id)) {
            return false;
        }


        if ($type == 'group') {
            if (($row = $this->db->fetchAll("SELECT  `c1`.`group` as `group_1`, `c1`.`catalog_section_href` as `catalog_section_href_1`, `c1`.`id` as `id_1`, `c2`.`id` as `id_2`, `c2`.`name` FROM `catalog_section_fields` as `c1`, `catalog_section_fields` as `c2` WHERE `c1`.`id` = '$id' AND `c2`.`group` = `c1`.`group` AND `c1`.`catalog_section_href` = `c2`.`catalog_section_href`"))) {
                foreach ($row as $res) {
                    $tableName = "catalog-fields-$res[catalog_section_href_1]";
                    if ($this->isTeableExists($tableName)) {

                        if ($this->fieldExists($tableName, $res['name'])) {
                            $sql = "ALTER TABLE `$tableName` DROP `$res[name]`";
                            $this->db->query($sql);
                        }
                    }
                    $this->db->delete('catalog_section_fields', "id=$res[id_2]");
                }
                $this->db->delete('catalog_section_fields', "id=" . $row[0]['id_2']);
            }
            die('Группа [' . $row[0]['group_1'] . '] удалена');
        }

        if ($type == 'sub_group') {
            if (($row = $this->db->fetchAll("SELECT  `c1`.`sub_group` as `sub_group_1`, `c1`.`catalog_section_href` as `catalog_section_href_1`, `c1`.`id` as `id_1`, `c2`.`id` as `id_2`, `c2`.`name` FROM `catalog_section_fields` as `c1`, `catalog_section_fields` as `c2` WHERE `c1`.`id` = '$id' AND `c2`.`sub_group` = `c1`.`sub_group` AND `c1`.`catalog_section_href` = `c2`.`catalog_section_href`"))) {

                foreach ($row as $res) {
                    $tableName = "catalog-fields-$res[catalog_section_href_1]";
                    if ($this->isTeableExists($tableName)) {

                        if ($this->fieldExists($tableName, $res['name'])) {
                            $sql = "ALTER TABLE `$tableName` DROP `$res[name]`";
                            $this->db->query($sql);
                        }
                    }
                    $this->db->delete('catalog_section_fields', "id=$res[id_2]");
                }
                $this->db->delete('catalog_section_fields', "id=" . $row[0]['id_2']);
            }
            die('Подгуппа [' . $row[0]['sub_group_1'] . '] удалена');
        }


        if ($type == 'fields') {
            if (($row = $this->db->fetchRow("SELECT `name`, `title`, `catalog_section_href` FROM `catalog_section_fields` WHERE `id` = '$id'"))) {
                $tableName = "catalog-fields-$row[catalog_section_href]";
                if ($this->isTeableExists($tableName)) {
                    if ($this->fieldExists($tableName, $row['name'])) {
                        $sql = "ALTER TABLE `$tableName` DROP `$row[name]`";
                        $this->db->query($sql);
                    }
                }
                $this->db->delete('catalog_section_fields', "id=$id");
                die('Поле [' . $row['title'] . '] удалено.');
            } else {
                die('Поле [' . $row['title'] . '] не найдено.');
            }
        }
    }

    private function createNode($sectionArtikul, $title, $type, $layout)
    {
//        for ($i = 0; $i < func_num_args(); $i++) {
//            var_dump(func_get_arg($i));
//        }
        if (!$sectionArtikul || !$title || !$type || !$layout) {
            die('Ошибка [1]! Не могу создать поле. ');
        }

        if ($layout == 'right_group') {
            $layout = 'right';
        }

        if ($layout == 'bottom_group') {
            $layout = 'bottom';
        }

        $title = trim($title);

        $nodeHref = $title;
        if (strpos($title, ':')) {
            list($nodeHref, $title) = explode(':', $title);
        }

        $nodeHref = $this->ru2Lat($nodeHref);
        $nodeHref = str_replace('--', '-', $nodeHref);
        $tableName = "catalog-fields-$sectionArtikul";



        if ($type == 'group') {
            if ($row = $this->db->fetchOne("SELECT `id` FROM `catalog_section_fields` WHERE `group`='$title' AND `catalog_section_href`='$sectionArtikul'")) {
                die('Группа [' . $title . '] уже существует.');
            } else {

                $fieldName = "Новое поле " . date('H:i:s Y/m/d');


                $nodeHref = $this->ru2Lat($fieldName);
                $nodeHref = str_replace('--', '-', $nodeHref);

                $data = array(
                    'is_default_field' => 'no',
                    'title' => $fieldName,
                    'name' => $fieldName,
                    'layout' => 'features_table',
                    'group_position' => '9999',
                    'sub_group_position' => '9999',
                    'title_position' => '9999',
                    'catalog_section_href' => $sectionArtikul,
                    'group' => $title,
                    'sub_group' => 'Новая подгруппа',
                    'type' => 'varchar',
                    'status' => 'hidden',
                    'status_to_group' => 'field',
                    'language' => 'ru');
                $this->db->insert('catalog_section_fields', $data);


                if ($this->isTeableExists($tableName)) {

                    if (!$this->fieldExists($tableName, $nodeHref)) {
                        $sql = "ALTER TABLE `$tableName` ADD `$nodeHref` VARCHAR (255) $this->dbFieldCharset";
                        $this->db->query($sql);
                    } else {
                        die('Поле [' . $title . '] уже существует.');
                    }
                } else {
                    $sql = "CREATE TABLE `$tableName` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `changed` int, `$nodeHref` VARCHAR(255) ) $this->dbFieldCharset;";
                    $this->db->query($sql);
                }

                die('Группа [' . $title . '] добавлена.');
            }
        }


        if ($type == 'sub_group' && ($id = $this->getVar('id', false))) {
            if ($row = $this->db->fetchOne("SELECT `id` FROM `catalog_section_fields` WHERE `sub_group`='$title' AND `catalog_section_href`='$sectionArtikul'")) {
                die('Подгруппа [' . $title . '] уже существует.');
            } else {

                if (!$group = $this->db->fetchOne("SELECT `group` FROM `catalog_section_fields` WHERE `id`='$id'")) {
                    die('Ошибка! Не могу создать подгруппу');
                }

                $fieldName = "Новое поле " . date('H:i:s Y/m/d');


                $nodeHref = $this->ru2Lat($fieldName);
                $nodeHref = str_replace('--', '-', $nodeHref);

                $data = array(
                    'is_default_field' => 'no',
                    'title' => $fieldName,
                    'name' => $fieldName,
                    'layout' => 'features_table',
                    'group_position' => '9999',
                    'sub_group_position' => '9999',
                    'title_position' => '9999',
                    'catalog_section_href' => $sectionArtikul,
                    'sub_group' => $title,
                    'group' => $group,
                    'type' => 'varchar',
                    'status' => 'hidden',
                    'status_to_group' => 'field',
                    'language' => 'ru');
                $this->db->insert('catalog_section_fields', $data);


                if ($this->isTeableExists($tableName)) {

                    if (!$this->fieldExists($tableName, $nodeHref)) {
                        $sql = "ALTER TABLE `$tableName` ADD `$nodeHref` VARCHAR (255) $this->dbFieldCharset";
                        $this->db->query($sql);
                    } else {
                        die('Поле [' . $title . '] уже существует.');
                    }
                } else {
                    $sql = "CREATE TABLE `$tableName` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `changed` int, `$nodeHref` VARCHAR(255) ) $this->dbFieldCharset;";
                    $this->db->query($sql);
                }

                die('Группа [' . $title . '] добавлена.');
            }
        }

        $groupTitle = 'A1';
        $subGroupTitle = 'A1';

        if ($layout == 'sub_group') {
            if (!($id = $this->getVar('id', false))) {
                die('Ошибка [2]! Не могу создать поле');
            }

            if (!($row1 = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `id`='$id'"))) {
                die('Ошибка [3]! Не могу создать поле');
            }

            $groupTitle = $row1['group'];
            $groupGroupTitle = $row1['sub_group'];
            $layout = 'features_table';
        }


        if (!($row = $this->db->fetchOne("SELECT `title` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `layout` = '$layout' AND `name`='$nodeHref' "))) {
            $data = array(
                'is_default_field' => 'no',
                'title' => $title,
                'name' => $nodeHref,
                'layout' => $layout,
                'group_position' => '9999',
                'sub_group_position' => '9999',
                'title_position' => '9999',
                'catalog_section_href' => $sectionArtikul,
                'group' => $groupTitle,
                'sub_group' => $groupGroupTitle,
                'type' => 'varchar',
                'status' => 'show',
                'status_to_group' => 'field',
                'language' => 'ru');
            $this->db->insert('catalog_section_fields', $data);
        } else {
            die('Поле [' . $title . '] уже существует.');
        }



        if ($this->isTeableExists($tableName)) {



            if (!$this->fieldExists($tableName, $nodeHref)) {
                $sql = "ALTER TABLE `$tableName` ADD `$nodeHref` VARCHAR (255) $this->dbFieldCharset";
                $this->db->query($sql);
            } else {
                die('Поле [' . $sql . $title . '] уже существует.');
            }
        } else {
            $sql = "CREATE TABLE `$tableName` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `changed` int, `$nodeHref` VARCHAR(255) ) $this->dbFieldCharset;";
            $this->db->query($sql);
        }
        die('Поле [' . $title . '] добавлено.');


        return true;
    }

    private function getChildrenNode($sectionArtikul, $id, $params, $type)
    {

        $this->loadDefaultCatalogsFields();

        if (!$sectionArtikul || $id === false || !$type) {
            return false;
        }

        $uniqGroupsName = array();
        $result = array();

        if ($id == '-1' && $type == 'group') {
            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'right_group'),
                "data" => 'Поля справа от основного фото товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );
            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'bottom_group'),
                "data" => 'Поля под описанием товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );

            $result[] = array(
                "attr" => array("id" => "node_0", "rel" => 'base'),
                "data" => 'Характеристики товара',
                "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
            );
        }

        $retTst = array();



        if ($id == '0' && $type == 'right_group') {
            $groupsTmp = array();

            $groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `layout` = 'right' ORDER BY `group_position` ");
            if (!$groups) {
                $groups = array();
            } else {
                foreach ($groups as $key => $val) {
                    $groupsTmp[$val['name']] = $val;
                }
            }

            if (isset(CatalogDetailFieldsLayout::$right)) {
                foreach (CatalogDetailFieldsLayout::$right as $key => $val) {

                    if (!isset($groupsTmp[$val])) {
                        $groupsTmp[$val] = array('id' => 'cf_' . $key,
                            'name' => $val,
                            'layout' => 'right',
                            'group' => 'A1',
                            'group_position' => $key,
                            'sub_group' => 'A1',
                            'sub_group_position' => $key,
                            'title_position' => $key,
                            'title' => '[__TITLE__]',
                            'type' => 'varchar',
                            'status' => 'show',
                            'language' => 'ru');
                    }
                }
            }

            $groups = $groupsTmp;


            if ($groups) {

                $index = 1;

                foreach ($groups as $key => $group) {

                    //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                    $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `layout` = 'right_group'";
                    $this->db->query($sql);
                    $retTst[] = $group;

                    if ($group['title'] == '[__TITLE__]') {

                        $tmpTitle = '';
                        if (isset($this->defaultFieldsArray[$group['name']])) {
                            $tmpTitle = $this->defaultFieldsArray[$group['name']];
                        }

                        if (is_array($this->defaultFieldsArray[$group['name']]) && isset($this->defaultFieldsArray[$fieldValue['name']]['title'])) {
                            $tmpTitle = $this->defaultFieldsArray[$group['name']]['title'];
                        }

                        $group['title'] = str_replace('[__TITLE__]', $tmpTitle, $group['title']);

                        if (isset($this->activePage[$group['name']])) {
                            $val = $this->activePage[$group['name']];
                        }
                    }
                    $index++;
                    if ($group['status'] == 'hidden') {
                        $group['title'] .= ' (Скрытое поле) ';
                    }
                    $result[] = array(
                        "attr" => array("id" => "node_" . $group['id'], "rel" => 'fields'),
                        "data" => $group['title'],
                        "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                    );
                }
            }
        } elseif ($id == '0' && $type == 'bottom_group') {

            $groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `layout` = 'bottom' ORDER BY `group_position`  ASC");

            $groupsTmp = array();

            if (!$groups) {
                $groups = array();
            } else {
                foreach ($groups as $key => $val) {
                    $groupsTmp[$val['name']] = $val;
                }
            }
            if (isset(CatalogDetailFieldsLayout::$bottom)) {
                foreach (CatalogDetailFieldsLayout::$bottom as $key => $val) {

                    if (!isset($groupsTmp[$val])) {
                        $groupsTmp[$val] = array('id' => 'cf_' . $key,
                            'name' => $val,
                            'layout' => 'bottom',
                            'group' => 'A1',
                            'group_position' => $key,
                            'sub_group' => 'A1',
                            'sub_group_position' => $key,
                            'title_position' => $key,
                            'title' => '[__TITLE__]',
                            'type' => 'varchar',
                            'status' => 'show',
                            'language' => 'ru');
                    }
                }
            }

            $groups = $groupsTmp;
            if ($groups) {

                $index = 1;

                foreach ($groups as $key => $group) {

                    //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                    $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `layout` = 'bottom'";
                    $this->db->query($sql);
                    $retTst[] = $group;

                    if ($group['title'] == '[__TITLE__]') {

                        $tmpTitle = '';
                        if (isset($this->defaultFieldsArray[$group['name']])) {
                            $tmpTitle = $this->defaultFieldsArray[$group['name']];
                        }

                        if (isset($this->defaultFieldsArray[$group['name']]) && is_array($this->defaultFieldsArray[$group['name']]) && isset($this->defaultFieldsArray[$fieldValue['name']]['title'])) {
                            $tmpTitle = $this->defaultFieldsArray[$group['name']]['title'];
                        }

                        $group['title'] = str_replace('[__TITLE__]', $tmpTitle, $group['title']);

                        if (isset($this->activePage[$group['name']])) {
                            $val = $this->activePage[$group['name']];
                        }
                    }


                    $index++;
                    if ($group['status'] == 'hidden') {
                        $group['title'] .= ' (Скрытое поле)';
                    }
                    $result[] = array(
                        "attr" => array("id" => "node_" . $group['id'], "rel" => 'fields'),
                        "data" => $group['title'],
                        "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                    );
                }


                // print_r($retTst); die;
            }
        } elseif ($id == '0' && $type == 'base') {
            if (($groups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `layout` = 'features_table' ORDER BY `group_position`  ASC"))) {

                $index = 1;

                foreach ($groups as $group) {
                    if (!in_array($group['group'], $uniqGroupsName)) {
                        $uniqGroupsName[] = $group['group'];
                        $subGroupsStr = '';
                        $groupName = $group['group'];

                        if ($groupName == 'A1') {
                            $groupName = 'Название группы не указанно';
                        }

                        if (empty($groupName)) {
                            $groupName = 'Незвание гурппы не указано';
                        }

                        //$ln = $this->db->update('catalog_section_fields', array('group_position' => $index), "id='$group[id]'");
                        $sql = "UPDATE `catalog_section_fields` SET `group_position` = $index WHERE `group` = '$group[group]'";
                        $this->db->query($sql);
                        $retTst[] = $group;


                        $index++;
                        if ($group['status'] == 'hidden') {
                            $group['title'] .= ' (Скрытое поле)';
                        }
                        $result[] = array(
                            "attr" => array("id" => "node_" . $group['id'], "rel" => 'group'),
                            "data" => $groupName,
                            "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed", //: ""
                        );
                    }
                }


                // print_r($retTst); die;
            }
        } elseif ($type == 'group') {

            if (($subGroups = $this->db->fetchAll("SELECT `c2`.* FROM `catalog_section_fields` as `c1`, `catalog_section_fields` as `c2` WHERE `c2`.`catalog_section_href` = '$sectionArtikul' AND `c2`.`group`= `c1`.`group` AND `c1`.id='$id'  AND `c2`.`layout` = 'features_table'  ORDER BY `sub_group_position` "))) {

                $subGroupLength = 0;
                $uniqSubGroupsName = array();
                $index = 1;
                foreach ($subGroups as $subGroup) {
                    $subGroupName = $subGroup['sub_group'];
                    if ($subGroupName == 'A1') {
                        $subGroupName = 'Название подгруппы не указано';
                    }
                    if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['sub_group'], $uniqSubGroupsName[$subGroup['group']])) {
                        $uniqSubGroupsName[$subGroup['group']][] = $subGroup['sub_group'];
                        $result[] = array(
                            "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'sub_group'),
                            "data" => $subGroupName,
                            "state" => /* ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? */"closed" //: ""
                        );

                        $sql = "UPDATE `catalog_section_fields` SET `sub_group_position`=$index WHERE `group`='$subGroup[group]' AND `sub_group` = '$subGroup[sub_group]'";
                        //   print $sql;
                        $this->db->query($sql);
                        $index++;

                        $subGroupLength++;
                    }
                }



                if ($subGroupLength == 0) {
                    
                }
            }
        } elseif ($type == 'sub_group') {

            if (($subGroupsTmp = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND id='$id'  AND `layout` = 'features_table' "))) {

                if (($subGroups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul'  AND `layout` = 'features_table' AND `sub_group`= '$subGroupsTmp[sub_group]'  AND `group` = '$subGroupsTmp[group]' ORDER BY `title_position` "))) {
                    $subGroupLength = 0;
                    $uniqSubGroupsName = array();
                    $index = 1;
                    foreach ($subGroups as $subGroup) {
                        $subGroupName = $subGroup['title'];

                        //if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['title'], $uniqSubGroupsName[$subGroup['group']])) {                     
                        $uniqSubGroupsName[$subGroup['group']][] = $subGroup['title'];
                        if ($subGroup['status'] == 'hidden') {
                            $subGroupName .= ' (Скрытое поле)';
                        }
                        $result[] = array(
                            "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'fields'),
                            "data" => $subGroupName,
                            "state" => ""
                        );

                        $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE `sub_group`= '$subGroup[sub_group]'  AND `group` = '$subGroup[group]' AND `title`='$subGroup[title]'";

                        $this->db->query($sql);
                        $index++;


                        $subGroupLength++;
                        // }
                    }

                    if ($subGroupLength == 0) {
                        
                    }
                }
            }
        }


        //  $result['dump'] = print_r($retTst, true);

        print json_encode($result);
    }

    private function renameNode($id, $title, $type)
    {

        if (!$id || !$title || !$type) {
            return false;
        }

        $title = trim($title);

        $nodeHref = $this->ru2Lat($title);
        $nodeHref = str_replace('--', '-', $nodeHref);

        $fieldName = 'title';
        $status = 'show';

        if ($type == 'group' && ($row = $this->db->fetchRow("SELECT `group`, `catalog_section_href` FROM `catalog_section_fields` WHERE `id` = '$id'"))) {
            $this->db->query("UPDATE `catalog_section_fields` SET `group` = '$title' WHERE `catalog_section_href`='$row[catalog_section_href]' AND `group`='$row[group]'");
            print $title;
        }

        if ($type == 'sub_group' && ($row = $this->db->fetchRow("SELECT `group`, `sub_group`, `catalog_section_href` FROM `catalog_section_fields` WHERE `id` = '$id'"))) {
            $this->db->query("UPDATE `catalog_section_fields` SET `sub_group` = '$title' WHERE `catalog_section_href`='$row[catalog_section_href]' AND `sub_group`='$row[sub_group]' AND `group`='$row[group]'");
            print $title;
        }

        if ($type == 'fields' && ($row = $this->db->fetchRow("SELECT `name`, `catalog_section_href`, `status` FROM `catalog_section_fields` WHERE `id` = '$id'"))) {
            $tableName = 'catalog-fields-' . $row['catalog_section_href'];
            $status = $row['status'];
            if ($this->isTeableExists($tableName) && $this->fieldExists($tableName, $row['name'])) {
                $this->db->query("ALTER TABLE `$tableName` CHANGE `$row[name]` `$nodeHref` VARCHAR( 255 ) $this->dbFieldCharset");
            }
            $this->db->query("UPDATE `catalog_section_fields` SET `$fieldName`='$title', `name`='$nodeHref'  WHERE `id` = '$id'");
            print $title . ($status == 'hidden' ? ' (Скрытое поле)' : '');
        }
    }

    private function moveNode($sectionArtikul, $id, $position, $type, $newParent)
    {
        $retArr = array();

        if (!$sectionArtikul || !$id || !is_numeric($id) || !$position || !is_numeric($position) || !$type || !in_array($type, array('group', 'sub_group', 'fields'))) {
            return false;
        }


        if ($type == 'group') {
            $groupsId = array();

            if (($gropName = $this->db->fetchOne("SELECT `group` FROM `catalog_section_fields` WHERE `id`='$id' LIMIT 1"))) {

                $sql = "UPDATE `catalog_section_fields` SET `group_position` = $position WHERE  `catalog_section_href`='$sectionArtikul' AND `group` = '$gropName'";

                $this->db->query($sql);
                $sql = "UPDATE `catalog_section_fields` SET `group_position` = (`group_position`+1) WHERE  `catalog_section_href`='$sectionArtikul' AND `group_position` >= $position";
                $this->db->query($sql);

                if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href`='$sectionArtikul' ORDER BY `group_position` ASC"))) {
                    $index = 1;

                    foreach ($row as $res) {
                        $testData[] = $res;
                        if (!isset($groupsId[$res['group']])) {
                            $groupsId[$res['group']] = $res['id'];
                            $sql = "UPDATE `catalog_section_fields` SET `group_position` = $index WHERE  `catalog_section_href`='$sectionArtikul' AND `group` = '$res[group]' ";
                            $this->db->query($sql);

                            $index++;
                        }
                    }

                    print "{ \"status\" : 1, \"id\" : " . $id . " }";
                }
            }
            die;
        } elseif ($type == 'sub_group') {

            if (($selectedSubGrop = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `id`='$id'"))) {
                $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = $position WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$selectedSubGrop[group]' AND `sub_group` = '$selectedSubGrop[sub_group]'";
                $this->db->query($sql);

                $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = (`sub_group_position`+1) WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$selectedSubGrop[group]' AND `sub_group_position` >= $position";
                $this->db->query($sql);


                if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$selectedSubGrop[group]' ORDER BY `sub_group_position`"))) {

                    $index = 1;
                    $subGroupsArr = array();
                    foreach ($row as $res) {
                        if (!isset($subGroupsArr[$res['sub_group']])) {
                            $sql = "UPDATE `catalog_section_fields` SET `sub_group_position` = $index WHERE  `catalog_section_href`='$sectionArtikul' AND `group` = '$res[group]' AND `sub_group`='$res[sub_group]'";
                            // print "$sql \n";
                            $this->db->query($sql);
                            $subGroupsArr[$res['sub_group']] = '';
                            $index++;
                        }
                    }
                    print "{ \"status\" : 1, \"id\" : " . $id . " }";
                }
            }
        } elseif ($type == 'fields') {

            if (($selectedField = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND id='$id'"))) {

                $sql = "UPDATE `catalog_section_fields` SET `title_position` = (`title_position`+1) WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$selectedField[group]' AND `sub_group`='$selectedField[sub_group]' AND `title_position` >= $position";

                $this->db->query($sql);

                $layout = 'features_table';

                if (isset($newParent)) {
                    if ($newParent == 'bottom_group') {
                        $layout = 'bottom';
                    }

                    if ($newParent == 'right_group') {
                        $layout = 'right';
                    }
                }

                $sql = "UPDATE `catalog_section_fields` SET `title_position` = $position, `layout`='$layout' WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$selectedField[group]' AND `sub_group`='$selectedField[sub_group]' AND `title` = '$selectedField[title]'";
                $this->db->query($sql);


                if (($subGroups = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `sub_group`= '$selectedField[sub_group]'  AND `group` = '$selectedField[group]' ORDER BY  `title_position` "))) {

                    $subGroupLength = 0;
                    $uniqSubGroupsName = array();
                    $index = 1;
                    foreach ($subGroups as $subGroup) {
                        $subGroupName = $subGroup['title'];

                        if (!isset($uniqSubGroupsName[$subGroup['group']]) || !in_array($subGroup['title'], $uniqSubGroupsName[$subGroup['group']])) {
                            $uniqSubGroupsName[$subGroup['group']][] = $subGroup['title'];
                            $result[] = array(
                                "attr" => array("id" => "node_" . $subGroup['id'], "rel" => 'fields'),
                                "data" => $subGroupName,
                                "state" => ""
                            );

                            $sql = "UPDATE `catalog_section_fields` SET `title_position` = $index WHERE  `catalog_section_href`='$sectionArtikul' AND `group`='$subGroup[group]' AND `sub_group`='$subGroup[sub_group]' AND `title`='$subGroup[title]'";
                            $this->db->query($sql);
                            $index++;
                            $subGroupLength++;
                        }
                    }
                    print "{ \"status\" : 1, \"id\" : " . $id . " }";
                }
            }
        }

        print "{ \"status\" : 0, \"id\" : " . $id . " }";
    }

    private function hideNode($id, $type)
    {
        $status = 'show';
        if (is_numeric($id)) {
            if (($row = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `id`='$id'"))) {
                if ($type == 'field') {
                    if ($row['status'] == 'show') {
                        $status = 'hidden';
                    }
                    $this->db->query("UPDATE `catalog_section_fields` SET `status`='$status' WHERE `id`='$id'");
                }
            }
        }

        print $status;
    }

    private function isTeableExists($tableName)
    {
        $row = $this->db->fetchAll('SHOW TABLES');

        foreach ($row as $res => $val) {
            list($index, $tName) = each($val);
            if ($tName == $tableName) {
                return true;
            }
        }
        return false;
    }

    private function fieldExists($tableName, $fieldName)
    {
        return $this->db->fetchRow("DESC `$tableName` `$fieldName`");
    }

    public function otzivi()
    {
        $retVal = "";
        if (($action = $this->getVar('action', false)) !== false) {

            if ($action == 'get' && $this->_isAdmin()) {
                if (($id = $this->getVar('id', false)) && is_numeric($id)) {
                    $retVal = json_encode(array('otziv' => $this->db->fetchRow("SELECT * FROM `otzivi` WHERE `id`='$id'")));
                }
            }

            if ($action == 'toggle' && $this->_isAdmin()) {
                if (($id = $this->getVar('id', false)) && is_numeric($id)) {
                    $status1 = 'show';
                    $retVal = 'Показать';
                    if ($status = $this->db->fetchOne("SELECT `status` FROM `otzivi` WHERE `id` = '$id'")) {

                        if ($status == 'hide') {
                            $status1 = 'show';
                            $retVal = 'Скрыть';
                        }

                        if ($status == 'show') {
                            $status1 = 'hide';
                            $retVal = 'Показать';
                        }

                        $this->db->query("UPDATE `otzivi` SET `status` = '$status1' WHERE `id` = '$id'");
                    }
                }
            }

            if ($action == 'add') {
                if (($fio = $this->getVar('fio', false)) && ($goodsArtikul = $this->getVar('goods_artikul', false)) && ($goodsName = $this->getVar('goods_name', false))
                        && ($city = $this->getVar('city', false)) && ($conclusion = $this->getVar('conclusion', false))) {
                    $email = $this->getVar('email', false);
                    $this->db->insert('otzivi', array('fio' => $fio, 'goods_artikul' => $goodsArtikul, 'goods_name' => $goodsName, 'date' => date('Y-m-d H:i:s'), 'email' => $email, 'city' => $city, 'body' => $conclusion));
                    if ($this->_isAdmin()) {
                        $retVal = "
                        <li id=\"id\">                
                            <div class=\"desc\">                    
                                <p><span>" . date("s:H d.m.Y") . "/ <span>$fio</span></span></p>
                                <p><strong>Товар:</strong><br>$goodsName</p>
                                <p><strong>e-mail:</strong><br>$email</p>
                                <p><strong>Город:</strong><br>$city</p>
                                <p><strong>Текст сообщения:</strong><br>$conclusion</p>                    
                            </div>                              
                        </li>";
                    } else {
                        $retVal = "
                        <li>                
                            <div class=\"desc\">                    
                                <p><span>Ваш отзыва добавлен. </span></p>
                                <p><span>" . date("s:H d.m.Y") . "/ <span>$fio</span></span></p>
                                <p><strong>Товар:</strong><br>$goodsName</p>
                                <p><strong>e-mail:</strong><br>$email</p>
                                <p><strong>Город:</strong><br>$city</p>
                                <p><strong>Текст сообщения:</strong><br>$conclusion</p>                    
                            </div>                              
                        </li>";
                    }
                } else {
                    
                }
            }

            if ($action == 'dell') {
                if (($id = $this->getVar('id', false)) && is_numeric($id)) {
                    if ($this->_isAdmin()) {
                        if (($row = $this->db->fetchRow("SELECT * FROM `otzivi` WHERE `id`='$id'"))) {
                            $this->db->delete('otzivi', "id='$id'");
                            $retVal = "Отзыв удалет";
                        } else {
                            $retVal = "Отзыв не найден";
                        }
                    }
                }
            }

            if ($action == 'update') {
                if (($id = $this->getVar('id', false)) && is_numeric($id)) {
                    if ($this->_isAdmin()) {
                        $email = $this->getVar('email', '');
                        $fio = $this->getVar('fio', '');
                        $city = $this->getVar('city', '');
                        $conclusion = $this->getVar('body', '');
                        if (($row = $this->db->fetchRow("SELECT * FROM `otzivi` WHERE `id`='$id'"))) {
                            $this->db->update('otzivi', array('fio' => $fio, 'date' => date('Y-m-d H:i:s'), 'email' => $email, 'city' => $city, 'body' => $conclusion), "id='$id'");

                            $retVal = "                      
                            
                                <p><span>" . date("s:H d.m.Y") . "/ <span>$fio</span></span></p>
                                <p><strong>Товар:</strong><br>$row[goods_name]</p>
                                <p><strong>e-mail:</strong><br>$email</p>
                                <p><strong>Город:</strong><br>$city</p>
                                <p><strong>Текст сообщения:</strong><br>$conclusion</p>                    
                            
                            ";
                        }
                    }
                }
            }


            if ($action == 'get_goods_list' && ($id = $this->getVar('id', false)) !== false && is_numeric($id)) {

                if (($row = $this->db->fetchAll("SELECT `name`, `id`, `artikul`, `type` FROM `catalog` WHERE `level` = '$id' ORDER BY `position`, `name`"))) {
                    foreach ($row as $res) {
                        if ($res['type'] == 'section') {
                            $retVal .= " <optgroup label='$res[name]' title='$res[name]'>\n";
                            if (($row1 = $this->db->fetchAll("SELECT `name`, `id`, `artikul`, `type` FROM `catalog` WHERE `level` = '$res[id]'"))) {
                                foreach ($row1 as $res1) {
                                    $retVal .= "  <option value='$res1[artikul]'> $res1[name] </option>\n";
                                }
                            }
                            //<optgroup label="Надувные лодки Барк гребные" title="Надувные лодки Барк гребные">
                        } elseif ($res['type'] == 'page') {
                            $retVal .= "<option value='$res[artikul]'> $res[name] </option>\n";
                        }
                    }
                }
            }
        }

        die($retVal);
    }

    public function commentary()
    {

        if (($action = $this->getVar('action', false)) !== false &&
                isset($_POST['id']) &&
                (is_numeric($_POST['id']) || is_array($_POST['id']))
        ) {
            $id = $_POST['id'];

            $sql = "id='$id'";
            if (is_array($id)) {
                $sql = "id IN (" . implode(',', $id) . ")";
            }

            switch ($action) {

                case ('active') : {
                        $this->db->query("UPDATE `comments` SET `visible` = '1' WHERE $sql");
                        die('Скрыть');
                        break;
                    } case ('hide'): {

                        $this->db->query("UPDATE `comments` SET `visible` = '0' WHERE $sql");
                        die('Активировать');
                        break;
                    } case ('delete'): {
                        $this->db->query("DELETE FROM `comments` WHERE $sql");
                        die;
                    } case ('comment_vote') : {
                        $type = 'yes';
                        if (isset($_POST['type']) && $_POST['type'] == 'no') {
                            $type = 'no';
                        }
                        //tip_helpful_yes
                        $this->db->query("UPDATE `comments` SET `tip_helpful_$type` = `tip_helpful_$type` + 1 WHERE `id`='$id'");
                        $_SESSION['comment']["tip_helpful"][$id] = $id;
                        die;
                    }
            }
        }
    }

}

?>
