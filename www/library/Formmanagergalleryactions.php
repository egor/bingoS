<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Formmanageractions.php';

class FormManagerGalleryActions extends FormManagerActions {

    protected $galleryType = 'gallrey';
    protected $catalogImagesOptions = array(
        'real' => array('path' => '/img/catalog/gallery/real/', 'size' => array('width' => 600, 'height' => 600), 'stamp' => '/img/watermarks/watermark600x600.png'),
        'big' => array('path' => '/img/catalog/gallery/big/', 'size' => array('width' => 370, 'height' => 370), 'stamp' => '/img/watermarks/watermark370x370.png'),
        'small1' => array('path' => '/img/catalog/gallery/small_1/', 'size' => array('width' => 122, 'height' => 120), 'stamp' => false),
    );

    public function setOptions($options = array()) {
        if (empty($options)) {

        } else {
            $this->options = array_merge($this->options, $options);
        }
    }

    protected function deleteImageInDb() {
        try {
            $id = $this->getId();
            $this->pic = $this->db->fetchOne("SELECT `pic` FROM `catalog_gallery` WHERE `id`='$id'");
            $this->db->delete('catalog_gallery', "id='$id'");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function sort() {

        try {
            $id = $this->getId();
            $vals = $this->getVar('vals', false);

            if (is_numeric($id) && $vals) {

                // $vals = mysql_real_escape_string($vals);
                $vals = str_replace('li-', '', $vals);
                //$vals = str_replace(',', "','", $vals);
                $data = explode(',', $vals);
                $dataLength = count($data);
                for ($i = 0; $i < $dataLength; $i++) {
                    $this->db->update('catalog_gallery', array('position' => $i), "id='" . $data[$i] . "'");
                }
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function save() {

        try {
            $id = $this->getId();
            $alt = $this->getVar('alt', false);
            $title = $this->getVar('title', false);
            //$id = $this->getVar('id', false);

            if ($alt && $title && $id) {
                $this->db->update('catalog_gallery', array('alt' => $alt, 'title' => $title), "id=$id");
            }
            die('Ok');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function uploadimage() {
        $this->galleryType = $this->getVar('gallery_type', 'gallery');
        try {
            $fileName = '';

            if (isset($this->catalogImagesOptions)) {
                foreach ($this->catalogImagesOptions as $key => $val) {
                    if (isset($val['path'])) {
                        $val['path'] = $val['path'] . $this->galleryType;
                    }

                    if (($tmpFileName = $this->copyFile($val)) !== false) {
                        if ($key == 'small1') {
                            $fileName = $tmpFileName[0];
                            // $_SESSION['form_manager'][$this->getId()]['img'] = $fileName;
                        }
                    }
                }
            }

            if (isset($this->dbRecord['artikul'])) {

                $alt = '';
                $title = '';

                if (isset($this->dbRecord['pic_alt'])) {
                    $alt = $this->dbRecord['pic_alt'];
                }

                if (isset($this->dbRecord['pic_title'])) {
                    $alt = $this->dbRecord['pic_title'];
                }

                $this->galleryType = $this->getVar('gallery_type', 'gallery');

                $artikul = $this->dbRecord['artikul'];

                if (isset($this->dbRecord['type']) && $this->dbRecord['type'] == 'section') {

                    $sectionArtikul = $this->dbRecord['artikul'];

                    if (isset($this->dbRecord['level'])) {
                        if ($this->dbRecord['level'] != '0') {
                            $data = $this->dataTreeManager($this->dbRecord['id']);
                            $sectionArtikul = $data['sectionArtikul'];
                        } else {
                            $sectionArtikul = $this->dbRecord['artikul'];
                        }
                    }

                    $title = '';
                    $alt = '';

                    $artikul = 'new-page-' .$sectionArtikul ;
                }

                $this->db->insert('catalog_gallery', array('goods_artikul' => $artikul, 'gallery_type' => $this->galleryType, 'pic' => $fileName, 'title' => $title, 'alt' => $alt));



                if ($this->isAjax) {
                    die("path:" . $this->catalogImagesOptions['small1']['path'] . $this->galleryType . '/' . "$fileName;id:" . $this->db->lastInsertId());
                }
            }
        } catch (Exception $e) {
            if ($this->isAjax) {
                die('err#~#@' . $e->getMessage());
            } else {
                $this->setErr($e->getMessage());
            }
        }
    }

}

?>
