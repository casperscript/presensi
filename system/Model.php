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

use PDO,
    PDOException;

class Model {

    public function __construct() {
        $this->databaseConfig = Config::Load('database');
        $this->defaultValue = array();
        $this->con = 'null';
        $this->db = null;

        $this->getUrl = new Url();
        $this->project = $this->getUrl->ProjectName;
        $this->session = $this->getUrl->mainConfig['project'][$this->project]['session'];
        $this->cookie = $this->getUrl->mainConfig['project'][$this->project]['cookie'];
    }

    protected function setConnection($con) {
        $this->con = $con;
    }

    protected function setDefaultValue($val = array()) {
        $this->defaultValue = $val;
    }

    // modified by husnanw from private to protected
    protected function openConnection() {
        $dsn = $this->databaseConfig[$this->con]['driver'] .
                ':host=' . $this->databaseConfig[$this->con]['host'] .
                ';port=' . $this->databaseConfig[$this->con]['port'] .
                ';dbname=' . $this->databaseConfig[$this->con]['dbname'];
        $opt[PDO::ATTR_PERSISTENT] = $this->databaseConfig[$this->con]['persistent'];
        try {
            $this->db = new PDO($dsn, $this->databaseConfig[$this->con]['user'], $this->databaseConfig[$this->con]['password'], $opt);
        } catch (PDOexception $err) {
            echo $this->databaseConfig[$this->con]['errorMsg'];
            //echo $err->getMessage();
            die;
        }
    }

    private function closeConnection() {
        $this->db = null;
    }

    private function sql_debug($sql_string, array $params = null) {
        if (!empty($params)) {
            $indexed = $params == array_values($params);
            foreach ($params as $k => $v) {
                if (is_object($v)) {
                    if ($v instanceof \DateTime) :
                        $v = $v->format('Y-m-d H:i:s');
                    else :
                        continue;
                    endif;
                } else if (is_string($v)) {
                    $v = "'$v'";
                } else if ($v === null) {
                    $v = 'NULL';
                } else if (is_array($v)) {
                    $v = implode(',', $v);
                }

                if ($indexed) {
                    $sql_string = preg_replace('/\?/', $v, $sql_string, 1);
                } else {
                    if ($k[0] != ':') :
                        $k = ':' . $k;
                    endif;
                    $sql_string = str_replace($k, $v, $sql_string);
                }
            }
        }
        return $sql_string;
    }

    public function getTabel($tabel) {
        $result = $this->getData('SHOW COLUMNS FROM ' . $tabel);
        $defaultValue = $this->defaultValue;
        $dataTabel = array();
        foreach ($result['value'] as $kol) {
            $dataTabel[$kol['Field']] = '';
        }
        foreach ($dataTabel as $key => $value) {
            if (isset($defaultValue[$key])) :
                $dataTabel[$key] = $defaultValue[$key];
            endif;
        }
        return $dataTabel;
    }

    public function getData($query, $arrData = array()) {
        if (is_null($this->db)) :
            $this->openConnection();
        endif;
        $sql_stat = $this->db->prepare($query);
        $sql_stat->execute($arrData);
        $sql_value = $sql_stat->fetchAll(PDO::FETCH_ASSOC);
        $sql_count = $sql_stat->rowCount();
        $sql_query = $this->sql_debug($query, $arrData);
        $this->closeConnection();
        return array(
            'value' => $sql_value,
            'count' => $sql_count,
            'query' => $sql_query . ';',
        );
    }

    public function save($tabel, $arrData) {
        if (is_null($this->db)) {
            $this->openConnection();
        }

        foreach ($arrData as $key => $value) {
            $keys[] = ':' . $key;
        }

        $valTable = implode(', ', $keys);
        $query = 'INSERT INTO ' . $tabel . ' VALUES (' . $valTable . ')';
        $error = 0;
        $message = '';
        $inserted_id = 0; //added by daniek
        try {
            $sql_stat = $this->db->prepare($query);
            $error = $sql_stat->execute($arrData);
            $message = $sql_stat->errorInfo()[2];
            $inserted_id = $this->db->lastInsertId(); //added by daniek
        } catch (Exception $err) {
            
        }
        $this->closeConnection();
        $sql_query = $this->sql_debug($query, $arrData);
        return array(
            'error' => $error,
            'message' => $message,
            'query' => $sql_query . ';',
            'inserted_id' => $inserted_id  //added by daniek
        );
    }

    public function save_update($tabel, $arrData) {
        if (is_null($this->db)) :
            $this->openConnection();
        endif;
        foreach ($arrData as $key => $value) :
            $keys[] = $key . '= :' . $key;
        endforeach;
        $valTable = implode(', ', $keys);
        $query = 'INSERT INTO ' . $tabel . ' SET ' . $valTable . ' ON DUPLICATE KEY UPDATE ' . $valTable;
        $error = 0;
        $message = '';
        try {
            $sql_stat = $this->db->prepare($query);
            $error = $sql_stat->execute($arrData);
            $message = $sql_stat->errorInfo()[2];
        } catch (Exception $err) {
            
        }
        $this->closeConnection();
        $sql_query = $this->sql_debug($query, $arrData);
        return array(
            'error' => $error,
            'message' => $message,
            'query' => $sql_query . ';',
        );
    }

    public function update($tabel, $arrData, $idKey) {
        if (is_null($this->db)) :
            $this->openConnection();
        endif;
        foreach ($arrData as $key => $value) :
            $keys1[] = $key . ' = :' . $key;
        endforeach;
        $valTable = implode(', ', $keys1);
        foreach ($idKey as $key => $value) :
            $keys2[] = '(' . $key . '= :' . $key . ')';
        endforeach;
        $keyTable = implode(' AND ', $keys2);
        $query = 'UPDATE ' . $tabel . ' SET ' . $valTable . ' WHERE ' . $keyTable;
        $error = 0;
        $message = '';
        try {
            $sql_stat = $this->db->prepare($query);
            $error = $sql_stat->execute(array_merge($arrData, $idKey));
            $message = $sql_stat->errorInfo()[2];
        } catch (Exception $err) {
            
        }
        $this->closeConnection();
        $sql_query = $this->sql_debug($query, array_merge($arrData, $idKey));
        return array(
            'error' => $error,
            'message' => $message,
            'query' => $sql_query . ';',
        );
    }

    public function delete($tabel, $idKey) {
        if (is_null($this->db)) :
            $this->openConnection();
        endif;
        foreach ($idKey as $key => $value) :
            $keys[] = '(' . $key . '= :' . $key . ')';
        endforeach;
        $keyTable = implode(' AND ', $keys);
        $query = 'DELETE FROM ' . $tabel . ' WHERE ' . $keyTable;
        $error = 0;
        $message = '';
        try {
            $sql_stat = $this->db->prepare($query);
            $error = $sql_stat->execute($idKey);
            $message = $sql_stat->errorInfo()[2];
        } catch (Exception $err) {
            
        }
        $this->closeConnection();
        $sql_query = $this->sql_debug($query, $idKey);
        return array(
            'error' => $error,
            'message' => $message,
            'query' => $sql_query . ';',
        );
    }

    protected function setSession($name, $data) {
        $_SESSION[$this->session][$name] = $data;
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

}