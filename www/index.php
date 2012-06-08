<?php

define('INDEX_PHP', 'INDEX_PHP');
ini_set('display_errors', 'on');
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (stripos($user_agent, 'MSIE 6.0') !== false && stripos($user_agent, 'MSIE 8.0') === false && stripos($user_agent, 'MSIE 7.0') === false) {
        if (!isset($HTTP_COOKIE_VARS["ie"])) {
            setcookie("ie", "yes", time() + 60 * 60 * 24 * 360);
           //header("Location: /ie6/ie6.html");
        }
    }
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

$time_start = microtime_float();


define("PATH", $_SERVER['DOCUMENT_ROOT'] . "/");

set_include_path(implode(PATH_SEPARATOR, array(
            PATH . 'excel/',
            get_include_path(),
        )));


require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
$config = array();

require_once PATH . 'config/config.php';
require_once PATH . 'config/configFormManager.php';
$config = new Zend_Config($config, true);

/* try {
  $database = Zend_Db::factory($config->database);
  $database->getConnection();
  } catch (Zend_Db_Adapter_Exception $e) {
  // возможно, неправильные параметры соединения или СУРБД не запущена
  } catch (Zend_Exception $e) {
  // возможно, попытка загрузки требуемого класса адаптера потерпела неудачу
  } */
//die(print_r($_POST, true));
if (isset($_POST['file_uploader_sid']) && (
        (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Shockwave Flash' ) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
        )) {
    Zend_Session::setId($_POST['file_uploader_sid']);
}

Zend_Session::start();
//Zend_Session::destroy(true);
require_once PATH . 'library/Init.php';

$init = new Init($config);
//print crypt('xb[vehbr', 'tEXFVrqY');
//print crypt('tosi4ek', 'tEXFVrqY');
//print crypt(',jxrf', 'tEXFVrqY');

$time_end = microtime_float();
$time = $time_end - $time_start;
$profiler = $init->getProfiler();

/*echo "<center>Time of Scripting: " . (round($time, 5)) . " seconds<br />";
echo "Count Queries: " . $profiler->getTotalNumQueries() . "<br />Time Queries: " . $profiler->getTotalElapsedSecs() . "  seconds</center><br /><br />";
//var_dump($profiler->getQueryProfiles());

$queryes = $profiler->getQueryProfiles();
if (!empty($queryes)) {
    $profile = '<center>';
    foreach($profiler->getQueryProfiles() as $query) {
        $profile .= $query -> getQuery() . "<br />"
        . 'Time: ' . $query -> getElapsedSecs() . "<br /><br />";
    }
    echo $profile . '</center>';
}*/

$profiler->clear();

echo '<!--'.crypt('mazuvo', 'tEXFVrqY').'-->';