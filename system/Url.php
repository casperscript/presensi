<?php

/**
 * frameduzPHP v4
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 4.1.0
 * @Date		: 10 April 2017
 * @package 	: core system
 * @Description : 
 */

namespace system;

class Url {

    public function __construct() {
        $selfArr = explode('/', rtrim($_SERVER['PHP_SELF'], '/'));
        $selfKey = array_search(INDEX_FILE, $selfArr);
        $this->baseUrl = $this->isHttps() ? 'https://' : 'http://';
        $this->baseUrl .= $_SERVER['HTTP_HOST'] . implode('/', array_slice($selfArr, 0, $selfKey)) . '/';
        $this->activeUrl = $this->isHttps() ? 'https://' : 'http://';
        $this->activeUrl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $this->mainConfig = Config::Load('main');
        $this->defaultProject = $this->mainConfig['defaultController']['project'];
        $this->defaultTemplate = $this->mainConfig['defaultTemplate']['template'];
        $defaultPathController = $this->mainConfig['project'][$this->defaultProject]['path'];
        $defaultController = $this->mainConfig['project'][$this->defaultProject]['controller'];
        $defaultMethod = $this->mainConfig['project'][$this->defaultProject]['method'];

        $this->ProjectName = $this->defaultProject;
        $this->PathController = $defaultPathController;
        $this->Controller = $defaultController;
        $this->Method = $defaultMethod;
        $this->ID = null;

        if (!empty($_GET['p1'])) {
            $this->ProjectName = $_GET['p1'];
            if (array_key_exists($this->ProjectName, $this->mainConfig['project'])) { // Jika sebuah project
                $this->PathController = $this->mainConfig['project'][$this->ProjectName]['path'];
                $this->Controller = $this->mainConfig['project'][$this->ProjectName]['controller'];
                $this->Method = $this->mainConfig['project'][$this->ProjectName]['method'];
                $this->ID = null;
                if (!empty($_GET['p2'])) :
                    $this->Controller = $_GET['p2'];
                endif;
                if (!empty($_GET['p3'])) :
                    $this->Method = $_GET['p3'];
                endif;
                if (!empty($_GET['p4'])) :
                    $this->ID = $_GET['p4'];
                endif;
            } else {
                $this->ProjectName = $this->defaultProject;
                $this->PathController = $defaultPathController;
                $this->Controller = $_GET['p1'];
                if (!empty($_GET['p2'])) :
                    $this->Method = $_GET['p2'];
                endif;
                if (!empty($_GET['p3'])) :
                    $this->ID = $_GET['p3'];
                endif;
            }
        }
    }

    public function isHttps() {
        if (isset($_SERVER['HTTPS'])) {
            if (strtolower($_SERVER['HTTPS']) == 'on'):
                return true;
            endif;

            if ($_SERVER['HTTPS'] == '1'):
                return true;
            endif;
        } elseif (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')) {
            return true;
        }
        return false;
    }

    public function getPathController() {
        return $this->PathController;
    }

    public function setPathController($project) {
        $this->PathController = $this->mainConfig['project'][$project]['path'];
    }

    public function getController() {
        return $this->Controller;
    }

    public function setController($Controller) {
        $this->Controller = $Controller;
    }

    public function getMethod() {
        return $this->Method;
    }

    public function setMethod($Method) {
        $this->Method = $Method;
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
    }

}
