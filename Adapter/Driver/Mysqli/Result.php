<?php

namespace Norm\Adapter\Driver\Mysqli;

require_once realpath(__DIR__ . '/../..') .'/DatabaseResult.php';


class Result implements \Norm\Adapter\DatabaseResult
{


    protected $_result;


    public function __construct(\mysqli_result $result)
    {

        $this->_result = $result;

    }


    public function dataSeek($offset)
    {

        return $this->_result->data_seek($offset);

    }


    public function fetchArray()
    {

        return $this->_result->fetch_array(MYSQLI_NUM);

    }


    public function fetchFields()
    {

        $fields = $this->_result->fetch_fields();

        if ($fields === false) {
            return null;
        }

        return $fields;

    }


}