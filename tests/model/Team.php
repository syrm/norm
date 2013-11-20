<?php

/**
 * orm:name(T_TEAM_TEA)
 */
class Team extends \Norm\Model
{


    /**
     * orm:primary(true)
     * orm:type(int)
     * orm:name(tea_id)
     */
    protected $_id;

    /**
     * orm:type(string)
     * orm:name(tea_name)
     */
    protected $_name;

    /**
     * orm:type(string)
     * orm:name(tea_alias)
     */
    protected $_alias;


}