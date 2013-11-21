<?php

require_once 'init.php';
require_once 'model/City.php';

$city = City::getById(3);
$city->setName("Edited " . $city->getName());
$city->update();

var_dump($city);

