<?php

/**
 * orm:name(countryLanguage)
 */
class CountryLanguage extends \Norm\Model
{

    /**
     * orm:primary(true)
     * orm:type(string)
     * orm:name(language)
     */
    protected $_language;

    /**
     * orm:type(string)
     * orm:name(countryCode)
     */
    protected $_countryCode;

    /**
     * orm:type(string)
     * orm:name(isOfficial)
     */
    protected $_isOfficial;

    /**
     * orm:type(double)
     * orm:name(percentage)
     */
    protected $_percentage;


}
