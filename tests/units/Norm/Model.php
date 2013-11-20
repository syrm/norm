<?php

namespace Norm\tests\units;

define('ROOT', realpath(__DIR__ . '/../../..') . '/');

require_once ROOT . 'Observer/Observer.php';
require_once ROOT . 'Adapter/Driver/Mysqli/Mysqli.php';
require_once ROOT . 'Query.php';
require_once ROOT . 'Model.php';
require_once ROOT . 'Metadata.php';
require_once ROOT . 'tests/Autoloader.php';


use mageekguy\atoum;

class Model extends atoum\test
{


    public function beforeTestMethod($method)
    {

        \Norm\tests\Autoloader::register();

    }


    public function testToString()
    {

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');
        \Match::setMetadata(\Norm\Metadata::getInstance());

        $this
            ->if($match = new \Match())
            ->and($match->setId(3))
            ->and($text = sprintf('%s', $match))
            ->then
                ->string($text)
                ->isIdenticalTo('Match#3');

    }


    public function testMagicCall()
    {

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');
        \Match::setMetadata(\Norm\Metadata::getInstance());

        $this
            ->if($match = new \Match())
            ->and($match->setId(3))
            ->and($id = $match->getId())
            ->then
                ->integer($id)
                ->isIdenticalTo(3);

    }


    public function testMagicGetBy()
    {

        $this->mockGenerator->generate('\Norm\Query', '\QueryMock');
        $queryMock = new \QueryMock\Query();
        $queryMock->getMockController()->first = function() {
            $match = new \Match;
            return $match;
        };

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        \Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default');
        \Norm\Configuration::getInstance()->setDatabase($databaseMock);

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');
        \Match::setMetadata(\Norm\Metadata::getInstance());
        \Match::staticSetQuery($queryMock);

        $this
            ->if($match = \Match::getById(1))
            ->then
                ->object($match)
                ->isInstanceOf('\Match');

    }


    public function testMagicFindBy()
    {

        $this->mockGenerator->generate('\Norm\Query', '\QueryMock');
        $queryMock = new \QueryMock\Query();
        $queryMock->getMockController()->first = function() {
            return array(3, 3, 3);
        };

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        \Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default');
        \Norm\Configuration::getInstance()->setDatabase($databaseMock);
        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        \Team::setMetadata(\Norm\Metadata::getInstance());
        \Team::setStaticQuery($queryMock);

        $this
            ->if($team = \Team::findByName("Test"))
            ->then
                ->object($team)
                ->isInstanceOf('\Iterator');

    }


    public function testSave()
    {

        $this->mockGenerator->generate('\Norm\Query', '\QueryMock');
        $queryMock = new \QueryMock\Query();
        $queryMock->getMockController()->execute = 3;

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');
        \Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default');
        \Norm\Configuration::getInstance()->setDatabase($databaseMock);

        \Team::setMetadata(\Norm\Metadata::getInstance());

        $this
            ->if($team = new \Team)
            ->and($team->setQuery($queryMock))
            ->and($team->setName('Bouh'))
            ->and($result = $team->save())
            ->and($sql = $queryMock->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("INSERT INTO `T_TEAM_TEA`\n (tea_name) VALUES ('Bouh')")
            ->then
                ->boolean($result)
                ->isIdenticalTo(true)
            ->and($id = $team->getId())
            ->then
                ->integer($id)
                ->isIdenticalTo(3);

    }


    public function testUpdate()
    {

        $this->mockGenerator->generate('\Norm\Query', '\QueryMock');
        $queryMock = new \QueryMock\Query();
        $queryMock->getMockController()->execute = 3;

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');
        \Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default');
        \Norm\Configuration::getInstance()->setDatabase($databaseMock);

        \Team::setMetadata(\Norm\Metadata::getInstance());

        $this
            ->if($team = new \Team) // in standard code use Team::getById(1)
            ->and($team->setQuery($queryMock))
            ->and($team->setId(4))
            ->and($team->setName('Bouh'))
            ->and($result = $team->update())
            ->and($sql = $queryMock->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("UPDATE `T_TEAM_TEA`\n SET tea_name = 'Bouh'\n WHERE (tea_id = :tea_id)")
            ->then
                ->boolean($result)
                ->isIdenticalTo(true);

    }


}
