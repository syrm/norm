<?php

namespace Norm\Adapter\Driver\Mysqli;

require_once 'Statement.php';
require_once realpath(__DIR__ . '/../..') .'/Database.php';

class Mysqli implements \Norm\Adapter\Database
{


    protected $_connection;


    public function connect($hostname, $username, $password, $database)
    {

        $this->_connection = new \mysqli($hostname, $username, $password, $database);

        if (mysqli_connect_error()) {
            trigger_error('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error(), E_USER_ERROR);
        }

        return $this;

    }


    public function error()
    {

        if ($this->_connection->error === '') {
            return false;
        } else {
            return $this->_connection->error;
        }

    }


    public function escape($value)
    {

        return $this->_connection->real_escape_string($value);

    }


    public function query($sql)
    {

        $this->_connection->query($sql);

    }


    public function prepare($sql)
    {

        $mysqliStatement = $this->_connection->prepare($sql);

        if ($mysqliStatement === false) {
            return false;
        }

        $statement = new Statement($mysqliStatement);
        return $statement;

    }


    public function getInsertId()
    {

        return $this->_connection->insert_id;

    }


    public function getSqlState()
    {

        return $this->_connection->sqlstate;

    }


    public function getErrorNo()
    {

        return $this->_connection->errno;

    }


    public function getErrorMessage()
    {

        return $this->_connection->error;

    }


}