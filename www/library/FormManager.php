<?php

class FormManagerObject {

    protected $db;
    protected $tpl;
    protected $_err = null;
    protected $id = '';
    protected $tableName = '';
    protected $options = array();
    protected $isAjaxUpload = false;
    private $unsetedFields = array();
    private $newFields = array();
    protected $referer = '/';
    protected $otherFields = '';
    protected $galleryType = 'gallery';

    public function __construct($tpl, $db, $tableName = '', $id = '') {
        $this->tpl = $tpl;
        $this->db = $db;
        $this->tableName = $tableName;
        $this->id = $id;

//        if (isset(CatalogFormOptions::$imgPath)) {
//            $this->setImagePath(CatalogFormOptions::$imgPath);
//        }
//        if (isset(CatalogFormOptions::$imgSize)) {
//            $this->options['imgSize'] = CatalogFormOptions::$imgSize;
//        }
    }

    public function setOptions($optionsArray=array()) {
        $this->options = array_merge($optionsArray, $this->options);
        $_SESSION['form_manager_options'] = $this->options;;
    }

    public function getOptions() {
        return $this->options;
    }

    public function deleteImage($imageFieldName='pic') {
        $isFlashUploader = (isset($this->options['isUseFlashUploader']) && $this->options['isUseFlashUploader']);
        if (($fileName = $this->db->fetchOne("SELECT `$imageFieldName` FROM `$this->tableName` WHERE `id` = '$this->id'"))) {
            if (isset($this->options['small1']['path'])) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . $this->options['small1']['path'] . '/' . $fileName)) {
                    @chmod($_SERVER['DOCUMENT_ROOT'] . $this->options['small1']['path'] . '/' . $fileName, 0666);
                    @unlink($_SERVER['DOCUMENT_ROOT'] . $this->options['small1']['path'] . '/' . $fileName);
                }
            }

            if (isset($this->options['imgPath']['big'])) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['big'] . '/' . $fileName)) {
                    @chmod($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['big'] . '/' . $fileName, 0666);
                    @unlink($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['big'] . '/' . $fileName);
                }
            }

            if (isset($this->options['imgPath']['real'])) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['real'] . '/' . $fileName)) {
                    @chmod($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['real'] . '/' . $fileName, 0666);
                    @unlink($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['real'] . '/' . $fileName);
                }
            }
        }

        if ($isFlashUploader) {

            die('/img/no-foto/no-foto-200x180.gif');
        }
    }

    protected function setImagePath($imagePathArray) {
        if (isset($imagePathArray['small'])) {
            $imagePathArray['small'] = preg_replace('/\/+$/', '', $imagePathArray['small']);
        }

        if (isset($imagePathArray['big'])) {
            $imagePathArray['big'] = preg_replace('/\/+$/', '', $imagePathArray['big']);
        }

        if (isset($imagePathArray['real'])) {
            $imagePathArray['real'] = preg_replace('/\/+$/', '', $imagePathArray['real']);
        }

        $this->options['imgPath'] = $imagePathArray;
    }

    public function getIsAjaxUpload() {
        return $this->isAjaxUpload;
    }

    public function unsetField($fieldName) {
        $this->unsetedFields[] = $fieldName;
    }

    public function addFieldValue($fieldName, $value) {
        $this->newFields[$fieldName] = $value;
    }

    public function save($type='insert') {
        

        if (isset($_POST['form_manager']) && is_array($_POST['form_manager']) && count($_POST['form_manager']) > 0) {

            if (isset($_SESSION['catalog'][$this->id]['img'])) {
                if (isset($this->options['imgPath']) && is_array($this->options['imgPath']) && count($this->options['imgPath']) > 0) {
                    foreach ($this->options['imgPath'] as $type => $path) {
                        if (is_file($_SERVER['DOCUMENT_ROOT'] . $path . '/' . $_SESSION['catalog'][$this->id]['img'])) {
                            $this->addFieldValue('pic', $_SESSION['catalog'][$this->id]['img']);
                            break;
                        } else {
                            if (isset($_SESSION['catalog'])) {
                                unset($_SESSION['catalog']);
                            }
                        }
                    }
                }
            }



            if (isset($_POST['HTTP_REFERER'])) {
                $this->referer = $_POST['HTTP_REFERER'];
                $this->unsetField('HTTP_REFERER');
            }

            if (count($this->unsetedFields) > 0) {
                foreach ($this->unsetedFields as $val) {
                    if (isset($_POST['form_manager'][$val])) {
                        unset($_POST['form_manager'][$val]);
                    }
                }
            }

            $saveArr = $this->newFields;

            if (isset($this->options['defaultFields']) && is_array($this->options['defaultFields']) && count($this->options['defaultFields']) > 0) {
                foreach ($this->options['defaultFields'] as $name => $value) {
                    if (isset($_POST['form_manager'][$name])) {
                        if (is_array($value)) {
                            if (isset($value['value'])) {
                                if (is_array($value['value']) && count($value['value']) > 0) {
                                    foreach ($value['value'] as $k => $v) {
                                        if ($_POST['form_manager'][$name] == $v) {
                                            $saveArr[$name] = "$k";
                                        }
                                    }
                                } elseif ($_POST['form_manager'][$name] == $value) {
                                    $saveArr[$name] = "$value";
                                }
                            }
                        } else {
                            $saveArr[$name] = $_POST['form_manager'][$name];
                        }
                    }
                }
            }
        }
    }

    public function setOtherFileds($form) {
        $this->otherFields = $form;
    }

}

class FormManager extends FormManagerObject {

    protected $activeGroup = '';
    protected $fields = array();
    protected $fieldsTypes = array();
    protected $fieldsValues = array();
    protected $groupTypes = array();
    protected $isAtoUpdate = true;

    public function autoUpdate($isUpdate) {
        $this->isAtoUpdate = $isUpdate;
    }

    public function addGroup($title) {
        $this->fields[$title] = array();
        $this->activeGroup = $title;

        return $this;
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

    public function setFieldType($name, $type) {
        $this->fieldsTypes[$name] = $type;
        return $this;
    }

    public function setFieldValue($name, $value) {
        $this->fieldsValues[$name] = $value;
        return $this;
    }

    public function setGroupType($name, $type) {
        $this->groupTypes[$name] = $type;
        return $this;
    }

    public function addField($name, $title=null) {

        if (empty($this->activeGroup)) {
            $this->activeGroup = 'Новая группа';
        }

        if (is_array($name)) {

            if (!isset($name['type'])) {
                $name['type'] = 'varchar';
            }

            if (isset($name['gallery_type'])) {
                $this->galleryType = $name['gallery_type'];
            }

            if (isset($name['group'])) {
                $arrTmp = $name;
                unset($arrTmp['group']);
                $this->fields[$name['group']][] = $arrTmp;
            } else {
                $this->fields[$this->activeGroup][] = $name;
            }
        } else {
            $this->fields[$this->activeGroup][] = array('name' => $name, 'title' => $title, 'type' => 'varchar');
        }
        return $this;
    }

    public function show($isShowStartEnd = true) {

        $galleryManager1 = new GalleryManager($this->tpl, $this->db, $this->tableName, $this->id);
        $galleryManager1->setGalleryType($this->galleryType);

        $this->tpl->define_dynamic('from_manager', 'adm/form_manager.tpl');
        $this->tpl->define_dynamic('adm_js', 'from_manager');
        $this->tpl->define_dynamic('adm_start', 'from_manager');
        $this->tpl->define_dynamic('adm_end', 'from_manager');
        //$this->tpl->define_dynamic('adm_mce', 'from_manager');
        //$this->tpl->parse('ADM_MCE', 'null');
        $this->tpl->define_dynamic('adm_slider', 'from_manager');
        $this->tpl->define_dynamic('adm_href', 'from_manager');
        $this->tpl->define_dynamic('adm_varchar', 'from_manager');
        $this->tpl->define_dynamic('adm_yes_no', 'from_manager');
        
        $this->tpl->define_dynamic('adm_image', 'from_manager');
        $this->tpl->define_dynamic('adm_image_uploader', 'adm_image');



        if (!isset(CatalogFormOptions::$isUseFlashUploader) || !CatalogFormOptions::$isUseFlashUploader) {
            $this->tpl->parse('ADM_IMAGE_UPLOADER', 'null');
        }

        $tplContents = array();

        $dataDb = array();

        if ((is_numeric($this->id)) && !($dataDb = $this->db->fetchRow("SELECT * FROM `$this->tableName` WHERE `id`='$this->id'"))) {
            $data = array();
        } else {
            $data = $dataDb;
        }



        if (!empty($_POST['form_manager'])) {
            $data = $_POST['form_manager'];
        }


        $this->tpl->parse('CONTENT', '.adm_js');

        if ($isShowStartEnd) {
            $this->tpl->parse('CONTENT', '.adm_start');            
        } else {
            $this->tpl->parse('ADM_START', 'null');
        }
        $this->tpl->parse('CONTENT', '.adm_mce');

        if (count($this->fields) > 0) {
            foreach ($this->fields as $group => $fieldArray) {

                $this->tpl->assign('ADM_SLIDER_TITLE', $group);
                $tplContents[] = '.adm_slider';
                $this->tpl->parse('CONTENT', '.adm_slider');
                if (count($fieldArray) > 0) {

                    foreach ($fieldArray as $key => $field) {
                        $value = '';
                        if (isset($data[$field['name']])) {
                            $value = $data[$field['name']];
                        }

                        if (isset($this->fieldsValues[$field['name']])) {
                            $value = $this->fieldsValues[$field['name']];
                        }

                        if (isset($this->groupTypes[$group])) {
                            if (is_array($this->groupTypes[$group]) && isset($this->groupTypes[$group]['type'])) {

                                $field['type'] = $this->groupTypes[$group]['type'];
                            } else {
                                $field['type'] = $this->groupTypes[$group];
                            }
                        }

                        if (isset($this->fieldsTypes[$field['name']])) {
                            if (is_array($this->fieldsTypes[$field['name']]) && isset($this->fieldsTypes[$field['name']]['type'])) {
                                $field['type'] = $this->fieldsTypes[$field['name']]['type'];
                            } else {
                                $field['type'] = $this->fieldsTypes[$field['name']];
                            }
                        }

                        if (!isset($field['type'])) {
                            $field['type'] = 'varchar';

                            if (isset($field['value']) && is_array($field['value']) && count($field['value']) > 0) {
                                $field['type'] = 'yes-no';
                            }
                        }

                        $ynOption = '';
                        if ($field['type'] == 'yes-no') {

                            if (!isset($field['value']) || !is_array($field['value']) || count($field['value']) <= 0) {
                                $field['value'][] = 'Да';
                                $field['value'][] = 'Нет';
                            }

                            foreach ($field['value'] as $ynKey => $ynValue) {
                                $selected = '';

                                if (empty($value) && isset($field['default'])) {
                                    if ($field['default'] == $ynKey) {
                                        $selected = 'selected';
                                    }
                                } else {
                                    if ($value == $ynKey) {
                                        $selected = 'selected';
                                    }
                                }

                                $ynOption .= "<option name='$ynKey' $selected>$ynValue</option>\n";
                            }
                        }

                        if ($field['type'] == 'image') {


                            // $this->fieldsTypes[$field['name']]                            
                            $src = '/img/no-foto/no-foto-200x180.gif';
                            $dellButtonCssStatus = '';


                            if (isset($this->options['small1']['path']) && is_file($_SERVER['DOCUMENT_ROOT'] . $this->options['small1']['path'] . '/' . $value)) {
                                $src = $this->options['small1']['path'] . '/' . $value;
                                //   $dellButtonCssStatus = 'dell-button-hide';
                            } elseif (isset($_SESSION['catalog'][$this->id]['img']) && is_file($_SERVER['DOCUMENT_ROOT'] . $this->options['small1']['path'] . '/' . $_SESSION['catalog'][$this->id]['img'])) {
                                $src = $this->options['small1']['path'] . '/' . $_SESSION['catalog'][$this->id]['img'];
                            } else {
                                $dellButtonCssStatus = 'dell-button-hide';
                            }


                            $this->tpl->assign(array(
                                'ADM_FIELD_TABLE_NAME' => $this->tableName,
                                'ADM_FIELD_ID' => $this->id,
                                'ADM_FIELD_IMG_SRC' => $src,
                                'ADM_UPLOAD_IMG_PATH' => $this->options['small1']['path'],
                                'ADM_IMAGE_DELL_BUTTON_CSS' => $dellButtonCssStatus,
                                'SESSION_NAME' => session_name(),
                                'ADM_SID' => session_id()
                            ));
                        }

                        if ($field['type'] == 'gallery') { 

                            $galleryManager1->showAdmin($field['name'], $field['title']);
                        }


                        $this->tpl->assign(array(
                            'ADM_FIELD_TITLE' => $field['title'],
                            'ADM_FIELD_NAME' => $field['name'],
                            'ADM_YES_NO_TYPE_OPTION' => $ynOption,
                            'ADM_FIELD_VALUE' => $value
                        ));
                         //echo 'CONTENT', '.adm_' . str_replace('-', '_', $field['type']).'<br>' ;
                        $this->tpl->parse('CONTENT', '.adm_' . str_replace('-', '_', $field['type']));
                    }
                }
            }
        }



        $this->tpl->assign('ADM_OTHER_FIELD_FORM', $this->otherFields);
       
        if ($isShowStartEnd) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $this->referer = $_SERVER['HTTP_REFERER'];
            }
            $this->tpl->assign('REFERER', $this->referer);
            $this->tpl->parse('CONTENT', '.adm_end');
        } else {
            $this->tpl->parse('ADM_END', 'null');
        }
    }

}

class GalleryManager extends FormManagerObject {

    protected $tableOptions = array(
        // Таблица галереи. В конструкторе мы передаем таблицу основной формы.
        'table_name' => 'catalog_gallery',
        // Имена полей в таблице бд        
        'file_name' => 'pic',
        'file_alt' => 'alt',
        'file_title' => 'title',
        'parent_filed' => 'goods_artikul', // Поле, по которому галерея связывается с основной таблицей
    );
    private $gallertyType = 'gallery';
    private $catalogImagesOptions = array(
        'real' => array('path' => '/img/catalog/gallery/real/', 'stamp' => false),
        'small1' => array('path' => '/img/catalog/gallery/small_1/', 'size' => array('width' => 122, 'height' => 120), 'stamp' => false),
    );

    public function __construct($tpl, $db, $tableName = '', $id = '') {



        parent::__construct($tpl, $db, $tableName, $id);
        /*
          if (isset(CatalogGalleryOptions::$imgPath)) {
          $this->setImagePath(CatalogGalleryOptions::$imgPath);
          }
          if (isset(CatalogGalleryOptions::$imgSize)) {
          $this->options['imgSize'] = CatalogGalleryOptions::$imgSize;
          }
         */
        $this->tpl->define_dynamic('_gallery_manager_admin', 'adm/gallery_manager_admin.tpl');
        $this->tpl->define_dynamic('gallery_manager_admin_js', '_gallery_manager_admin');
        $this->tpl->define_dynamic('gallery_manager_admin', '_gallery_manager_admin');
        $this->tpl->define_dynamic('gallery_images_list_admin', 'gallery_manager_admin');
        $this->tpl->parse('GALLERY_IMAGES_LIST_ADMIN', 'null');

        $this->tpl->parse('CONTENT', '.gallery_manager_admin_js');
    }

    public function setGalleryType($type) {
        $this->gallertyType = $type;
    }

    protected function getCatalogArtikul() {
        return $this->db->fetchOne("SELECT `artikul` FROM $this->tableName WHERE `id`='$this->id'");
    }

    protected function getFileName($id, $isTestExists = true) {
//        if (($fileName = $this->db->fetchOne("SELECT `$this->options[file_name]` FROM `$this->options[file_name]` WHERE `id`='$id'")) !== false) {
//            if (!$isTestExists && !is_file($_SERVER['DOCUMENT_ROOT'].$this->options['path'].'/'.$fileName)) {
//                return true;
//            }
//        }
    }

    protected function getCatalogAltTitle($altField='pic_alt', $titleField='pic_title') {
        $ret = $this->db->fetchRow("SELECT `$altField`, `$titleField` FROM $this->tableName WHERE `id`='$this->id'");
        if (!$ret) {
            $ret[$altField] = '';
            $ret[$altField] = '';
        }
        return $ret;
    }

    public function showAdmin($htmlFormFieldName, $fieldTitle) {

        $src = '/img/no-foto/no-foto-200x180.gif';
        $dellButtonCssStatus = '';
        $ynOption = '';
        $value = '';
        $catalogAltTitle = $this->getCatalogAltTitle();

        $this->tpl->assign(array(
            'ADM_FIELD_TABLE_NAME' => $this->tableName,
            'ADM_FIELD_ID' => $this->id,
            'ADM_FIELD_IMG_SRC' => $src,
            'ADM_IMAGE_DELL_BUTTON_CSS' => $dellButtonCssStatus,
            'ADM_SID' => session_id(),
            'ADM_FIELD_TITLE' => $fieldTitle,
            'ADM_FIELD_NAME' => $htmlFormFieldName,
            'ADM_GALLERY_TYPE' => $this->gallertyType,
            'ADM_YES_NO_TYPE_OPTION' => $ynOption,
            'ADM_DEFAUULT_ALT_PICK' => (isset($catalogAltTitle['pic_alt']) ? $catalogAltTitle['pic_alt'] : ''),
            'ADM_DEFAUULT_TITLE_PICK' => (isset($catalogAltTitle['pic_title']) ? $catalogAltTitle['pic_title'] : ''),
            'ADM_FIELD_VALUE' => $value
        ));

        //SL-024  (FC 028)
        
        

        if (($artikul = $this->getCatalogArtikul()) && ($row = $this->db->fetchAll("SELECT * FROM `" . $this->tableOptions['table_name'] . "` WHERE `" . $this->tableOptions['parent_filed'] . "`='$artikul' AND `gallery_type`='$this->galleryType' ORDER BY `position`, `pic`"))) {
            
            foreach ($row as $res) {
                $src = '/img/no-foto/no-foto-122x120.gif';                
              
                if (isset($this->catalogImagesOptions['small1']['path']) && is_file($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small1']['path'] . $this->galleryType . '/' . $res['pic'])) {
                    $src = $this->catalogImagesOptions['small1']['path'] . $this->galleryType . '/' . $res['pic'];
                }

                $this->tpl->assign(array(
                    'ADM_GALLERY_IMG_SRC' => $src,
                    'ADM_GALLERY_IMG_TITLE' => $res['title'],
                    'ADM_GALLERY_IMG_ALT' => $res['alt'],
                    'ADM_GALLERY_IMAGE_ID' => $res['id'],
                    'ADM_GALLERY_IMG_NAME' => $res['pic']
                ));
                $this->tpl->parse('GALLERY_IMAGES_LIST_ADMIN', '.gallery_images_list_admin');
            }
        }

        $this->tpl->parse('CONTENT', '.gallery_manager_admin');
    }

}

?>
