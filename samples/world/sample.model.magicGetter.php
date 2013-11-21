<?php

require_once 'init.php';
require_once 'model/City.php';

echo "#######################\n";
echo "# getBy'PropertyName' #\n";
echo "#######################\n\n";

$city = City::getById(1);
var_dump($city);

echo "###############################################\n";
echo "# getBy'PropertyName'And'OtherProperty'And... #\n";
echo "###############################################\n\n";

$city = City::getByCountryCodeAndDistrict("AFG", "Kabol");
var_dump($city);

echo "########################\n";
echo "# findBy'PropertyName' #\n";
echo "########################\n\n";

$cities = City::findByCountryCode("AFG");

foreach($cities as $i => $city) {
    var_dump($city);

    if ($i > 1) {
        break;
    }
}

