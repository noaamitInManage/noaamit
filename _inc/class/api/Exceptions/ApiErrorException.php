<?php

class ApiErrorException extends Exception
{
    private $error_id;

    /**
     * ApiErrorException constructor.
     *
     * @param $error_id
     */
    public function __construct($error_id)
    {
        parent::__construct();

        $this->error_id = $error_id;
    }

    /**
     * Get error id.
     *
     * @return int
     */
    public function get_error_id()
    {
        return $this->error_id;
    }
}