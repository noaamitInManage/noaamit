<?php

/**
 * Trait SendsApiResponses
 *
 * Handles api reponses
 */
trait SendsApiResponses
{
    /*----------------------------------------------------------------------------------*/
    /**
     * Returns an api response object
     *
     * @return ApiResponse
     */
    public function response()
    {
        return new ApiResponse();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sends a successful response
     *
     * @param $dataArr
     * @param string $message
     * @return string
     */
    public function respond($dataArr = [], $message = '')
    {
        return $this->response()->success()->set_data($dataArr)->set_message($message)->json();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sends a failure response
     *
     * @param $error_id
     * @param null $content
     * @param array $additional_dataArr
     * @return string
     */
    public function respond_failure($error_id, $content = null, $additional_dataArr = [])
    {
        return $this->response()->failure()->set_err($error_id, $content)->set_additional_data($additional_dataArr)->json();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sends a response by version
     *
     * @param ApiResponse[] $responsesArr
     * @return mixed
     */
    public function response_versioning($responsesArr)
    {
        $version = array_key_exists($this->version, $responsesArr) ? $this->version : '1.0';

        return $responsesArr[$version]->json();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sends a missing data response
     *
     * @return string
     */
    public function respond_missing_data()
    {
        return $this->respond_failure(configManager::$common_errorsArr['missing_data']);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Sends a must be logged in response
     *
     * @return string
     */
    public function respond_must_be_logged_in()
    {
        return $this->respond_failure(configManager::$common_errorsArr['must_be_logged_in']);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Send an unauthorized response.
     *
     * @return string
     */
    public function respond_unauthorized()
    {
        return $this->respond_failure(configManager::$common_errorsArr['unauthorized']);
    }

    /*----------------------------------------------------------------------------------*/
}