<?php

require_once 'init.php';
require_once 'model/City.php';

$city = new City;
$city->setName('Test');
$city->setCountryCode('AFG');
$city->setDistrict('TestDistrict');
$city->setPopulation(1);
$city->save();

// City populated by the auto increment id
var_dump($city);

