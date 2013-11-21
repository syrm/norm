<?php

require_once 'init.php';

$q = new Norm\Query();
$q->from('city c')
  ->innerJoin('countryLanguage cl', 'cl.countryCode = c.countryCode')
  ->where('name like :search', array(':search' => 'C%'))->limit(0, 3);

foreach($q as $city) {
    echo "City: " . $city->getName() . "\n";
    echo "CountryLanguage: " . $city->cl->getLanguage() . "\n";
    echo "\n";
}

