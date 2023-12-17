<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 18/06/2017
 * Time: 14:05
 */
class BaseManager
{
    protected $db;
    protected $ts;

    /*----------------------------------------------------------------------------------*/
    /**
     * BaseManager constructor.
     * @param bool $db_connection
     */
    public function __construct($db_connection = true)
    {
        if ($db_connection) {
            $this->db = Database::getInstance();
        }

        $this->ts = time();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_db_connection
     * @description Returns the database connection
     * @return Database|null
     */
    public function get_db_connection()
    {
        return $this->db;
    }

    /*----------------------------------------------------------------------------------*/
}