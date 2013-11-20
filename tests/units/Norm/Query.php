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

class Query extends atoum\test
{


    public function beforeTestMethod($method)
    {

        \Norm\tests\Autoloader::register();

    }


    public function testIterator()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Iterator');

    }


    public function testCountable()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Countable');

    }


    public function testFrom()
    {

        $this
            ->if($q = new \Norm\Query())
            ->and($q->from('test'))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS *\n FROM `test`\n");

    }


    public function testDelete()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->delete("test"))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($target = $q->getTarget())
            ->then
                ->string($target)
                ->isIdenticalTo('test')
            ->if($type = $q->getType())
            ->then
                ->string($type)
                ->isIdenticalTo($q::TYPE_DELETE);

    }


    public function testUpdate()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($sql = $q->update("test")->set(array('toto' => 'tutu'))->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("UPDATE `test`\n SET toto = 'tutu'\n");

    }


    public function testInsert()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->escape = function($value) { return $value; };

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($sql = $q->insert("test")->set(array('toto' => 'tutu'))->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("INSERT INTO `test`\n (toto) VALUES ('tutu')");

    }


    public function testSelect()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select("zim.a, boum as truc, vlan"))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($selects = $q->getSelect())
            ->then
                ->array($selects)
                ->isIdenticalTo(array('zim.a, boum as truc, vlan'))
            ->if($q = $q->select("boum", false))
            ->and($selects = $q->getSelect())
            ->then
                ->array($selects)
                ->isIdenticalTo(array('boum'));

    }


    public function testParseTableName()
    {

        $this
            ->if($result = \Norm\Query::parseTableName(''))
            ->then
                ->variable($result)
                ->isNull();

        $this
            ->if($result = \Norm\Query::parseTableName('T_BOUH_BOU'))
            ->then
                ->array($result)
                ->hasSize(2)
                ->string($result[0])
                ->isIdenticalTo('T_BOUH_BOU')
                ->string($result[1])
                ->isIdenticalTo('T_BOUH_BOU');

        $this
            ->if($result = \Norm\Query::parseTableName('T_BOUH_BOU as Bou'))
            ->then
                ->array($result)
                ->hasSize(2)
                ->string($result[0])
                ->isIdenticalTo('T_BOUH_BOU')
                ->string($result[1])
                ->isIdenticalTo('Bou');

        $this
            ->if($result = \Norm\Query::parseTableName('T_BOUH_BOU Bou'))
            ->then
                ->array($result)
                ->hasSize(2)
                ->string($result[0])
                ->isIdenticalTo('T_BOUH_BOU')
                ->string($result[1])
                ->isIdenticalTo('Bou');

        $this
            ->if($result = \Norm\Query::parseTableName('T_BOUH_BOU AS Bou'))
            ->then
                ->array($result)
                ->hasSize(2)
                ->string($result[0])
                ->isIdenticalTo('T_BOUH_BOU')
                ->string($result[1])
                ->isIdenticalTo('Bou');

    }


    public function testSelectFromWhere()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->where(':id', array(':a' => 'bouh')))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n WHERE (:id)\n");

    }


    public function testWhereSingleValue()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->where(':id', 'bouh'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n WHERE (:id)\n");

    }


    public function testWhereAppend()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->where(':id', 'bouh'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->where(':truc', 'choum', false)->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n WHERE (:truc)\n");

    }


    public function testWhereException()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->exception(function() use ($q) {
                    $q->where('id', 'bouh');
                })
                ->hasCode(1)
                ->hasMessage('Can\'t find a placeholder to bind bouh');

    }


    public function testGroup()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->group('zoom'))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n GROUP BY zoom\n")
            ->if($q = $q->group('ziom', false))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n GROUP BY ziom\n");

    }


    public function testHaving()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->having('zoom'))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n HAVING (zoom)\n")
            ->if($q = $q->having('ziom', false))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n HAVING (ziom)\n");

    }


    public function testLimit()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->limit(8, 3))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n LIMIT 3 OFFSET 8\n");

    }


    public function testOrder()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->order('zoom'))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n ORDER BY zoom\n")
            ->if($q = $q->order('ziom', false))
            ->and($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT`\n ORDER BY ziom\n");

    }


    public function testInnerJoin()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT m')->innerJoin('Pouf', 'Pouf.a = m.id'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT` m\n INNER JOIN `Pouf` ON (Pouf.a = m.id)\n");

    }


    public function testLeftJoin()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT m')->leftJoin('Pouf', 'Pouf.a = m.id'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT` m\n LEFT JOIN `Pouf` ON (Pouf.a = m.id)\n");

    }


    public function testRightJoin()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT m')->rightJoin('Pouf', 'Pouf.a = m.id'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql())
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT SQL_CALC_FOUND_ROWS bouh\n FROM `T_MATCH_MAT` m\n RIGHT JOIN `Pouf` ON (Pouf.a = m.id)\n");

    }


    public function testSkipFoundRow()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT m'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($sql = $q->getSql(true))
            ->then
                ->string($sql)
                ->isIdenticalTo("SELECT bouh\n FROM `T_MATCH_MAT` m\n");


    }


    public function testGetValues()
    {

        $this
            ->if($q = new \Norm\Query())
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($q = $q->select('bouh')->from('T_MATCH_MAT')->where(':id', 'bouh'))
            ->then
                ->object($q)
                ->isInstanceOf('\Norm\Query')
            ->if($values = $q->getValues())
            ->then
                ->array($values)
                ->isIdenticalTo(array(':id' => 'bouh'));

    }


    public function testFirst()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->fetchArray = array(3, 'Olympique de Marseille', 'OM');

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_id';
            $stdClass->orgname = 'tea_id';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_name';
            $stdClass->orgname = 'tea_name';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_alias';
            $stdClass->orgname = 'tea_alias';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $teamRef = new \Team();
        $teamRef->setId(3);
        $teamRef->setName('Olympique de Marseille');
        $teamRef->setAlias('OM');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($team = $q->first())
            ->then
                ->object($team)
                ->isCloneOf($teamRef);

    }


    public function testFirstNoresult()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = null;

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($team = $q->first())
            ->then
                ->variable($team)
                ->isNull();

    }


    public function testCount()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->fetchArray = array(32);

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'count';
            $stdClass->orgname = 'count';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($count = $q->count())
            ->then
                ->variable($count)
                ->isIdenticalTo(32);

    }


    public function testAttachDetach()
    {

        $this->mockGenerator->generate('\Norm\Observer\Observer', '\ObserverMock');
        $observerMock = new \ObserverMock\Observer();

        $this
            ->if($q = new \Norm\Query())
            ->and($q->attach($observerMock))
            ->and($observers = $q->getObservers())
            ->then
                ->array($observers)
                ->isIdenticalTo(array($observerMock))
            ->if($q->detach($observerMock))
            ->and($observers = $q->getObservers())
            ->then
                ->array($observers)
                ->isIdenticalTo(array());

    }


    public function testNotify()
    {

        $this->mockGenerator->generate('\Norm\Observer\Observer', '\ObserverMock');
        $observerMock = new \ObserverMock\Observer();

        $this
            ->if($q = new \Norm\Query())
            ->and($q->attach($observerMock))
            ->and($q->notify('Bouh'))
            ->then
                ->mock($observerMock)
                ->call('update')
                ->withArguments('Bouh')
                ->once();

    }


    public function testExecuteInsert()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->getInsertId = 32;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->insert('T_TEAM_TEA'))
            ->then
                ->exception(function() use ($q) {
                    $q->execute();
                })
                ->hasMessage('Query is empty')
            ->if($q->set(array('id' => 3)))
            ->and($result = $q->execute())
            ->then
                ->variable($result)
                ->isIdenticalTo(32);

    }


    public function testExecuteUpdate()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseStatementMock->getMockController()->getAffectedRows = 5;

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->update('T_TEAM_TEA'))
            ->and($q->where('tea_id = :id', 3))
            ->then
                ->exception(function() use ($q) {
                    $q->execute();
                })
                ->hasMessage('Query is empty')
            ->if($q->set(array('id' => 3)))
            ->and($result = $q->execute())
            ->then
                ->variable($result)
                ->isIdenticalTo(5);

    }


    public function testExecuteSelect()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->fetchArray = array(3);

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'count';
            $stdClass->orgname = 'count';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->where('id = :id AND name = :name AND note = :note', array(':id' => 3, ':name' => 'Bouh', ':note' => 3.7)))
            ->and($q->execute())
            ->and($q->execute())
            ->and($nb = count($q))
            ->then
                ->variable($nb)
                ->isIdenticalTo(3);

    }


    public function testExecuteWrongQuery()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->getErrorNo = 37;
        $databaseMock->getMockController()->getErrorMessage = 'Bouh query broken';
        $databaseMock->getMockController()->prepare = false;

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->exception(function() use ($q) {
                    $q->execute();
                })
                ->hasCode(37)
                ->hasMessage('Bouh query broken');

    }


    public function testExecuteDelete()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseStatementMock->getMockController()->getAffectedRows = 5;

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->delete('T_TEAM_TEA'))
            ->and($q->where('tea_id = :id', 3))
            ->and($result = $q->execute())
            ->then
                ->variable($result)
                ->isIdenticalTo(5);

    }


    public function testGetMetadata()
    {

        $this
            ->if($q = new \Norm\Query())
            ->and($metadata = $q->getMetadata())
            ->then
                ->object($metadata)
                ->isInstanceOf("\Norm\Metadata");

    }


    public function testRewind()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->dataSeek = null;
        $databaseResultMock->getMockController()->fetchArray = array(3, 'Olympique de Marseille', 'OM');

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_id';
            $stdClass->orgname = 'tea_id';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_name';
            $stdClass->orgname = 'tea_name';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_alias';
            $stdClass->orgname = 'tea_alias';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->rewind())
            ->and($key = $q->key())
            ->then
                ->variable($key)
                ->isIdenticalTo(0)
            ->mock($databaseResultMock)
                ->call('dataSeek')->once();

    }


    public function testValid()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->dataSeek = null;

        $databaseResultMock->getMockController()->fetchFields = function() { return null; };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->rewind())
            ->and($isValid = $q->valid())
            ->then
                ->boolean($isValid)
                ->isIdenticalTo(false);


        $databaseResultMock->getMockController()->fetchArray = array(3, 'Olympique de Marseille', 'OM');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->rewind())
            ->and($isValid = $q->valid())
            ->then
                ->boolean($isValid)
                ->isIdenticalTo(true);

    }


    public function testCurrent()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->dataSeek = null;
        $databaseResultMock->getMockController()->fetchArray = array(3, 'Olympique de Marseille', 'OM');

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_id';
            $stdClass->orgname = 'tea_id';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_name';
            $stdClass->orgname = 'tea_name';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_alias';
            $stdClass->orgname = 'tea_alias';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $team = new \Team;
        $team->setId(3);
        $team->setName('Olympique de Marseille');
        $team->setAlias('OM');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->rewind())
            ->and($isValid = $q->valid())
            ->then
                ->boolean($isValid)
                ->isIdenticalTo(true)
            ->and($current = $q->current())
            ->then
                ->object($current)
                ->isInstanceOf('\Team')
                ->isCloneOf($team);

    }


    public function testNext()
    {

        $this->mockGenerator->generate('\Norm\Adapter\Database', '\DatabaseMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseStatement', '\DatabaseStatementMock');
        $this->mockGenerator->generate('\Norm\Adapter\DatabaseResult', '\DatabaseResultMock');

        $databaseMock = new \DatabaseMock\Database();
        $databaseStatementMock = new \DatabaseStatementMock\DatabaseStatement();
        $databaseResultMock = new \DatabaseResultMock\DatabaseResult();

        $databaseMock->getMockController()->connect = $databaseMock;
        $databaseMock->getMockController()->error = false;
        $databaseMock->getMockController()->prepare = $databaseStatementMock;
        $databaseStatementMock->getMockController()->execute = $databaseStatementMock;
        $databaseStatementMock->getMockController()->getResult = $databaseResultMock;
        $databaseResultMock->getMockController()->dataSeek = null;
        $databaseResultMock->getMockController()->fetchArray = array(3, 'Olympique de Marseille', 'OM');

        $databaseResultMock->getMockController()->fetchFields = function() {
            $fields = array();

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_id';
            $stdClass->orgname = 'tea_id';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_name';
            $stdClass->orgname = 'tea_name';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            $stdClass = new \stdClass();
            $stdClass->name    = 'tea_alias';
            $stdClass->orgname = 'tea_alias';
            $stdClass->table   = 'T_TEAM_TEA';
            $fields[] = $stdClass;

            return $fields;

        };

        \Norm\Configuration::getInstance()->setModel(ROOT . 'tests/model/*.php');

        $team = new \Team;
        $team->setId(3);
        $team->setName('Olympique de Marseille');
        $team->setAlias('OM');

        $this
            ->if($q = new \Norm\Query())
            ->and(\Norm\Configuration::getInstance()->setConnection('', '', '', '', 'default'))
            ->and(\Norm\Configuration::getInstance()->setDatabase($databaseMock))
            ->and($q->from('T_TEAM_TEA'))
            ->and($q->rewind())
            ->and($key = $q->key())
            ->then
                ->variable($key)
                ->isIdenticalTo(0)
            ->if($q->next())
            ->and($key = $q->key())
            ->then
                ->variable($key)
                ->isIdenticalTo(1);

    }


}
