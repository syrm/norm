<?php

namespace Norm\Adapter;

interface DatabaseStatement
{

    public function bindParams(array $params);
    public function execute();
    public function getAffectedRows();
    public function getResult();
    public function close();

}