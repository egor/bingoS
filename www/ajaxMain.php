<?php
ini_set('display_errors', 'on');

define("PATH", $_SERVER['DOCUMENT_ROOT'] . "/");

require_once PATH . 'Zend/Loader/Autoloader.php';
require_once PATH . 'Zend/Loader/Autoloader.php';
//require_once PATH . 'Zend/Json.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

Zend_Session::start();

$goodsSession = new Zend_Session_Namespace('goods');

require_once PATH . 'library/Templates.php';


$templates = new Templates('tpl/');
require_once PATH . 'config/config.php';
$config = new Zend_Config($config, true);

try {
   $db = Zend_Db::factory($config->database);
   $db->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
   echo $e->__toString();
  // throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
} catch (Zend_Exception $e) {
   echo $e->__toString();
   //throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
}

$zendSessionNameSpace = new Zend_Session_Namespace('Zend_Auth');
$auth = Zend_Auth::getInstance();

