<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Abstract.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Interface.php';
if (!defined('ABSTRACT_BASE_PHP'))
    die('err');

/**
 * Description of FormManagerActions
 *
 * @author kot
 */
class FormManagerActions extends Main_Abstract implements Main_Interface {

    protected $options = array(
        'tmpFilesTimeLimit' => 120,
        'tmpFilesLimit' => 21
    );
    private $catalogImagesOptions = array(
        'real' => array('path' => '/img/catalog/real/', 'size' => array('width' => 600, 'height' => 600), 'stamp' => '/img/watermarks/kovea_600x600.png'),
        'big' => array('path' => '/img/catalog/big/', 'size' => array('width' => 370, 'height' => 370), 'stamp' => '/img/watermarks/kovea_420x420.png'),
        'small1' => array('path' => '/img/catalog/small_1/', 'size' => array('width' => 200, 'height' => 180), 'stamp' => false),
        'small2' => array('path' => '/img/catalog/small_2/', 'size' => array('width' => 122, 'height' => 120), 'stamp' => false),
    );
    
    protected $errorMessages = array(
        'ru' => array(
            'bigDirNoExists' => 'Не могу найти каталог для средних картинок Менеджера Форм ([__TM_PATH__])',
            'smallDirNoExists' => 'Не могу найти каталог для маленьких картинок Менеджера Форм ([__TM_PATH__])',
            'small1DirNoExists' => 'Не могу найти каталог для маленьких картинок Менеджера Форм ([__TM_PATH__])',
            'realDirNoExists' => 'Не могу найти каталог для больших картинок Менеджера Форм ([__TM_PATH__])',
            'tmpDirNoWriteable' => 'Каталог ([__TM_PATH__]) не открыт для записи',
            'noFieldNameInFilesArray' => 'Укажите ключевое поля для массива _FILES[???]',
            'noSetFieldNameInFilesArray' => 'Не могу найти ключ (__FILES_KEY__) в массиве _FILES[???]',
            'noPriveleges' => 'У Вас не хватает прав доступа',
            'сopyFalse' => 'Не удалось переместить файл во временный каталог ([__TMP_FILE_NAME__])',
            'noDigitParam' => 'Параметр должен быть числом',
            'dellTmpFileError' => 'Не могу удалить файл ([__TMP_FILE_NAME__])',
            'tmpFilesTimeLimit' => 'Укажтие в настройках лимит времени для хранения временных файлов ("tmpFilesTimeLimit"=>120). Значение должно быть в секундах',
            'tmpFilesLimit' => 'Укажтие в настройках лимит временных файлов ("tmpFilesLimit"=>2)',
            'toMatchTmpFiles' => 'Превышен лимит временных файлов'
        )
    );
    protected $aciveLangiage = 'ru';
    protected $isAjax = true;
    protected $tmpFileName = '';
    protected $allowUsers = array(
        'admin'
    );
    protected $dbRecord = false;
    protected $isAcl = false;
    protected $pic;
    

    public function factory() {
        $this->isAjax = !(isset($_FILES['pic_catalog_gallery']));
        if (isset($_SESSION['form_manager_options'])) {
            $this->catalogImagesOptions = $_SESSION['form_manager_options'];
        }
        $this->getDbRecord();
        $this->setOptions();
        return true;
    }

    public function setOptions($options = array()) {
        if (empty($options)) {
            if (isset(CatalogFormOptions::$imgPath) && is_array(CatalogFormOptions::$imgPath)) {
                $this->options['imgPath'] = CatalogFormOptions::$imgPath;
            }
            if (isset(CatalogFormOptions::$imgSize) && is_array(CatalogFormOptions::$imgSize)) {
                $this->options['imgSize'] = CatalogFormOptions::$imgSize;
            }
        } else {
            $this->options = array_merge($this->options, $options);
        }
    }

    protected function getDbRecord() {
        try {
            $id = $this->getId();
            $this->dbRecord = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id`='$id'");
            if (isset($this->dbRecord['pic'])) {
                $this->pic = $this->dbRecord['pic'];
            }
        } catch (Exception $e) {
            
        }
    }

    protected function slideFileName($fileName) {
        $retArr = array('name' => '', 'exec' => '');
        $ln1 = strrpos($fileName, '.');
        $fileExec = substr($fileName, $ln1, strlen($fileName));
        $fileExec = strtolower($fileExec);
        $fileName = substr($fileName, 0, $ln1);
        $fileName = $this->ru2Lat($fileName);
        $retArr['name'] = $fileName;
        $retArr['exec'] = $fileExec;
        return $retArr;
    }

    protected function testOptionsParams() {
        // Проверка временноко каталока
        if (!isset($this->options['tmpFilesLimit'])) {
            throw new Exception($this->errorMessages[$this->aciveLangiage]['tmpFilesLimit']);
        }

        if (!isset($this->options['tmpFilesTimeLimit'])) {
            throw new Exception($this->errorMessages[$this->aciveLangiage]['tmpFilesTimeLimit']);
        }

        // Проверка для средних картинок

        if (isset($this->catalogImagesOptions['big']['path'])) {

            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['path'], $this->errorMessages[$this->aciveLangiage]['bigDirNoExists']));
            }

            if (!is_writable($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['big'], $this->errorMessages[$this->aciveLangiage]['bigDirNoWriteable']));
            }
        }

        // Проверка для маленьких картинок

        if (isset($this->catalogImagesOptions['small1']['path'])) {

            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small1']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small1']['path'], $this->errorMessages[$this->aciveLangiage]['smallDirNoExists']));
            }

            if (!is_writable($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small1']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small1']['path'], $this->errorMessages[$this->aciveLangiage]['smallDirNoWriteable']));
            }
        }


        // Проверка для маленьких картинок

        if (isset($this->catalogImagesOptions['small2']['path'])) {

            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small2']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small2']['path'], $this->errorMessages[$this->aciveLangiage]['smallDirNoExists']));
            }

            if (!is_writable($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small2']['path'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['small2']['path'], $this->errorMessages[$this->aciveLangiage]['smallDirNoWriteable']));
            }
        }


        // Проверка для больших картинок
        
       

        if (isset($this->catalogImagesOptions['big']['real'])) {

            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['real'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['real'], $this->errorMessages[$this->aciveLangiage]['realDirNoExists']));
            }

            if (!is_writable($_SERVER['DOCUMENT_ROOT'] . $this->options['imgPath']['real'])) {
                throw new Exception(str_replace('[__TM_PATH__]', $_SERVER['DOCUMENT_ROOT'] . $this->catalogImagesOptions['big']['real'], $this->errorMessages[$this->aciveLangiage]['realDirNoWriteable']));
            }
        }
    }

    protected function testFileArray($field = null) {
        if ($field === null && isset($_POST['file_data_name'])) {
            $field = $_POST['file_data_name'];
        }

        if ($field === null) {
            throw new Exception($this->errorMessages[$this->aciveLangiage]['noFieldNameInFilesArray']);
        }

        if (!isset($_FILES[$field])) {
            throw new Exception($this->errorMessages[$this->aciveLangiage]['noFieldNameInFilesArray']);
        }
    }

    protected function isDirs() {
        if (!isset($this->options['tmp'])) {
            
        }
    }

    public function main() {
        return $this->error404();
    }

    protected function getId() {
        $id = end($this->url);
        if (!is_numeric($id)) {
            throw new Exception($this->errorMessages[$this->aciveLangiage]['noDigitParam']);
        }
        return $id;
    }

    protected function uploadCatPic($from, $to, $maxwidth, $maxheight, $quality = 80, $stampPath=null) {
        ini_set('max_execution_time', '120');

        // защита от Null-байт уязвимости PHP
        $from = preg_replace('/\0/uis', '', $from);
        $to = preg_replace('/\0/uis', '', $to);

        $stamp = null;

        if ($stampPath != null && is_file($stampPath)) {
            $stamp = imagecreatefrompng($stampPath);
        }


        // информация об изображении
        $imageinfo = @getimagesize($from);
        // если получить информацию не удалось - ошибка
        if (!$imageinfo) {
            $this->_err .= '<br />Ошибка получения информации об изображении';
            return false;
        }
        // получаем параметры изображения
        $width = $imageinfo[0];  // ширина
        $height = $imageinfo[1]; // высота
        $format = $imageinfo[2]; // ID формата (число)
        $mime = $imageinfo['mime']; // mime-тип
        // определяем формат и создаём изображения
        switch ($format) {
            case 2: $img = imagecreatefromjpeg($from);
                break; // jpg
            case 3: $img = imagecreatefrompng($from);
                break; // png
            case 1: $img = imagecreatefromgif($from);
                break; // gif
            default: $this->_err .= '<br />Неверный или недопустимый формат загружаемого файла!';
                return false;
                break;
        }
        // если создать изображение не удалось - ошибка
        if (!$img) {
            $this->_err .= '<br />Ошибка создания изображения!';
            return false;
        }

        // меняем размеры изображения
        $newwidth = $width;
        $newheight = $height;
        // требуется квадратная картинка
        if ($maxwidth == $maxheight) {
            // размеры картинки больше по X и по Y
            if ($width > $maxwidth && $height > $maxheight) {
                // пропорции картинки одинаковы
                if ($width == $height) {
                    $newwidth = $maxwidth;
                    $newheight = $maxheight;
                }
                // ширина больше
                elseif ($width > $height) {
                    $newwidth = $maxwidth;
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);
                }
                // высота больше
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
            }
            // в остальных случаях ничего менять не надо
            else {
                $newwidth = $width;
                $newheight = $height;
            }
        }
        // требуется горизонтальная картинка
        elseif ($maxwidth > $maxheight) {
            // размеры картинки больше по X и по Y
            if ($width > $maxwidth && $height > $maxheight) {
                // ширина больше
                if ($width > $height) {
                    $newwidth = $maxwidth;
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);

                    if ($newheight > $maxheight) {
                        $newheight = $maxheight;
                        $newwidth = intval(((float) $newheight / (float) $height) * $width);
                    }
                }
                // высота больше или равна ширине
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
            }
            // в остальных случаях ничего менять не надо
            else {
                //echo '1';
                $newwidth = $width;
                $newheight = $height;
            }
        }
        // требуется вертикальная картинка
        elseif ($maxwidth < $maxheight) {
            // размеры картинки больше по X и по Y
            if ($width > $maxwidth && $height > $maxheight) {
                // ширина больше или равна высоте
                if ($width >= $height) {
                    $newwidth = $maxwidth;
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);
                }
                // высота больше
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
            }
            // в остальных случаях ничего менять не надо
            else {
                $newwidth = $width;
                $newheight = $height;
            }
        }

        // если изменений над картинкой производить не надо - просто копируем её
        /* if ($newwidth == $width && $newheight == $height && $quality == 80) {
          echo '123';
          if (copy($from, $to)) return true;
          else {
          $this->_err .= '<br />Ошибка копирования файла!';
          return false;
          }
          } */

        // создаём новое изображение
        //$new = imagecreatetruecolor($newwidth, $newheight);
        $new = imagecreatetruecolor($maxwidth, $maxheight);
        $black = imagecolorallocate($new, 0, 0, 0);
        $white = imagecolorallocate($new, 255, 255, 255);
        // копируем старое в новое с учётом новых размеров
        imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
        //imagecolortransparent($new, $white);
        $center_w = round(($maxwidth - $newwidth) / 2);
        $center_w = ($center_w < 0) ? 0 : $center_w;
        $center_h = round(($maxheight - $newheight) / 2);
        $center_h = ($center_h < 0) ? 0 : $center_h;
       // $center_w = 0;
       // $center_h = 0;
        
        imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
        if ($stamp != null) {
            imagecopyresampled($new, $stamp, 0, 0, 0, 0, $maxwidth - 1, $maxheight - 1, $maxwidth - 1, $maxheight - 1);
            //imagecopymerge($new,$stamp, 0, 0, 0, 0, $width, $height, 20);
        }
        // создаём файл с новым изображением
        switch ($format) {
            case 2: // jpg
                if ($quality < 0)
                    $quality = 0;
                if ($quality > 100)
                    $quality = 100;
                imagejpeg($new, $to, $quality);
                break;
            case 3: // png
                $quality = intval($quality * 9 / 100);
                if ($quality < 0)
                    $quality = 0;
                if ($quality > 9)
                    $quality = 9;
                imagepng($new, $to, $quality);
                break;
            case 1: // gif
                imagegif($new, $to);
                break;
        }

        @chmod($to, 0644);

        return true;
    }

    protected function copyFile($value) {
 
      
        if (count($_FILES) <= 0) {
            return false;
        }
       
                        
        $fileValues = array_values($_FILES);
        $returnArray = array();
        $retArr[] = $value;
        if (is_array($fileValues) && count($fileValues) > 0) {
            
            foreach ($fileValues as $key => $fileData) {
              
                if (isset($fileData['size']) && $fileData['size'] > 0) {  
                    
                    if (isset($value['path'])) {
                        
                        $tmpFileName = $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'];
                        
                        $tmpFileName = str_replace('//', '/', $tmpFileName);

                        if (is_file($tmpFileName)) {
                            @chmod($tmpFileName, 0666);
                            @unlink($tmpFileName);
                        }

                        $stamp = null;

                        if (isset($value['stamp'])) {
                            $stamp = $_SERVER['DOCUMENT_ROOT'] . '/' . $value['stamp'];
                        }
                        
                        $stamp = str_replace('//', '/', $stamp);

                        if (isset($value['size']['width']) && isset($value['size']['height'])) {                             
                            $this->uploadCatPic($fileData['tmp_name'], $tmpFileName, $value['size']['width'], $value['size']['height'], 100, $stamp);
                        } elseif (isset($value['size']) && $value['size'] == 'copy') {
                            @copy($fileData['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name']);
                        }
                        
                        
                        $returnArray[] = $fileData['name'];
                        
                        
                    }
                }
            }
        }
     
          
        return $returnArray;
    }

    public function uploadimage() {
  
        try {
            $fileName = '';
            
            if (isset($this->catalogImagesOptions)) {
             
                foreach ($this->catalogImagesOptions as $key => $val) {
                    
                    
                    
                    if (($tmpFileName = $this->copyFile($val)) !== false) {
                        if ($key == 'small1') {
                            $_SESSION['form_manager'][$this->getId()]['img'] = $tmpFileName[0];
                            $fileName = $val['path'].$tmpFileName[0];
                            
                        }
                    } 
                }
            }

             
            if ($this->isAjax) {
                die('mess-copy-tmp#~#@' . $fileName);
            }
        } catch (Exception $e) {
            if ($this->isAjax) {
                die('err#~#@' . $e->getMessage());
            } else {
                $this->setErr($e->getMessage());
            }
        }
        return $fileName;
    }

    protected function deleteImageInDb() {
       $_SESSION['form_manager'][$this->getId()]['img'] = '';
    }

    public function deleteImage() {

        try {
            $id = $this->getId();
            $path = '';

            if (isset($this->catalogImagesOptions)) {
                $this->deleteImageInDb();

                foreach ($this->catalogImagesOptions as $key => $val) {

                    if (is_file($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $this->pic)) {
                        @chmod($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $this->pic, 0666);
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $this->pic);
                    } elseif (isset($_SESSION['catalog'][$id]['img']) && is_file($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $_SESSION['catalog'][$id]['img'])) {
                        @chmod($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $_SESSION['catalog'][$id]['img'], 0666);
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $val['path'] . $_SESSION['catalog'][$id]['img']);
                    }
                }
            }
            if ($this->isAjax) {
                die('/img/no-foto/no-foto-200x180.gif');
            }
        } catch (Exception $e) {
            if ($this->isAjax) {
                die($e->getMessage());
            } else {
                $this->addErr($e->getMessage());
            }
        }

        if ($this->isAjax) {
            die('Error');
        }
        return true;
    }
    
    public function __destruct() {
        if (isset($_SESSION['form_manager_options'])) {
            unset($_SESSION['form_manager_options']);
        }
    }

}

?>
