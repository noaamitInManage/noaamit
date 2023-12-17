<?php

/**
 * Created by JetBrains PhpStorm.
 * @User: gal
 * @Date: 24/06/13
 * @Time: 14:33
 * To change this template use File | Settings | File Templates.
 *
 * @version : 1.0
 *
 *
 * @example: $Api = new apiManager($platform,$version,$method_name); // first call will run the method
 *                    if we need to run more method in this script we can call to :
 *             $Api->execute('some_method');
 * @template method:
 *                    public function getTemplateFunction(){
 *                        $item_id = intval($_POST['id']);
 *                        if(!$item_id){
 *                            return (json_encode(array("err"=>"unvalid param","status"=>"","msg"=>"קלט לא תקין")));
 *                        }
 *                        switch($this->version){
 *                        case '1.0':
 *                        default:
 *                            $Template_controller = new Template_controllerManager($item_id);
 *                            return (json_encode(array("err"=>"","status"=>"1","msg"=>$Template_controller->content)));
 *                        break;
 *
 *                        }
 *                    }
 * @Last_modified:
 */
class apiManager extends BaseApiManager
{
    const LOG_TABLE = 'tb_api_log';

    public $log_id = 0;
    public $platform = '';
    public $application_version = '';
    public $udid = '';
    public $version = '';
    public $method_name = '';
    public $media_server = ''; // application images load image from this url
    /**
     * For adding support for additional platform just add her to this array
     * the api call will have this value : http://www.inmanage.co.il/api/[PLATFORM_VALUE]/2.0/method_name
     */
    public $active_platformArr = array(
        'iphone',
        'android',
        'website',
        'server',
        //'wmobile',
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
    public $deprecated_versionArr = array(//        '1.0',
    );
    /**
     * Unsupported version the user can't continue if  his version in this array
     */
    public $not_supported_versionArr = array(//        '1.0'
    );
    /**
     * @important !!!
     * In the case of deprecated / Unsupported version the user get  massage with referral to the store , each platform must to hold the link to the store !!!
     */
    public $store_link = array(
        "iphone" => 'http://apple.com',
        "android" => 'http://play.google.com/store/apps/details?id=il.co.pais.android',
        "wmobile" => 'http://play.google.com/store/apps/details?id=il.co.pais.android',
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
        "getCities" => 10080, //60 * 24 * 7
        "getStores" => 60, //60 * 24 * 7
    );


    /**
     * limit user call to this methods
     * prevent abusing the api
     *
     * [METHOD_NAME] => [LIMIT_NUMBER]
     */
    public $limit_methodArr = array(
        "sendContact" => 3,
        "getSmsLoginToken" => 5,
        "loginWithSmToken" => 5,
    );

    public $limit_second = 600; //60 * 10

    public $familiar_ipsArr = array(
        '62.219.212.139', // Inmanage
        '81.218.173.175', // Inmanage
        '37.142.40.96', // Inmanage wifi
        '207.232.22.164', // Inmanage
    );

    public $secure_token_request = true;

    public $exclude_secure_token_methodsArr = array(
        "getHostUrl",
        "clearSession",
        "applicationToken",
    );
	public static $GET_methods = array (
		'validateVersion',
		'getContentPage',
		'generalDeclaration',
		'getHostUrl',
	);
    /**
     * Methods that should be called with a GET HTTP method
     * @var array
     */
    public $get_methodsArr = [
        //
    ];

    protected $ts = 0;

    /*----------------------------------------------------------------------------------*/
    /*
     *
     */
    function __construct($platform = '', $version = '', $method_name = '')
    {
        $this->version = $version;
        $this->platform = $platform;
        $this->method_name = trim($method_name);
        $this->ts = time();

        if (!$this->version_active()) {
            exit ($this->build_response(0, 'גרסה לא פעילה'));
        }

        if (!$this->platform_active()) {
            exit ($this->build_response(0, 'פלטפורמה לא נתמכת'));
        }

        if (!isset($_SESSION['api']['platform'])) {
            $_SESSION['api']['platform'] = $this->platform;
            $_SESSION['api']['version'] = $this->version;

        }


        if (in_array($this->method_name, $this->method_lock_by_ipArr) && !in_array($_SERVER['REMOTE_ADDR'], $this->allowed_ipsArr)) {
            exit ($this->build_response(0, 'Unauthorized access'));
        }

        if (!$this->check_method($this->method_name)) {
            exit ($this->build_response(0, 'מתודה לא קיימת'));
        }

        if ((!in_array($_SERVER['REMOTE_ADDR'], $this->familiar_ipsArr)) && key_exists($this->method_name, $this->limit_methodArr)) {
            $_SESSION['api']['methods_limit'][$this->method_name][] = time();

            // ip abuse
            $first_call_in_allowed_time_by_ip = $this->write_lock_ip_record();
            if (($first_call_in_allowed_time_by_ip + $this->limit_second) > $this->ts) {
                exit ($this->build_response(0, 'abuse'));
            }

            // session abused
            if ((count($_SESSION['api']['methods_limit'][$this->method_name]) >= $this->limit_methodArr[$this->method_name])) {

                //after X min allow to cal this method
                $session_methods_limit = count($_SESSION['api']['methods_limit'][$this->method_name]);
                $first_call_in_allowed_time = $_SESSION['api']['methods_limit'][$this->method_name][$session_methods_limit - $this->limit_methodArr[$this->method_name]];

                if (($first_call_in_allowed_time + $this->limit_second) > $this->ts) {
                    exit ($this->build_response(0, 'abuse'));
                }
            }
        }

        if ($this->secure_token_request === true && (!in_array($this->method_name, $this->exclude_secure_token_methodsArr))
            && !in_array($_SERVER['REMOTE_ADDR'], $this->familiar_ipsArr)
        ) {
            $validate_token = secureToken::validate_token();
            if ($validate_token === false) {
                exit ($this->build_response(0, 30)); // טוקן לא קיים. יש ליצור טוקן חדש
            }
        }
		$idfa = isset($_REQUEST['idfa']) ? ($_REQUEST['idfa']) : '';
		if ($idfa != '') {
			$_SESSION['idfa'] = $idfa;
		}

        $this->write_to_log();

        if (!$_SESSION['lang']) {
            $_SESSION['lang'] = default_lang;
        }
        $this->media_server = 'https://' . $_SERVER['HTTP_HOST'];
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
        return siteFunctions::write_api_lock_ip_record($this->method_name, $this->limit_methodArr[$this->method_name], $this->ts);
    }

    /*----------------------------------------------------------------------------------*/
    public function check_method($method_name)
    {
        if (method_exists($this, $method_name)) {
            return true;
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /*
     *	 @example: $Api->execute('getStartUpMsg');
     * 	 @return value: {"id":2,"message":"..."}
     *
     */
    public function execute($method_override = '')
    {
        $method_name = !empty($method_override) ? $method_override : $this->method_name;
        if ($this->check_method($method_name)) {
            $response = $this->{$method_name}();
            $this->update_log($response);
            return $response;
        }

        return ($this->build_response(0, 'מתודה לא קיימת'));
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @param int $status  1/0
     * @param int|string $err
     * @param string|array $data
     * @param string $message
     * @return false|string
     */
    public function build_response($status, $err = 0, $data = '', $message = '')
    {
        if (!empty($err)) {
            $err = (is_string($err)) ? $err : errorManager::get_error($err);
        }
        return json_encode(
            array("status" =>$status,
                "err" => $err, //errorManager::get_error(152)
                "data" => $data,
                "message" => $message
            )
        );
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
    public function write_to_log($start_ts = 0)
    {

        if (!featureFlagManager::get('write_to_api_log')) {
            return false;
        }

        $session_id = session_id();
        $Browser = new Browser();
        $Db = Database::getInstance();
        $db_fields = array(
            "method_name" => $this->method_name,
            "platform" => $this->platform,
            "version" => $this->version,
            "device" => $Browser->getPlatform(),
            "device_version" => $Browser->getVersion(),
            "user_agent" => $Browser->getUserAgent(),
            "session_id" => $session_id,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "SERVER_ADDR" => $_SERVER['SERVER_ADDR'],
            "REQUEST_METHOD" => $_SERVER['REQUEST_METHOD'],
            'user_id' => (isset($_SESSION['user_id']) && $_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
            'client_name' => explode(".", $_SERVER['HTTP_HOST'], 2)[0],
            "insert_ts" => $start_ts,
            "last_update" => time(),
        );
        if (featureFlagManager::get("log_api_request_data")) {
            $db_fields['request'] = !empty($_REQUEST) ? json_encode($_REQUEST) : '';
        }
        foreach ($db_fields as $key => $value) {
            $db_fields[$key] = $Db->make_escape($value);
        }
        $this->log_id = $Db->insert(self::LOG_TABLE, $db_fields);
        return true;

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @desc : save tracking on the user action and getting statistics on the api users,
     *            the data write to the table `tb_api_log` , to cancel the db write on can turn off
     *            the var {write_log}
     */
    public function update_log($response)
    {
        if (!featureFlagManager::get("write_to_api_log") || !featureFlagManager::get("log_api_response_data")) {
            return false;
        }

        $Db = Database::getInstance();
        $db_fields = array(
            "response" => json_encode($response),
        );
        foreach ($db_fields as $key => $value) {
            $db_fields[$key] = $Db->make_escape($value);
        }
        if (is_int($this->log_id) && $this->log_id > 0) {
            $Db->update(self::LOG_TABLE, $db_fields, 'id', '=', $this->log_id);
        }
        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method : validateVersion
     *
     *
     * Check if version is validate
     *
     *
     * link: http://dev.meshulam.co.il/api/{platform}/1.0/validateVersion
     * @expect_values : $_POST['not']); Option int (example : 1)
     *
     *
     * @link : http://dev.meshulam.co.il/api/{platform}/1.0/validateVersion
     * @return false|Json|string
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

            return $this->build_response(1, 0, $data);
        }

        if (in_array($this->version, $this->deprecated_versionArr)) {
            $data = array(
                "versionState" => 'deprecated',
                'appStoreUrl' => $this->store_link[$this->platform],
                'message' => lang('version_lock_deprecated_message'),
            );

            return $this->build_response(1, 0, $data);
        }

        if (in_array($this->version, $this->not_supported_versionArr)) {
            $data = array(
                "versionState" => 'not supported',
                'appStoreUrl' => $this->store_link[$this->platform],
                'message' => lang('version_lock_not_supported_message'),
            );

            return $this->build_response(1, 0, $data);
        }

        $data = array(
            "versionState" => 'Unknown version',
        );

        return $this->build_response(1, 0, $data);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: setSettings
     *
     * set app const params
     *
     * url : http://api.{domain}.co.il/api/{platform}/1.0/setSettings/
     *
     * @expect_values : $_POST['lang_id']); Required int (example : 1)
     *                $_POST['lang']); Required string  (example : he)
     *                $_POST['resolution']);  Required string  (example :iphone3, iphone4, iphone5, hdpi,  xhdpi,  xxhdpi ,tablet)
     *                $_POST['application_version']);  Required string  (app version )
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * err: "unvalid param",
     * status: 0,
     * msg: "אין נתונים"
     * }
     * @example: Case 2 (success):
     * {
     * •    err: "",
     * •    status: 1,
     * •    store_id: 22
     * }
     */
    public function setSettings()
    {
        $lang_id = isset($_REQUEST['lang_id']) ? ($_REQUEST['lang_id']) : '';
        $lang = isset($_REQUEST['lang']) ? ($_REQUEST['lang']) : '';
        $email = isset($_REQUEST['email']) ? ($_REQUEST['email']) : '';
        $udid = isset($_REQUEST['udid']) ? ($_REQUEST['udid']) : '';
        $resolution = isset($_REQUEST['resolution']) ? ($_REQUEST['resolution']) : '';
        $application_version = isset($_REQUEST['application_version']) ? ($_REQUEST['application_version']) : '';

        $valid = true;

        $expected_paramsArr = array(
            'resolution',
            //	'email',
            'udid',
            'lang_id',
            'lang',

        );
        if (isset($application_version) && $application_version) {
            $this->application_version = $_SESSION['api']['application_version'] = strtolower($application_version);
        }

        if (isset($udid) && $udid) {
            $this->udid = $_SESSION['api']['udid'] = strtolower($udid);
        }

        foreach ($expected_paramsArr AS $key => $value) {
            if (!${$value}) {
                return $this->build_response(0, 152);
            }
        }
        if (isset($resolution) && $resolution) {
            $this->resolution = $_SESSION['api']['resolution'] = strtolower($resolution);
        }

        // startup messages
        $startup_messagesArr = [];
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/startup_messages.' . $_SESSION['lang'] . '.inc.php'); //$startup_messagesArr
        $startup_message = "";
        if (count($startup_messagesArr)) {
            $startup_message = reset($startup_messagesArr);
        }

        $get_methodsArr = siteFunctions::get_GET_methodsArr();
        $this->get_methodsArr = array_merge($this->get_methodsArr, $get_methodsArr);

        $data = [];
        switch ($this->version) {
            case '1.0':
            default:
                foreach ($expected_paramsArr AS $key => $value) {
                    $_SESSION[$value] = ${$value};
                    $data[$value] = ${$value};
                }
		        if(in_array($_SERVER['REMOTE_ADDR'],array('207.232.22.164', '62.219.212.139','81.218.173.175', "37.142.40.96"))) {
			        $data['familiar_ip'] = 'familiar_ip=true';
		        }
                $data["server_time"] = (isset($_REQUEST['serverTs']) && $_REQUEST['serverTs']) ? (int)$_REQUEST['serverTs'] : $this->ts;

        }
        return $this->build_response(1, 0, $data, $startup_message);


    }
    /*----------------------------------------------------------------------------------*/
    /**
     * Method: clearSession
     *
     * clear user session ( mobile app use this function when the app more then 20 min in background )
     *
     * url : http://api.{domain}.co.il/api/{platform}/1.0/clearSession/
     *
     * @expect_values : null
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * err: " unvalid user",
     * status: 0,
     * }
     * Case 2 (success):
     *
     *
     * {
     * "err":"",
     * "status": 1,
     *
     * }
     */
    public function clearSession()
    {

        $sess_api = $_SESSION['api'];
        session_destroy();
        session_start();
        $_SESSION['api'] = $sess_api;

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1);
        }
    }

    /*----------------------------------------------------------------------------------*/
    public function ping()
    {
        return $this->build_response(1);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * Method : getContentPage
     *
     * get content pages from the server
     *
     * link: http://www.salat.com/api/{platform}/1.0/getContentPage/
     * @expect_values :
     *    $_POST['pageId']) Required int
     * @return false|Json|string
     * @example:
     *
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     * @example:
     *    Case 2 (success):
     * {
     * "err": "",
     * "status": 1,
     * "data": {
     * "id": "30",
     * "title": "עמוד בדיקה 2",
     * "content": "<p>\r\n\tעמוד בדיקה</p>\r\n",
     * "url": "http://apiurl.co.il/salat2/frames.php",
     * "last_update": "1451197718"
     * }
     * }
     */
    public function getContentPage()
    {
        $page_id = (int)$_REQUEST['pageId'];

        if (!$page_id) {
            return $this->build_response(1, 152);
        }
        $Content = new contentManager($page_id);
        switch ($this->version) {
            case '1.0':
            default:
                $data = $Content;
                break;
        }
        return $this->build_response(1, 0, $data);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     *
     * Method: registerPushNotification
     *
     *
     *
     * url : http://api.{domain}.co.il/api/{platform}/1.0/registerPushNotification/
     *
     * @expect_values :
     *    $_POST['deviceToken']); Required (string)
     *  $_POST['udid']); Required (string)
     * @return false|Json|string
     * @example: Case 1 (error):
     * {
     * err: "unvalid param",
     * status: 0,
     * msg: "ההרשמה נכשלה"
     * }
     * @example:
     * Case 2 (success):
     * {
     * •    err: "",
     * •    status: 1,
     * }
     */
    public function registerPushNotification()
    {

        $device_token = !empty($_REQUEST['deviceToken']) ?: '';
        $udid = !empty($_REQUEST['udid']) ?: '';

        switch ($this->version) {
            case '1.0':
            default:
                $User = User::getInstance();
                if ($device_token && $udid) {
                    $status = siteFunctions::register_push_notification($this->platform, $device_token, $udid, $User->id);
                } else {
                    return $this->build_response(0, 152);
                }
                break;
        }
        return $this->build_response($status);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: getStartupSplash
     *
     *
     * get startup images from the server , run it on the app start
     *
     *
     * url : http://aroma.inmanage.com/api/{platform}/1.0/getStartupSplash/
     *
     * @expect_values : null
     *
     * @example: Case 1 (error):
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     * "image": "/_media/splash/2-iphone5.png"
     * },
     * "message": ""
     * }
     *
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     * "image": ""
     * },
     * "message": ""
     * }
     */
    public function getStartupSplash()
    {
        $splashsArr = [];
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/splashs.' . $_SESSION['lang'] . '.inc.php'); //$splashsArr

        if (count($splashsArr) == 0) {
            return $this->build_response(1, 0, ["image" => '']);
        }

        $first = reset(array_keys($splashsArr));
        $Splash = new splashManager($first, $_SESSION["api"]["resolution"]);

        if (!$Splash->image) {
            return $this->build_response(1, 0, ["image" => '']);
        }

        switch ($this->version) {
            case '1.0':
            default:
                $data = ["image" => $Splash->image];
        }
        return $this->build_response(1, 0, $data);
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * Method: generalDeclaration
     *
     * get application general declaration , You must call this method at any app's first  operating , add keep the data in the device
     *
     *
     *    url : http://tran.{domain}.co.il/api/{platform}/1.0/generalDeclaration/
     * @expect_values :
     *    null
     * @return false|Json|string
     * @example:
     * Case 2 (success):
     * {
     * •    err: "",
     * •    status: 1,
     * •    data:
     * {
     * o    languagesArr:
     * [
     *     {
     *     active: "1",
     *     id: "1",
     *     title: "he",
     *     description: "עברית",
     *     direction: "2"
     * },
     *     {
     *     active: "1",
     *     id: "2",
     *     title: "en",
     *     description: "אנגלית",
     *     direction: "1"
     * }
     * ],
     * o    subjectArr:
     * {
     *     1: "נושא א",
     *     2: "נושא ב",
     *     3: "נושא ג",
     *     4: "נושא ד"
     * },
     * o    featuresArr: null,
     * o    cachedArr:
     * [
     *     {
     *     method_name: "getCities",
     *     cache_time: 10080
     * },
     *     {
     *     method_name: "getStores",
     *     cache_time: 60
     * }
     * ],
     * o    pickup_channelArr:
     * {
     *     0: "active",
     *     1: "takeaway is not active ",
     *     2: "sit in the store not active ",
     *     4: "drive is not active",
     *     8: "delivery is not active"
     * },
     * o    media_server: "http://tran.{domain}.co.il/",
     * o    server_time: 1394434146,
     * o    media_file:
     * {
     *     path: "/_static/media_zip/iphone.zip",
     * check_sum: "77e48e5f5988acab2e8db64226267e02"
     * 
     * }
     * }
     * }
     **/
    public function generalDeclaration()
    {

        $general_declarationArr = siteFunctions::get_general_declaration($this);
        switch ($this->version) {
            case '1.0':
            default:
                $data = $general_declarationArr;
        }
        return $this->build_response(1, 0, $data);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method : getHostUrl
     *
     *
     * get the main server url - all the method need to call to this url
     *
     *
     * link: http://api.{domain}.co.il/api/{platform}/1.0/getHostUrl/
     * @expect_values :
     *    null
     * @link : http://api.{domain}.co.il/api/{platform}/1.0/getHostUrl/
     * @return false|Json|string
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
     * url: "http://api.{domain}.co.il,
     *
     * }
     */
    public function getHostUrl()
    {
        global $project_live_url,
               $project_dev_url,
			   $project_cdn_api;

        switch ($this->version) {
            case('1.0'):
            default:
                $url = $project_live_url;
                break;
        }

        $dataArr = [
            "url" => "https://{$url}",
            "color" => siteFunctions::get_environment_color($url),
            "get_methods" => siteFunctions::get_GET_methodsArr(),
            'get_url' => configManager::is_dev_mode() ? "https://{$project_dev_url}" : "https://{$project_cdn_api}"
        ];

        return $this->build_response(1, 0, $dataArr);

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: errorReport
     *
     * Crash report , before application crashes send the log to this method
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/errorReport/
     *
     * @expect_values :
     *  $_POST ['content'] Required string
     *
     * @return false|Json|string
     * @example:
     * {
     * err: "",
     * status: 1
     * }
     */

    public function errorReport()
    {
        $content = $_REQUEST['content'] ?? '';

        siteFunctions::errorReport($content);

        switch ($this->version) {
            case '1.0':
            default:
        }
        return $this->build_response(1);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * setLang method
     *
     *
     * set application language
     *
     * url  : http://api.{domain}.co.il/api/{platform}/1.0/setLang/
     *
     *
     * @expect_values :
     * - $_POST['lang_id']); Required int (example :1 )
     * - $_POST['lang']); Required string  (example :he )
     *
     * @return false|Json|string
     * @example:    {err: "",status: 1}
     */
    public function setLang()
    {
        $lang_id = ($_REQUEST['lang_id']) ?: '';
        $lang = ($_REQUEST['lang']) ?: '';
        if ((!$lang_id) || (!$lang)) {
            return $this->build_response(0, 152);
        }

        $_SESSION['lang_id'] = $lang_id;
        $_SESSION['lang'] = $lang;
        $dataArr = array(
            "lang_id" => $lang_id,
            "lang" => $lang,
        );
        return $this->build_response(1, 0, $dataArr);

    }

    /*----------------------------------------------------------------------------------*/
    /**
     *
     * Method: doPayment
     *
     *
     *  mcdonald's payment method
     *  the credit card and the paypal options will open a webview to the customer for fillup the payment details
     *  the cash method will return url that identical to the end (and success) for the first 2 process
     *  you need to identify that the url contain the "/success/" string and to pull&parse with regex the json that  is existing
     *  in the end of the string (start with json=)
     *
     *
     *  if the url contain that the url contain the "/failure/" OR "ErrorCode=" string we need to make alert with the content value and send the user to cart page
     * https://tran.{domain}.co.il/resource/payment/cg/failure/?json={"id":500,"content":"\u05ea\u05e7\u05dc\u05d4 \u05d1\u05ea\u05d4\u05dc\u05d9\u05da \u05d4\u05ea\u05e9\u05dc\u05d5\u05dd , \u05d0\u05e0\u05d0 \u05d1\u05d7\u05e8 \u05d0\u05de\u05e6\u05e2\u05d9 \u05ea\u05e9\u05dc\u05d5\u05dd \u05d7\u05d3\u05e9"}
     *
     * url : http://api.{domain}.co.il/api/{platform}/1.0/doPayment/
     *
     * @expect_values :
     *  $_POST['paymentId']); Required int (example : 65 ) (options : 1.credit card , 2. PayPal , 4.cash)
     *  $_POST['tokenId']); Not Required int (example : 65 ) (example : 1 , you can get the token list in loginUser method in user_cardArr Array) send this param only on paymentId =1 and user re-use the card
     *
     *
     *
     * order status options :
     *
     * $order_statusArr = array(
     * 1 => "open",    // order open - we have a record in tb_order , for sending macdonald's this id /
     * 2 => "pending", // order insert into macdonald's server in pending mode                        /
     * 3 => "make",    // order set to make mode                                                       /
     * 4 => "cancel",  // user cancel the order                                                       /
     * 5 => "close",   // user take the meal from the restaurant                                       /
     * 6 => "fail",    // didn't get order id from macdonald's server                                   /
     * );
     * @return Json
     * @example:Case 1 (error):
     * {
     * err: "unvalid param",
     * status: 0,
     * msg: "קלט לא תקין"
     * }
     * @example:
     * Case 2 (success): if payment id = 4 || order success you will get this url
     * {
     * err: "",
     * status: 1,
     * url: "http://api.{domain}.co.il/resource/payment/cash/success/?json={"prepare_time":5,"order_status":2,"order_id":22,"local_order_id":15}"
     * }
     * }
     */


    public function doPayment()
    {
        global $debugStoresArr;
        $payment_id = isset($_REQUEST['paymentId']) ? intval(trim($_REQUEST['paymentId'])) : '';
        $token_id = (isset($_REQUEST['tokenId']) && ($_REQUEST['tokenId'])) ? intval(trim($_REQUEST['tokenId'])) : '';
        global $languagesArr;
        if (!$payment_id) {
            return (json_encode(array("err" => 'unvalid param', "status" => 0)));
        }

        $Cart = Cart::getInstance();

        $Menu = new menuManager($Cart->get_store());

        $Branch = new branchManager($Cart->get_store());
        if (($_SERVER['HTTP_HOST'] == "inmanage.mcdonalds.co.il") || ($_SERVER['HTTP_HOST'] == "dev.mcdonalds.co.il")) {
            if (!in_array($Branch->StoreIndex, $debugStoresArr)) {
                return (json_encode(array("err" => errorManager::get_error(1050), "status" => 0)));

            }
        }
        /*if(!$Branch->branch_is_open()){
            return(json_encode(array("err"=>errorManager::get_error(600),"status"=>0)));
        }*/

        $User = User::getInstance();

        // duplicate check

        $Branch = new branchManager($Cart->get_store());
        if (!$Branch->branch_is_open()) {
            return (json_encode(array("err" => errorManager::get_error(600), "status" => 0)));
        }
        if (!$User->id) {
            return (json_encode(array("err" => errorManager::get_error(281), "status" => 0))); // only login user can add to favorites
        }

        $cartArr = $Cart->get_cart(0, 0, 1);
        if ($cartArr['total_items'] == 0) { // check if payment method is valid in this store
            return (json_encode(array("err" => errorManager::get_error(219), "status" => 0)));
        }

        if (!$Menu->payment_is_valid($payment_id)) { // check if payment method is valid in this store
            return (json_encode(array("err" => errorManager::get_error(210), "status" => 0)));
        }

        if (!$Menu->payment_method_limit($payment_id)) { // check if payment method is valid in this store
            return (json_encode(array("err" => errorManager::get_error(212), "status" => 0)));
        }
        if (mcdonaldsManager::get_pickup_channel() == 4) {
            if (!$Branch->enable_delivery() || !($Branch->ActiveStatus == 0)) {
                return (json_encode(array("err" => errorManager::get_error(601), "status" => 0)));
            }
        }

        if ($duplicateArr = mcdonaldsManager::duplicate_check()) {
            return (json_encode(array(
                "err" => '',
                "status" => 1,
                "mode" => "duplicate_order",
                "order_id" => $duplicateArr["order_id"],
                "prepare_time" => $duplicateArr["prepare_time"],
                "order_status" => $duplicateArr["status"],
            )));

        }

        //	if($User->id==1){
        $net_total = str_replace(',', '', $cartArr['total']);
        if ($net_total > $Cart->max_allowed_payment_in_app) {
            return (json_encode(array("err" => errorManager::get_error(221), "status" => 0)));

        }
        //	}
        /*if($payment_id==2){ // paypal
            if($User->id!=1){
                $errArr=errorManager::get_error(213);
                $errArr['content']='מצטערים אך עקב עבודות שדרוג לא ניתן לשלם בשיטת תשלום זו כרגע , אנא השתמש בשיטה אחרת.';
                return(json_encode(array("err"=>$errArr,"status"=>0)));
            }
        }*/

        if ($token_id) {
            switch ($payment_id) {
                case 1: // credit card (cg)
                    $Cg = new credit_guardManager();
                    if (!$Cg->is_user_token($token_id, $User->id)) {
                        return (json_encode(array("err" => errorManager::get_error(213), "status" => 0)));
                    }
                    break;
                case 2: // PAYPAL
                    $PaypalObj = new PaypalManager();
                    if (!$PaypalObj->is_user_token($token_id, $User->id)) {
                        return (json_encode(array("err" => errorManager::get_error(215), "status" => 0)));
                    }
                    break;
            }

        }

        /*if($User->id==397){//idan dr
            return(json_encode(array("err"=>errorManager::get_error(215),"status"=>0)));
        }*/

        if (($this->version > '1.0') && ($payment_id == 4) && ($coupons_and_benefitArr = $Cart->get_cart_coupon_and_compensation_items())) { // dont allow to pay in cash if user use coupon&benefit
            return (json_encode(array("err" => "", "status" => 0, "item_to_removeArr" => $coupons_and_benefitArr)));
        } else if (($this->version == '1.0') && ($payment_id == 4) && ($coupons_and_benefitArr = $Cart->get_cart_coupon_and_compensation_items())) {
            return (json_encode(array("err" => errorManager::get_error(222), "status" => 0)));

        }

        $answerArr = $Cart->do_payment($payment_id, $token_id);

        //	mail('gal@inmanage.co.il','reason - '.__FILE__,print_r(array($answerArr,__FILE__,__LINE__,__CLASS__,__METHOD__,__FUNCTION__),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        if ($answerArr['error'] && $answerArr['reason']) {
            $errArr = errorManager::get_error($answerArr['error']);
            $errArr['content'] = $answerArr['reason'];
            return (json_encode(array("err" => $errArr, "status" => 0)));
        }

        //mail('gal@inmanage.co.il','mail - '.__FILE__,print_r(array($answerArr,__FILE__,__LINE__,__CLASS__,__METHOD__,__FUNCTION__),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        if ($answerArr['error']) { // order fail
            return (json_encode(array("err" => errorManager::get_error($answerArr['error']), "status" => 0)));
        }

        switch ($this->version) {
            case '1.0':
            default:
                $Summer = new summerSaleManager();

                return (json_encode(array("err" => '', "status" => 1, "eligible_for_summer_sale" => $Summer->eligible_for_summer_sale(), "url" => $answerArr['url'], "cartArr" => $Cart->get_cart(0, 0, 1))));
                break;
        }

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: addUser
     *
     * add new user ,
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/addUser/
     *
     * @expect_values :
     *    $_POST['firstName']); string  Required
     *    $_POST['lastName']); string  Required
     *    $_POST['email']); string  Required
     *    $_POST['password']); string  Required - for regular register
     *    $_POST['gender']); integer  Optional (options  : 1 - male | 2 - Female)
     *
     *    $_POST['fbid']); string  Optional - for register from facebook
     *    $_POST['accessToken']); string  Optional - for register from facebook
     *
     *    $_POST['has_multi_azrieli']); integer  Required   (options  : 1 | 0)
     *    $_POST['agreeGetAdvertisement']); integer  Required   (options  : 1 | 0)
     *    $_POST['terms']); integer  Required   (options  : 1 | 0)
     *
     *    $_POST['imageData']); string  Required - image data base64 decoded
     *
     *    $_POST['udid']); string  Required
     *    $_POST['deviceToken']); string  Optional
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 151,
     * "content": "כתובת דוא\"ל לא תיקנית"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 160,
     * "content": "אורך סיסמא מינמאלי הינו 4  תווים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 161,
     * "content": "יש לאשר תנאי שימוש"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 162,
     * "content": "אורך שם מינמאלי הינו 2 תווים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 163,
     * "content": "משתמש קיים במערכת, יש לבצע התחברות."
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 165,
     * "content": "חלה תקלה במהלך ההתחברות לFacebook, נסה להתחבר מחדש."
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     * "user_id": "16",
     * "first_name": "Nechami",
     * "last_name": "Karelitz",
     * "email": "nechami@inmanage.net",
     * "gender": "2",
     * "picture": "", - link or base64
     * "is_base64_image": true, - image is base64
     * "birthday": "",
     * "childernArr": "",
     * "multi_azrieli": 0,
     * "user_brandsArr": "",
     * "user_categoryArr": "",
     * "show_toast": true
     * },
     * "message": ""
     * }
     */

    public function addUser()
    {
        $register_type = (isset($_REQUEST["registerType"]) && $_REQUEST["registerType"]) ? siteFunctions::safe_value($_REQUEST["registerType"], "number") : "";

        if (!$register_type || !in_array($register_type, registerEnum::ALLOWED_TYPES)) {

            return $this->respond_failure(152);
        }

        if ($register_type == registerEnum::MANUAL) {
            unset($_REQUEST['fbId']);
            unset($_REQUEST['appleId']);
            unset($_REQUEST['googleId']);
        } else {
            foreach (registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD as $type => $prop) {
                if ($type !== $register_type) {
                    unset($_REQUEST[registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD[$type]]);
                }
            }
        }

        $first_name = (isset($_REQUEST["firstName"]) && $_REQUEST["firstName"]) ? siteFunctions::safe_value($_REQUEST["firstName"], "text") : "";
        $last_name = (isset($_REQUEST["lastName"]) && $_REQUEST["lastName"]) ? siteFunctions::safe_value($_REQUEST["lastName"], "text") : "";
        $email = (isset($_REQUEST["email"]) && $_REQUEST["email"]) ? ($_REQUEST["email"]) : "";
        $password = (isset($_REQUEST["password"]) && $_REQUEST["password"]) ? ($_REQUEST["password"]) : "";
        $phone = (isset($_REQUEST["phoneNumber"]) && $_REQUEST["phoneNumber"]) ? siteFunctions::safe_value($_REQUEST["phoneNumber"], "mobile_phone") : "";
        $phone_prefix = (isset($_REQUEST["phoneNumberPrefix"]) && $_REQUEST["phoneNumberPrefix"]) ? siteFunctions::safe_value($_REQUEST["phoneNumberPrefix"], "phone_number_prefix") : "";
        $terms = (isset($_REQUEST["approveTerms"]) && $_REQUEST["approveTerms"]) ? siteFunctions::safe_value($_REQUEST["approveTerms"], "number") : "";
        $user_image_url = (isset($_REQUEST["userImageUrl"]) && $_REQUEST["userImageUrl"]) ? siteFunctions::safe_value($_REQUEST["userImageUrl"], "text") : "";

        $facebook_id = (isset($_REQUEST["fbId"]) && $_REQUEST["fbId"]) ? siteFunctions::safe_value($_REQUEST["fbId"], "text") : "";
        $apple_id = (isset($_REQUEST["appleId"]) && $_REQUEST["appleId"]) ? siteFunctions::safe_value($_REQUEST["appleId"], "text") : "";
        $apple_identity_token = (isset($_REQUEST["appleIdToken"]) && $_REQUEST["appleIdToken"]) ? siteFunctions::safe_value($_REQUEST["appleIdToken"], "text") : "";
        $apple_authriztion_token = (isset($_REQUEST["authorizationCode"]) && $_REQUEST["authorizationCode"]) ? siteFunctions::safe_value($_REQUEST["authorizationCode"], "text") : "";
        $google_id = (isset($_REQUEST["googleId"]) && $_REQUEST["googleId"]) ? siteFunctions::safe_value($_REQUEST["googleId"], "text") : "";


        if (User::$decrypt) {
            $password = ($password !== "") ? Encryption::decrypt($password) : "";
            $email = siteFunctions::safe_value(($email !== "") ? Encryption::decrypt($email) : "", "email");
        } else {
            $email = siteFunctions::safe_value($email, "email");
            $password = siteFunctions::safe_value($password, "text");
        }

        if (!$email ) {

            return $this->respond_failure(152);
        }


        if (in_array($register_type, registerEnum::SOCIAL_LOGIN_TYPES)) {
            $field_to_check = registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD[$register_type];
            if (!$_REQUEST[$field_to_check]) {
                return $this->respond_failure(30);
            }
        }

        if ($terms != 1) {
            return $this->respond_failure(161);
        }
        $apple_type_user = false;
        $Apple = null;
        if ($apple_id && $apple_identity_token) {
//            $apple_detailsArr = AppleSignInManager::get_apple_payload($apple_id, $apple_identity_token);
            $Apple = new AppleSignInManager($apple_id, $apple_identity_token,$apple_authriztion_token);
            $apple_detailsArr = $Apple->get_apple_payload();
            if (!is_array($apple_detailsArr)) {
                return $this->respond_failure(4007);
            }
            else {
                $apple_type_user = true;
            }
            $_REQUEST['email'] = $apple_detailsArr['email'];
        }

        $missing_fields = [];
        foreach (registerEnum::REQUIRED_FIELDS_FOR_REGISTRATION[$register_type] as $field) {
            if (!isset($_REQUEST[$field]) || !$_REQUEST[$field]) {
                $missing_fields[] = $field;
            }
        }
        if (count($missing_fields) > 0) {

            return $this->respond_failure(152);
        }
        if ($result['status']) {

            return $this->respond_failure($result['error_code']); // Email address is already exist
        }

        $result = User::user_register($first_name, $last_name, $email, $phone, $phone_prefix, $password, $facebook_id, $apple_id, $google_id, $register_type, $user_image_url);
        if ($result["status"] == 1 && $result['data']['allow_editing_email']) {

            $user = User::user_exists($email)['data'];


        }
        elseif($result["status"] == 1 && $register_type != registerEnum::MANUAL && $apple_type_user && $Apple !== null) {
            $user = User::user_connect($email, null, $register_type, $result['data']['user_id'])['userArr'];
            $Apple->update_user_id($result['data']['user_id']);
        }
        elseif($result["status"] == 1 && $register_type != registerEnum::MANUAL) {
            $user = User::user_connect($email, null, $register_type, $result['data']['user_id'])['userArr'];
        }
        else {
            return $this->respond_failure($result["err"]);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return (json_encode(
                    array("status" => 1,
                        "err" => "",
                        "data" => [
                            'id' => $user['id'],
                            "user_id" => $user["id"],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'email' => $user['email'],
                            "fb_id" => $user["fb_id"],
                            "apple_id" => $user["apple_id"],
                            "google_id" => $user["google_id"],
                            "phone" => $user["cellphone"],
                            "phone_prefix" => $user["cellphone_prefix"],
                            "allow_editing_email" => $user["allow_editing_email"],
                            "user_image_url" => $user["image_url"],
                        ],
                        "message" => ""
                    )
                ));
                break;
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: loginUser
     *
     * add new user ,
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/loginUser/
     *
     * @expect_values :
     *    $_POST['email']); string  Required - for regular register
     *    $_POST['password']); string  Required - for regular register
     *
     *    $_POST['fbid']); string  Optional - for register from facebook
     *    $_POST['accessToken']); string  Optional - for register from facebook
     *
     *    $_POST['udid']); string  Required
     *    $_POST['deviceToken']); string  Required
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 170,
     * "content": "משתמש לא קיים במערכת יש לבצע הרשמה"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 171,
     * "content": "פרטי התחברות אינם נכונים"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     * "user_id": "16",
     * "first_name": "Nechami",
     * "last_name": "Karelitz",
     * "email": "nechami@inmanage.net",
     * "gender": "2",
     * "picture": "", - link or base64
     * "is_base64_image": true, - image is base64
     * "birthday": "",
     * "childernArr": "",
     * "multi_azrieli": 0,
     * "user_brandsArr": "",
     * "user_categoryArr": "",
     * "show_toast": true
     * },
     * "message": ""
     * }
     */
    public function loginUser()
    {
        $user_id = (isset($_REQUEST["userId"]) && $_REQUEST["userId"]) ? $_REQUEST["userId"] : "";
        $email = (isset($_REQUEST["email"]) && $_REQUEST["email"]) ? (strtolower($_REQUEST["email"])) : "";
        $password = (isset($_REQUEST["password"]) && $_REQUEST["password"]) ? ($_REQUEST["password"]) : "";

        $fbid = (isset($_REQUEST["fbid"]) && $_REQUEST["fbid"]) ? siteFunctions::safe_value($_REQUEST["fbid"], "number") : "";
        $access_token = (isset($_REQUEST["accessToken"]) && $_REQUEST["accessToken"]) ? ($_REQUEST["accessToken"]) : "";

        $apple_id = (isset($_REQUEST["appleId"]) && $_REQUEST["appleId"]) ? ($_REQUEST["appleId"]) : "";
        $apple_identity_token = (isset($_REQUEST["identityToken"]) && $_REQUEST["identityToken"]) ? ($_REQUEST["identityToken"]) : "";

        $udid = (isset($_REQUEST["udid"]) && $_REQUEST["udid"]) ? siteFunctions::safe_value($_REQUEST["udid"], "text") : "";
        $device_token = (isset($_REQUEST["deviceToken"]) && $_REQUEST["deviceToken"]) ? siteFunctions::safe_value($_REQUEST["deviceToken"], "text") : "";

        $use_old_decryption = (isset($_REQUEST["useOldDecryption"]) && $_REQUEST["useOldDecryption"]) ? siteFunctions::safe_value($_REQUEST["useOldDecryption"], "number_flag") : "";
        if (User::$decrypt && (!in_array("gal@inmanage.co.il",array("gal@inmanage.co.il")))) {
            $encryption_algo_used = $use_old_decryption ? "aes_128_old" : "aes-128-gcm";
            $password = ($password !== "") ? Encryption::decrypt($password, $encryption_algo_used) : "";
			$user_id = $user_id ? siteFunctions::safe_value(Encryption::decrypt($user_id, $encryption_algo_used), 'number') : '';
            $email = ($email !== "") ? Encryption::decrypt($email, $encryption_algo_used) : "";
        }

        if ($email !== "") {
            $email = siteFunctions::safe_value($email, "email");
            if ($email === "") {
                return $this->build_response(0, 151);
            }
        }
        if (User::user_exists($email, $fbid, $apple_id, $user_id) === null) {
            return $this->build_response(0, 170);
        }

        if ((($email !== "" && $password !== "") || ($fbid !== "" && $access_token !== "") || ($apple_id !== "" && $apple_identity_token !== "")) /*&& $udid !== "" && $device_token !== ""*/) {
            $connect_result = User::user_connect($email, $password, $fbid, $access_token, [], $apple_id, $apple_identity_token, [], $user_id);
            if ($connect_result === false) {
                return $this->build_response(0, 171);
            }

            if ($connect_result !== true) {
                return $this->build_response(0, $connect_result);
            }

            $User = User::getInstance();
            if ($device_token !== "" && $udid !== "") {
                siteFunctions::register_push_notification($this->platform, $device_token, $udid, $User->id);
            }
            User::save_user_login_information($User->id, $this);
        } else {
            return $this->build_response(0, 152);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1, 0, $User->get_login_data());
        }

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: startRestorePassword
     *
     * send email to user with link to restore his password
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/startRestorePassword/
     *
     * @expect_values :
     *    $_POST['email']); string  Required
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 180,
     * "content": "משתמש לא קיים במערכת"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 181,
     * "content": "חלה תקלה במהלך שחזור הסיסמא, אנא נסה שוב"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": ""
     * "message": ""
     * }
     */

    public function startRestorePassword()
    {
        $email = (isset($_REQUEST["email"]) && $_REQUEST["email"]) ? siteFunctions::safe_value(trim(strtolower($_REQUEST["email"])), "email") : "";

        if ($email === "") {
            return $this->build_response(0, 152);
        }

        $user_row = User::user_exists($email);
        if ($user_row === null) {
            return $this->build_response(0, 180);
        }

        if (!User::startRestorePassword($user_row["id"], $email)) {
            return $this->build_response(0, 181);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1);
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: restorePassword
     *
     * set new password to user. if the link didn't expire yet
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/restorePassword/
     *
     * @expect_values :
     *    $_POST['token']); string  Required: 808b899672fe99584f9b0d7de3c237f0
     *    $_POST['password']); string  Required
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 160,
     * "content": "אורך סיסמא מינמאלי הינו 4  תווים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 180,
     * "content": "משתמש לא קיים במערכת"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 200,
     * "content": "תוקף שחזור הסיסמא פג. התחל שחזור סיסמא מחדש"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 181,
     * "content": "חלה תקלה במהלך שחזור הסיסמא, אנא נסה שוב"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": ""
     * "message": ""
     * }
     */

    public function restorePassword()
    {
        $token = (isset($_REQUEST["token"]) && $_REQUEST["token"]) ? siteFunctions::safe_value(trim(strtolower($_REQUEST["token"])), "text") : "";
        $password = (isset($_REQUEST["password"]) && $_REQUEST["password"]) ? siteFunctions::safe_value($_REQUEST["password"], "text") : "";


        $use_old_decryption = (isset($_REQUEST["useOldDecryption"]) && $_REQUEST["useOldDecryption"]) ? siteFunctions::safe_value($_REQUEST["useOldDecryption"], "number_flag") : "";
        if (User::$decrypt) {
            $encryption_algo_used = $use_old_decryption ? "aes_128_old" : "aes-128-gcm";
            $password = ($password !== "") ? Encryption::decrypt($password, $encryption_algo_used) : "";
        }

        if ($token === "" || $password === "") {
            return $this->build_response(0, 152);
        }
        if (strlen($password) < 4) {
            return $this->build_response(0, 160);
        }

        $result = User::restorePassword($password, $token);

        if ($result["status"] == 0) {
            return $this->build_response(0, $result['err']);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1);
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: editUserInformation
     *
     * set user detials
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/editUserInformation/
     *
     * @expect_values :
     * $_POST['firstName']; Optional String
     * $_POST['lastName']; Optional String
     * $_POST['hasMultiAzrieli']; Optional Integer- (options  : 1 | 0)
     * $_POST['agreeGetAdvertisement']; Optional Integer- (options  : 1 | 0)
     * $_POST['imageData']; Optional String- base64
     * $_POST['mallId']; Optional Integer
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 151,
     * "content": "כתובת דוא\"ל לא תיקנית"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 162,
     * "content": "אורך שם מינמאלי הינו 2 תווים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 150,
     * "content": "על מנת לבצע פעולה זו עליך להיות מחובר"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 190,
     * "content": "חלה שגיאה במהלך עדכון הפרטים, אנא נסה שוב"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     * "user_id": "16",
     * "first_name": "Nechami",
     * "last_name": "Karelitz",
     * "email": "nechami@inmanage.net",
     * "gender": "2",
     * "picture": "", - link or base64
     * "is_base64_image": true, - image is base64
     * "birthday": "",
     * "childernArr": "",
     * "multi_azrieli": 0,
     * "user_brandsArr": "",
     * "user_categoryArr": "",
     * "show_toast": true
     * },
     * "message": ""
     * }
     */

    public function editUserInformation()
    {
        $first_name = (isset($_REQUEST["firstName"])) ? siteFunctions::safe_value($_REQUEST["firstName"], "text") : "00";
        $last_name = (isset($_REQUEST["lastName"])) ? siteFunctions::safe_value($_REQUEST["lastName"], "text") : "00";
        $image_data = (isset($_REQUEST["imageData"])) ? siteFunctions::safe_value($_REQUEST["imageData"], "base64_image") : "00";

        if ((isset($_REQUEST["firstName"]) && strlen($first_name) < 2) || (isset($_REQUEST["lastName"]) && strlen($last_name) < 2)) {
            return $this->build_response(0, 162);
        }

        $User = User::getInstance();
        if (!$User->id) {
            return $this->build_response(0, 150);
        }

        $userInformationArr = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "picture" => $image_data,
            "is_base64" => true,
        );

        if (count(array_diff($userInformationArr, array("00"))) && !$User->editUserInformation($userInformationArr)) {
            return $this->build_response(0, 190);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1, 0, $User->get_login_data());
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: sendContact
     *
     * send contact form
     *
     * url : http://apiurl.co.il/api/{platform}/1.0/sendContact/
     *
     * @expect_values :
     * $_POST['fullName']; Required String
     * $_POST['email']; Required String
     * $_POST['phone']; Required String
     * $_POST['message']; Required String
     *
     * @return string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 151,
     * "content": "כתובת דוא"ל לא תיקנית"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * {
     * "err": {
     * "id": 20,
     * "content": "מספר הטלפון שהוזן אינו תקין"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": {
     *
     * },
     * "message": ""
     * }
     */
    public function sendContact()
    {
        $topic_id = (isset($_REQUEST["topicId"])) ? siteFunctions::safe_value($_REQUEST["topicId"], "number") : 0;
        $message = (isset($_REQUEST["message"])) ? siteFunctions::safe_value($_REQUEST["message"], "text") : "";

        $User = User::getInstance();
        if (!$User->id) {
            return $this->build_response(0, 150);
        }

        if (!$User->email) {
            return $this->build_response(0, 154);
        }

        if ($topic_id == 0 || $message == "") {
            return $this->build_response(0, 152);
        }

        $res = contactManager::send($topic_id, $message);
        if (!$res) {
            return $this->build_response(0, 155);
        }

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1);
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: applicationToken
     *
     * get secured token to add to all api requests. run it as first call
     *
     * @expect_values :
     * $_REQUEST['udid']; Required String: "12324"
     *
     * @return false|Json|string
     * @example:
     * Case 1 (error):
     * {
     * "err": {
     * "id": 152,
     * "content": "חסרים נתונים"
     * },
     * "status": 0
     * }
     *
     * @example:
     * Case 2 (success):
     * {
     * "status": 1,
     * "err": "",
     * "data": "",
     * "message": ""
     * }
     */

    public function applicationToken()
    {

        $udid = ($_REQUEST["udid"] && isset($_REQUEST["udid"])) ? siteFunctions::safe_value($_REQUEST["udid"], "text") : "";

        if (!$udid || $_SERVER["HTTP_" . secureToken::header_key] !== secureToken::header_value) {
            return $this->build_response(0, 152);
        }

        $token = secureToken::create_token($udid);
        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1, 0, $token);
        }
    }

    /*----------------------------------------------------------------------------------*/

    /*----------------------------------------------------------------------------------*/
    /**
     * @name logout
     * @description Logs the user out
     * @return string
     */
    public function logout()
    {
        User::user_logout();

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1);
        }
    }

    public function getMetaTags()
    {
        global $react_modulesArr;
        $route_name = (isset($_REQUEST["route"]) && $_REQUEST['route']) ? siteFunctions::safe_value($_REQUEST['route'], 'text') : '';
        $object_id = (isset($_REQUEST["objectId"]) && $_REQUEST['objectId']) ? siteFunctions::safe_value($_REQUEST['objectId'], 'number') : 0;
        $route_name = strtolower($route_name);
        $mdl_id = isset($react_modulesArr[$route_name]) ? $react_modulesArr[$route_name] : null;

        $MetaTagsManager = new MetaTagsManager($mdl_id, $object_id);

        switch ($this->version) {
            case '1.0':
            default:
                return $this->build_response(1, 0, $MetaTagsManager->resolve());
        }
    }

    public function removeUser()
    {
        $User_remove = new UserRemoveManager();
        $user_id = $User_remove->id;
        if (!$user_id) {
            return $this->build_response(0, 150);
        }
        if ($User_remove->remove_user()) {
            return $this->build_response(0);
        }
        return $this->build_response(1);

    }
}

?>