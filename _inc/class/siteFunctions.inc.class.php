<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 05/03/14
 * Time: 14:01
 *
 */

/**
 * @author : gal zalait
 * @desc : this class Responsible for linking the API to the customer
 * @var : 1.0
 * @last_update : 24/08/2013
 */
class siteFunctions
{


    private $ts;

    /*----------------------------------------------------------------------------------*/

    public function __construct()
    {
        $this->ts = time();
    }

    /*----------------------------------------------------------------------------------*/

    public function __destruct()
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

    /*----------------------------------------------------------------------------------*/
    public static function get_environment_color($url): array
    {
        global $project_live_url,
               $project_dev_url;

        $r = '';
        $g = '';
        $b = '';

        switch ($url) {
            case $project_live_url:
                $r = 120;
                $g = 100;
                $b = 100;
                break;
            case $project_dev_url:
                //$color="rgb(158, 50, 50)";
                $r = 158;
                $g = 50;
                $b = 50;

                break;
            case ("api.site.co.il"): // brown
                //$color="rgb(178, 123, 55)";
                $r = 178;
                $g = 123;
                $b = 55;

                break;
        }

        return array(
            "r" => $r,
            "g" => $g,
            "b" => $b,
        );
    }

    /*----------------------------------------------------------------------------------*/


    public static function get_general_declaration($ApiObj): array
    {
        global $throw_errorArr, $throw_to_menu_errorArr, $throw_to_registrationArr;

        $cachedArr = array();
        $languagesArr = array();
        $gd_translationsArr = array();
        $contentsArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/languages.inc.php');//$languagesArr
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/translations_gd.' . $_SESSION['lang'] . '.inc.php');//$gd_translationsArr
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/contents.' . $_SESSION['lang'] . '.inc.php');//$contentsArr
        $translate_last_update = filemtime($_SERVER['DOCUMENT_ROOT'] . '/_static/translations_gd.' . $_SESSION['lang'] . '.inc.php');

        foreach ($ApiObj->method_cachedArr AS $method_name => $time_in_min) {
            $cachedArr[] = array(
                "method_name" => $method_name,
                "cache_time" => $time_in_min
            );
        }

        $return = array(
            "languagesArr" => array_values($languagesArr),
            "cachedArr" => $cachedArr,
            "server_time" => $ApiObj->ts,
            "media_server" => $ApiObj->media_server,
            "throw_errorArr" => $throw_errorArr,
            "throw_to_menu_errorArr" => $throw_to_menu_errorArr,
            "throw_to_registrationArr" => $throw_to_registrationArr,
            "translationsArr" => $gd_translationsArr,
            "translations_last_update" => $translate_last_update,
            "contentArr" => array_values($contentsArr),
            "applicationImageArr" => self::get_gd_imagesArr(),
            "time_zone" => date_default_timezone_get(),
            "language" => $_SESSION['lang'],
            "user_image_width" => User::$user_image_width,
            "user_image_height" => User::$user_image_height,
            "menuArr" => self::get_mobile_side_menuArr(),

        );

        $parametersArr = self::get_parametersArr();
        return array_merge($return, $parametersArr);
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_mobile_side_menuArr(): array
    {
        $side_menuArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/side_menu.' . $_SESSION['lang'] . '.inc.php');// $side_menuArr

        $menuArr = array();
        foreach ($side_menuArr as $valueArr) {
            if ($valueArr['is_website'] == 0) {
                $menuArr[] = array(
                    'id' => $valueArr['id'],
                    'icon' => $valueArr['icon'],
                    'title' => $valueArr['title'],
                    'deep_link' => $valueArr['link'],
                    'login_only' => $valueArr['login_only'],
                    'order_num' => $valueArr['order_num'],
                );
            }
        }

        return array_values($menuArr);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: get_cached_moduleArr
     *
     * This is a generic method to get an array when saved at cach:
     * if dosen't success reading from cach- reads from static file.
     * if dosen't success reading from static files takes values form db.
     *
     * @expect_values :
     * $file_name; Required String: "coupons.he" - this is the name of the file in the cach,
     *                              and the same prefix name of static file (without ".inc.php")
     *                              for a multi lang file- switch "he" with $_SESSION['lang'].
     * $array_name; Required String: "couponsArr"- the name of the array in the static file
     * $module_updateStaticFiles_file_name; Required String: "coupons"
     *                              - the name of the file with the module class that is in charge of updating static file.
     *                              the path to the file is: '/salat2/_inc/UpdateStaticFiles/'.
     *                              send parameter without the '.class.php' suffix.
     * $module_updateStaticFiles_class_name; Required String: "couponsLangsUpdateStaticFiles"
     *                              - The name of the class in the $module_updateStaticFiles_file_name file
     * $module_updateStaticFiles_function_module_db_array; Required String: "getCouponsArr"
     *                              - function name for getting the data from the db.
     *                              the functin needs to be static function in the $module_updateStaticFiles_class_name class.
     *                              use this function also for creating the static file, and for saving at cach.
     * $type_id; Optinal Integer: 1 - the type of getting data- start from. by deafult starts from 1 - from cach memory.
     *                              if doesn't success $type_id will change automatically to 2- from static file.
     *                              if doesn't success $type_id will chang automatically to 3- from database.
     *
     * @Example - 1 - for using function:
     * public static function get_couponsArr(){
     * $file_name = "coupons".$_SESSION['lang']; //('/_static/coupons.he.inc.php') - also the name that saved in chach
     * $array_name = "couponsArr"; // the name of the array in the static file
     * $module_updateStaticFiles_file_name = "coupons"; // the name of the file that olds the couponsLangsUpdateStaticFiles class
     * $module_updateStaticFiles_class_name = "couponsLangsUpdateStaticFiles"; // the name of the class that is in charge of updating static file.
     * $module_updateStaticFiles_function_module_db_array = "getCouponsArr"; // the name of the function in the couponsLangsUpdateStaticFiles that returns the data from database
     * return siteFunctions::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array);
     * }
     * @Example - 2 - for how function look with sent parameters:
     * public static function get_cached_moduleArr("coupons".$_SESSION['lang'], "couponsArr", "coupons", "couponsLangsUpdateStaticFiles", "getCouponsArr", $type_id = 1){
     * switch($type_id){
     * case 1: // memory
     * $couponsArr = self::load_from_memory("coupons".$_SESSION['lang']);
     * if(count($couponsArr) > 1){
     * return $couponsArr;
     * }else{
     * return self::get_cached_moduleArr("coupons".$_SESSION['lang'], "couponsArr", "coupons", "couponsLangsUpdateStaticFiles", "getCouponsArr", 2);
     * }
     * break;
     * case 2: // static file
     * $static_file_path = $_SERVER['DOCUMENT_ROOT'].'/_static/coupons'.$_SESSION['lang'].'.inc.php'; // $couponsArr
     * if(is_file($static_file_path)){
     * include ($static_file_path);
     * return $couponsArr;
     * }else{
     * return self::get_cached_moduleArr("coupons".$_SESSION['lang'], "couponsArr", "coupons", "couponsLangsUpdateStaticFiles", "getCouponsArr", 3);
     * }
     * break;
     * case 3: // DB
     * include_once($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/UpdateStaticFiles/coupons.class.php');
     * return couponsLangsUpdateStaticFiles::getCouponsArr();
     * }
     * }
     *
     * @param $file_name
     * @param $array_name
     * @param $module_updateStaticFiles_file_name
     * @param $module_updateStaticFiles_class_name
     * @param $module_updateStaticFiles_function_module_db_array
     * @param int $type_id
     * @return Array
     */
    public static function get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array, $type_id = 1): array
    {
        switch ($type_id) {
            case 1: // memory
                ${$array_name} = self::load_from_memory($file_name);
                if (count(${$array_name}) > 1) {
                    return ${$array_name};
                }

                return self::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array, 2);
            case 2: // static file
                $static_file_path = $_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.inc.php';
                if (is_file($static_file_path)) {
                    include($static_file_path);
                    return ${$array_name};
                }

                return self::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array, 3);
            case 3: // DB
                include_once($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/UpdateStaticFiles/' . $module_updateStaticFiles_file_name . '.class.php');
                return $module_updateStaticFiles_class_name::$module_updateStaticFiles_function_module_db_array();
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_gd_imagesArr($type_id = 1)
    {
        $file_name = 'gd_images';
        $array_name = 'gd_imagesArr';
        switch ($type_id) {
            case 1: // memory
                ${$array_name} = self::load_from_memory($file_name);
                if (count(${$array_name}) > 1) {
                    return ${$array_name};
                }

                return self::get_gd_imagesArr(2);
            case 2: // static file
                $static_file_path = $_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.inc.php';
                if (is_file($static_file_path)) {
                    include($static_file_path);//$gd_imagesArr
                    return ${$array_name};
                }

                return self::get_gd_imagesArr(3);
            case 3: // DB
                include_once($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/UpdateStaticFiles/gd_images.class.php');
                return gd_imagesUpdateStaticFiles::getGdImagesStaticArray();
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_parametersArr(): array
    {
        $file_name = "parameters";
        $array_name = "parametersArr";
        $module_updateStaticFiles_file_name = "parameters";
        $module_updateStaticFiles_class_name = "parametersUpdateStaticFiles";
        $module_updateStaticFiles_function_module_db_array = "getParametersArr";
        return self::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array);
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_GET_methodsArr(): array
    {
        $file_name = "GET_methodsArr";
        $array_name = "GET_methodsArr";
        $module_updateStaticFiles_file_name = "getMethodsUpdateStaticFiles";
        $module_updateStaticFiles_class_name = "getMethodsUpdateStaticFiles";
        $module_updateStaticFiles_function_module_db_array = "get_GET_methodsArr";
        return self::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array);
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_environments_settingsArr(): array
    {
        $file_name = "environments_settings";
        $array_name = "environments_settingsArr";
        $module_updateStaticFiles_file_name = "environments_settingsUpdateStaticFiles";
        $module_updateStaticFiles_class_name = "environments_settingsUpdateStaticFiles";
        $module_updateStaticFiles_function_module_db_array = "getItems";
        return self::get_cached_moduleArr($file_name, $array_name, $module_updateStaticFiles_file_name, $module_updateStaticFiles_class_name, $module_updateStaticFiles_function_module_db_array);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * save array to memory useing memcached libery
     * @param $key
     * @param $value
     * @param int $time - time in sec to save value
     * @param array $infoArr
     * @return bool
     */
    public static function save_to_memory($key, $value, $time = 2592000, $infoArr = array()): bool
    {
        global $Db;
        if (!memcached_is_on) {
            return false;
        }

	    if($time > 2592000) {
		    $time += time();
	    }

        $ts = time();

        $Memcached = new Memcache();
        $status = $Memcached->addServer('localhost');

        if ($status) {
            if ($Memcached->set($key, $value, 0, $time)) {
//                $Memcached->close();
                return true;
            }

            $Memcached->close();
            $db_fields = array(
                'key' => $key,
                'value' => serialize($value),
                'time' => $ts,
                'info' => serialize($infoArr),
                'last_update' => $ts,
            );

            $Db->insert('memcached_error', $db_fields);
            return false;
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/
    public static function load_from_memory($key)
    {
        if (!memcached_is_on) {
            return false;
        }

        $Memcached = new Memcache();
        $status = $Memcached->addServer('localhost');
        if ($status) {
            $value = $Memcached->get($key);
            $Memcached->close();
            $success = $value;
            if ($success) {
                return $value;
            }
        } else {
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function register_push_notification($type, $device_token, $udid, $user_id = 0, $source = ''): bool
    {
        global $Db;

        $ts = time();

        $device_token = $Db->make_escape($device_token);
        $user_id = (int)$user_id;
        $udid = $Db->make_escape($udid);
        $type = $Db->make_escape($type);

        $udid_exist_query = "SELECT * FROM `tb_push__device` WHERE `udid` = '{$udid}'";
        $udid_exist_result = $Db->query($udid_exist_query);
        $udid_exist = ($udid_exist_result->num_rows > 0);
        $udid_exist_row = $Db->get_stream($udid_exist_result);
        $udid_exist_row_id = $udid_exist_row["id"];

        $user_exist = false;
        if ($user_id) {
            // check if first time - register - if user_id is in table
            $user_exist_query = "SELECT * FROM `tb_push__device` WHERE `user_id` = '{$user_id}'";
            $user_exist_result = $Db->query($user_exist_query);
            $user_exist = ($user_exist_result->num_rows > 0);
            $user_exist_row = $Db->get_stream($user_exist_result);
            $user_exist_row_id = $user_exist_row["id"];
        }
        if ($udid_exist == false) {
            // first time on device
            if (!$user_id || $user_exist == false) {
                // user not connected and first time on device
                // insert to table

                // OR
                // connected user - dosen't exist in table and first time on device
                // insert to table

                $db_fields = array(
                    "user_id" => $user_id,
                    "udid" => $udid,
                    "token_device" => $device_token,
                    "device_type" => $type,
                    "source" => $source,
                    "last_update" => $ts,
                );
                $Db->insert('tb_push__device', $db_fields);
            } elseif ($user_exist == true) {
                // connected user - exist in table and first time on device
                // update user row - udid, token, and device_type by user_id
                $db_fields = array(
                    'token_device' => $device_token,
                    'udid' => $udid,
                    'device_type' => $type,
                    'source' => $source,
                    'last_update' => $ts,
                );
                $Db->update('tb_push__device', $db_fields, 'user_id', $user_id);
            }

        } else {
            // device udid exist in table
            if ($user_id && $user_exist == false) {
                // user connected, user doesn't exist in table, device exist in table
                // update user_id and token by udid
                $db_fields = array(
                    'token_device' => $device_token,
                    'source' => $source,
                    'user_id' => $user_id,
                    'last_update' => $ts,
                );
                $Db->update('tb_push__device', $db_fields, 'udid', $udid);
            } elseif ($user_exist == true) {
                // user connected, user exist in table, device exist in table
                // delete user row - if not same row
                // update udid row - user_id, token
                if ($user_exist_row_id != $udid_exist_row_id) {
                    // todo: check with other user
                    $Db->delete('tb_push__device', 'user_id', $user_id);
                }

                $db_fieldsArr = array(
                    'token_device' => $device_token,
                    'source' => $source,
                    'user_id' => $user_id,
                    'last_update' => $ts,
                );
                $Db->update('tb_push__device', $db_fieldsArr, 'udid', $udid);
            } else {
                // not connected, udid exist
                // update udid row - token
                $db_fieldsArr = array(
                    'token_device' => $device_token,
                    'source' => $source,
                    'last_update' => $ts,
                );
                $Db->update('tb_push__device', $db_fieldsArr, 'udid', $udid);
            }
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /*
     * Method: errorReport
     *
     * save crash log to db.
     *
     * @expect_values :
     * 	$content; Required String. application crash log.
     *
     * @return null
     */

    public static function errorReport($content): void
    {
        global $Db, $Api;
        $ts = time();

        $db_fieldsArr = array(
            "content" => $content,
            "post" => base64_encode(json_encode($_POST)),
            "session" => base64_encode(json_encode($_SESSION)),
            "request" => base64_encode(json_encode($_REQUEST)),
            "server" => base64_encode(json_encode($_SERVER)),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "platform" => $Api->platform,
            "udid" => $_SESSION['api']['udid'] ?? "",
            "server_version" => $Api->version,
            "application_version" => $_SESSION['api']['application_version'] ?? "",
            "last_update" => $ts
        );
        $Db->insert('tb_error_report', $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/

    public static function safe_value($content, $type)
    {
        switch ($type) {
            case "text":
                $content = addslashes(strip_tags($content));
                break;
            case "number":
                $content = (int)$content;
                break;
            case "email":
                $content = validate_EmailAddress($content) ? $content : "";
                break;
            case "number_flag":
                $content = ($content == 0 || $content == 1) ? $content : 0;
                break;
            case "num_only":
                $content = preg_replace('/[^0-9]/', '', $content);
                break;
            case "home_phone":
                global $home_phone_prefix;
                $content = self::safe_value($content, "num_only");
                $prefix = "";
                if (strlen($content) == 10) {
                    $prefix = substr($content, 0, 3);
                } elseif (strlen($content) == 9) {
                    $prefix = substr($content, 0, 2);
                }
                if (!in_array($prefix, $home_phone_prefix)) {
                    $content = "";
                }
                break;
            case "mobile_phone":
                global $phone_prefixArr;
                $content = self::safe_value($content, "num_only");
                $prefix = "";
                if (strlen($content) == 10) {
                    $prefix = substr($content, 0, 3);
                }
                if (!in_array($prefix, $phone_prefixArr)) {
                    $content = "";
                }
                break;
            case "phone":
                $home_phone = self::safe_value($content, "home_phone");
                if ($home_phone === "") {
                    $content = self::safe_value($content, "mobile_phone");
                } else {
                    $content = $home_phone;
                }
                break;
            case "base64_image":
                $content = (strip_tags($content) == $content && htmlentities(str_replace('"', '', $content)) == $content) ?
                    str_replace(array("\n", "\r", "\\n", "\\r"), array('', '', '', ''), trim($content)) :
                    "";
                break;
            case "array":
                $content = is_array($content) ? $content : array();
                break;

            default:
                break;
        }
        return $content;
    }

    /*----------------------------------------------------------------------------------*/

    public static function replace_template_tags($template_path, $tagsArr)
    {
        $html = file_get_contents($template_path);

        $keyArr = array();
        $valueArr = array();
        foreach ($tagsArr as $key => $value) {
            $keyArr[] = ('[-' . $key . '-]');
            $valueArr[] = $value;
        }

        return str_replace($keyArr, $valueArr, $html);
    }

    /*----------------------------------------------------------------------------------*/
    /*
     * @param $emailsArr
     * @param string $subject
     * @param $html
     * @param array $htmlArr  array(
     * 						array(
     * 							"name"=>x
     * 							"content"=>y
     * 						),
     * 						);
     * $param $custom_style =  1- send mcdonald's custom mail style , 0 reg mail
     * @return bool
     */
    public static function send_mail($emailsArr, $subject, $html, $htmlArr = array(), $custom_style = 0, $mail_type = 'default', $attached_file = ''): bool
    {
        if(isset($_REQUEST['dont_send_email_to_the_user'])) {
            return false;
        }

        $footer = lang('mail_footer');
        if (strpos($html, $footer) !== false) {
            $footer = '';
        }

        if ($custom_style && file_exists($template_path = $_SERVER['DOCUMENT_ROOT'] . '/_media/emails/' . $custom_style . '.html')) {

            $htmlArr['footer'] = lang('mail_footer');
            $htmlArr['date'] = date('[H:i] d/m/Y ');
            $htmlArr['title'] = $htmlArr['title'] ?: $subject;

            $html = self::replace_template_tags($template_path, $htmlArr);
        } else {
            if (count($htmlArr)) {
                if ($mail_type == 'default') {
                    $html .= '<table style="text-align: center;">';
                    foreach ($htmlArr AS $key => $valueArr) {
                        $html .= <<<HTML
				<tr>
					<td style="padding-left: 5px;">{$valueArr['name']}</td>
					<td>{$valueArr['content']}</td>
				</tr>
HTML;
                    }

                    $html .= '</table>';
                } elseif ($mail_type == 'share') {
                    $content_title = $htmlArr['name'];
                    $content_url = $htmlArr['content'];

                    $html .= <<<HTML
                <br /><a href="{$content_url}">{$content_title}</a>
HTML;
                }
            }

            $html = "<br/>" . $html. "<br/><br/>";
        }


        if (!class_exists('Seo')) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/Seo.class.inc.php'); // Seo
        }
        $Seo = new Seo();

        include_once($_SERVER['DOCUMENT_ROOT'] . '/resource/PHPMailer/PHPMailerAutoload.php');
        $Mail = new PHPMailer();
        $Mail->CharSet = "UTF-8";
        $Mail->From = configManager::$no_replay_email;
        $Mail->FromName = lang('email_sender_name');
        $Mail->Subject = $subject;
        $Mail->isHTML();
        $Mail->Body = $html;

        if (is_array($emailsArr)) {
            foreach ($emailsArr as $email) {
                $Mail->addAddress($email);
            }
        } else {
            $Mail->addAddress($emailsArr);
        }

        if ($attached_file && file_exists($attached_file)) {
            $Mail->addAttachment($attached_file);
        }

        // sending
        $result = $Mail->send();

        @unlink($attached_file);

        return $result;
    }



    /*----------------------------------------------------------------------------------*/
    /**
     * Method: sendContact
     *
     * send contact form
     *
     * @expect_values :
     * $full_name; Required String: "Nechami Karelitz"
     * $email; Required String: "nechami@inmanage.net"
     * $phone; Required String: "0501234567"
     * $message; Required String: "message"
     *
     * @return Null
     */

    public static function sendContact($full_name, $email, $phone, $message)
    {
        global $Db;

        $ts = time();

        $db_fields = array(
            "name" => $full_name,
            "email" => $email,
            "phone" => $phone,
            "content" => $message,
            "is_done" => 0,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "created_ts" => $ts,
            "last_update" => $ts,
        );

        // save at db
        foreach ($db_fields AS $key => $value) {
            $db_fields[$key] = $Db->make_escape($value);
        }
        $Db->insert('tb_contacts', $db_fields);

        // send emails to admins
        $contactArr = array();
        $contactArr[] = array(
            "name" => "שם מלא",
            "content" => $full_name
        );
        $contactArr[] = array(
            "name" => "אימייל",
            "content" => $email
        );
        $contactArr[] = array(
            "name" => "טלפון",
            "content" => $phone
        );
        $contactArr[] = array(
            "name" => "הודעה",
            "content" => $message
        );
        self::send_mail(configManager::$adminEmailsArr, lang("contact_message_subject"), "", $contactArr, 1);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: get_media_url
     *
     * get path to image by id
     *
     * @expect_values :
     * $media_id; Required Integer: 177
     *
     * @return String: "/_media/media/19/177.gif?t=1400660036"
     */

    public static function get_media_url($media_id)
    {
        $image_path = "";
        if ($media_id) {
            $Image = new mediaManager($media_id);
            $image_path = $Image->path;
        }
        return $image_path;
    }

    /*----------------------------------------------------------------------------------*/

    public static function send_sms_error($url, $response, $phone)
    {
        global $admin_emailArr;
        $z = print_r($response, true);

        $html = <<<HTML
		<div  style="direction:rtl; text-align:right;padding:5px;">
		<span>

	הודעת ה SMS לא הגיעה ליעדה
	מספר:
	{$phone}

	נתוני שליחה
	{$url}
	פירוט:



	{$response}
	{$z}


	נא לטפל בהקדם.
		<br>
		<br>
		</span>
HTML;

        return self::send_mail(implode(',', $admin_emailArr), "שליחת SMS נכשלה", $html);
    }

    /*----------------------------------------------------------------------------------*/

    public static function write_api_lock_ip_record($method_name, $limit, $ts)
    {
        global $Db;

        $first_call_in_allowed_time_by_ip = 0;

        $tb_name = 'tb_lock_api_by_ip';
        $db_fields = array(
            "method_name" => $method_name,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "last_update" => $ts,
        );

        $res = $Db->insert($tb_name, $db_fields);

        $query = "
		    SELECT * FROM {$tb_name}
		        WHERE `method_name`='{$method_name}'
		            AND ip ='{$_SERVER['REMOTE_ADDR']}'
                ORDER BY `last_update` DESC
                    LIMIT {$limit}
        ";
        $result = $Db->query($query);

        // return first row time in allowed limit for method
        if ($result->num_rows < $limit) {
            return 0;
        }

        while ($row = $Db->get_stream($result)) {
            $first_call_in_allowed_time_by_ip = $row['last_update'];
        }

        return $first_call_in_allowed_time_by_ip;
    }

    /*----------------------------------------------------------------------------------*/

    public static function write_to_api_log($method_name, $platform, $version)
    {
        global $Db;

        $session_id = session_id();
        $Browser = new Browser();
        $tb_name = 'tb_api_log';
        $db_fields = array(
            "method_name" => $method_name,
            "platform" => $platform,
            "version" => $version,
            "device" => $Browser->getPlatform(),
            "device_version" => $Browser->getVersion(),
            "user_agent" => $Browser->getUserAgent(),
	        "SERVER_ADDR" => $_SERVER['SERVER_ADDR'],
	        'ip' => $_SERVER['REMOTE_ADDR'],
	        'user_id' => (isset($_SESSION['user_id']) && $_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
	        "session_id" => $session_id,
            "last_update" => time(),
        );

        $Db->insert($tb_name, $db_fields);
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_country_by_ip($ip = null)
    {
        $Db = Database::getInstance();
        $ip = $ip ?? $_SERVER['REMOTE_ADDR'];

        $ip_num = ip2long($ip);
        $query = "SELECT `countryCode` FROM `tb_geoip` WHERE `beginIpNum` <= '{$ip_num}' AND `endIpNum` >= '{$ip_num}'";
        $res = $Db->query($query);
        if ($res->num_rows) {
            $row = $Db->get_stream($res);
            return $row['countryCode'];
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/

    public static function save_base64_to_file($base64_image, $path, $file_name)
    {
        // Split the data
        $dataArr = explode(',', substr($base64_image, 5), 2);
        if (count($dataArr) > 1) {
            $mime = $dataArr[0];
            $image_data = $dataArr[1];

            $mime_split_wo_base64 = explode(';', $mime, 2);
            $mime_split = explode('/', $mime_split_wo_base64[0], 2);

            $ext = $mime_split[1] == 'jpeg' ? 'jpg' : $mime_split[1];
        } else {
            $image_data = $base64_image;
            $ext = 'jpg';
        }

        $full_path = $path . '/' . $file_name . '.' . $ext;

        // Open the file
        $handle = fopen($_SERVER['DOCUMENT_ROOT'] . $full_path, 'wb');

        // Write the file
        fwrite($handle, base64_decode($image_data));

        // Close the file
        fclose($handle);

        return $full_path;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_platform_id()
    {
        return isset($_SESSION['api']['platform']) && array_key_exists($_SESSION['api']['platform'], configManager::$sourceArr) ? configManager::$sourceArr[$_SESSION['api']['platform']] : configManager::$sourceArr[configManager::$default_platform];
    }

    /*----------------------------------------------------------------------------------*/

    public static function parallel_requests($data, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curly[$id], CURLOPT_TIMEOUT, 5);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // get content and remove handles
        foreach ($curly as $id => $c) {
            $result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_url_by_env()
    {
        $site_type = self::get_site_type();
        $env = self::get_env();

        return 'https://' . configManager::$env_urlsArr[$site_type][$env][0];
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_site_type()
    {
        $current = $_SERVER['HTTP_HOST'];

        foreach (configManager::$env_urlsArr as $site_type => $envsArr) {
            foreach ($envsArr as $env => $urlsArr) {
                if (in_array($current, $urlsArr)) {
                    return $site_type;
                }
            }
        }

        return 'main';
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_env()
    {
        $current = $_SERVER['HTTP_HOST'];
        $site_type = self::get_site_type();

        foreach (configManager::$env_urlsArr[$site_type] as $env => $urlsArr) {
            if (in_array($current, $urlsArr)) {
                return $env;
            }
        }

        return 'live';
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_modules_folder()
    {
        $site_type = self::get_site_type();

        return array_key_exists($site_type, configManager::$modules_folderArr) ? configManager::$modules_folderArr[$site_type] : configManager::$modules_folderArr['main'];
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_timezonesArr($with_key = false)
    {
        $timezone_abbrArr = DateTimeZone::listAbbreviations();
        $timezone_idsArr = array();
        foreach ($timezone_abbrArr as $abbr => $timezonesArr) {
            foreach ($timezonesArr as $timezoneArr) {
                $timezone = $timezoneArr['timezone_id'];

                if (!in_array($timezone, $timezone_idsArr) && preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $timezone)) {
                    if ($with_key) {
                        $timezone_idsArr[$timezone] = $timezone;
                    } else {
                        $timezone_idsArr[] = $timezone;
                    }
                }
            }
        }

        return $timezone_idsArr;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_time_timestamp($value)
    {
        return Carbon::parse('01/01/1970 ' . $value)->getTimestamp();
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_diff_in_minutes($start_ts, $end_ts, $timezone)
    {
        $start = Carbon::createFromTimestamp($start_ts, $timezone);
        $end = Carbon::createFromTimestamp($end_ts, $timezone);
        $diff = $end->diffInMinutes($start);

        return $diff;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_closest_number($number, $numbersArr)
    {
        $distancesArr = array();

        foreach ($numbersArr as $key => $value) {
            $distancesArr[$key] = abs($number - $value);
        }

        return $numbersArr[array_search(min($distancesArr), $distancesArr)];
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_opening_videos_gdArr()
    {
        $part_path = '/_media/opening_videos/';
        $dir = $_SERVER['DOCUMENT_ROOT'] . $part_path;
        $dirArr = scandir($dir);
        unset($dirArr[0], $dirArr[1]);

        $filesArr = array();
        foreach ($dirArr as $file_name) {
            $fileArr = explode('.', $file_name);
            $res = $fileArr[0];
            $ext = $fileArr[1];

            $filesArr[$res] = array(
                'url' => siteFunctions::get_url_by_env() . $part_path . $file_name,
                'check_sum' => md5_file($dir . $file_name),
                'ext' => $ext,
                'resolution' => $res,
            );
        }

        return $filesArr;
    }

    /*----------------------------------------------------------------------------------*/


    /**
     * Method Name: save_order_session_to_db
     * Saves the user's current session in case it's lost during the payment process on cg for desktop
     * @param $user_id
     * @return int - the row id from the database
     */
    public static function save_order_session_to_db($user_id)
    {
        $Db = Database::getInstance();
        $ts = time();
        $session = base64_encode(serialize($_SESSION));
        $rand_key = random_int(1000, 9999);
        $db_fieldsArr = array(
            'user_id' => $user_id,
            'session' => $session,
            'last_update' => $ts,
        );
        $serialized_session_id = $Db->insert('tb_desktop__serialized_sessions', $db_fieldsArr);

        //for a stronger md5 key
        $serialized_session_id .= $rand_key;
        // update hash

        $db_fieldsArr = array(
            'hash' => md5($serialized_session_id),
        );
        $Db->update('tb_desktop__serialized_sessions', $db_fieldsArr, 'id', "=", $serialized_session_id);

        return $serialized_session_id;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Method Name: load_order_session_from_db
     * Loads the session saved in the db in case of session loss during the payment process.
     * @param $serialized_session_id - NOT session_id() - The ID of the session row in the tb_orders__serialized_sessions table
     * @return bool
     */
    public static function load_order_session_from_db($serialized_session_id)
    {
        $serialized_session_id = siteFunctions::safe_value($serialized_session_id, "text");

        if ($serialized_session_id == '') {
            return false;
        }

        $Db = Database::getInstance();

        $ts = time();
        $sql = "
            SELECT `id`, `session` FROM `tb_desktop__serialized_sessions` WHERE `hash` = '{$serialized_session_id}'
        ";
        $result = $Db->query($sql);
        if (($Db->get_num_rows($result) > 0)) {
            $row = $Db->get_stream($result);
            $_SESSION = unserialize(base64_decode($row['session']));

            $db_fieldsArr = array(
                'used_ts' => $ts,
            );
            $Db->update('tb_desktop__serialized_sessions', $db_fieldsArr, 'id', "=", $row['id']);

            return $_SESSION;
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $card_ex // example :0917
     *
     * @return boolean
     */
    public static function check_if_card_expiration_is_valid($card_ex): bool
    {
        [$m, $y] = array(substr($card_ex, 0, 2), substr($card_ex, 2, 2));
        $expires = \DateTime::createFromFormat('my', $m.$y);
        $now = new \DateTime();

        return $expires >= $now;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @param string $card_type
     * @return string
     */
    public static function get_credit_card_image($card_type = ''): string
    {
        $card_type = strtolower(trim($card_type));
        $media_id = 0;
        switch ($card_type) {
            case 'isracard':
                $media_id = 504;
                break;
            case 'visa':
                $media_id = 505;
                break;
            case 'diners':
                $media_id = 508;
                break;
            case 'amex':
                $media_id = 506;
                break;
            case 'alphacard':
                $media_id = 507;
                break;
            case 'paypal':
                $media_id = 509;
                break;
        }

        return $media_id ? (new mediaManager($media_id))->path : '';
    }
}