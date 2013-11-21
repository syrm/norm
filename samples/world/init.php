<?php

require_once '../../Query.php';
require_once '../../Model.php';

require_once 'config.php';

$configuration = \Norm\Configuration::getInstance();
$configuration->setModel('model/*.php');

$configuration->setConnection(
    $database['hostname'],
    $database['username'],
    $database['password'],
    $database['database'],
    'default');