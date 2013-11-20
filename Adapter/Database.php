<?php

namespace Norm\Adapter;

interface Database
{

    public function connect($hostname, $username, $password, $database);
    public function query($sql);
    public function prepare($sql);
    public function escape($value);
    public function error();
    public function getInsertId();
    public function getSqlState();
    public function getErrorNo();
    public function getErrorMessage();

}