<?php

namespace Norm;

class Configuration
{

    protected static $_instance;
    protected $_model = null;
    protected $_cache = null;
    protected $_configConnections = array();
    protected $_connections = array();
    protected $_database = null;


    public static function getInstance()
    {

        if (isset(self::$_instance) === false) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }


    protected function __construct() { }


    public function setDatabase(Adapter\Database $database)
    {

        $this->_database = $database;

    }


    public function getDatabase()
    {

        if ($this->_database === null) {
            $this->_database = new Adapter\Driver\Mysqli\Mysqli();
        }

        return $this->_database;

    }


    public function setConnection($hostname, $username, $password, $database, $name='default')
    {

        $this->_configConnections[$name] = array(
            'hostname' => $hostname,
            'username' => $username,
            'password' => $password,
            'database' => $database
        );

    }


    public function getConnection($name='default')
    {

        if (isset($this->_configConnections[$name]) === false) {
            trigger_error("Use of undefined configuration '" . $name . "'", E_USER_ERROR);
        }

        if (isset($this->_connections[$name]) === false) {
            $this->_connections[$name] = $this->getDatabase()->connect(
                            $this->_configConnections[$name]['hostname'],
                            $this->_configConnections[$name]['username'],
                            $this->_configConnections[$name]['password'],
                            $this->_configConnections[$name]['database']);

            $this->_connections[$name]->query("SET NAMES 'utf8'");
        }

        return $this->_connections[$name];

    }


    public function setModel($path)
    {


        $this->_model = $path;

    }


    public function getModel()
    {

        return $this->_model;

    }


    public function setCache($file)
    {

        $this->_cache = $file;

    }


    public function getCache()
    {

        return $this->_cache;

    }


}
