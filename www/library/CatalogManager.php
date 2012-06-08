<?php

define('CATALOG_MANAGER_PHP', 'CATALOG_MANAGER_PHP');

require_once PATH . 'excel/PHPExcel.php';
require_once PATH . 'excel/PHPExcel/IOFactory.php';

/* Размер базы
 * mysql_select_db( $dbname );
  $result = mysql_query( "SHOW TABLE STATUS" );
  $dbsize = 0;

  while( $row = mysql_fetch_array( $result ) ) {

  $dbsize += $row[ "Data_length" ] + $row[ "Index_length" ];

  }

 */

class CatalogManager {

    protected $db;
    protected $tpl;
    protected $systemFieldsArray = array(
        'section' => 'Раздел',
        'sub_section' => 'Подраздел',
        'artikul' => 'Артикул'
    );
    protected $positionFieldsTitles = array(
        'section_position' => 'Позиция раздела',
        'sub_section_position' => 'Позиция подраздела',
        'position' => 'Позиция раздела'
    );
    protected $sectionFieldsTypesValues = array(
        'yes-no' => array('values' => array('Нет', 'Да'), 'default' => 'Нет'),
        'have-not-have' => array('values' => array('Нет', 'Есть'), 'default' => 'Нет')
    );
    protected $defaultFieldsArray = array(
        'name' => 'Название',
        'position' => array('title' => 'Позиция в списке', 'default' => '9999'),
        //'href'=>'Ссылка',
        'header' => 'Заголовок H1',
        'title' => 'Заголовок Title',
        'keywords' => 'Мета тэг Keywords',
        'description' => 'Мета тэг Description',
        'preview' => 'Краткое описание',
        'body' => 'Подробное описание',
        'pic' => 'Изображение',
        'pic_alt' => 'Альтернативный текст для изображения',
        'pic_title' => 'Всплывающая подсказка для изображения',
        // 'visibility' => array('title' => 'Отображать', 'value' => array('Нет', 'Да'), 'default' => 'Да'),
        'brand' => 'Бренд',
        'country_of_origin' => 'Страна производитель',
        'guarantee' => 'Гарантия',
        'cost_old' => 'Цена розничная',
        'cost' => 'Цена на сайте',
        'used_complete' => 'Комплект артикулы',
        'featured_products' => 'Рекомендуемые товары артикулы',
        'status' => array('title' => 'Статус', 'value' => array('hit' => 'Хит', 'new' => 'Новинка', 'action' => 'Акция')),
        'availability' => array('title' => 'Наличие на складе', 'values' => array('Нет', 'Есть'))
    );
    protected $exelDefaultColumnsArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    protected $isUseSectionPosition = true;
    protected $isUseSubSectionPosition = true;
    protected $isUseNamePosition = true;
    protected $tableName = 'catalog';
    protected $activeUserGroup;
    protected $config = array('htmlFormFieldName' => 'file');
    protected $objPHPExcel = null;
    protected $dataArray = array();
    // Заполняется при тестировании структуры. Читается при заливке данных
    protected $userFieldsImport = array();
    protected $fileFormats = array(
        'xls' => 'Excel5',
        'xlsx' => 'Excel2007',
        'ods' => 'Excel5'
    );
    protected $isUseSubSection = true;
    protected $catalogOptions = array();
    protected $allowImportFileTypes = array(
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.spreadsheet',
        'vnd.oasis.opendocument.spreadsheet',
        'application/octet-stream',
        'application/x-msexcel'
    );
    protected $allowImportFileExec = array(
        'xls', 'xlsx', 'ods'
    );
    protected $_err = false;
    protected $recordExits = array();
    protected $titlesGroups = array();
    protected $titlesSubGroups = array();
    protected $titlesFieldsGroups = array();
    protected $titlesArray = array();
    protected $startUsersRows = 0;
    protected $usedSubSection = array();
    protected $isEmptyCatalog = true;
    private $sections = array();

    public function __construct($db = null, $tpl = null) {


        if (is_file('config/configCatalog.php')) {
            require_once 'config/configCatalog.php';

            if (isset($defaultFields) && is_array($defaultFields) && count($defaultFields) > 0) {
                $this->defaultFieldsArray = $defaultFields;
            }
        }

        if ($db != null && $tpl != null) {
            $this->db = $db;
            $this->tpl = $tpl;
            $this->objPHPExcel = new PHPExcel();
            PHPExcel_Settings::setLocale('ru');

            $len = $this->db->fetchOne("SELECT COUNT(`id`) FROM `catalog`");
            $this->isEmptyCatalog = ($len == '0');

            // Получаем настроки для разделов и каталога.

            if (!($this->sections = $this->db->fetchAll("SELECT `name` FROM `catalog` WHERE `level`= 0 "))) {
                $this->sections = array();
            }

            $options = $this->db->fetchAll("SELECT * FROM `catalog_options`");
            if ($options) {
                foreach ($options as $value) {
                    if (isset($value['section_href']))
                        $this->catalogOptions[$value['section_href']] = $value;
                }
            }
        }
    }

    public function getExistsRecords() {
        return $this->recordExits;
    }

    public function addErr($errText) {
        if (!$this->_err) {
            $this->_err = $errText . '<br />';
        } else {
            $this->_err .= $errText . '<br />';
        }
    }

    public function getErrs() {
        return $this->_err;
    }

    public function getFile() {

        if (isset($_FILES['file']['size']) && $_FILES['file']['size'] > 0) {
            $file = $_FILES['file'];

            if (isset($file['type'])) {
                if (in_array($file['type'], $this->allowImportFileTypes)) {
                    $exec = substr($file['name'], strrpos($file['name'], '.') + 1, strlen($file['name']));
                    if (!in_array($exec, $this->allowImportFileExec)) {
                        $this->addErr('Недопустимый тип файла');
                    } else {

                        $objReader = PHPExcel_IOFactory::createReader($this->fileFormats[$exec]);
                        $objReader->setReadDataOnly(true);
                        return $objReader->load($file['tmp_name']);
                        //return PHPExcel_IOFactory::load($file['tmp_name']);
                    }
                } else {
                    $this->addErr('Недопустимый формат файла');
                }
            } else {
                $this->addErr('Не могу определить тип файла');
            }
        }
        return false;
    }

    // Импорт

    public function initImport() {
        if (!($file = $this->getFile())) {
            return false;
        }
        $this->objPHPExcel = $file;

        // Читаем активные листы

        return $this->readLists();
        return true;
    }

    public function readLists() {

        $allSheets = $this->objPHPExcel->getAllSheets();
        $countSheets = count($allSheets);
        $return = true;



        if ($countSheets > 0) {

            foreach ($allSheets as $sheet) {
                $return1 = $this->testListHeaders($sheet);
                if (!$return1) {
                    $return = $return1;
                }
            }
        } else {
            $this->addErr("Файл импорта пустой!");
        }

        return $return;
    }

    protected function isSectionExists($sectionName) {
        if (!empty($this->sections)) {
            foreach ($this->sections as $res) {
                if ($res['name'] == $sectionName) {
                    return true;
                }
            }
        }
        return false;
    }

    // Проверка структуры файла импорта

    protected function testListHeaders($sheet) {
        $title = $sheet->getTitle();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $collCounter = 0;

        // Проверяем структуру системных полей         
        $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
        $sectionName = trim($sheet->getCellByColumnAndRow(0, 4)->getValue());
        $subSectionName = '';

        $uniqueFields = array();



        $collCounter++;

        $return = true;

        if (isset($this->systemFieldsArray['section']) && $this->systemFieldsArray['section'] != $fieldName) {
            $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $this->systemFieldsArray['section'] . "] в файле импорта [" . $fieldName . "]");
            $return = false;
        }

        if ($this->isUseSectionPosition) {
            $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
            $collCounter++;
            if (isset($this->positionFieldsTitles['section_position']) && $this->positionFieldsTitles['section_position'] != $fieldName) { //
                $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $this->positionFieldsTitles['section_position'] . "] в файле импорта [" . $fieldName . "]");
                $return = false;
            }

            $subSectionName = trim($sheet->getCellByColumnAndRow(2, 4)->getValue());
        } else {
            $subSectionName = trim($sheet->getCellByColumnAndRow(1, 4)->getValue());
        }

        // Для определения нужны ли нам подразделы - проверяем наличие раздела. 
        // Есил раздел есть - проверяем если ли в нем подраздел

        /*
         * Определение наличия подразделов. Вариант 2.
         * 
         * 
         *    При импорте страниц проверятся наличие подразделов в настройках раздела или каталога. 
         *    Если настроек раздела нет - смотрим общие настройки каталога. По умолчаню
         *    подразделы должны созадваться. 
         * 
         */

        $sectionHref = $this->ru2Lat($sectionName);
        //$isUseSubSection = true;

        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section'])) {
            // Проверяем наличие подразделов в настройках каталога для раздела
            $isUseSubSection = ($this->catalogOptions[$sectionHref]['is_use_sub_section'] == '1');
            $this->isUseSubSection = $isUseSubSection;
        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
            // Проверяем наличие подразделов в настройках каталога для каталога
            $isUseSubSection = false;
            $this->isUseSubSection = false;
        }



        // Проверяем наличие поля "Подраздел" в файде импорта. Это нужно для новых разделов.
        // Или елси в файле импорта появился новый раздел, которого нет на сайте.
        if ($this->isEmptyCatalog || !$this->isSectionExists($sectionName)) {
            // Поле "Подраздел" может быть или во втором столбике или в третьем в зависимости от необходимости использования поля "Позиция раздела"
            if ($this->isUseSectionPosition) {
                $subSectionTmpName = trim($sheet->getCellByColumnAndRow(2, 3)->getValue());
            } else {
                $subSectionTmpName = trim($sheet->getCellByColumnAndRow(2, 3)->getValue());
            }

            $isUseSubSection = ($subSectionTmpName == $this->systemFieldsArray['sub_section']);

            // Проверяем есть ли настройки.

            if (($optionId = $this->db->fetchOne("SELECT `id` FROM `catalog_options` WHERE `section_href`='$sectionHref'"))) {
//            $this->db->update('catalog_options', array(
//                      'section_href' => $sectionHref,
//                      'is_use_sub_section' => ($isUseSubSection ? '1' : '0')
//                   ), "id=$optionId");
            } else {
                $this->db->insert('catalog_options', array(
                    'section_href' => $sectionHref,
                    'is_use_sub_section' => ($isUseSubSection ? '1' : '0')
                ));
            }
        } else {

            if ($isUseSubSection) {

                if (!$isUseSubSection && !isset($this->usedSubSection[$sectionHref])) {
                    if (!isset($this->catalogOptions[$sectionHref]['is_use_sub_section'])) {


                        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section']) && $this->catalogOptions[$sectionHref]['is_use_sub_section'] == '0') {
                            // Проверяем наличие подразделов в настройках каталога для раздела
                            $isUseSubSection = false;
                        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
                            // Проверяем наличие подразделов в настройках каталога для каталога
                            $isUseSubSection = false;
                            $this->isUseSubSection = false;
                        }

                        $this->db->insert('catalog_options', array(
                            'section_href' => $sectionHref,
                            'is_use_sub_section' => ($isUseSubSection ? '1' : '0')
                        ));
                    } else {
                        $this->db->update('catalog_options', array(
                            'is_use_sub_section' => ($isUseSubSection ? '1' : '0')
                                ), "id=" . $this->catalogOptions[$sectionHref]['id']);
                    }
                }


                $this->usedSubSection[$sectionHref] = $isUseSubSection;
            }
        }

        if ($isUseSubSection) {
            $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
            $collCounter++;
            if (isset($this->systemFieldsArray['sub_section']) && $this->systemFieldsArray['sub_section'] != $fieldName) {
                $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $this->systemFieldsArray['sub_section'] . "] в файле импорта [" . $fieldName . "]");
                $return = false;
            }

            if ($this->isUseSectionPosition) {
                $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
                $collCounter++;
                if (isset($this->positionFieldsTitles['sub_section_position']) && $this->positionFieldsTitles['sub_section_position'] != $fieldName) {

                    $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $this->positionFieldsTitles['sub_section_position'] . "] в файле импорта [" . $fieldName . "]");
                    $return = false;
                }
            }
        } else {
            $tmpFieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
            if ($tmpFieldName == $this->systemFieldsArray['sub_section']) {
                $this->addErr("Для раздела [$sectionName] наличие подразделов не установлено в настройках каталога. В файле импорта найдено поле [$tmpFieldName]");
                $return = false;
            }
        }

        $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
        $collCounter++;
        if (isset($this->systemFieldsArray['artikul']) && $this->systemFieldsArray['artikul'] != $fieldName) {

            $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . " Строка [3]). Ожидается [" . $this->systemFieldsArray['artikul'] . "] в файле импорта [" . $fieldName . "]");
            $return = false;
        }

        // Провека основных полей

        foreach ($this->defaultFieldsArray as $key => $value) {
            // $group = trim($sheet->getCellByColumnAndRow($i, 1)->getValue());
            // $subGroup = trim($sheet->getCellByColumnAndRow($i, 2)->getValue());
            $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());


            if (is_array($value) && isset($value['title'])) {
                $value = $value['title'];
            }

            if ($value != $fieldName) {

                $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $value . "] в файле импорта [" . $fieldName . "]");
                $return = false;
            }
            ++$collCounter;
        }

        // Проверка дополнительных полей

        $sql = "SELECT `artikul` FROM `catalog` WHERE `catalog`.`href`='$sectionHref'";

        $sectionArtikul = $this->db->fetchOne($sql);

        $sectionFields = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `is_default_field` = 'no'");
        if ($sectionFields) {
            $collCounter++;
            for ($collCounter; $collCounter <= $highestColumnIndex; ++$collCounter) {
                $group = trim($sheet->getCellByColumnAndRow($collCounter, 1)->getValue());
                $subGroup = trim($sheet->getCellByColumnAndRow($collCounter, 2)->getValue());
                $fieldName = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());
                if (isset($sectionField['name']) && $sectionField['name'] == $fieldName) {

                    if ($sectionFields) {

                        if ($sectionFields['group'] == 'A1') {
                            $sectionFields['group'] = '';
                        }

                        //print $collCounter.' = groupDb: '.$sectionFields['group'].' == ('.$group.') subGroupDb: '.($sectionFields['sub_group']).' == ('.$subGroup.') fieldDb: '.($sectionFields['title']).' == ('.$fieldName.') <br>';

                        if ($sectionFields['group'] != $group) {
                            //    $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [$collCounter] Строка [3]). Ожидается [".$sectionFields['group']."] в файле импорта [".$group."]");
                        }

                        if ($sectionFields['sub_group'] == 'A1') {
                            $sectionFields['sub_group'] = '';
                        }

                        if ($sectionFields['sub_group'] != $subGroup) {
                            //$this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [$collCounter] Строка [3]). Ожидается [".$sectionFields['sub_group']."] в файле импорта [".$subGroup."]");
                        }



                        if ($sectionFields['title'] != $fieldName) {

                            $this->addErr("Ошибка в структуре каталога (Лист: [$title] Колонка [" . ($collCounter + 1) . "] Строка [3]). Ожидается [" . $sectionFields['title'] . "] в файле импорта [" . $fieldName . "]");
                            $return = false;
                        } else {
                            $this->userFieldsImport[$sectionArtikul][] = $sectionFields;
                        }
                    }
                }
            }
        } else {
            $this->startUsersRows = $collCounter;
        }
        //  var_dump($this->titlesArray);
        return $return;
    }

    // Проверка значений 

    public function testListsValues() {

        $allSheets = $this->objPHPExcel->getAllSheets();
        $countSheets = count($allSheets);
        $return = true;

        $sectionArtikulsList = $this->db->fetchAll("SELECT  `c1`.`artikul`, `c1`.`href`, `c2`.`type` FROM `catalog` as `c1`, `catalog` as `c2` WHERE `c1`.`level` = '0' AND `c2`.`level` = `c1`.`id` ");
        $sectionArtikuls = array();

        $options = $this->db->fetchAll("SELECT * FROM `catalog_options`");
        if ($options) {
            foreach ($options as $value) {
                if (isset($value['section_href']))
                    $this->catalogOptions[$value['section_href']] = $value;
            }
        }

        if ($sectionArtikulsList) {
            foreach ($sectionArtikulsList as $res) {
                $sectionArtikuls[$res['href']] = array('artikul' => $res['artikul'], 'type' => $res['type']);
            }
        }

        if ($countSheets > 0) {

            foreach ($allSheets as $sheet) {
                $return = $this->testSheetValues($sheet, $sectionArtikuls);
            }
        } else {
            $this->addErr("Файл импорта пустой!");
        }

        return $return;
    }

    protected function testSheetValues($sheet, $sectionArtikuls) {

        $title = $sheet->getTitle();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $return = true;

        // Проверка системных полей

        $sysTitles = array_values($this->systemFieldsArray);
        $uniqFields = array();
        $sectionHref = '';

        $artikul = '';
        // Проверка ведется с учетом того, что название раздела в первой колонке будет всегда одинаковое
        $sectionHref = $this->ru2Lat(trim($sheet->getCellByColumnAndRow(0, 4)->getValue()));

        $sectionName = '';
        $sectionArtukul = '';
        $isUseSubSection = true;
        $isUniqueHref = false;

        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section']) && $this->catalogOptions[$sectionHref]['is_use_sub_section'] == '0') {

            // Проверяем наличие подразделов в настройках каталога для раздела
            $isUseSubSection = false;
        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
            // Проверяем наличие подразделов в настройках каталога для каталога
            $isUseSubSection = false;
            $this->isUseSubSection = false;
        }





        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section'])) {
            // Проверяем наличие подразделов в настройках каталога для раздела
            $isUseSubSection = ($this->catalogOptions[$sectionHref]['is_use_sub_section'] == '1');
            $this->isUseSubSection = $isUseSubSection;
        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
            // Проверяем наличие подразделов в настройках каталога для каталога
            $isUseSubSection = false;
            $this->isUseSubSection = false;
        }

        // Если в каталоге еще нет данных будет учитываться опция уникальности ссылок для всего каталога, а не для каждого в отдельности

        if ($this->isEmptyCatalog) {
            $isUniqueHref = (isset($this->catalogOptions['0']['is_use_unique_goods_names']) && $this->catalogOptions['0']['is_use_unique_goods_names'] == '1');
        }


        for ($i = 0; $i < $highestColumnIndex; $i++) {

            $sheetFieldTitle = trim($sheet->getCellByColumnAndRow($i, 3)->getValue());
            $sectionFields = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtukul' AND `name` = '" . $this->ru2Lat($sheetFieldTitle) . "'");


            for ($f = 3; $f < $highestRow; $f++) {
                $value = trim($sheet->getCellByColumnAndRow($i, $f)->getValue());


                //print "Столбец i: [$i] Строка f: [$f] Значение: $value<br>";

                if ($f > 3 && !empty($value)) {
                    if ($i == 0) {

                        if ($value != $sectionName && $f > 4) {
                         
                            $this->addErr("Ошибка (Лист $title)! Название раздела в одном листе должно быть одинавовым (Столбец[" . ($i + 1) . "] Строка[$f])");
                            $return = false;
                        }

                        $sectionName = $value;
                        if (empty($value)) {
                            //  $this->addErr("Ошибка (Лист $title)! Название раздела не может быть пустым (Столбец[" . ($i + 1) . "] Строка[$f])");
                            // $return = false;
                        }
                    }

                    $ln = 1;
                    if ($this->isUseSectionPosition) {
                        $ln = 2;
                    }

                    if ($isUseSubSection) {


                        if ($i == $ln) {

                            if (empty($value)) {
                                $this->addErr("Ошибка (Лист $title)! Название подраздела не может быть пустым (Столбец[" . ($i + 1) . "] Строка[$f])");
                                $return = false;
                            }
                        }
                        if ($this->isUseSubSectionPosition) {
                            $ln = 4;
                        }
                    }

                    if ($i == $ln) {

                        $artikul = $value;

                        if (empty($value)) {
                            $this->addErr("Ошибка (Лист $title)! Артикул не может быть пустым (Столбец[" . ($i + 1) . "] Строка[$f])");
                            $return = false;
                        }

                        if (isset($uniqFields['artikul']) && in_array($artikul, $uniqFields['artikul'])) {
                            $this->addErr("Ошибка (Лист $title)! Артикул [$artikul] уже существует (Столбец[" . ($i + 1) . "] Строка[$f])");
                            $return = false;
                        } else {
                            $uniqFields['artikul'][] = $artikul;
                        }
                    }

                    if ($i == ($ln + 1)) {

                        if (empty($value)) {
                            $this->addErr("Ошибка (Лист $title)! Название товара не может быть пустым (Столбец[" . ($i + 1) . "] Строка[$f])");
                            $return = false;
                        }



                        if (!$isUniqueHref) {

                            if (isset($uniqFields['name']) && in_array($value, $uniqFields['name'])) {
                                $this->addErr("Ошибка (Лист $title)! Название товара [$value] уже существует (Столбец[" . ($i + 1) . "] Строка[$f])");
                                $return = false;
                            } else {
                                $uniqFields['name'][] = $value;
                            }
                        }
                    }

                    // Проверка данных из полей по умолчанию

                    $defaultFieldLn = ($ln + 1);

                    if ($i > $defaultFieldLn) {
                        foreach ($this->defaultFieldsArray as $key => $defaultFieldTitle) {

                            if (is_array($title)) {

                                $fieldTitle = ' зтого поля ';

                                if (isset($defaultFieldTitle['title'])) {
                                    $fieldTitle = $defaultFieldTitle['title'] . ' поля ';
                                }

                                if (!empty($value) && isset($title['value']) && !in_array($value, $title['value'])) {
                                    $this->addErr("Ошибка (Лист $title)! Неверное значение [$value] Ожидается одно из [" . implode(', ', $defaultFieldTitle['value']) . "] (Столбец[" . ($i + 1) . "] Строка[$f])");
                                    $return = false;
                                }

                                if (isset($defaultFieldTitle['isEmpty']) && $defaultFieldTitle['isEmpty'] === true && empty($value)) {
                                    $this->addErr("Ошибка (Лист $title)! Значение $fieldTitle не может быть пустым (Столбец[" . ($i + 1) . "] Строка[$f])");
                                    $return = false;
                                }

                                if (!empty($value) && isset($defaultFieldTitle['isUnique']) && $defaultFieldTitle['isUnique'] === true && in_array($value, $uniqFields[$key])) {
                                    $this->addErr("Ошибка (Лист $title)! Значение $fieldTitle уже существует (Столбец[" . ($i + 1) . "] Строка[$f])");
                                    $return = false;
                                } else {
                                    $uniqFields[$key] = $value;
                                }
                            }
                            ++$defaultFieldLn;
                        }
                    }


                    // Проверка полей созданные ползователем
                    if ($i > $defaultFieldLn && !empty($sectionArtukul)) {
                        if (!empty($value) && isset($sectionFields['type']) && isset($this->sectionFieldsTypesValues[$sectionFields['type']]['values']) && !in_array($value, $this->sectionFieldsTypesValues[$sectionFields['type']]['values'])) {

                            $this->addErr("Ошибка (Лист $title)! Неверное значение [$value] Ожидается одно из [" . implode(', ', $defaultFieldTitle['value']) . "] (Столбец[" . ($i + 1) . "] Строка[$f])");
                            $return = false;
                        }
                    }
                } else {
                    return true;
                }
            }
        }
        return $return;
    }

    // Импорт

    public function runImport() {
        $allSheets = $this->objPHPExcel->getAllSheets();
        $countSheets = count($allSheets);
        $return = true;

        $sectionArtikulsList = $this->db->fetchAll("SELECT `c1`.`artikul`, `c1`.`href`, `c2`.`type` FROM `catalog` as `c1`, `catalog` as `c2` WHERE `c1`.`level` = '0' AND `c2`.`level` = `c1`.`id` ");

        $sectionArtikuls = array();

        $allPages = $this->db->fetchAll("SELECT * FROM `catalog` WHERE `type`='page'");

        if ($sectionArtikulsList) {
            foreach ($sectionArtikulsList as $res) {
                $sectionArtikuls[$res['href']] = array('artikul' => $res['artikul'], 'type' => $res['type']);
            }
        }

        if ($countSheets > 0) {

            foreach ($allSheets as $sheet) {
                $return = $this->importSheetValues($sheet, $sectionArtikuls, $allPages);
            }
        } else {
            $this->addErr("Файл импорта пустой!");
        }

        return $return;
    }

    public function importSheetValues($sheet, $sectionArtikuls, $allPages) {
        $title = $sheet->getTitle();

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $return = true;

        $sections = array();

        $titleGroupName = '';
        $titleGroupIndex = 0;

        $tablesList = $this->db->fetchAll('SHOW TABLES');
        $existsTablesArray = array();
        $defaultFieldsArrayLength = count($this->defaultFieldsArray);

        for ($i = 4; $i <= $highestRow; $i++) {
            // Добавляем раздел 
            $f1 = 0;

            $sysArr = array_values($this->systemFieldsArray);

            $sectionName = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());

            if (!empty($sectionName)) {

                $sectionHref = $this->ru2Lat(trim($sheet->getCellByColumnAndRow($f1, $i)->getValue()));
                $f1++;
                $sectionPosition = '9999';

                if (!empty($sectionName)) {

                    if ($this->isUseSectionPosition) {
                        $sectionPosition = $this->ru2Lat(trim($sheet->getCellByColumnAndRow($f1, $i)->getValue()));
                        $sectionPosition = "$sectionPosition";
                        $f1++;
                    }

                    $isUseSubSection = true;



                    if (($sectionValues = $this->db->fetchRow("SELECT `id`, `artikul`, `href` FROM `catalog` WHERE `level` = '0' AND `href`='$sectionHref'"))) {
                        $sectionId = $sectionValues['id'];
                        $sectionArtikul = $sectionValues['artikul'];

                        if (isset($_POST['import_section_name']) && is_array($_POST['import_section_name']) && in_array($sectionArtikul, $_POST['import_section_name'])) {
                            return true;
                        }

                        if (!empty($sectionPosition)) {
                            $data = array('position' => $sectionPosition);
                            $this->db->update('catalog', $data, "id=$sectionId");
                        }

                        if (($sectionFieldsRow = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `is_default_field` = 'no'"))) {
                            $existsTablesArray[$sectionArtikul] = array();
                            foreach ($sectionFieldsRow as $sectionFieldsRes) {
                                $existsTablesArray[$sectionArtikul][$sectionFieldsRes['name']] = $sectionFieldsRes['type'];
                            }
                        }

                        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section'])) {
                            // Проверяем наличие подразделов в настройках каталога для раздела
                            $isUseSubSection = ($this->catalogOptions[$sectionHref]['is_use_sub_section'] == '1');
                            $this->isUseSubSection = $isUseSubSection;
                        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
                            // Проверяем наличие подразделов в настройках каталога для каталога
                            $isUseSubSection = false;
                            $this->isUseSubSection = false;
                        }

                        if ($isUseSubSection) {
                            if (isset($this->usedSubSection[$sectionHref])) {
                                $isUseSubSection = $this->usedSubSection[$sectionHref];
                            }
                        }
                    } else {
                        $sectionArtikul = uniqid();
                        $data = array(
                            'name' => $sectionName,
                            'header' => $sectionName,
                            'title' => $sectionName,
                            'keywords' => $sectionName,
                            'description' => $sectionName,
                            'href' => $sectionHref,
                            'artikul' => $sectionArtikul,
                            'type' => 'section',
                            'level' => '0'
                        );
                        if (!empty($sectionPosition)) {
                            $data['position'] = $sectionPosition;
                        }

                        $this->db->insert('catalog', $data);
                        $sectionId = $this->db->lastInsertId();

                        // Создание таблиц для характеристик товара
                        // ----------------------------------

                        $isTableExists = false;
                        // Добавляем подраздел, если есть.
                        $isUniqueHref = false;

                        if (isset($this->catalogOptions[$sectionHref]['is_use_sub_section'])) {
                            // Проверяем наличие подразделов в настройках каталога для раздела
                            $isUseSubSection = ($this->catalogOptions[$sectionHref]['is_use_sub_section'] == '1');
                            $this->isUseSubSection = $isUseSubSection;
                        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
                            // Проверяем наличие подразделов в настройках каталога для каталога
                            $isUseSubSection = false;
                            $this->isUseSubSection = false;
                        }

                        if ($isUseSubSection) {
                            if (isset($this->usedSubSection[$sectionHref])) {
                                $isUseSubSection = $this->usedSubSection[$sectionHref];
                            }
                        }

                        // Создание таблиц для дополнительных полей если, их нет

                        $f2 = $f1;

                        if ($isUseSubSection) {
                            $f2++;
                            if ($this->isUseSubSectionPosition) {
                                $f2++;
                            }
                        }


                        $f2 += $defaultFieldsArrayLength;
                        $f2++;

                        if (is_array($sectionArtikuls) && count($sectionArtikuls) > 0 && is_array($tablesList) && count($tablesList) > 0) {

                            foreach ($sectionArtikuls as $sectionArtikul1) {
                                if (isset($sectionArtikul1['artikul']) && !$this->isTableExists('catalog-fields-' . $sectionArtikul1['artikul'], $tablesList)) {
                                    $existsTablesArray[$sectionArtikul1['artikul']] = $this->addSectionFieldsTables($sheet, $f2, $highestColumnIndex, $sectionArtikul1['artikul']);     //$sheet, $f1, $highestColumnIndex, $sectionArtikul)          
                                } elseif (isset($sectionArtikul1['artikul']) && !$this->isTableExists('catalog-fields-' . $sectionArtikul, $tablesList)) {
                                    if (!isset($existsTablesArray[$sectionArtikul])) {
                                        $existsTablesArray[$sectionArtikul] = $this->addSectionFieldsTables($sheet, $f2, $highestColumnIndex, $sectionArtikul);
                                    }
                                }
                            }
                        } else {
                            //  $existsTablesArray[$sectionArtikul] = $this->addSectionFieldsTables($sheet, $this->startUsersRows, $highestColumnIndex, $sectionArtikul);     //$sheet, $f1, $highestColumnIndex, $sectionArtikul)          
                            $existsTablesArray[$sectionArtikul] = $this->addSectionFieldsTables($sheet, $f2, $highestColumnIndex, $sectionArtikul);     //$sheet, $f1, $highestColumnIndex, $sectionArtikul)          
                        }

                        // --------------------------------
                    }









                    $contentArtikulFieldsArray = array();

                    if (isset($this->catalogOptions[$sectionHref]['is_use_unique_goods_names']) && $this->catalogOptions[$sectionHref]['is_use_unique_goods_names'] == '1') {
                        // Проверяем должны ли быть ссылки уникальными в пределах одного раздела для раздела
                        $isUniqueHref = true;
                        $contentArtikulFieldsArray = explode(';', $this->catalogOptions[$sectionHref]['is_unique_url_fields']);
                    } elseif (isset($this->catalogOptions['0']['is_use_unique_goods_names']) && $this->catalogOptions['0']['is_use_unique_goods_names'] == '1') {
                        // Проверяем должны ли быть ссылки уникальными в пределах одного раздела для каталога
                        $contentArtikulFieldsArray = explode(';', $this->catalogOptions[0]['is_unique_url_fields']);
                        $isUniqueHref = true;
                    }


                    if ($isUseSubSection) {

                        $subSectionName = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());
                        $subSectionHref = $this->ru2Lat($subSectionName);
                        $subSectionPosition = '';
                        $f1++;

                        if ($this->isUseSubSectionPosition) {
                            $subSectionPosition = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());
                            $f1++;
                        }

                        if (($subSectionId = $this->db->fetchOne("SELECT `id` FROM `catalog` WHERE `href` = '$subSectionHref' AND `level` = '$sectionId' AND `type` = 'section'"))) {
                            if (!empty($subSectionPosition)) {
                                $data = array('position' => "$subSectionPosition");
                                $this->db->update('catalog', $data, "id=$subSectionId");
                            }
                        } else {
                            $data = array(
                                'name' => $subSectionName,
                                'href' => $subSectionHref,
                                'level' => $sectionId,
                                'header' => $subSectionName,
                                'title' => $subSectionName,
                                'keywords' => $subSectionName,
                                'description' => $subSectionName,
                                'type' => 'section'
                            );

                            if (!empty($subSectionPosition)) {
                                $data['position'] = $subSectionPosition;
                            }


                            $this->db->insert('catalog', $data);
                            $subSectionId = $this->db->lastInsertId();
                        }
                    }

                    $artikul = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());
                    $f1++;

                    if (($pageArr = $this->isRecordExists($artikul, $allPages))) {

                        if ($pageArr['changed'] != '0') {
                            // Проверяем поля, которые были отредактированны на сайте                 
                            $this->recordExits[$title]['inAdmin'][$artikul] =
                                    array('db' => $pageArr, 'file' => array());
                        } else {

                            $this->recordExits[$title]['inDb'][$artikul] = true;
                        }
                    } else {
                        // $this->recordExits[$title]['inDb'][$artikul] = true; 
                    }

                    // Добавляем основные поля

                    $data = array(
                        'level' => ($isUseSubSection && !empty($subSectionId) ? $subSectionId : $sectionId),
                        'type' => 'page'
                    );

                    foreach ($this->defaultFieldsArray as $defaultFieldName => $defaultFieldTitle) {
                        $defaultFieldTitle1 = $defaultFieldTitle;
                        $defaultFieldValue = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());
                        $defaultFieldValue1 = $defaultFieldValue;
                        $f1++;

                        if (is_array($defaultFieldTitle)) {
                            if (isset($defaultFieldTitle['title'])) {
                                $defaultFieldTitle1 = $defaultFieldTitle['title'];
                            }

                            if (isset($defaultFieldTitle['value'])) {
                                if (is_array($defaultFieldTitle['value'])) {
                                    foreach ($defaultFieldTitle['value'] as $key => $value) {

                                        if ($value == $defaultFieldValue1) {
                                            //                                print "$title  ($defaultFieldTitle[title] [ $defaultFieldTitle[title] ]]) [$f1 : $i] |(key: $key) $value | == |$defaultFieldValue| ".sizeof($value)."=".sizeof($defaultFieldValue)." <br>";
                                            $defaultFieldValue = $key;
                                        }
                                    }
                                }
                            }
                        }

                        //  print "index: ($title [строка: $i столбец: $f1] ) ".$sheet->getCellByColumnAndRow($f1, 3)->getValue()."  name: $defaultFieldName value: $defaultFieldValue <br>";
                        if (preg_match('/\=CONCATENATE.*/i', $defaultFieldValue)) {
                            if (isset($data['name'])) {
                                $defaultFieldValue = $data['name'];
                            }
                        }
                        $data[$defaultFieldName] = "$defaultFieldValue";
                    }


                    $data['artikul'] = $artikul;

                    if (isset($data['name'])) {
                        $href = $data['name'];
                        if (count($contentArtikulFieldsArray) > 0) {
                            $href = '';
                            $counter1 = 0;
                            foreach ($contentArtikulFieldsArray as $contField) {

                                if ($contField == 'section') {
                                    $href .= $sectionHref . ' ';
                                } elseif ($contField == 'sub_section' && $isUseSubSection) {
                                    $href .= $subSectionHref . ' ';
                                } elseif (isset($data[$contField]) && !empty($data[$contField])) {
                                    $href .= $this->ru2Lat($data[$contField]) . ' ';
                                    $counter1++;
                                }
                            }
                            $data['name'] = trim($data['name']);
                            $href .= ' ' . $data['name'];
                        }
                        // var_dump($href);
                        $href = $this->ru2Lat($href);
                        $href = str_replace('--', '-', $href);
                        $data['href'] = $href;
                    }

                    //    print "$sectionName f1: $f1<br>";
                    // Добавляем данные в дополнительные поля
                    //$isUserColls = $this->db->fetchAll("SELECT `name`, `type` FROM `catalog_section_fields` WHERE `catalog_section_href`='$sectionArtikul'");

                    if (is_array($sectionArtikul) && isset($sectionArtikul['artikul'])) {
                        $sectionArtikul = $sectionArtikul['artikul'];
                    }



                    if (isset($existsTablesArray[$sectionArtikul])) {

                        $data1 = array('catalog_artikul' => $artikul);


                        foreach ($existsTablesArray[$sectionArtikul] as $userFieldName => $userFieldType) {

                            $userFieldValue = trim($sheet->getCellByColumnAndRow($f1, $i)->getValue());



                            //print "section: $sectionName f1: $f1 name: $userFieldName val: $userFieldValue <br>";

                            if (isset($this->sectionFieldsTypesValues[$userFieldType])) {
                                if (is_array($this->sectionFieldsTypesValues[$userFieldType])) {

                                    foreach ($this->sectionFieldsTypesValues[$userFieldType] as $key => $value) {
                                        if ($userFieldValue == $value) {
                                            $userFieldValue = $key;
                                        }
                                    }
                                }
                            }
                            $userFieldValue = trim($userFieldValue);
                            $data1[$userFieldName] = "$userFieldValue";
                            $f1++;
                        }


                        if (($userDataId = $this->db->fetchOne("SELECT `id` FROM `catalog-fields-$sectionArtikul` WHERE `catalog_artikul`='$artikul'"))) {
                            $this->db->update('catalog-fields-' . $sectionArtikul, $data1, "id=$userDataId");
                        } else {

                            $this->db->insert('catalog-fields-' . $sectionArtikul, $data1);
                        }
                    } else {
                        
                    }

                    if (isset($this->recordExits[$title]['inAdmin'][$artikul])) {
                        $this->recordExits[$title]['inAdmin'][$artikul]['file'] = $data;
                    }



                    if (!isset($this->recordExits[$title]['inAdmin'][$artikul])) {

                        if (isset($data['position']) && empty($data['position'])) {
                            $data['position'] = '9999';
                        } else {
                            $data['position'] = (string) $data['position'];
                        }

                        if (isset($this->recordExits[$title]['inDb'][$artikul])) {
                            unset($data['href']);

                            $this->db->update('catalog', $data, "artikul='$artikul'");
                        } else {

                            $this->db->insert('catalog', $data);
                        }
                    }
                }
            } else {
                return;
            }
        }

        if (isset($this->recordExits[$title]['inDb'])) {
            unset($this->recordExits[$title]['inDb']);
        }
    }

    protected function addSectionFieldsTables($sheet, $f1, $highestColumnIndex, $sectionArtikul, $isDump = false) {
        // Добавляем дополнитльные поля, если их нет в базе

        $isTableExists = false;

        $sqlFieldTable = '';
        $isFieldExists = false;
        $tableName = 'catalog-fields-' . $sectionArtikul;

        $isCreateUserFields = false;
        $sqlFieldTable = "CREATE TABLE `$tableName` (`id`INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255)  CHARACTER SET cp1251 COLLATE cp1251_general_ci, `changed` INT DEFAULT 0 ";
        $sqlTableCatalogSectionFields = "";
        $data2 = array();
        $data3 = array();
        $groupName = '';
        $uniqueFields = array();
        $isEmptyGroup = true;
        $isEmptySubGroup = true;
        $subGroupTitles = array();

        for ($collCounter = $f1; $collCounter < $highestColumnIndex; ++$collCounter) {
            $group1 = trim($sheet->getCellByColumnAndRow($collCounter, 1)->getValue());
            $group1Tmp = $group1;
            $subGroup1Tmp = trim($sheet->getCellByColumnAndRow($collCounter, 2)->getValue());
            if (!empty($subGroup1Tmp)) {
                $subGroup1 = $subGroup1Tmp;
            }
            $title1 = trim($sheet->getCellByColumnAndRow($collCounter, 3)->getValue());

            $this->titlesGroups[$collCounter] = $group1;
            $this->titlesSubGroups[$collCounter] = $subGroup1Tmp;
            $this->titlesFieldsGroups[$collCounter] = $title1;
            $title1Lat = $this->ru2Lat($title1);

            //print "group: |$group1| subGroup: |$subGroup1| title1: |$title1| <br>";

            if (empty($title1Lat)) {
                $this->addErr('Название поля не может быть пустым строка [3] колонка [' . ( $collCounter + 1) . ']');
                return false;
            }

            if (in_array($title1Lat, $uniqueFields)) {
                $title1Lat .= '-' . $this->ru2Lat($subGroup1);
            }

            if (in_array($title1Lat, $uniqueFields)) {
                $this->addErr('Названия полей не могут пвторятся в одном листе (' . $title . ') строка [3] колонка [' . ( $collCounter + 1) . ']');
                return false;
            } else {
                $uniqueFields [] = $title1Lat;
            }


            if (!empty($title1)) {

                if (strlen($title1Lat) > 35) {
                    $title1Lat = substr($title1Lat, 0, 35);
                }
                if (!$isCreateUserFields) {

                    if (!$isTableExists) {
                        
                    } else {
                        // $sqlFieldTable = "ALTER TABLE `$tableName` ";
                    }
                }


                if (!$isTableExists) {

                    if ($isEmptyGroup) {

                        if ($group1 == '') {
                            $group1 = 'A1';
                        }

                        if ($subGroup1Tmp == '') {
                            $subGroup1 = 'A1';
                        }
                    }



                    $isEmptyGroup = (empty($group1Tmp) && empty($subGroup1Tmp) && $isEmptyGroup);

                    if (!isset($subGroupTitles[$group1Tmp])) {
                        $isEmptySubGroup = true;
                    }

                    $subGroupTitles[$group1Tmp] = $subGroup1Tmp;


                    //  print "grroupName: <b>$groupName</b> <br/>";
                    if ($isEmptyGroup) {
                        $groupName = $group1Tmp;
                    } elseif (!empty($group1Tmp)) {
                        $groupName = $group1Tmp;
                    }


                    if (empty($subGroupTitles[$group1Tmp]) && $isEmptySubGroup) {
                        $subGroup1 = 'A1';
                    } else {

                        $isEmptySubGroup = false;
                    }



                    // var_dump($isEmptyGroup);
                    $data2 = array(
                        'name' => $title1Lat,
                        'title' => $title1,
                        'catalog_section_href' => $sectionArtikul,
                        'group' => $groupName,
                        'sub_group' => $subGroup1,
                        'type' => 'varchar'
                    );

                    // var_dump($data2);


                    if (count($data2) > 0) {
                        if (!$isDump) {
                            $this->db->insert('catalog_section_fields', $data2);
                        }
                    }
                    $sqlFieldTable .= ", `$title1Lat`  VARCHAR (255) CHARACTER SET cp1251 COLLATE cp1251_general_ci";
                    $data3[$title1Lat] = 'varchar';
                } else {
                    //if (!($isFieldExists = $this->db->fetchOne("DESC `$tableName` `$title1Lat` "))) {
                    // $sqlFieldTable .= ( $isCreateUserFields ? ',' : '') . " ADD `$title1Lat` VARCHAR (255)";
                    // }
                }


                $isCreateUserFields = true;
            }
            // print "Group: ".$this->titlesGroups[$f1];
            // print " SubGroup: ".$this->titlesSubGroups[$f1];
            // print " Title: ". $this->titlesFieldsGroups[$f1]."<br>";
        }


        if ($isCreateUserFields) {
            $sqlFieldTable .= ', INDEX (`catalog_artikul`))DEFAULT CHARSET=cp1251;';

            if (!$this->isTableExists($tableName)) {
                if (!$isDump) {
                    $this->db->query($sqlFieldTable);
                }

                return $data3;
            }
            //print "$sqlFieldTable <br>";
        }
        return array();
    }

    // Экспорт

    public function runExport() {
        $fields = array_keys($this->defaultFieldsArray);
        if (!in_array('href', $fields)) {
            $fields[] = 'href';
        }
        $fields = implode(', ', $fields);
        $dbh = $this->db->query("SELECT $fields, artikul FROM `$this->tableName` WHERE `level` = 0 ORDER BY `position`, `name`");
        $dbh->setFetchMode(Zend_Db::FETCH_ASSOC);
        $sectionList = $dbh->fetchAll();

        $fileName = 'Каталог ' . date('d-m-Y H.i');
        $fileFormat = '.xls';
        $fileExelType = 'Excel5';

        //export_name export_format
        if (isset($_POST['export_name']) && !empty($_POST['export_name'])) {
            $fileName = $_POST['export_name'];
        }

        if (isset($_POST['export_format']) && !empty($_POST['export_format'])) {
            if (isset($this->fileFormats[$_POST['export_format']])) {
                $fileExelType = $this->fileFormats[$_POST['export_format']];
                $fileName .= '.' . $_POST['export_format'];
            }
        }

        if (is_array($sectionList) && count($sectionList) > 0) {
            $counter = 0;
            foreach ($sectionList as $name => $value) {
                $sheet = null;
                if ($counter == 0) {
                    $sheet = $this->objPHPExcel->getActiveSheet();
                    if (strlen($value['name']) > 31) {
                        $value['name'] = iconv_substr($value['name'], 0, 21, 'UTF-8') . ' ... ';
                    }
                    $sheet->setTitle($value['name']);
                } else {
                    if (strlen($value['name']) > 31) {
                        $value['name'] = iconv_substr($value['name'], 0, 21, 'UTF-8') . ' ... ';
                    }
                    $sheet = $this->addList($value['name'], $value['artikul']);
                }
                $counter++;
                $fieldsArray = $this->addTitles($value, $sheet);
                $this->addValues($value['artikul'], $sheet, $fieldsArray);
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, $fileExelType);
        //$objWriter->save($_SERVER['DOCUMENT_ROOT'].'/tmp/test.xls');
        //   die;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        return false;
    }

    protected function addList($listName, $href) {
        $sheet = $this->objPHPExcel->createSheet();
        if (iconv_strlen($listName, 'UTF-8') > 31) {
            $listName = iconv_substr($listName, 0, 26, 'UTF-8') . ' ... ';
        }


        $sheet->setTitle($listName);
        return $sheet;
    }

    protected function addTitles($href, &$sheet) {




        $isUseSubSection = true;

        if (isset($this->catalogOptions[$href['href']]['is_use_sub_section'])) {
            // Проверяем наличие подразделов в настройках каталога для раздела                
            $isUseSubSection = ($this->catalogOptions[$href['href']]['is_use_sub_section'] == '1');
            $this->isUseSubSection = $isUseSubSection;
        } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
            // Проверяем наличие подразделов в настройках каталога для каталога
            $isUseSubSection = false;
            $this->isUseSubSection = false;
        }

        $defaultFieldsLength = count($this->defaultFieldsArray);
        $defaultFieldsLength += count($this->systemFieldsArray);

        if ($isUseSubSection) {
            $defaultFieldsLength++;
        }

        if ($isUseSubSection) {
            $defaultFieldsLength++;
        }

        if ($this->isUseNamePosition) {
            // $defaultFieldsLength++;
        }
        //   var_dump($defaultFieldsLength); die;
        $defaultFieldsLength--;
        $defaultMarge = $this->getColumnRange($defaultFieldsLength);
        $sheet->mergeCells('A1:' . $defaultMarge . '1');
        //$sheet->setCellValueByColumnAndRow(0, 1, 'Основная группа');
        $sheet->setCellValueByColumnAndRow(0, 1, '');
        $sheet->mergeCells('A2:' . $defaultMarge . '2');
        $sheet->setCellValueByColumnAndRow(0, 2, '');
        //$sheet->setCellValueByColumnAndRow(0, 2, 'Основная подгруппа');
        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(25);
        $counter = 0;
        $returnFieldsArray = array();
        // Добавляем заголовки для системных полей


        foreach ($this->systemFieldsArray as $fieldName => $fieldTitle) {
            if (is_array($fieldTitle) && isset($fieldTitle['title'])) {
                $fieldTitle = $fieldTitle['title'];
            }



            //var_dump($isUseSubSection);

            if ($fieldName != 'sub_section') {

                $sheet->setCellValueByColumnAndRow($counter, 3, $fieldTitle);
                $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($fieldTitle, 'UTF-8'));
                $counter++;
            }
            // Если используется позиция для раздела - создаем поле
            if ($fieldName == 'section' && $this->isUseSectionPosition) {
                if (isset($this->defaultFieldsArray['position'])) {
                    $fieldTitle = 'Позиция раздела'; //$this->defaultFieldsArray['position'];
                }
                $sheet->setCellValueByColumnAndRow($counter, 3, $fieldTitle);
                //   $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($fieldTitle, 'UTF-8'));
                $counter++;
            }
            // Если используется позиция для подраздела - создаем поле
            //print "$fieldName ".($isUseSubSection ? 'yes' : 'no')." == ".($fieldName == 'sub_section' && $isUseSubSection ? 'yes' : 'no')." <br>";

            if ($fieldName == 'sub_section' && $isUseSubSection) {
                $sheet->setCellValueByColumnAndRow($counter, 3, $fieldTitle);
                $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($fieldTitle, 'UTF-8'));
                $counter++;
            }

            if ($fieldName == 'sub_section' && $isUseSubSection) {
                if (isset($this->defaultFieldsArray['position'])) {
                    $fieldTitle = 'Позиция подраздела'; //$this->defaultFieldsArray['position'];
                }
                $sheet->setCellValueByColumnAndRow($counter, 3, $fieldTitle);
                $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($fieldTitle, 'UTF-8'));
                $counter++;
            }
        }

        // Добавляем поля по умолчанию

        foreach ($this->defaultFieldsArray as $fieldName => $fieldTitle) {

            $fieldType = null;

            if (is_array($fieldTitle)) {

                if (isset($fieldTitle['value'])) {
                    $fieldType = $fieldTitle;
                }

                if (isset($fieldTitle['title'])) {
                    $fieldTitle = $fieldTitle['title'];
                }
            }


            $returnFieldsArray[$fieldName] = (!empty($fieldType) ? $fieldType : '');
            $sheet->setCellValueByColumnAndRow($counter, 3, $fieldTitle);
            $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($fieldTitle, 'UTF-8'));
            $counter++;
        }


        // Добавляем дополнительные поля


        $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '" . $href['artikul'] . "' AND `is_default_field` = 'no' ORDER BY `group`, `sub_group` ");
        $arr = array();

        if ($row) {

            foreach ($row as $res) {
                $group = $res['group'];
                $subGroup = $res['sub_group'];
                $arr[$group][$subGroup][] = $res;
            }
        }

        if (count($arr) > 0) {
            $counter1 = $counter;
            $counter2 = $counter;
            $counter3 = $counter;
            $len1 = $defaultFieldsLength + 1;
            $fieldsGroupsLength = array();


            foreach ($arr as $group => $val) {

                if ($group == 'A1') {
                    $group = '';
                }

                $len2 = ($len1 + count($val));
                $firstRangeVal = $len1;
                $lastRangeVal = $firstRangeVal - 1;
                $countVal1 = 0;
                $countVal1Summ = 0;

                $isColor = true;
                $isColor1 = true;

                foreach ($val as $subGroup => $val1) {

                    $countVal1 = count($val1);
                    if (!isset($fieldsGroupsLength[$group])) {
                        $countVal1Summ = 0;
                    }

                    if ($subGroup == 'A1') {
                        $subGroup = '';
                    }

                    $countVal1Summ += $countVal1;
                    $fieldsGroupsLength[$group] = $countVal1Summ;



                    $lastRangeVal += $countVal1;

                    // print "count: $countVal1 firstRange: $firstRangeVal lastRange: $lastRangeVal<br>";

                    $defaultMarge1 = $this->getColumnRange($firstRangeVal);
                    $defaultMarge2 = $this->getColumnRange($lastRangeVal);

                    //  print "defaultMarge1: $defaultMarge1 defaultMarge2: $defaultMarge2 <br>";



                    $sheet->setCellValueByColumnAndRow($firstRangeVal, 2, $subGroup);
                    $firstRangeVal += $countVal1;
                    if ($firstRangeVal == $lastRangeVal) {
                        $firstRangeVal++;
                    }

                    //$lastRangeVal ++;



                    if (!empty($subGroup)) {
                        $sheet->mergeCells($defaultMarge1 . '2:' . $defaultMarge2 . '2');
                        $sheet->getStyle($defaultMarge1 . '2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(($isColor ? 'C0C0C0' : 'eaeaea'));
                        $isColor = !$isColor;
                        $sheet->getStyle($defaultMarge1 . '2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($defaultMarge1 . '2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    //print 'counter2: '.$counter2.'  '. $defaultMarge1.'1:'.$defaultMarge2.'1 count(val1): '.count($val1).' firstRange:'.$firstRangeVal.' lastRange: '.$lastRangeVal.'<br>';
                    $counter2++;


                    $fieldCount = count($val1);
                    for ($i1 = 0; $i1 < $fieldCount; $i1++) {
                        $field = $val1[$i1];
                        // print $field['title'].'<br>';
                        $sheet->setCellValueByColumnAndRow($counter3, 3, $field['title']);
                        $sheet->getDefaultColumnDimension()->setWidth(iconv_strlen($field['title'], 'UTF-8') + 10);
                        // print "counter3: $counter3 <br>";
                        $counter3++;

                        $returnFieldsArray[$field['name']] = (!empty($field['type']) ? $field['type'] : '');
                    }
                }

                if ($len1 == $len2) {
                    $len1++;
                }

                if (isset($fieldsGroupsLength[$group])) {
                    $len2 = ($len1 + $fieldsGroupsLength[$group] - 1);
                }
                //  print "len1: |$len1| len2: |$len2| <br>";
                $defaultMarge1 = $this->getColumnRange($len1);
                $defaultMarge2 = $this->getColumnRange($len2);

                //   print $defaultMarge1 . '1:' . $defaultMarge2 . '1 <br>';
                if (!empty($group)) {
                    $sheet->mergeCells($defaultMarge1 . '1:' . $defaultMarge2 . '1');
                    $sheet->getStyle($defaultMarge1 . '1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(($isColor1 ? 'C0C0C0' : 'eaeaea'));
                    $isColor1 = !$isColor1;
                    $sheet->getStyle($defaultMarge1 . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($defaultMarge1 . '1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $sheet->setCellValueByColumnAndRow($len1, 1, $group);
                }

                $len1 = $len2 + 1;
            }
            //  die;
        }

        return $returnFieldsArray;
    }

    protected function addValues($artikul, &$sheet, $fieldsArray = array()) {

        // Выводим значения поумолчанию


        $sectionList = $this->db->fetchAll("SELECT `c1`.`name` as `section_name`,`c1`.`href` as `section_href`, `c1`.`position` as `section_position`, `c2`.`name` as `sub_section_name`, `c2`.`position` as `sub_section_position`, `c3`.* FROM `catalog` as `c1`, `catalog` as `c2`, `catalog` as `c3` WHERE `c1`.`artikul` = '$artikul' AND `c1`.`id`=`c2`.`level` AND `c2`.`id`=`c3`.`level`  ORDER BY `c1`.`position`, `c1`.`name`,  `c2`.`position`, `c2`.`name`,  `c3`.`position`, `c3`.`name`");

        $isUseSubSection = $this->isUseSubSection;

        if (!$this->isUseSubSection) {
            $sectionList = $this->db->fetchAll("SELECT `c1`.`name` as `section_name`, `c1`.`href` as `section_href`, `c1`.`position` as `section_position`, `c2`.* FROM `catalog` as `c1`, `catalog` as `c2` WHERE `c1`.`artikul` = '$artikul' AND `c1`.`id`=`c2`.`level` ORDER BY `c1`.`position`, `c1`.`name`,  `c2`.`position`, `c2`.`name`");
        }


        if (isset($sectionList[0]['type'])) {


            $sectionFieldsValues = array();

            $tableName = 'catalog-fields-' . $artikul;
            $sectionFieldsValues = array();
            $isTableExists = $this->isTableExists($tableName);

            $sheet->getDefaultStyle()->getFont()->setName('Arial');
            $sheet->getDefaultStyle()->getFont()->setSize(10);


            $lineCounter = 4;
            foreach ($sectionList as $section) {
                $rowCounter = 0;
                // Заливаем значения для системных полей
                $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $section['section_name']);
                ++$rowCounter;



                if ($this->isUseSectionPosition) {
                    if ($section['section_position'] == '9999') {
                        $section['section_position'] = '';
                    }
                    $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $section['section_position']);
                    ++$rowCounter;
                }
                //  $isUseSubSection = true;
//
//                if ($isUseSubSection) {
//
//                    if (isset($this->catalogOptions[$section['section_href']]['is_use_sub_section']) && $this->catalogOptions[$section['section_href']]['is_use_sub_section'] == '0') {
//                        // Проверяем наличие подразделов в настройках каталога для раздела
//                        $isUseSubSection = false;
//                    } elseif (isset($this->catalogOptions['0']['is_use_sub_section']) && $this->catalogOptions['0']['is_use_sub_section'] == '0') {
//                        // Проверяем наличие подразделов в настройках каталога для каталога
//                        $isUseSubSection = false;
//                        $this->isUseSubSection = false;
//                    }
//                }
//            if ($isUseSubSection) {
//               if (isset($this->usedSubSection[$sectionHref])) {
//                  $isUseSubSection = $this->usedSubSection[$sectionHref];
//                  
//               }               
//            }


                if ($isUseSubSection && isset($section['sub_section_name'])) {

                    $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $section['sub_section_name']);
                    ++$rowCounter;

                    if ($this->isUseSubSectionPosition) {
                        if ($section['sub_section_position'] == '9999') {
                            $section['sub_section_position'] = '';
                        }
                        $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $section['sub_section_position']);
                        ++$rowCounter;
                    }
                }

                $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $section['artikul']);
                ++$rowCounter;

                if ($isTableExists) {
                    $sectionFieldsValues = $this->db->fetchRow("SELECT * FROM `$tableName` WHERE `catalog_artikul` = '$section[artikul]'");
                }



                foreach ($fieldsArray as $fieldName => $fieldType) {


                    if (isset($section['position']) && $section['position'] == '9999') {
                        $section['position'] = '';
                    }


                    //  print "|$fieldName| [".(isset($section[$fieldName]) ? 'ok' : 'no')."]<br>";

                    if (isset($section[$fieldName])) { // Заливаем стандартные поля               
                        $value = $section[$fieldName];
                        if (is_array($fieldType)) {
                            if (is_numeric($value)) {
                                $value = intval($value);
                            }
                            if (isset($fieldType['value']) && is_array($fieldType['value']) && isset($fieldType['value'][$value])) {

                                $value = $fieldType['value'][$value];
                            }
                        }
                        $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $value);
                        ++$rowCounter;
                    } elseif (isset($sectionFieldsValues[$fieldName])) {
                        $value = $sectionFieldsValues[$fieldName];

                        if (is_numeric($value) && strlen($value) > 5) {
                            $value = substr($value, 0, 5);
                        }

                        if (is_string($fieldType) && !empty($fieldType) && isset($this->sectionFieldsTypesValues[$fieldType]['values'][$value])) {
                            $value = $this->sectionFieldsTypesValues[$fieldType]['values'][$value];
                        }

                        $sheet->setCellValueByColumnAndRow($rowCounter, $lineCounter, $value);
                        ++$rowCounter;
                    }
                }
//var_dump($section); die;
                // Добавляем значения для характеристик товара

                if ($isTableExists) {
                    
                }
                ++$lineCounter;
                //$sheet->setCellValueByColumnAndRow($len1, 1, $group);
            }
        }
    }

    protected function getFieldOptions($href) {

        $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href` = '$href' AND `is_default_field` = 'no'");
        if ($row) {
            return $row;
        }
        return false;
    }

    protected function getColumnRange($ln) {
        $ret = '';
        $count = count($this->exelDefaultColumnsArray);

        $first = 'A';
        $last = '';
        $last = '';

        if ($ln < $count) {
            $first = $this->exelDefaultColumnsArray[$ln];
        } else {
            $i = 0;
            for ($i = 0, $f = 0; $i <= $ln; $i += $count, $f++) {
                // print "i: $i f: $f <br>";
                if ($f > 0) {
                    $first = $this->exelDefaultColumnsArray[($f - 1)];
                }
                $x = ($ln - $i);
                if (isset($this->exelDefaultColumnsArray[$x])) {
                    $last = $this->exelDefaultColumnsArray[$x];
                }
            }
        }

        return $first . $last;
    }

    protected function isTableExists($tableName, $tablesRow = array()) {

        if (!is_array($tablesRow) || count($tablesRow) <= 0) {
            $tablesRow = $this->db->fetchAll("SHOW TABLES");
        }

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

    protected function isUseSubSections($sectionHref) {
        $type = $this->db->fetchOne("SELECT `c1`.`type` FROM `catalog` as `c1`, `catalog` as `c2` WHERE `c2`.`href` = '$sectionHref' AND `c1`.`level` = `c2`.`id`");
        return ($type == 'section');
    }

    protected function ru2Lat($str) {

        $rus = array('ё', 'ж', 'ц', 'ч', 'ш', 'щ', 'ю', 'я', 'Ё', 'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ю', 'Я', 'Ї', 'ї', 'Є', 'є', 'І', 'і', 'ь', 'Ь', 'Ъ', 'ъ');
        $lat = array('yo', 'zh', 'tc', 'ch', 'sh', 'sh', 'yu', 'ya', 'YO', 'ZH', 'TC', 'CH', 'SH', 'SH', 'YU', 'YA', 'YI', 'yi', 'E', 'e', 'I', 'i', '', '', '', '');
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
        // $str = str_ireplace(' ', '-', trim($str));       
        $str = preg_replace('/\W/', '-', trim($str));

        return (strtolower($str));
    }

    protected function isRecordExists($artikul, $arr) {
        if ($arr) {
            foreach ($arr as $val) {
                if (isset($val['artikul']) && $val['artikul'] == $artikul) {
                    return $val;
                }
            }
        }
        return false;
    }

    public function getDefaultFields() {

        return array_merge($this->defaultFieldsArray, $this->systemFieldsArray);
    }

    public function __destruct() {
        if ($this->objPHPExcel !== null) {
            $this->objPHPExcel->disconnectWorksheets();
            unset($this->objPHPExcel);
        }
    }

}

?>
