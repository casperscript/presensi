<?php
/**
 * frameduzPHP v4
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 4.1.0
 * @Date	: 10 April 2017
 * @package 	: core system
 * @Description : 
 */
class frameduzPHP {

    private $getUrl;
    private $logs;

    public function __construct($logs = false) {
        session_start();
        $this->logs = $logs;
        spl_autoload_register(array($this, 'loader'));
        $this->disableMagicQuotes();
        $this->getUrl = new system\Url;
        $this->runController();
    }

    private function loader($file) {
        if ($this->logs) :
            echo 'LOG[\'autoload\'] : ' . $file . ' --> Time : ' . round((microtime(true) - TIME_LOAD), 3) . ' sec<br>';
        endif;
        if (file_exists($file = ROOT . str_ireplace('\\', '/', $file) . '.php')) :
            require_once $file;
        endif;
    }

    private function disableMagicQuotes() {
        if (get_magic_quotes_gpc()) {
            array_walk_recursive($_GET, array($this, 'stripslashesGPC'));
            array_walk_recursive($_POST, array($this, 'stripslashesGPC'));
            array_walk_recursive($_COOKIE, array($this, 'stripslashesGPC'));
            array_walk_recursive($_REQUEST, array($this, 'stripslashesGPC'));
        }
    }

    private function stripslashesGPC() {
        $value = stripslashes($value);
    }

    private function runController() {
        $PathController = $this->getUrl->getPathController();
        $PathController = 'app\\' . $PathController . '\\controller\\';
        $template = $this->getUrl->defaultTemplate;
        $controller = $this->getUrl->getController();
        $method = $this->getUrl->getMethod();
        $idKey = $this->getUrl->getID();
        $ctrl = $PathController . $controller;

        if (class_exists($ctrl)) {
            if (method_exists($ctrl, $method)) {
                $ctrl = new $ctrl();
                $ctrl->runMethod($method, $idKey);
            } else {
                http_response_code(403);
                $error = '<a href="javascript:window.history.back();">{ Method not found }</a>';
                include_once('error/page/method.php');
            }
        } else {
            http_response_code(403);
            $error = '<a href="javascript:window.history.back();">{ Controller not found }</a>';
            include_once('error/page/controller.php');
        }
    }

}

new frameduzPHP;
