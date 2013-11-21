<?php

require_once 'init.php';

$q = new Norm\Query();
$q->from('city')->where('name like :search', array(':search' => 'C%'))->limit(0, 3);

foreach($q as $city) {
    echo "City: " . $city->getName() . "\n";
    echo "District: " . $city->getDistrict() . "\n";
    echo "Population: " . $city->getPopulation() . "\n";
    echo "\n";
}

