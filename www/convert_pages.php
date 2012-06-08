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

$pages_old = $databaseMotori->fetchAll("SELECT * FROM `page` ORDER BY `id`");

//print_r($pages_old);
//exit();

$ids = array();
$noIndex = array();

foreach ($pages_old as $row) {
    $level = 0;
    if ($row['level'] > 0) {
        if (isset ($ids[$row['level']]) && !empty ($ids[$row['level']])) {
            $level = (int) $ids[$row['level']];
        } else {
            $noIndex[] = array(
                'id'    => $row['id'],
                'href'  => $row['link'],
                'name'  => $row['name']
            );
            continue;
        }
    }

    $data = array(
        'href'          => $row['link'],
        'type'          => $row['type'],
        'menu'          => $row['menu'] == 'horizontal' ? 'horisontal' : $row['menu'],
        'position'      => $row['position'],
        'preview'       => $row['preview'],
        'body'          => $row['description'],
        'level'         => $level,
        'visibility'    => $row['visibility'],
        //'top'           => $row['top'],
        'title'         => $row['metatitle'],
        'header'        => $row['metah1'] == '' ? $row['name'] : $row['metah1'],
        'keywords'      => $row['metakeywords'],
        'description'   => $row['metadescription'],
        'pic'           => $row['pic'],
        'hpic'          => $row['hpic'],
        'tube'          => $row['tube'],
        'date'          => $row['date']
    );

    $databaseTemp->insert('page', $data);

    $ids[$row['id']] = $databaseTemp->lastInsertId();
}

if (!empty ($noIndex)) {
    print_r($noIndex);
}