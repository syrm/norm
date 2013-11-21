<?php

/**
 * orm:name(city)
 */
class City extends \Norm\Model
{

    /**
     * orm:primary(true)
     * orm:type(int)
     * orm:name(ID)
     */
    protected $_id;

    /**
     * orm:type(string)
     * orm:name(name)
     */
    protected $_name;

    /**
     * orm:type(string)
     * orm:name(countryCode)
     */
    protected $_countryCode;

    /**
     * orm:type(string)
     * orm:name(district)
     */
    protected $_district;

    /**
     * orm:type(string)
     * orm:name(population)
     */
    protected $_population;


    public function getName()
    {

        return strtoupper($this->_name);

    }


}
