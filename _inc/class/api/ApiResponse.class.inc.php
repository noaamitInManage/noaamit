<?php

/**
 * Class ApiResponse
 *
 * An api response object
 */
class ApiResponse
{
    /**
     * Response statuses
     */
    private const STATUS_SUCCESS = 1;
    private const STATUS_FAILURE = 0;

    /**
     * Determines whether the request succeeded or failed
     * @var int
     */
    private $status = self::STATUS_SUCCESS;

    /**
     * Response data
     * @var array
     */
    private $data = [];

    /**
     * Response error
     * @var array
     */
    private $err = [];

    /**
     * Response message
     * @var string
     */
    private $message = '';

    /**
     * Additional data
     * @var array
     */
    private $additional_dataArr = [];

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response status to success
     *
     * @return ApiResponse
     */
    public function success(): ApiResponse
    {
        return $this->set_status(self::STATUS_SUCCESS);
    }
    
    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response status to failure
     *
     * @return ApiResponse
     */
    public function failure(): ApiResponse
    {
        return $this->set_status(self::STATUS_FAILURE);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response status
     *
     * @return int
     */
    public function get_status(): int
    {
        return $this->status;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response status
     *
     * @param int $status
     * @return ApiResponse
     */
    public function set_status(int $status): ApiResponse
    {
        $this->status = $status;

        return $this;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response data
     *
     * @return array
     */
    public function get_data(): array
    {
        return $this->data ?? [];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response data
     *
     * @param array $data
     * @return ApiResponse
     */
    public function set_data(?array $data): ApiResponse
    {
        $this->data = $data;

        return $this;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response error
     *
     * @return array
     */
    public function get_err(): array
    {
        return $this->err;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response error by id / hard coded content
     *
     * @param int $id
     * @param string|null $content
     * @return ApiResponse
     */
    public function set_err($id, $content = null): ApiResponse
    {
        if ($content === null) {
            $err = errorManager::get_error($id);
        } else {
            $err = [
                'id' => $id,
                'content' => $content,
            ];
        }

        $this->err = $err;

        return $this;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response message
     *
     * @return string
     */
    public function get_message(): string
    {
        return $this->message;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets response message
     *
     * @param string $message
     * @return ApiResponse
     */
    public function set_message(string $message): ApiResponse
    {
        $this->message = $message;

        return $this;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns additional data
     *
     * @return array
     */
    public function get_additional_data(): array
    {
        return $this->additional_dataArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sets additional data
     *
     * @param $additional_dataArr
     * @return ApiResponse
     */
    public function set_additional_data($additional_dataArr): ApiResponse
    {
        $this->additional_dataArr = $additional_dataArr;

        return $this;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response in an array form
     *
     * @return array
     */
    public function array(): array
    {
        return [
            'status' => $this->get_status(),
            'data' => $this->get_data(),
            'err' => $this->get_err(),
            'message' => $this->get_message(),
            'additional_dataArr' => $this->get_additional_data(),
        ];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns response in a json form
     *
     * @param bool $force_object
     * @return string
     */
    public function json($force_object = false): string
    {
        $options = $force_object ? JSON_FORCE_OBJECT : 0;

        return json_encode($this->array(), $options);
    }

    /*----------------------------------------------------------------------------------*/
}