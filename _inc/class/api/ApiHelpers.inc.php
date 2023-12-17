<?php

/**
 * Trait ApiHelpers
 *
 * This trait contains helper methods to be used in any API class.
 */
trait ApiHelpers
{
    /*----------------------------------------------------------------------------------*/
    /**
     * Returns a parameter from the request
     *
     * @param $key
     * @param $type
     * @param null $fallback_value
     * @return mixed
     */
    public function get_request_parameter($key, $type, $fallback_value = null)
    {
        return isset($_REQUEST[$key]) ? siteFunctions::safe_value($_REQUEST[$key], $type) : $fallback_value;
    }

    /*----------------------------------------------------------------------------------*/

	/**
	 * Returns a file from the request
	 *
	 * @param $key
	 * @param $extArr
	 * @param null $fallback_value
	 * @return mixed
	 */
    public function get_request_file($key, $extArr = null, $fallback_value = null)
    {
        return isset($_FILES[$key]) ?  siteFunctions::safe_value($_FILES[$key], 'file', $extArr) : $fallback_value;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Checks for missing data
     *
     * @param $conditionsArr
     * @throws MissingDataException
     */
    public function check_missing_data($conditionsArr)
    {
        foreach ($conditionsArr as $condition) {
            if (!$condition) {
                throw new MissingDataException();
            }
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Checks for an authenticated user
     *
     * @throws MustBeLoggedInException
     */
    public function check_auth()
    {
        $User = User::getInstance();

        if (!$User->id) {
            throw new MustBeLoggedInException();
        }
    }

    /*----------------------------------------------------------------------------------*/
}