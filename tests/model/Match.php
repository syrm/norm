<?php

require_once realpath(__DIR__ . '/../..') .'/Model.php';

/**
 * orm:name(T_MATCH_MAT)
 */
class Match extends \Norm\Model
{


    /**
     * orm:primary(true)
     * orm:type(int)
     * orm:name(mat_id)
     */
    protected $_id;

    /**
     * orm:type(int)
     * orm:name(tea_id_home)
     */
    protected $_teamHomeId;

    /**
     * orm:type(int)
     * orm:name(tea_id_away)
     */
    protected $_teamAwayId;

    /**
     * orm:type(datetime)
     * orm:name(mat_date)
     */
    protected $_date;

    /**
     * orm:type(enum)
     * orm:name(mat_over)
     */
    protected $_over;


}