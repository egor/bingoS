<?php

$isAjax = (isset($_POST['sid']) && !empty($_POST) && (
        (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Shockwave Flash' ) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
        )
        );
ini_set('display_errors', 'on');


if ($isAjax) {
   ini_set('session.auto_start', 'Off');
   //$_COOKIE['PHPSESSID'] = $_POST['sid'];
   setcookie('PHPSESSID', $_POST['sid']);
   @session_start();
}
//die('err: '.(defined('IM_PATH') ? IM_PATH : '0'));


if (!defined('ADMIN_PHP') && !isset($_POST['sid']))
   die();

if (!defined('IM_PATH')) {
   define('IM_PATH', (!$isAjax ? $_SERVER['DOCUMENT_ROOT'] : $_POST['Path']));
   //define('IM_PATH', $_SERVER['DOCUMENT_ROOT'] );
}

class ImageManager {

   protected $options = array();
   protected $tpl;
   protected $db;
   protected $_err = false;
   protected $path;

   public function __construct($name) {
     
      $this->options(
              array('name'=> $name.'_',
                  'tagFileName'=> $name.'im_file'
                  )
              );

      // var_dump(IM_PATH);
      $this->path = preg_replace('/\/+$/', '', IM_PATH);
   }

   public function options($key, $value=null) {

      if ($key == null) {
         return $this->options;
      }

      if ($value == null && is_string($key)) {
         if (is_string($key) && isset($this->options[$key])) {
            return $this->options[$key];
         }
      } else {

         if (is_array($key)) {

            if (!isset($key['tpl']) && !isset($key['db'])) {

               $this->options = array_merge($this->options, $key);
            } elseif (isset($key['tpl'])) {
               $this->tpl = $key['tpl'];
            } elseif (isset($key['db'])) {
               $this->tpl = $key['db'];
            }
         } else {
            if ($key != 'tpl' && $key != 'db') {
               $this->options[$key] = $value;
            } elseif ($key == 'tpl') {
               $this->tpl = $value;
            } elseif ($key == 'db') {
               $this->db = $value;
            }
         }

         return true;
      }

      return false;
   }

   protected function addErr($errMsg) {
      if (!$this->_err) {
         $this->_err = array($errMsg);
      } else {
         $this->_err = $errMsg;
      }
   }

   public function getError($isString=false) {
      if (!$this->_err) {
         return '';
      }
      if (!$isString) {
         return $this->_err;
      }

      return implode('<br />', $this->_err);
   }

   protected function getActiveFile() {

      if (!($activeFileName = $this->options('activeFileName'))) {
         $this->addErr('Не могу найти опцию [activeFileName]');
         return false;
      }

      if (!($noPhoto = $this->options('noPhoto'))) {
         $this->addErr('Не могу найти опцию [noPhoto]');
         return false;
      }
      $activeFileName = preg_replace('/^\/+/', '', $activeFileName);
      $activeFileName = '/' . $activeFileName;
      $noPhoto = preg_replace('/^\/+/', '', $noPhoto);
      $noPhoto = '/' . $noPhoto;
      if (!is_file($this->path . $noPhoto)) {
         $this->addErr('Не могу найти файл параметра [noPhoto=' . $noPhoto . ']');
      }

      if (!is_file($this->path . $activeFileName)) {
         return $noPhoto;
      } else {
         return $activeFileName;
      }
   }

   public function showForm($isRunFunction=false) {

      if (!$isRunFunction) {
         if (!($tplPath = $this->options('tplPath'))) {
            $tplPath = 'adm/image_manager.tpl';
            $this->options('tplPath', $tplPath);
         }

         $this->tpl->define_dynamic('_image_manager', $tplPath);
         $this->tpl->define_dynamic('image_manager', '_image_manager');
      }

      $this->tpl->define_dynamic('im_form', 'image_manager');
      $this->tpl->define_dynamic('im_alt_title', 'im_form');
      $this->tpl->define_dynamic('im_multi', 'im_form');     
      
      $this->tpl->parse('IM_FORM', 'null');            
      $this->tpl->parse('IM_MULTI', 'null');            
      
     
      if (($activeFile = $this->getActiveFile())) {
         $smallWidth = 180;
         $smallHeight = 200;


         if ($size = $this->options('size')) {
            if (isset($size['small']['width'])) {
               $smallWidth = $size['small']['width'];
            }
            if (isset($size['small']['height'])) {
               $smallHeight = $size['small']['height'];
            }
         }

         $smallPath = 'false';

         $bigWidth = 'false';
         $bigHeight = 'false';
         $bigPath = 'false';

         $realWidth = 'false';
         $realHeight = 'false';
         $realPath = 'false';




         if (isset($this->options['path']['small'])) {
            $smallPath = $this->options['path']['small'];
         }


         if (isset($this->options['size']['big'])) {
            if ($this->options['size']['big'] == 'copy') {
               $bigWidth = 'copy';
               $bigHeight = 'copy';
            } else {
               if (isset($this->options['size']['big']['width'])) {
                  $bigWidth = $this->options['size']['big']['width'];
               }

               if (isset($this->options['size']['big']['height'])) {
                  $bigHeight = $this->options['size']['big']['height'];
               }
            }
         }

         if (isset($this->options['path']['big'])) {
            $bigPath = $this->options['path']['big'];
         }


         if (isset($this->options['path']['big'])) {
            $bigPath = $this->options['path']['big'];
         }


         if (isset($this->options['size']['real'])) {
            if ($this->options['size']['real'] == 'copy') {
               $realWidth = 'copy';
               $realHeight = 'copy';
            } else {
               if (isset($this->options['size']['real']['width'])) {
                  $realWidth = $this->options['size']['real']['width'];
               }

               if (isset($this->options['size']['real']['height'])) {
                  $realHeight = $this->options['size']['real']['height'];
               }
            }
         }

         if (isset($this->options['path']['real'])) {
            $realPath = $this->options['path']['real'];
         }
         
         }
         
         $multiItems = '';
        
         if ($this->options('multi')) {
           $activeFile = $this->options('noPhoto');
            if (($tableName = $this->options('dbTableName')) && 
                    ($parentArticulFieldName = $this->options('parentArticulFieldName')) && ($parentArticul = $this->options('parentArticul'))) {
               
               if (!($dbAltPicFieldName = $this->options('dbAltPicFieldName'))) {
                  $dbAltPicFieldName = '';
               }
               
               if (!($dbTitlePicFieldName = $this->options('dbTitlePicFieldName'))) {
                  $dbTitlePicFieldName = '';
               }
                              
               if (($dbPicFieldName = $this->options('dbPicFieldName'))) {
                  
                  if (($row = $this->db->fetchAll("SELECT * FROM `$tableName` WHERE `$parentArticulFieldName` = '$parentArticul' ORDER BY `position`, `id`"))) {
                     $counter = 0;
                     $name = $this->options('name');
                     
                     
                     foreach ($row as $res) {
                        
                        if ($counter == 0) {
                           $activeFile = "$smallPath/$res[$dbPicFieldName]";
                           $this->options('id', $res['id'] );
                        } else {
                           $multiItems .= "<div style='float: left; margin-left: 10px;' id='_".$name."div-multi-$res[id]'>";
                           $multiItems .= "<img src='$smallPath/$res[$dbPicFieldName]' alt='$dbAltPicFieldName' title='$dbTitlePicFieldName' id='$res[id]' />";                        
                           $multiItems .= '<div id="_'.$name.'im-dell-img"><a href="#" id="'.$res['id'].'" class="_'.$name.'im-dell-img-button im-href"> <img src="/img/admin_icons/image_delete_16x16.gif" /> &nbsp; Удалить фото</a></div>';
                           $multiItems .= "</div>";
                        }
                        $counter++;
                     }
                  }
               }
            }
          $this->tpl->assign('ADN_IM_MULTY_ITEMS', $multiItems );
         } 
         
         
         
         $this->tpl->assign(
                 array(
                     'ADM_IM_NAME'=>'_'.$this->options('name'),                   
                     'ADM_IM_ID' => $this->options('id'),                     
                     'ADM_IM_MULTI' => ($this->options('multi') ? 'true' : 'false'),
                     'ADM_IMG_FILE_NAME' => $activeFile,
                     'ADM_IM_WIDTH' => $smallWidth,
                     'ADM_IM_HEIGHT' => $smallHeight,
                     'ADM_IM_IS_MULTY' => ($this->options('isMulty') ? 'true' : 'false'),
                     'ADM_IM_URL' => $this->options('url'),
                     'ADM_IM_SID' => session_id(),
                     'ADM_IM_SMALL_WIDTH' => $smallWidth,
                     'ADM_IM_SMALL_HEIGHT' => $smallHeight,
                     'ADM_IM_SMALL_PATH' => $smallPath,
                     'ADM_IM_BIG_WIDTH' => $bigWidth,
                     'ADM_IM_BIG_HEIGHT' => $bigHeight,
                     'ADM_IM_BIG_PATH' => $bigPath,
                     'ADM_IM_REAL_WIDTH' => $realWidth,
                     'ADM_IM_REAL_HEIGHT' => $realHeight,
                     'ADM_IM_REAL_PATH' => $realPath,
                     'ADM_IM_PATH' => $this->path,
                     'ADM_IM_PARENT_ARTICUL' => (!($parentArticul = $this->options('parentArticul')) ? '' : $parentArticul),
                     'ADM_IM_PARENT_ARTICUL_FIELD_NAME' => (!($parentArticulFieldName = $this->options('parentArticulFieldName')) ? '' : $parentArticulFieldName),
                     'ADM_IM_PARENT_GALLERY_TYPE' => (($galleryType = $this->options('galleryType')) ? $galleryType : ''),
                     
                     'ADM_IM_DB_TABLE_NAME' => $this->options('dbTableName'),
                     'ADM_IM_DB_PIC_FIELD_NAME' => $this->options('dbPicFieldName'),
                     'ADM_IM_DB_PIC_ALT_FIELD_NAME' => $this->options('dbAltPicFieldName'),
                     'ADM_IM_DB_PIC_TITLE_FIELD_NAME' => $this->options('dbTitlePicFieldName'),
                     
         ));
      

      if ($this->options('multi')) {
         $this->tpl->parse('IM_ALT_TITLE', 'null');  
         $this->tpl->parse('IM_MULTI', 'im_multi');  
      }
      
      $this->tpl->parse('IM_FORM', '.im_form');

      if (!$isRunFunction) {
         $this->tpl->parse('CONTENT', '.image_manager');
      }
   }

   public function getTpl() {
      return $this->tpl;
   }

   public function run() {

      if (!($tplPath = $this->options('tplPath'))) {
         $tplPath = 'adm/image_manager.tpl';
         $this->options('tplPath', $tplPath);
      }

      $this->tpl->define_dynamic('_image_manager', $tplPath);
      $this->tpl->define_dynamic('image_manager', '_image_manager');

      $this->showForm(true);

      $this->tpl->parse('CONTENT', '.image_manager');
   }

   public function upload() {
   
      if (!($tagFileName = $this->options('tagFileName'))) {
         $this->addErr('Укажите имя тега file');
         return false;
      }
      
      if (!isset($_FILES[$tagFileName])) {
         $this->addErr('Не могу найти значения тега file с именем [' . $tagFileName . ']');
         return false;
      }

      $isSmall = false;
      $smallWidth = 133;
      $smallHeight = 133;
      $smallPath = '';
      $isBig = false;
      $bigWidth = 420;
      $bigHeight = 420;
      $bigPath = '';
      $isReal = 'copy';
      $realWidth = 600;
      $realHeight = 600;
      $riealPath = '';

      if (isset($this->options['size']['small'])) {

         if ($this->options['size']['small'] == 'copy') {
            $isSmall = 'copy';
         }

         if ($isSmall != 'copy') {
            if (isset($this->options['size']['small']['width'])) {
               if (!is_numeric($this->options['size']['small']['width'])) {
                  $this->addErr('Параметр [size][small][width] должен быть числом > 0');
                  return false;
               }

               $smallWidth = intval($this->options['size']['small']['width']);
               if ($smallWidth <= 0) {
                  $this->addErr('Параметр [size][small][width] должен быть > 0');
                  return false;
               }
            }

            if (isset($this->options['size']['small']['height'])) {
               if (!is_numeric($this->options['size']['small']['height'])) {
                  $this->addErr('Параметр [size][small][height] должен быть числом > 0');
                  return false;
               }
               $smallHeight = intval($this->options['size']['small']['height']);
               if ($smallHeight <= 0) {
                  $this->addErr('Параметр [size][small][height] должен быть > 0');
                  return false;
               }
            }

            if (is_numeric($smallHeight) && is_numeric($smallWidth)) {
               $isSmall = true;
            }
         }
      }


      if (isset($this->options['size']['big'])) {
         if ($this->options['size']['big'] == 'copy') {
            $isBig = 'copy';
         }

         if ($isBig != 'copy') {
            if (isset($this->options['size']['big']['width'])) {
               if (!is_numeric($this->options['size']['big']['width'])) {
                  $this->addErr('Параметр [size][big][width] должен быть числом > 0');
                  return false;
               }
               $bigWidth = intval($this->options['size']['big']['width']);
               if ($bigWidth <= 0) {
                  $this->addErr('Параметр [size][big][width] должен быть > 0');
                  return false;
               }
            }

            if (isset($this->options['size']['big']['height'])) {
               if (!is_numeric($this->options['size']['big']['height'])) {
                  $this->addErr('Параметр [size][big][height] должен быть числом > 0');
                  return false;
               }
               $bigHeight = intval($this->options['size']['big']['height']);
               if ($bigHeight <= 0) {
                  $this->addErr('Параметр [size][big][height] должен быть > 0');
                  return false;
               }
            }

            if (is_numeric($bigHeight) && is_numeric($bigWidth)) {
               $isBig = true;
            }
         }
      }
   
      if (isset($this->options['size']['real'])) {
         if ($this->options['size']['real'] == 'copy') {
            $isReal = 'copy';
         } elseif ($isReal !== false) {
            $isReal = true;
         }

         if ($isReal != 'copy') {
            if (isset($this->options['size']['real']['width'])) {
               if (!is_numeric($this->options['size']['real']['width'])) {
                  $this->addErr('Параметр [size][real][width] должен быть числом > 0');
                  return false;
               }
               $bigReal = intval($this->options['size']['real']['width']);
               if ($realWidth <= 0) {
                  $this->addErr('Параметр [size][real][width] должен быть > 0');
                  return false;
               }
            }

            if (isset($this->options['size']['real']['height'])) {
               if (!is_numeric($this->options['size']['real']['height'])) {
                  $this->addErr('Параметр [size][real][height] должен быть числом > 0');
                  return false;
               }
               $bigReal = intval($this->options['size']['real']['height']);
               if ($bigReal <= 0) {
                  $this->addErr('Параметр [size][real][height] должен быть > 0');
                  return false;
               }
            }

            if (is_numeric($realHeight) && is_numeric($realWidth)) {
               $isReal = true;
            }
         }
      }

      if (!isset($_FILES[$tagFileName]['size']) || $_FILES[$tagFileName]['size'] <= 0) {
         $this->addErr('Размер файла не может быть 0.');
         return false;
      }


      if (isset($this->options['path']['small']) && $this->options['path']['small'] != 'false') {
         if (!file_exists($this->path . $this->options['path']['small'] . '/')) {
            $this->addErr('Не могу найти путь к каталогу [' . $this->path . $this->options['path']['small'] . ']');

            return false;
         }

         $smallPath = $this->options['path']['small'];
      } elseif ($isSmall && $this->options['path']['small'] != 'false') {
         $this->addErr('Укажите опцию $imageManager->options("path", array("small"=>"Путь к папке с большими картинками. (/img/catalog/small)"');
         return false;
      } elseif (isset($this->options['path']['small']) && $this->options['path']['small'] == 'false') {
         $isSmall = false;
      }

      if (isset($this->options['path']['big']) && $this->options['path']['big'] != 'false') {
         
            if (!file_exists($this->path . $this->options['path']['big'])) {
               $this->addErr('Не могу найти путь к каталогу [' . $this->path . $this->options['path']['big'] . ']');
               return false;
            }
         $bigPath = $this->options['path']['big'];
      } elseif ($isBig && $this->options['path']['big'] != 'false') {
         $this->addErr('Укажите опцию $imageManager->options("path", array("big"=>"Путь к папке с большими картинками. (/img/catalog/big)"');
         return false;
      } elseif (isset($this->options['path']['big']) && $this->options['path']['big'] == 'false') {
         $isBig = false;
      }


      if (isset($this->options['path']['real']) && $this->options['path']['real'] != 'false') {
         if (!file_exists($this->path . $this->options['path']['real'])) {
            $this->addErr('Не могу найти путь к каталогу [' . $this->path . $this->options['path']['real'] . ']');
            return false;
         }

         $realPath = $this->options['path']['real'];
      } elseif ($isReal && $this->options['path']['real'] != 'false') {
         $this->addErr('Укажите опцию $imageManager->options("path", array("real"=>"Путь к папке с большими картинками. (/img/catalog/real)"');
         return false;
      } elseif (isset($this->options['path']['real']) && $this->options['path']['real'] == 'false') {
         $isReal = false;
      }



      $from = $_FILES[$tagFileName]['tmp_name'];
      $name = $_FILES[$tagFileName]['name'];
      
      $this->options('fileName', $name);

      $ret = '';
      
   

      if ($isSmall !== false) {
         @chmod($this->path . '/' . $smallPath, 0777);
         if (is_file($this->path . '/' . $smallPath . '/' . $name)) {
            @chmod($this->path . '/' . $smallPath . '/' . $name, 0666);
            @unlink($this->path . '/' . $smallPath . '/' . $name);
         }

         if (is_string($isSmall) && $isSmall == 'copy') {
            @copy($from, $this->path . '/' . $smallPath . '/' . $name);
            $ret = 'path#~#' . $smallPath . '/' . $name;
           
            
         } else {
            if (!$this->uploadPic($from, $this->path . '/' . $smallPath . '/' . $name, $smallWidth, $smallHeight, 100, null, false)) {
               $this->addErr('Ошибка при создании маленького изображения');
               return false;
            } else {

               $ret = 'path#~#' . $smallPath . '/' . $name;
            }
         }
      }

      if ($isBig !== false) {
         @chmod($this->path . '/' . $bigPath, 0777);
         if (is_file($this->path . '/' . $bigPath . '/' . $name)) {
            @chmod($this->path . '/' . $bigPath . '/' . $name, 0666);
            @unlink($this->path . '/' . $bigPath . '/' . $name);
         }
         if ($isBig == 'copy') {
            @copy($from, $this->path . '/' . $bigPath . '/' . $name);
         } else {
            if (!$this->uploadPic($from, $this->path . '/' . $bigPath . '/' . $name, $bigWidth, $bigHeight, 100)) {
               $this->addErr('Ошибка при создании большого изображения');
               return false;
            }
         }
      }

   
    
      if ($isReal !== false) {
         @chmod($this->path . '/' . $realPath, 0777);
         if (is_file($this->path . '/' . $realPath . '/' . $name)) {
            @chmod($this->path . '/' . $realPath . '/' . $name, 0666);
            @unlink($this->path . '/' . $realPath . '/' . $name);
         }
         
         
         
         if (is_string($isReal) && $isReal == 'copy') {
            @copy($from, $this->path . '/' . $realPath . '/' . $name);
         } else {
            
            if (!$this->uploadPic($from, $this->path . '/' . $realPath . '/' . $name, $realWidth, $realHeight, 100)) {
               $this->addErr('Ошибка при создании изображения реального размера');
               return false;
            }
         }
      }

      return $ret;

      //$to = 
   }

   public function uploadPic($from, $to, $maxwidth, $maxheight, $quality = 100, $stampPath=null, $isCenter=true) {


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
         $this->addErr('Ошибка получения информации об изображении');
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
         default: $this->addErr('Неверный или недопустимый формат загружаемого файла!');
            return false;
            break;
      }
      // если создать изображение не удалось - ошибка
      if (!$img) {
         $this->addErr('Ошибка создания изображения!');
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
      $center_h = 0;
         $center_w = 0;
      if ($isCenter) {
         $center_w = round(($maxwidth - $newwidth) / 2);
         $center_w = ($center_w < 0) ? 0 : $center_w;
         $center_h = round(($maxheight - $newheight) / 2);
         $center_h = ($center_h < 0) ? 0 : $center_h;
      }
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

}


if ($isAjax) {

   $imageManager1 = new ImageManager($_POST['name']);
   $size = array('small' => 'copy',
       'big' => 'copy',
       'real' => 'copy'
   );

   $path = array(
       'small' => '',
       'big' => '',
       'real' => ''
   );

   if (isset($_POST['smallWidth'])) {
      $size['small'] = array('width' => $_POST['smallWidth']);
   }

   if (isset($_POST['smallHeight'])) {
      $size['small']['height'] = $_POST['smallHeight'];
   }
   
   

   if (isset($_POST['smallPath'])) {
      $path['small'] = $_POST['smallPath'];
   }

   if (isset($_POST['bigWidth'])) {
      $size['big'] = array('width' => $_POST['bigWidth']);
   }

   if (isset($_POST['bigHeight'])) {
      $size['big']['height'] = $_POST['bigHeight'];
   }

   if (isset($_POST['bigPath'])) {
      $path['big'] = $_POST['bigPath'];
   }

   if (isset($_POST['realWidth'])) {
      $size['real'] = array('width' => $_POST['realWidth']);
   }

   if (isset($_POST['realHeight'])) {
      $size['real']['height'] = $_POST['realHeight'];
      
   }

   if (isset($_POST['realPath'])) {
      $path['real'] = $_POST['realPath'];
   }
   
   
   $imageManager1->options('size', $size);
   $imageManager1->options('path', $path);

   if (isset($_POST['action'])) {
      
      set_include_path(implode(PATH_SEPARATOR, array(
            IM_PATH  ,     
            get_include_path(),
        )));

      
      require_once IM_PATH . '/Zend/Loader/Autoloader.php';
      $autoloader = Zend_Loader_Autoloader::getInstance();
     
      $autoloader->setFallbackAutoloader(true);
      
      
      require_once IM_PATH . '/config/config.php';
      $config = new Zend_Config($config, true);
      try {
         $database = Zend_Db::factory($config->database);
         $database->getConnection();
      } catch (Zend_Db_Adapter_Exception $e) {
         throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
      } catch (Zend_Exception $e) {
         throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
      }

      $isMulti = (isset($_POST['isMulti']) && $_POST['isMulti'] == 'multi');
      


      if ($_POST['action'] == 'upload') {
        
         $pic = $imageManager1->upload();
         $err = $imageManager1->getError(true);
         
         
         if (!empty($err)) {
            print "err#~#$err";
         } else {
            
            
            
            if (isset($_POST['tName']) && isset($_POST['fPic']) && (isset($_POST['id']) || isset($_POST['parentArticul'])) ) {
               $tableName = mysql_escape_string($_POST['tName']);
               $fPic = mysql_escape_string($_POST['fPic']);
               
               if (isset($_POST['id'])) {
                  $id = mysql_escape_string($_POST['id']);            
               }
               
               
                 
               $data = array($fPic=>$imageManager1->options('fileName'));
               
              
               $galleryType = 'gallery';
               
               if (isset($_POST['galleryType'])) {
                  $galleryType = $_POST['galleryType'];
               }
               
               if ($isMulti) {
                 
              
                  
                  if (!isset($_POST['PAFieldName'])) {
                     print "err#~#Не могу найти параметр [parentArticulFieldName]";
                     die;
                  }
                  
                  $parentArticul = mysql_escape_string($_POST['parentArticul']);
                  $parentArticulFieldName = mysql_escape_string($_POST['PAFieldName']);
                  $data = array($fPic=>$imageManager1->options('fileName'));
                  $data['gallery_type'] = $galleryType;
                  $data[$parentArticulFieldName] = $parentArticul;
                 
                  $database->insert($tableName, $data);                  
                  $pic.= '#~#'.$database->lastInsertId();
                  
               } else {            
                  $database->update($tableName, array($fPic=>$imageManager1->options('fileName')),"id=$id");
               }
               
              
               
               print $pic;
               die;   
            }
         }
      }
    
      if ($_POST['action'] == 'deleteImage') {
         if (isset($_POST['tName']) && isset($_POST['fPic']) && isset($_POST['id'])  ) {
            $tableName = mysql_escape_string($_POST['tName']);

            $fPic = mysql_escape_string($_POST['fPic']);
            $id = mysql_escape_string($_POST['id']);
           
            if (is_numeric($id)) {               
               if (($fileName = $database->fetchOne("SELECT $fPic FROM $tableName WHERE `id` = '$id'"))) {                  
                  if (isset($_POST['smallPath']) && is_file(IM_PATH.'/'.$_POST['smallPath'].'/'.$fileName)) {
                     @chmod(IM_PATH.'/'.$_POST['smallPath'].'/'.$fileName, 0666);
                     @unlink(IM_PATH.'/'.$_POST['smallPath'].'/'.$fileName);
                  }
                  
                  if (isset($_POST['bigPath']) && is_file(IM_PATH.'/'.$_POST['bigPath'].'/'.$fileName)) {
                     @chmod(IM_PATH.'/'.$_POST['bigPath'].'/'.$fileName, 0666);
                     @unlink(IM_PATH.'/'.$_POST['bigPath'].'/'.$fileName);
                  }
                  
                  if (isset($_POST['realPath']) && is_file(IM_PATH.'/'.$_POST['realPath'].'/'.$fileName)) {
                     @chmod(IM_PATH.'/'.$_POST['realPath'].'/'.$fileName, 0666);
                     @unlink(IM_PATH.'/'.$_POST['realPath'].'/'.$fileName);
                  }
                  
                  if (!$isMulti) {
                     $database->query("UPDATE `$tableName` SET $fPic='' WHERE `id` = '$id'");
                     
                  } else {
                    
                     $database->query("DELETE FROM `$tableName` WHERE `id` = '$id'");
                  }
                  
               }
            }
         }

         print "path#~#/img/nophoto_s.jpg";
      }
   }




   die;
}
?>
