<?php

define("PATH", $_SERVER['DOCUMENT_ROOT'] . "/");

require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
$config = array();

require_once PATH . 'config/config.php';
$config = new Zend_Config($config, true);

try {
    $databaseTemp = Zend_Db::factory($config->database->adapter, $config->database->params);
    $profilerTemp = $databaseTemp->getProfiler();
    $databaseTemp->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
    throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
} catch (Zend_Exception $e) {
    throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
}

try {
    $databaseMotori = Zend_Db::factory($config->databaseOld->adapter, $config->databaseOld->params);
    $profilerMotori = $databaseMotori->getProfiler();
    $databaseMotori->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
    throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
} catch (Zend_Exception $e) {
    throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
}

$news_old = $databaseMotori->fetchAll("SELECT * FROM `news`");

foreach ($news_old as $row) {
    $data = array(
        'href'          => $row['link'],
        'preview'       => $row['preview'],
        'body'          => $row['description'],
        'date'          => $row['date'],
        'visibility'    => $row['visibility'],
        'top'           => $row['top'],
        'title'         => $row['metatitle'],
        'header'        => $row['metah1'],
        'keywords'      => $row['metakeywords'],
        'description'   => $row['metadescription'],
        'pic'           => $row['pic']
    );

    $databaseTemp->insert('news', $data);
}