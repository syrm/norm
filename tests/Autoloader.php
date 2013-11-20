<?php

namespace Norm\tests;

class Autoloader
{


    static public function register()
    {

        spl_autoload_register('\Norm\tests\Autoloader::autoload');

    }


    static public function autoload($class)
    {


        if (file_exists($file = ROOT . '/tests/model/' . $class . '.php') === true) {
            include_once $file;
            return;
        }


        if (file_exists($file = str_replace('\\', '/', $class) . '.php') === true) {
            include_once $file;
            return;
        }

    }


}