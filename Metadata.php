<?php

namespace Norm;

require_once 'Adapter/Metadata.php';

class Metadata implements Adapter\Metadata
{

    protected $_metadata;
    protected static $_instance;


    public static function getInstance()
    {

        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }


    protected function __construct()
    {

        $directory = Configuration::getInstance()->getModel();

        if ($directory === null) {
            $this->_metadata = array();
            return;
        }

        $cache = Configuration::getInstance()->getCache();

        $classes   = array();
        $metadata  = array();

        if ($cache !== null && file_exists($cache)) {
            $this->_metadata = unserialize(file_get_contents($cache));
            return;
        }

        foreach(glob($directory) as $file) {
            $next = null;
            $data = array('T_NAMESPACE' => '');
            foreach(token_get_all(file_get_contents($file)) as $element) {
                if (is_array($element) === true) {
                    if (in_array($element[0], array(T_NAMESPACE, T_CLASS)) === true) {
                        $next = $element[0];
                    }

                    if ($next === T_NAMESPACE && in_array($element[0], array(T_STRING, T_NS_SEPARATOR)) === true) {
                        $data[token_name($next)] .= $element[1];
                    }

                    if ($next === T_CLASS && $element[0] === T_STRING) {
                        $data[token_name($next)] = $element[1];
                        $next = null;

                        if (isset($data['T_CLASS']) === true) {
                            if ($data['T_NAMESPACE'] !== '') {
                                $fqn = '\\' . $data['T_NAMESPACE'] . '\\';
                            } else {
                                $fqn = '\\';
                            }

                            $fqn .= $data['T_CLASS'];

                            $classes[] = $fqn;
                            $data = array('T_NAMESPACE' => '');
                        }
                    }
                }
            }

            require_once $file;
        }

        foreach($classes as $class) {
            $model = strtolower($class);

            $params = array();

            $reflectionClass = new \ReflectionClass($class);

            $classCommentInfos = $this->parseComment($reflectionClass->getDocComment());

            if (isset($classCommentInfos['name']) === false) {
                continue;
            }

            $metadata[$classCommentInfos['name']] = array();
            $metadata[$classCommentInfos['name']]['columns'] = array();
            $metadata[$classCommentInfos['name']]['class'] = $class;

            foreach($reflectionClass->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {

                $reflectionProperty = new \ReflectionProperty($model, $property->name);
                $commentInfos = $this->parseComment($reflectionProperty->getDocComment());

                if ($commentInfos === null) {
                    continue;
                }

                if (isset($commentInfos['type']) === false) {
                    $commentInfos['type'] = "auto";
                }

                $param   = array();
                $sqlName = $commentInfos['name'];
                $commentInfos['name'] = $property->name;
                $params[$sqlName]     = $commentInfos;


            }
            $metadata[$classCommentInfos['name']]['columns'] = $params;

        }

        if ($cache !== null) {
            file_put_contents($cache, serialize($metadata));
        }

        $this->_metadata = $metadata;

    }


    public function parseComment($comment)
    {

        if (preg_match_all('/\* orm:(.+)\((.+)\)/', $comment, $matches) > 0) {
            $param  = array_combine($matches[1], $matches[2]);

            foreach($param as $key => $value) {
                if ($value === 'true') {
                    $param[$key] = true;
                } else if ($value === 'false') {
                    $param[$key] = false;
                } else if (is_numeric($value) === true) {
                    $param[$key] = (int) $value;
                } else if (strpos($value, ',') !== false) {
                    $array = explode(',', $value);
                    $param[$key] = array_map('trim', $array);
                }
            }

            return $param;

        }

        return null;

    }


    public function getTable($class)
    {

        foreach($this->_metadata as $table => $columns) {
            if ($columns['class'] === '\\' . $class) {
                return $table;
            }
        }

        return null;

    }


    public function getPrimary($table)
    {

        list($table) = explode(' ', $table);

        if (isset($this->_metadata[$table]) === false) {
            return null;
        }

        foreach($this->_metadata[$table]['columns'] as $sqlName => $column) {
            if (isset($column['primary']) === true && $column['primary'] === true) {
                return array(
                    'key'    => $sqlName,
                    'params' => $column);
            }
        }

        return null;

    }


    public function getColumns($table)
    {

        list($table) = explode(' ', $table);

        if (isset($this->_metadata[$table]) === false) {
            return array();
        }

        if (count($this->_metadata[$table]['columns']) === 0) {
            return array();
        }

        return $this->_metadata[$table]['columns'];

    }


    public function getColumnByName($table, $propertyName)
    {

        list($table) = explode(' ', $table);

        if (isset($this->_metadata[$table]) === false) {
            return null;
        }

        if (count($this->_metadata[$table]['columns']) === 0) {
            return null;
        }

        foreach($this->_metadata[$table]['columns'] as $key => $column) {
            if ($column['name'] === $propertyName || $column['name'] === '_' . $propertyName) {
                $column['key'] = $key;
                return $column;
            }
        }

        return null;

    }


    public function getColumnByKey($table, $sqlName)
    {

        list($table) = explode(' ', $table);

        if (isset($this->_metadata[$table]['columns'][$sqlName]) === false) {
            return null;
        }

        return $this->_metadata[$table]['columns'][$sqlName];

    }


    public function getClass($table)
    {

        list($table) = explode(' ', $table);

        if (isset($this->_metadata[$table]['class']) === false) {
            return null;
        }

        return $this->_metadata[$table]['class'];

    }


    public function mapToAnonymous($columns, $targets)
    {

        $object = new Model();

        foreach($columns as $column) {
            $object->{$column->name} = $column->value;
        }

        return $object;

    }


    public function mapToObjects($columns, $targets)
    {
        $mainTarget = array_shift($targets);

        list($table, $alias) = Query::parseTableName($mainTarget);
        $object = $this->mapToObject($columns, $table, $alias, true);

        if ($object === null) {
            return $this->mapToAnonymous($columns, $targets);
        }

        foreach($targets as $target) {
            list($table, $alias) = Query::parseTableName($target);
            $subObject = $this->mapToObject($columns, $table, $alias);
            $object->{'set' . ucfirst($alias)}($subObject);

        }

        return $object;

    }


    public function mapToObject($columns, $table, $alias, $mainObject=false)
    {

        $class = self::getClass($table);

        if ($class === null) {
            return null;
        }

        if ($columns === null) {
            return null;
        }

        $object = new $class;

        foreach($columns as $column) {
            if ($alias !== $column->table && $column->table != '') {
                continue;
            }

            $columnInfo = $this->getColumnByKey($table, $column->orgname);

            if ($column->orgname === '') { // dynamic column (like NOW() as bouh)
                if ($mainObject === true) {
                    $method = 'set' . ucfirst($column->name);
                }
            } else if ($columnInfo === null) { // not a column of the model
                $method = 'set' . ucfirst($column->orgname);
            } else {
                $method = 'set' . ucfirst(substr($columnInfo['name'], 1));
            }

            if (isset($method) === true) {
                $object->$method($column->value);
                unset($method);
            }
        }

        return $object;

    }


}