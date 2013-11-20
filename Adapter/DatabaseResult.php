<?php

namespace Norm\Adapter;

interface DatabaseResult
{

    public function dataSeek($offset);
    public function fetchArray();
    public function fetchFields();

}