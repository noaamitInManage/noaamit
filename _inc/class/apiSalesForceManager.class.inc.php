<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 26/06/2017
 * Time: 13:05
 */
class apiSalesForceManager
{
    public $access_token = 'gi1NxqGOzDCTkOM5fE0FslqSJzWHAxmX';

    public $platform = '';
    public $application_version = '';
    public $udid = '';
    public $version = '';
    public $method_name = '';
    public $write_log = true;
    /**
     * For adding support for additional platform just add her to this array
     * the api call will have this value : http://www.inmanage.co.il/api/[PLATFORM_VALUE]/2.0/method_name
     */
    public $active_platformArr = array(
        'server',
    );
    /*
     * hold in this array the active version , If a version is not in this array you can not continue
     */
    public $active_versionArr = array(
        '1.0'
    );
    /**
     * The user can continue with this version but  he get a massage thet the version is deprecated and he should upgrade the version
     */
    public $deprecated_versionArr = array(//'1.1'
    );
    /**
     * Unsupported version the user can't continue if  his version in this array
     */
    public $not_supported_versionArr = array(

    );

    public $allowed_ipsArr = array();

    public $method_lock_by_ipArr = array();

    /* mobile application makeing cache on this method
            example:
            "method_name" => time in minute
            "getCities"=>60

            mobile app will save the data from getCities for 60 min

        */
    public $method_cachedArr = array(

    );


    /**
     * limit user call to this methods
     * prevent abusing the api
     *
     * [METHOD_NAME] => [LIMIT_NUMBER]
     */
    public $limit_methodArr = array(

    );

    public $limit_second = 600; //60 * 10

    public $familiar_ipsArr = array(
        '62.219.212.139', // Inmanage
        '81.218.173.175', // Inmanage
        '37.142.40.96', // Inmanage wifi
    );

    public $secure_token_request = true;

    public $exclude_secure_token_methodsArr = array(

    );

    private $ts = 0;

    /*----------------------------------------------------------------------------------*/
    /*
     *
     */
    function __construct($platform = '', $version = '', $method_name = '')
    {
        // TODO: REMOVE
        if ($method_name == "updateCompanyRooms-Opp") {
            $method_name = "updateCompanyRooms";
        }

        $this->version = $version;
        $this->platform = $platform;
        $this->method_name = trim($method_name);
        $this->ts = time();

        if (!$this->version_active()) {
            exit (json_encode(array("err" => "unvalid version", "status" => 0, "msg" => "גרסה לא פעילה"), JSON_FORCE_OBJECT));
        }
        if (!$this->platform_active()) {
            exit (json_encode(array("err" => "unvalid platform", "status" => 0, "msg" => "פלטפורמה לא נתמכת"), JSON_FORCE_OBJECT));
        } else if (!isset($_SESSION['api']['platform'])) {
            $_SESSION['api']['platform'] = $this->platform;
            $_SESSION['api']['version'] = $this->version;

        }


        if (in_array($this->method_name, $this->method_lock_by_ipArr)) {
            if (!in_array($_SERVER['REMOTE_ADDR'], $this->allowed_ipsArr)) {
                exit (json_encode(array("err" => "Unauthorized access", "status" => 0, "msg" => "Unauthorized access"), JSON_FORCE_OBJECT));
            }
        }

        if (!$this->check_method($this->method_name)) {
            exit (json_encode(array("err" => "unknown method", "status" => 0, "msg" => "מתודה לא קיימת"), JSON_FORCE_OBJECT));
        }

        if ((!in_array($_SERVER['REMOTE_ADDR'], $this->familiar_ipsArr)) && key_exists($this->method_name, $this->limit_methodArr)) {
            $_SESSION['api']['methods_limit'][$this->method_name][] = time();

            // ip abuse
            $first_call_in_allowed_time_by_ip = $this->write_lock_ip_record();
            if (($first_call_in_allowed_time_by_ip + $this->limit_second) > $this->ts) {
                exit(json_encode(array("err" => "abuse ", "status" => 0, "message" => "abuse "), JSON_FORCE_OBJECT));
            }

            // session abused
            if ((count($_SESSION['api']['methods_limit'][$this->method_name]) >= $this->limit_methodArr[$this->method_name])) {

                //after X min allow to cal this method
                $session_methods_limit = count($_SESSION['api']['methods_limit'][$this->method_name]);
                $first_call_in_allowed_time = $_SESSION['api']['methods_limit'][$this->method_name][$session_methods_limit - $this->limit_methodArr[$this->method_name]];

                if (($first_call_in_allowed_time + $this->limit_second) > $this->ts) {
                    exit(json_encode(array("err" => "abuse ", "status" => "", "message" => "abuse ")));
                }
            }
        }

        if ($this->secure_token_request === true && (!in_array($this->method_name, $this->exclude_secure_token_methodsArr))
            && !in_array($_SERVER['REMOTE_ADDR'], $this->familiar_ipsArr)
        ) {
            $access_token = isset($_POST['accessToken']) && $_POST['accessToken'] ? siteFunctions::safe_value($_POST['accessToken'], 'text') : '';
            if ($access_token != $this->access_token) {
                exit(json_encode(array("err" => errorManager::get_error(30), "status" => 0, "data" => array(), "message" => ""), JSON_FORCE_OBJECT)); // טוקן לא קיים. יש ליצור טוקן חדש
            }
        }

        if ($this->write_log) {
            $this->write_to_log();
        }
        if (!$_SESSION['lang']) {
            $_SESSION['lang'] = default_lang;
        }
        $this->media_server = 'http://' . $_SERVER['HTTP_HOST'];
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*------------------------------------lock ip functions ---------------------------------------*/
    protected function write_lock_ip_record()
    {
        return siteFunctions::write_salesforce_api_lock_ip_record($this->method_name, $this->limit_methodArr[$this->method_name], $this->ts);
    }

    /*----------------------------------------------------------------------------------*/
    public function check_method($method_name)
    {
        if (method_exists($this, $method_name)) {
            return true;
        } else {
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/
    /*
     *	 @example: $Api->execute('getStartUpMsg');
     * 	 @return value: {"id":2,"message":"..."}
     *
     */
    public function execute($method_override = '')
    {

        $method_name = ($method_override) ? $method_override : $this->method_name;
        if ($this->check_method($method_name)) {
            return $this->{$method_name}();
        } else {
            return (json_encode(array("err" => "unknown method", "status" => 0, "msg" => "מתודה לא קיימת"), JSON_FORCE_OBJECT));
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function version_active()
    {
        return in_array($this->version, $this->active_versionArr);
    }

    /*----------------------------------------------------------------------------------*/

    public function platform_active()
    {
        return in_array($this->platform, $this->active_platformArr);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @desc : save tracking on the user action and getting statistics on the api users,
     *            the data write to the table `tb_api_log` , to cancel the db write on can turn off
     *            the var {write_log}
     */
    public function write_to_log()
    {
        siteFunctions::write_to_salesforce_api_log($this->method_name, $this->platform, $this->version);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method : validateVersion
     *
     *
     * Check if version is validate
     *
     *
     * link: http://dev.meshulam.co.il/api/iphone/1.0/validateVersion
     * @expect_values : $_POST['not']); Option int (example : 1)
     *
     *
     * @link : http://dev.meshulam.co.il/api/iphone/1.0/validateVersion
     * @return Json
     * @example:
     *
     * Case 1 (error):
     * {
     * err: "Error",
     * status: 0,
     * data; "",
     * }
     * @example:
     *    Case 2 (success):
     * {"status":1,"err":"","data": { "versionState":"not supported", "appStoreUrl":"http://www.google.com","message":"על מנת להמשיך להשתמש באפליקציה עליך לשדרגה כעת. הגרסה שברשותך לא פעילה"} }
     */
    public function validateVersion()
    {
        if (in_array($this->version, $this->active_versionArr)) {
            $data = array(
                "versionState" => 'valid'
            );
            return (json_encode(array("status" => 1, "err" => array(), "data" => $data, "message" => ""), JSON_FORCE_OBJECT));
        } else if (in_array($this->version, $this->deprecated_versionArr)) {
            $data = array(
                "versionState" => 'deprecated',
                'appStoreUrl' => $this->store_link[$this->platform],
                'message' => lang('version_lock_deprecated_message'),
            );
            return (json_encode(array("status" => 1, "err" => array(), "data" => $data, "message" => ""), JSON_FORCE_OBJECT));
        } else if (in_array($this->version, $this->not_supported_versionArr)) {
            $data = array(
                "versionState" => 'not supported',
                'appStoreUrl' => $this->store_link[$this->platform],
                'message' => lang('version_lock_not_supported_message'),
            );
            return (json_encode(array("status" => 1, "err" => array(), "data" => $data, "message" => ""), JSON_FORCE_OBJECT));
        } else {
            $data = array(
                "versionState" => 'Unknown version',
            );
            return (json_encode(array("status" => 0, "err" => array(), "data" => $data, "message" => ""), JSON_FORCE_OBJECT));
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method : getHostUrl
     *
     *
     * get the main server url - all the method need to call to this url
     *
     *
     * link: http://api.mcdonalds.co.il/api/iphone/1.0/getHostUrl/
     * @expect_values :
     *    null
     * @link : http://api.mcdonalds.co.il/api/iphone/1.0/getHostUrl/
     * @return Json
     * @example:
     *
     * Case 1 (error):
     * {
     * err: "unvalid param",
     * status: 0,
     * msg: "קלט לא תקין"
     * }
     * @example:
     *    Case 2 (success):
     *
     * err: "",
     * status: 1,
     * url: "http://api.mcdonlads.co.il,
     *
     * }
     */
    public function getHostUrl()
    {
        global $project_live_url,
               $project_dev_url;

        $url = '';
        $dataArr = array();

        switch ($this->version) {
            case('1.0'):
            default:
                $url = $project_live_url;
                break;
        }

        $dataArr = array(
            "url" => "http://{$url}",
            "color" => siteFunctions::get_environment_color($url),

        );

        return (json_encode(
            array(
                "err" => '',
                "status" => 1,
                "data" => $dataArr,
            )
        ));

    }

    /*----------------------------------------------------------------------------------*/

    public function addUser()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $userArr) {
            $result = User::salesforce_add_user($userArr);
            if (!$result) {
                $failedArr[] = $userArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function updateUser()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $userArr) {
            $result = User::salesforce_update_user($userArr);
            if (!$result) {
                $failedArr[] = $userArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function deleteUser()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $userArr) {
            $result = User::salesforce_delete_user($userArr);
            if (!$result) {
                $failedArr[] = $userArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function addCompany()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();
        file_put_contents(_project_server_path . '/daniel_logs.txt', print_r(array($data), true) . PHP_EOL, FILE_APPEND | LOCK_EX);

        $failedArr = array();
        foreach ($data as $companyArr) {
            $result = companiesManager::salesforce_add_company($companyArr);
            if (!$result) {
                $failedArr[] = $companyArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function updateCompany()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $companyArr) {
            $result = companiesManager::salesforce_update_company($companyArr);
            if (!$result) {
                $failedArr[] = $companyArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function deleteCompany()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $companyArr) {
            $result = companiesManager::salesforce_delete_company($companyArr);
            if (!$result) {
                $failedArr[] = $companyArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function addMeetingRoom()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $meeting_roomArr) {
            $result = meetingRoomsManager::salesforce_add_meeting_room($meeting_roomArr);
            if (!$result) {
                $failedArr[] = $meeting_roomArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function updateMeetingRoom()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $meeting_roomArr) {
            $result = meetingRoomsManager::salesforce_update_meeting_room($meeting_roomArr);
            if (!$result) {
                $failedArr[] = $meeting_roomArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function deleteMeetingRoom()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        $failedArr = array();
        foreach ($data as $meeting_roomArr) {
            $result = meetingRoomsManager::salesforce_delete_meeting_room($meeting_roomArr);
            if (!$result) {
                $failedArr[] = $meeting_roomArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function updateCompanyRooms()
    {
        $data = isset($_REQUEST['data']) ? json_decode(urldecode($_REQUEST['data']), true) : array();

        if (!$data['AccountId'] || !$data['Rooms']) {
            return (json_encode(array("err" => errorManager::get_error(152), "status" => 0, "message" => "", "data" => ""))); // Required data missing
        }

        $Company = companiesManager::get_by_salesforce_id($data['AccountId']);
        if (!$Company->id) {
            return (json_encode(array("err" => errorManager::get_error(230), "status" => 0, "message" => "", "data" => ""))); // Company doesn't exist
        }

        $failedArr = array();
        foreach ($data['Rooms'] as $roomArr) {
            $result = roomsManager::salesforce_update_room($roomArr, $Company->id);
            if (!$result) {
                $failedArr[] = $roomArr['Id'];
            }
        }

        $status = 1;
        $answerArr = array();

        if (count($failedArr)) {
            $status = 2;
            $answerArr = array(
                'failed' => $failedArr,
            );
        }

        return (json_encode(
            array(
                "err" => array(),
                "status" => $status,
                "data" => $answerArr,
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/

    public function sendErrorReport()
    {
        $method = isset($_REQUEST['method']) && $_REQUEST['method'] ? siteFunctions::safe_value($_REQUEST['method'], 'text') : '';
        $request = isset($_REQUEST['request']) && $_REQUEST['request'] ? $_REQUEST['request'] : '';
        $serverResponse = isset($_REQUEST['serverResponse']) && $_REQUEST['serverResponse'] ? $_REQUEST['serverResponse'] : '';

        siteFunctions::write_salesforce_api_error_report($method, $request, $serverResponse);

        return (json_encode(
            array(
                "err" => array(),
                "status" => 1,
                "data" => array(),
            ), JSON_FORCE_OBJECT
        ));
    }

    /*----------------------------------------------------------------------------------*/
}