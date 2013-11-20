<?php

namespace Norm\tests\units;

define('ROOT', realpath(__DIR__ . '/../../..') . '/');

require_once ROOT . 'Query.php';
require_once ROOT . 'Model.php';
require_once ROOT . 'Metadata.php';
require_once ROOT . 'tests/Autoloader.php';


use mageekguy\atoum;

class Metadata extends atoum\test
{


    public function beforeTestMethod($method)
    {

        \Norm\tests\Autoloader::register();

    }


    public function testGetTable()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($table = $metadata->getTable('Match'))
            ->then
                ->string($table)
                ->isIdenticalTo('T_MATCH_MAT');

    }


    public function testGetTableNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($table = $metadata->getTable('NotFound'))
            ->then
                ->variable($table)
                ->isNull();

    }


    public function testGetPrimary()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($primary = $metadata->getPrimary('T_MATCH_MAT'))
            ->then
                ->array($primary)
                ->isIdenticalTo(array(
                    'key' => 'mat_id',
                    'params' => array(
                        'primary' => true,
                        'type' => 'int',
                        'name' => '_id'
                       )
                ));

    }


    public function testGetPrimaryNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($primary = $metadata->getPrimary('T_TABLE_NO_PRIMARY_TNP'))
            ->then
                ->variable($primary)
                ->isNull();

    }


    public function testGetPrimaryTableNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($primary = $metadata->getPrimary('NotFound'))
            ->then
                ->variable($primary)
                ->isNull();

    }


    public function testGetClass()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($class = $metadata->getClass('T_MATCH_MAT'))
            ->then
                ->string($class)
                ->isIdenticalTo('Match');

    }


    public function testGetClassNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($class = $metadata->getClass('T_NOT_FOUND_NFO'))
            ->then
                ->variable($class)
                ->isNull();

    }


    public function testGetColumns()
    {

        $columnsRef = array(
            'tea_id' => array(
                'primary' => true,
                'type' => 'int',
                'name' => '_id'
            ),
            'tea_name' => array(
                'type' => 'string',
                'name' => '_name'
            ),
            'tea_alias' => array(
                'type' => 'string',
                'name' => '_alias'
            )
        );

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($columns = $metadata->getColumns('T_TEAM_TEA'))
            ->then
                ->array($columns)
                ->isIdenticalTo($columnsRef);

    }


    public function testGetColumnsTableNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($columns = $metadata->getColumns('T_NOT_FOUND_NFO'))
            ->then
                ->array($columns)
                ->isIdenticalTo(array());

    }


    public function testGetColumnsTableNoColumns()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($columns = $metadata->getColumns('T_TABLE_NO_COLUMNS_TNC'))
            ->then
                ->array($columns)
                ->isIdenticalTo(array());

    }


    public function testGetColumnByName()
    {

        $columnRef = array(
            'type' => 'string',
            'name' => '_name',
            'key'  => 'tea_name'
        );

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByName('T_TEAM_TEA', '_name'))
            ->then
                ->array($column)
                ->isIdenticalTo($columnRef);

    }


    public function testGetColumnByNameTableNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByName('T_NOT_FOUND_NFO', '_name'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testGetColumnByNameTableNoColumns()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByName('T_TABLE_NO_COLUMNS_TNC', '_name'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testGetColumnByNameColumnNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByName('T_TEAM_TEA', '_notFound'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testGetColumnByKey()
    {

        $columnRef = array(
            'type' => 'string',
            'name' => '_name'
        );

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByKey('T_TEAM_TEA', 'tea_name'))
            ->then
                ->array($column)
                ->isIdenticalTo($columnRef);

    }


    public function testGetColumnByKeyTableNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByKey('T_NOT_FOUND_NFO', 'tea_name'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testGetColumnByKeyTableNoColumns()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByKey('T_TABLE_NO_COLUMNS_TNC', '_name'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testGetColumnByKeyColumnNotFound()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($column = $metadata->getColumnByKey('T_TEAM_TEA', '_notFound'))
            ->then
                ->variable($column)
                ->isNull();

    }


    public function testMapToObjects()
    {

        $stdClassId = new \stdClass();
        $stdClassId->name    = 'tea_id';
        $stdClassId->orgname = 'tea_id';
        $stdClassId->table   = 'T_TEAM_TEA';

        $stdClassName = new \stdClass();
        $stdClassName->name    = 'tea_name';
        $stdClassName->orgname = 'tea_name';
        $stdClassName->table   = 'T_TEAM_TEA';

        $stdClassAlias = new \stdClass();
        $stdClassAlias->name    = 'tea_alias';
        $stdClassAlias->orgname = 'tea_alias';
        $stdClassAlias->table   = 'T_TEAM_TEA';

        $stdClassId->value    = 3;
        $stdClassName->value  = 'Olympique de Marseille';
        $stdClassAlias->value = 'OM';

        $columns = array($stdClassId, $stdClassName, $stdClassAlias);
        $targets = array('T_TEAM_TEA');

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($team = new \Team())
            ->and($team->setId(3))
            ->and($team->setName('Olympique de Marseille'))
            ->and($team->setAlias('OM'))
            ->and($object = $metadata->mapToObjects($columns, $targets))
            ->then
                ->object($object)
                ->isCloneOf($team);

    }


    public function testMapToObjectsNoTable()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($object = $metadata->mapToObjects(array(), array()))
            ->then
                ->object($object)
                ->isInstanceOf("\Norm\Model");

    }


    public function testMapToObjectsWithSubObject()
    {

        $stdClassMatchId = new \stdClass();
        $stdClassMatchId->name    = 'mat_id';
        $stdClassMatchId->orgname = 'mat_id';
        $stdClassMatchId->table   = 'T_MATCH_MAT';

        $stdClassMatchDate = new \stdClass();
        $stdClassMatchDate->name    = 'mat_date';
        $stdClassMatchDate->orgname = 'mat_date';
        $stdClassMatchDate->table   = 'T_MATCH_MAT';

        $stdClassTeamId = new \stdClass();
        $stdClassTeamId->name     = 'tea_id';
        $stdClassTeamId->orgname  = 'tea_id';
        $stdClassTeamId->table    = 'teamHomeId';
        $stdClassTeamId->orgtable = 'T_TEAM_TEA';

        $stdClassTeamName = new \stdClass();
        $stdClassTeamName->name     = 'tea_name';
        $stdClassTeamName->orgname  = 'tea_name';
        $stdClassTeamName->table    = 'teamHomeId';
        $stdClassTeamName->orgtable = 'T_TEAM_TEA';

        $stdClassMatchId->value   = 7;
        $stdClassMatchDate->value = '2012-12-11';
        $stdClassTeamId->value    = 3;

        $stdClassTeamId->value    = 3;
        $stdClassTeamName->value  = 'Olympique de Marseille';

        $columns = array($stdClassMatchId, $stdClassMatchDate, $stdClassTeamId, $stdClassTeamName);
        $targets = array('T_MATCH_MAT', 'T_TEAM_TEA teamHomeId');

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($match = new \Match())
            ->and($match->setId(7))
            ->and($match->setTeamHomeId(3))
            ->and($match->setDate('2012-12-11'))
            ->and($team = new \Team())
            ->and($team->setId(3))
            ->and($team->setName('Olympique de Marseille'))
            ->and($match->setTeamHomeId($team))
            ->and($object = $metadata->mapToObjects($columns, $targets))
            ->then
                ->object($object)
                ->isCloneOf($match);

    }


    public function testMapToObject()
    {

        $stdClass = new \stdClass();
        $stdClass->name     = 'now';
        $stdClass->orgname  = 'now';
        $stdClass->table    = 'teamHome';
        $stdClass->orgtable = 'T_TEAM_TEA';
        $stdClass->value    = 3;

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($object = $metadata->mapToObject(array($stdClass), 'T_TEAM_TEA', 'teamHome'))
            ->and($teamRef = new \Team())
            ->and($teamRef->now = 3)
            ->then
                ->object($object)
                ->isCloneOf($teamRef);

    }


    public function testMapToObjectColumnsNull()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($object = $metadata->mapToObject(null, 'T_TEAM_TEA', 'teamHome'))
            ->then
                ->variable($object)
                ->isNull();

    }


    public function testGetInstance()
    {

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($metadata2 = \Norm\Metadata::getInstance(ROOT . 'tests/model/*.php'))
            ->then
                ->object($metadata)
                ->isIdenticalTo($metadata2);

    }


    public function testParseComment()
    {

        $comment = "/**
                     * orm:type(string)
                     * orm:a(true)
                     * orm:b(false)
                     * orm:c(3)
                     * orm:d(1,2,3)
                     * orm:name(tea_name)
                     */";

        $paramsRef = array('type' => 'string', 'name' => 'tea_name', 'a' => true, 'b' => false, 'c' => 3, 'd' => array('1', '2', '3'));

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($params = $metadata->parseComment($comment))
            ->and(ksort($params))
            ->and(ksort($paramsRef))
            ->then
                ->array($params)
                ->isIdenticalTo($paramsRef);

        $comment = "/** Nothing */";

        $this
            ->if(\Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php'))
            ->if($metadata = \Norm\Metadata::getInstance())
            ->and($params = $metadata->parseComment($comment))
            ->then
                ->variable($params)
                ->isNull();

    }


}