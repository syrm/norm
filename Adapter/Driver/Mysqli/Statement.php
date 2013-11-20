<?php

namespace Norm\Adapter\Driver\Mysqli;

require_once realpath(__DIR__ . '/../..') .'/DatabaseStatement.php';
require_once 'Result.php';


class Statement implements \Norm\Adapter\DatabaseStatement
{


    protected $_statement;


    public function __construct(\mysqli_stmt $statement)
    {

        $this->_statement = $statement;

    }


    public function bindParams(array $params)
    {

        if (strnatcmp(phpversion(),'5.3') >= 0) {
            $refs = array();
            foreach($params as $key => $value) {
                $refs[$key] = &$params[$key];
            }
        } else {
            $refs = $params;
        }

        call_user_func_array(array($this->_statement, 'bind_param'), $refs);

    }


    public function execute()
    {

        return $this->_statement->execute();

    }


    public function getAffectedRows()
    {

        return $this->_statement->affected_rows;

    }


    public function getResult()
    {

        $result = $this->_statement->get_result();

        if ($result === false) {
            return null;
        }

        return new Result($result);

    }


    public function close()
    {

        $this->_statement->close();

    }


}