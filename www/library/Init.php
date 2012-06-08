<?php

class Init {

    private $_config = null;
    private $_url = null;
    private $_basePath = null;
    private $_lang = null;
    private $_getParam = null;
    private $_site_db = null;
    private $dbProfiler = null;
    

    /**
     * Путь к папке с контроллерами если не указано в конфиге
     * @var string
     */
    private $_defaultControllerPath = '/app/controllers';

    /**
     * Имя каталога по умолчанию для контроллеров
     * @ var string
     */
    private $_defaulControllersDirName = 'default';

    /**
     * Имя контроллера по умолчанию
     * @ var string
     */
    private $_defaulControllerName = 'default';
    
    private $_pregUrlCharsets = '[^a-zA-Z0-9\?\&\=]';

    public function __construct($config) {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->toArray();
        } else {
            if (!is_array($config)) {
                $config = (array) $config;
            }

            $this->_config = $config;
        }

        $this->modRewrite();

        $runConfig = array(
            'url' => $this->_url,
            'basePath' => $this->_basePath,
            'lang' => $this->_lang,
            'getParam' => $this->_getParam
        );

        Zend_Registry::set('run', $runConfig);

        $this->dispatch();
    }

    /**
     * Ищет контроллер и возвращает объект контроллера
     * @return boolean
     */
    private function _getControllerPath() {

        $url = $this->_url;
        $activeModuleDirName = $_SERVER['DOCUMENT_ROOT'] . $this->_defaultControllerPath . '/'. $this->_defaulControllersDirName;
        $activeControllerFileName = ucwords($this->_defaulControllerName) . 'Controller.php';
        $activeController = ucwords($this->_defaulControllerName);
        $activeAction = 'index';

        // Проверка на наличие каталога модуля                        

        if (isset($url[0]) && is_dir($_SERVER['DOCUMENT_ROOT'] . $this->_defaultControllerPath . '/' . $url[0])) {
            $activeModuleDirName = $_SERVER['DOCUMENT_ROOT'] . $this->_defaultControllerPath . '/' . $url[0];
            array_shift($url);
        } elseif (is_dir($_SERVER['DOCUMENT_ROOT'] . $this->_defaultControllerPath . '/' . $activeModuleDirName)) {
            array_shift($url);
        } 
        
        // Проверяем наличие файла контроллера            

        if (isset($url[0]) && is_file($activeModuleDirName . '/' . ucwords($url[0]) . 'Controller.php')) {
            $activeController = ucwords($url[0]) . 'Controller';
            $activeControllerFileName = ucwords($url[0]) . 'Controller.php';
            array_shift($url);
        } elseif (is_file($activeModuleDirName . '/' . ucwords($activeController) . 'Controller.php')) {
            $activeControllerFileName = $activeController . 'Controller.php';
            $activeController .= 'Controller';
        } 

        if (is_file($activeModuleDirName . '/Controller.php')) {
            require_once $activeModuleDirName . '/Controller.php';
        } else {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/library/Controller.php';
        }
        
        require_once $activeModuleDirName . '/' . $activeControllerFileName;

        $controller1 = new $activeController($this->_config);

        
        if (isset($url[0])) {
            $activeAction = $url[0];
            array_shift($url);
        }


        if (!$controller1->factory()) {
            $this->redirect('404');
        }

        if ($activeAction != 'index' && method_exists($controller1, 'beforeAction')) {
            $controller1->beforeAction(true);
        }


        $actionFunctionName = $activeAction . 'Action';

        if (method_exists($controller1, $actionFunctionName)) {

            if (!$controller1->$actionFunctionName($url)) {
                $this->redirect('404');
            }
        } else {
            
            if (method_exists($controller1, 'main')) {
                if (!$controller1->main()) {
                    $this->redirect('404');
                }
            }
            return false;
        }

        $controller1->finalise();
        $this->dbProfiler = $controller1->getProfiler();
        $this->_site_db = $controller1->getDb();
        return true;


        return false;
    }

    private function dispatch() {

       // if (!$this->_getControllerPath()) {
            $dir = PATH . 'library/';
            $fileName = $dir . 'Content' . '.php';
            $className = 'Content';
            $action = $this->_url[0];

            if (file_exists($dir . ucfirst(strtolower($this->_url[0])) . '.php')) {
                $fileName = $dir . ucfirst(strtolower($this->_url[0])) . '.php';
                $className = ucfirst(strtolower($this->_url[0]));

                $action = (isset($this->_url[1]) && !empty($this->_url[1])) ? $this->_url[1] : 'main';
            } else {
                if (!file_exists($fileName)) {
                    throw new Exception('Base Class not found');
                }
            }
      
            require_once $fileName;

            $controller = new $className($this->_config);
           
            if (!$controller->factory()) {
                $this->redirect('404');
            }


            if ($action != 'index' && method_exists($controller, 'beforeAction')) {
                $controller->beforeAction(true);
            }
           
            if (method_exists($controller, $action)) {

                if (!$controller->$action()) {
                    $this->redirect('404');
                }
            } else {
                if (!$controller->main()) {
                    $this->redirect('404');
                }
            }

            $controller->finalise();

            $this->dbProfiler = $controller->getProfiler();
            $this->_site_db = $controller->getDb();
       // }
    }

    private function modRewrite() {
        $request = substr($_SERVER['REQUEST_URI'], 1);
        
        $request = preg_replace($this->_pregUrlCharsets, '', $request);

        $getParam = array();

        if (!empty($request)) {
            $request = explode('?', $request);

            if (isset($request[1]) && !empty($request[1])) {
                $getParam = $this->extractGetParam($request[1]);
            }

            $request = explode('/', urldecode($request[0]));

            if (end($request) === '') {
                array_pop($request);
            }
        } else {
            $request[] = 'index';
        }

        $request = $this->checkLang($request);

        if ($request[0] == '404') {
            $request[0] = 'error404';
        }

        $this->_url = $request;
        $this->_getParam = $getParam;
    }

    private function extractGetParam($str = null) {
        if (null === $str) {
            return array();
        }

        $returnArray = array();

        $strArray = explode('&', $str);

        foreach ($strArray as $param) {
            $get = explode('=', $param);

            if (isset($get[1]) && !empty($get[1])) {
                $returnArray[$get[0]] = urldecode($get[1]);
            }
        }

        return $returnArray;
    }

    private function checkLang($url = null) {
        if (null === $url) {
            return '';
        }

        $basePath = '/';
        $lang = $this->_config['language']['defaultLanguage'];

        if (array_key_exists($url[0], $this->_config['language']['allowLanguage'])) {
            $lang = $this->_config['language']['allowLanguage'][$lang];

            if ($lang != $this->_config['language']['defaultLanguage']) {
                $basePath .= $lang . '/';
            } else {
                $basePath .= ($this->_config['language']['useDefLangPath'] ? $lang . '/' : '');
            }

            $url = array_shift($url);
        }

        $this->_basePath = $basePath;
        $this->_lang = $lang;

        return $url;
    }

    private function redirect($url = null) {
        
        if (null === $url) {
            throw new Exception('Error redirect function!');
        }
        
        if (!headers_sent()) {
            header("location: " . $this->_basePath . $url);
        } else {
            

            throw new Exception('Unexpected Error');
        }
    }

    public function getProfiler() {
        return $this->dbProfiler;
    }

    public function getDb() {
        return $this->_site_db;
    }

}