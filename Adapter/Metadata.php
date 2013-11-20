<?php

namespace Norm\Adapter;

interface Metadata
{

    public static function getInstance();
    public function mapToObject($columns, $targets, $alias);
    public function mapToObjects($columns, $targets);

}