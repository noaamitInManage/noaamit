<?php

class ApiExceptionHandler
{
    use SendsApiResponses;

    /*----------------------------------------------------------------------------------*/
    /**
     * Handles api exceptions
     *
     * @param Exception|Error $exception
     * @return string
     */
    public function handle($exception)
    {
        switch (true) {
            case $exception instanceof ApiErrorException:
                return $this->handle_api_error_exception($exception);
            case $exception instanceof MissingDataException:
                return $this->handle_missing_data_exception($exception);
            case $exception instanceof MustBeLoggedInException:
                return $this->handle_must_be_logged_in_exception($exception);
            default:
                return $this->handle_general_api_exception($exception);
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Handles a general api exception
     *
     * @param Exception|Error $exception
     * @return string
     */
    public function handle_general_api_exception($exception)
    {
        $additional_dataArr = siteFunctions::get_env() == 'dev' ? ['exception' => $this->get_exceptionArr($exception)] : [];

        exit($this->respond_failure(configManager::$common_errorsArr['general_api'], null, $additional_dataArr));
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Handles a must be logged in exception
     *
     * @param Exception $exception
     * @return string
     */
    public function handle_must_be_logged_in_exception(Exception $exception)
    {
        exit($this->respond_must_be_logged_in());
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Handles a missing data exception
     *
     * @param Exception $exception
     * @return string
     */
    public function handle_missing_data_exception(Exception $exception)
    {
        exit($this->respond_missing_data());
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Handle an api error exception.
     *
     * @param \ApiErrorException $exception
     * @return string
     */
    public function handle_api_error_exception(ApiErrorException $exception)
    {
        exit($this->respond_failure($exception->get_error_id()));
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Returns exception array
     *
     * @param Exception $exception
     * @return array
     */
    private function get_exceptionArr($exception)
    {
        return [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];
    }

    /*----------------------------------------------------------------------------------*/
}