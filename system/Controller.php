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

namespace system;

class Controller {

    public function __construct() {
        $this->getUrl = new Url();
        $this->files = new Files();
        $this->project = $this->getUrl->ProjectName;
        $this->session = $this->getUrl->mainConfig['project'][$this->project]['session'];
        $setting = $this->getUrl->mainConfig['setting'];
        if (!empty($setting)) {
            foreach ($setting as $set => $value) :
                $this->{$set} = $value;
            endforeach;
        }
    }

    public function runMethod($method, $idKey) {
        return $this->{$method}($idKey);
    }

    public function getView($project, $controller, $method, $idKey) {
        $PathController = 'app\\' . $project . '\\controller\\';
        $Controller = $controller;
        $ctrl = $PathController . $Controller;
        $this->setSession('getView', array('PathController' => $project, 'Controller' => $Controller));
        if (class_exists($ctrl)) {
            if (method_exists($ctrl, $method)) {
                $ctrl = new $ctrl();
                $ctrl->runMethod($method, $idKey);
            } else {
                echo 'method not exist';
            }
        } else {
            echo 'controller not exist';
        }
    }

    protected function showView($fileView = '', $data = array(), $template = '', $validation = false) {
        $PathController = $this->getUrl->getPathController();
        $Controller = $this->getUrl->getController();
        $template = (empty($template)) ? $this->getUrl->mainConfig['template'][$this->getUrl->defaultTemplate]['basePath'] : $this->getUrl->mainConfig['template'][$template]['basePath'];
        $basePath = $this->getUrl->baseUrl . 'template/' . $template;
        extract($data, EXTR_SKIP);
        $viewPath = APP . $PathController . '/view/' . $Controller . '/' . $fileView . '.' . $Controller . '.php';
        require_once TEMPLATE . $template . 'index.php';
        unset($data, $template);
    }

    protected function subView($fileView = '', $data = array()) {
        $caller = $this->getSession('getView');
        $PathController = !empty($caller['PathController']) ? $caller['PathController'] : $this->getUrl->getPathController();
        $Controller = !empty($caller['Controller']) ? $caller['Controller'] : $this->getUrl->getController();
        extract($data, EXTR_SKIP);
        $viewPath = APP . $PathController . '/view/' . $Controller . '/' . $fileView . '.' . $Controller . '.php';
        require $viewPath;
        $this->delSession('getView');
        unset($data);
    }

    protected function getProject() {
        return ($this->getUrl->ProjectName == $this->getUrl->defaultProject) ? '' : $this->getUrl->ProjectName . '/';
    }

    protected function getController() {
        return $this->getUrl->getController();
    }

    protected function link($location = '') {
        return $this->getUrl->baseUrl . $location;
    }

    protected function activeLink($location = '') {
        return $this->getUrl->activeUrl . $location;
    }

    protected function redirect($location, $status = 302) {
        $location = (empty($location)) ? $this->link() : $location;
        if (substr($location, 0, 4) != 'http') :
            $location = $this->link() . $location;
        endif;
        header('Location: ' . $location, true, $status);
        exit;
    }

    protected function setSession($name, $data) {
        $_SESSION[$this->session][$name] = $data;
    }

    protected function setNewSession($projectName, $name, $data) {
        $_SESSION[$this->getUrl->mainConfig['project'][$projectName]['session']][$name] = $data;
    }

    protected function getSession($name) {
        return isset($_SESSION[$this->session][$name]) ? $_SESSION[$this->session][$name] : '';
    }

    protected function delSession($name) {
        if (isset($_SESSION[$this->session][$name])) :
            unset($_SESSION[$this->session][$name]);
        endif;
    }

    protected function desSession() {
        if (isset($_SESSION[$this->session])) :
            unset($_SESSION[$this->session]);
        endif;
    }

    protected function post($validation = false, $key = false, $filterType = false) {
        if ($validation === true) {
            if (isset($_SESSION[$this->session])) {
                if (!$key) :
                    return filter_var_array($_POST, FILTER_SANITIZE_MAGIC_QUOTES);
                endif;
                if ($filterType) :
                    return filter_input(INPUT_POST, $key, $filterType);
                else :
                    return $_POST[$key];
                endif;
            } else {
                return false;
            }
        } else {
            if (!$key) :
                return filter_var_array($_POST, FILTER_SANITIZE_MAGIC_QUOTES);
            endif;
            if ($filterType) :
                return filter_input(INPUT_POST, $key, $filterType);
            else :
                return $_POST[$key];
            endif;
        }
    }

}