<?

/**
 * @author : gal zalait
 * @desc : Generic user class
 * @var : 1.0
 * @last_update :  02/01/2013
 */
class User
{

	public $id = 0;

	public $user_type = 0; //BITWISE OPTIONS
	public $notification_typesArr = array();


	public $first_name = '';
	public $last_name = '';
	public $email = '';
	public $cellphone = '';
	public $picture = '';
	public $gender = '';
	public $birthday_ts = '';

	public $fbid = '';
	public $apple_id = '';
	public $approveFbShare = 0; // ???
	public $tb_users = 'tb_users';
	const cookie_name = 'groupon_u';
	const prefix = 'grou_';
	const suffix = '_pon';
	const validate_group = true; // ???

	// The amount of minutes to give user to set his password from sending the email
	const restore_password_time = 240; // minutes
	// flag if to decrypt email and password in login and register methods
	static $decrypt = true;

	public $userArr = array();

	public static $user_image_width = 250;
	public static $user_image_height = 250;
	public static $default_image_id = 624;

	private $ts = 0;
	private static $instnace = null;

	public static $table_tokenArr = array(
		1 => 'tb_cg__token', //cg
		2 => 'tb_paypal__token', // paypal
	);

	public $limit_of_favorites = 25;


	/*----------------------------------------------------------------------------------*/

	function __construct()
	{

		$this->ts = time();
		if (isset($_SESSION['user_id'])) {
			$this->id = $_SESSION['user_id'];
		} else if (isset($_COOKIE[self::cookie_name])) {
			$this->connect_from_cookie($_COOKIE[self::cookie_name]);
		}

		if (isset($_SESSION['userArr']) && ($_SESSION['userArr'])) {
			$this->updateUserObject($_SESSION['userArr'], false);
		}

	}

	/*----------------------------------------------------------------------------------*/

	public static function getInstance()
	{
		if (null === self::$instnace) {
			//	echo "new";
			self::$instnace = new User();
		} else {
			//	echo "memory";
		}
		return self::$instnace;
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

	/*----------------------------------------------------------------------------------*/

	private function getCookie($key)
	{
		return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : '');
	}

	/*----------------------------------------------------------------------------------*/

	private function setCookie($key, $value, $expire = 0)
	{
		$_COOKIE[$key] = $value;
		setcookie($key, $value, ($expire == 0 ? time() + 10000000 : $expire), '/', $_SERVER['HTTP_HOST']);
	}

	/*----------------------------------------------------------------------------------*/

	private function delCookie($key)
	{
		$_COOKIE[$key] = '';
		setcookie($key, "", 1, '/', $_SERVER['HTTP_HOST']);
	}

	/*----------------------------------------------------------------------------------*/

	private function connect_from_cookie($hash)
	{
		$Db = Database::getInstance();
		$query = "SELECT id ,fbid,apple_id,email,first_name,last_name,picture
		            FROM `tb_users`
		                WHERE `cookie_hash`='{$hash}' ";
		$ts = time();

		$result = $Db->query($query);

		if ($result->num_rows) {
			$row = $Db->get_stream($result);
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['userArr'] = $row;

			$md5 = md5(self::prefix . trim($row['id']) . self::suffix);
			self::delCookie(self::cookie_name);
			self::setCookie(self::cookie_name, $md5);                    // uses for reload of user from cookie
			$Db->query("UPDATE `tb_users`
										SET `last_login`='{$ts}' 
											WHERE `id`='{$row['id']}'");

			$login_infoArr = array(
				'platform' => 'website',
			);
			self::save_user_login_information($row['id'], (object)$login_infoArr);
		}
	}

	/*----------------------------------------------------------------------------------*/

	public static function user_register($first_name, $last_name, $email, $phone, $phone_prefix, $password, $fbid = "", $apple_id = "", $google_id = "", $register_type = registerEnum::MANUAL, $user_image_url = '')
	{
		$result = array("status" => 1, "err" => "", "data" => "", "message" => "");
		$ts = time();
		$Db = Database::getInstance();
		$social_external_id = null;
		$social_type_column = registerEnum::REGISTER_TYPE_TO_DB_COLUMN[$register_type];
		$allow_editing_email = 0;
		if (in_array($register_type, registerEnum::SOCIAL_LOGIN_TYPES)) {
			$social_external_id = $_REQUEST[registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD[$register_type]];
			$social_type_column = registerEnum::REGISTER_TYPE_TO_DB_COLUMN[$register_type];
			if (!$social_external_id && !$social_type_column) {
				return array("status" => 0, "err" => 164, "data" => "", "message" => ""); // חלה שגיאה בתהליך ההרשמה
			}

			$sql = "SELECT `id` FROM `tb_users` WHERE `{$social_type_column}` = '{$social_external_id}';";

			if ($Db->query($sql)->num_rows > 0) {
				return array("status" => 0, "err" => 164, "data" => "", "message" => "");
			}
		}

		// TODO:: TEMP, remove later
		$register_with_facebook = $fbid && $register_type == registerEnum::FACEBOOK ? true : null;
		$apple_id = null;

		if ($register_with_facebook) {
			if ($phone && $phone_prefix) {
				$allow_editing_email = 1;
			}

		} elseif ($apple_id) {

		} else {
			if ($register_type == registerEnum::MANUAL) {
				$allow_editing_email = 1;
				if (strlen($password) < 8 || (!User::password_policy_verify($password))) {
					return array("status" => 0, "err" => 160, "data" => "", "message" => errorManager::get_message(160)); // סיסמא לא תיקנית
				}
			}
			if ($phone && $phone_prefix) {
				$sql = "SELECT `id` FROM `tb_users` WHERE `cellphone` LIKE '{$phone}' AND `cellphone_prefix` LIKE '{$phone_prefix}'";
				$res = $Db->query($sql);
				if ($res->num_rows) {

					return array("status" => 0, "err" => 166, "data" => "", "message" => ""); // A user with the same cellphone already exists, please sign in
				}
			}

			$sql = "SELECT `id` FROM `tb_users` WHERE `email` = '{$email}'";
			$sqlResult = $Db->query($sql) or db_showError(__FILE__, __LINE__, $sql);
			if ($sqlResult->num_rows > 0) {
				$rowArr = $Db->get_stream($sqlResult);
				if ($rowArr['id']) {
					$sql = "UPDATE `tb_users` SET {$social_type_column} = '{$social_external_id}', `last_update` = {$ts}
                            WHERE `id` = {$rowArr['id']}";
					if ($Db->query($sql)) {
						return $result;
					}

					return array("status" => 0, "err" => 164, "data" => "", "message" => ""); // חלה שגיאה בתהליך ההרשמה
				}

			}
		}
		// setting default push permissions
		$active = ($register_type == registerEnum::MANUAL) || ($register_type == registerEnum::FACEBOOK && ($phone_prefix && $phone));

		// Create User Account
		$password = ($password !== "") ? md5(self::prefix . $password . self::suffix) : "";
		$db_fields = array(
			"first_name" => $first_name,
			"last_name" => $last_name,
			"email" => $email,
			"cellphone" => $phone,
			"cellphone_prefix" => $phone_prefix,
			"password" => $password,
			"register_ts" => $ts,
			"last_login" => $ts,
			"user_type" => $register_type,
			"active" => !$active,
			"allow_editing_email" => $allow_editing_email,
		);

		if ($social_external_id && $social_type_column) {
			$db_fields[$social_type_column] = $social_external_id;
		}

		$user_id = $Db->insert("tb_users", $db_fields);

		if (!$user_id ) {
			$result["status"] = 0;
			$result["err"] = 164; // חלה שגי�?ה במהלך ההרשמה.
		} else {
			$result['data'] = [
				'allow_editing_email' => $allow_editing_email,
				'user_id' => $user_id
			];


		}

		return $result;
	}
	/*----------------------------------------------------------------------------------*/


	static public function user_exists($email = "", $fbid = "", $apple_id = "", $user_id = '', $google_id = "", $cellphone = "",$cellphone_prefix = "")
	{
		$row = null;
		$Db = Database::getInstance();
		$error_code = 168;
		$whereArr = array();
		$status = 0;

		if ($user_id) {
			$error_code = 51001; // User already exist
			$whereArr[] = "`id`='{$user_id}'";
		}
		if ($email !== "") {
			$error_code = 168;
			$whereArr[] = "`email`='{$email}'";
		}
		if ($fbid !== "") {
			$error_code = 51002; // Facebook ID already exist
			$whereArr[] = "`fb_id`='{$fbid}'";
		}
		if ($google_id !== "") {
			$error_code = 51003; // Google ID already exist
			$whereArr[] = "`google_id`='{$google_id}'";
		}
		if ($apple_id !== "") {
			$error_code = 51004; // apple ID already exist
			$whereArr[] = "`apple_id`='{$apple_id}'";
		}
		if ($cellphone !== "" && $cellphone_prefix !== "") {
			$error_code = 51013; //  cellphone already exist
			$whereArr[] = "`cellphone`='{$cellphone}' AND `cellphone_prefix` = '{$cellphone_prefix}'";
		}
		$where = "WHERE " . implode(" OR ", $whereArr);

		$query = "
                SELECT *
                    FROM `tb_users`
                        {$where}
         ";

		$result = $Db->query($query);
		if ($result && $result->num_rows > 0) {
			$status = 1;
			$row = $Db->get_stream($result);
		}
		$function_responseArr = ['status' => $status, 'error_code' => $error_code, 'data' => $row];

		return $function_responseArr;
	}
	/*----------------------------------------------------------------------------------*/

	public static function check_facebook_id($fbid, $user_access_token, $facebook_api_response_fieldsArr = array())
	{
		if (!$facebook_api_response_fieldsArr) {
			include($_SERVER['DOCUMENT_ROOT'] . '/resource/FacebookSDK/facebookSDK.php');
		}
		if (!is_array($facebook_api_response_fieldsArr) || ($facebook_api_response_fieldsArr["id"] != $fbid)) {
			return 172;
		}

		return $facebook_api_response_fieldsArr;
	}

	/*----------------------------------------------------------------------------------*/

	public static function create_connect_facebook_account_token($email, $fbid, $password = '')
	{
		global $website_url, $Seo;
		$Db = Database::getInstance();

		$ts = time();
		$token_max_age = $ts - configManager::$facebook_account_connect_token_lifetime;
		if ($password != '') {
			$password = md5(self::prefix . $password . self::suffix);
		}

		// Check if token already exists for given email. If so - update created time
		$sql = "
            SELECT `id`, `fbid`, `token` FROM `tb_users_facebook_account_connect_tokens` WHERE `email` = '{$email}' AND `created_ts` <= '{$token_max_age}' AND `used_ts` = 0
        ";
		$result = $Db->query($sql) or db_showError(__FILE__, __LINE__, $sql);
		if ($result->num_rows) {
			$rowArr = $Db->get_stream($result);
			if ($rowArr['fbid'] != $fbid) {
				return 158; // כבר בוצע ניסיון לחיבור חשבון זה לחשבון פייסבוק אחר
			} else {
				$db_fieldsArr = array(
					'created_ts' => $ts,
				);
				siteFunctions::update_db('tb_users_facebook_account_connect_tokens', $db_fieldsArr, 'id', $rowArr['id']);

				$token = $rowArr['token'];
			}
		} else {
			$token = sha1($email . $fbid . $ts . rand(999, 9999));

			$db_fieldsArr = array(
				'email' => $email,
				'fbid' => $fbid,
				'token' => $token,
				'used_ts' => 0,
				'created_ts' => $ts,
			);
			if ($password != '') {
				$db_fieldsArr['password'] = $password;
			}
			siteFunctions::insert_to_db('tb_users_facebook_account_connect_tokens', $db_fieldsArr);
		}

		$mail_content = str_replace('[token_url]', siteFunctions::get_environment_url() . '/' . $Seo->getStaticUrl(4, 3) . '?token=' . $token, lang('facebook_account_token_mail_content'));
		siteFunctions::send_mail($email, lang('facebook_account_token_mail_subject'), $mail_content);

		return true;
	}

	/*----------------------------------------------------------------------------------*/

	public static function connect_facebook_account_to_user($token)
	{
		$Db = Database::getInstance();
		$ts = time();
		$token_max_age = $ts - configManager::$facebook_account_connect_token_lifetime;

		$sql = "
            SELECT Users.`id`, Token.`password`, Token.`fbid` FROM `tb_users_facebook_account_connect_tokens` AS Token
              LEFT JOIN `tb_users` AS Users ON Users.`email` = Token.`email`
            WHERE Token.`token` = '{$token}' AND Token.`created_ts` > '{$token_max_age}' AND Token.`used_ts` = 0
        ";
		$result = $Db->query($sql) or db_showError(__FILE__, __LINE__, $sql);
		if (!$result->num_rows) {
			return array('status' => 0, 'err' => 157, 'data' => array()); // קישור פג תוקף
		}
		$rowArr = $Db->get_stream($result);

		if ($rowArr['password'] != '') {
			$db_fieldsArr = array(
				'password' => $rowArr['password'],
				'last_update' => $ts,
			);
		} else {
			$db_fieldsArr = array(
				'fbid' => $rowArr['fbid'],
				'last_update' => $ts,
			);
		}

		siteFunctions::update_db('tb_users', $db_fieldsArr, 'id', $rowArr['id']);

		$db_fieldsArr = array(
			'used_ts' => $ts,
		);
		siteFunctions::update_db('tb_users_facebook_account_connect_tokens', $db_fieldsArr, 'token', $token);

		return array('status' => 1, 'err' => 0, 'data' => $rowArr);
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * user facebook registertion
	 * enter to db with fbid!=''
	 */

	static public function fb_register($fbData)
	{
		global $fbConf, $facebookGroups;
		$Db = Database::getInstance();

		/*
		accessToken: "AAACsPOGwpzgBACOYGR5XOZCk3BjJlTt8ZAt7ddcocdjT7sJmJR58Un4MEZA94ubYBJaTbnPrRZAEtBd2sMJ2vUMfBwn1fb1qLlqZAjX0yEAZDZD"
		expiresIn: 4653
		signedRequest: "z8VknnYwW9qCc7PH3VwY3ALWTVCt4CSPatCpTb1CEB8.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImNvZGUiOiJBUUJMY0xKenlxcTMxRzgxV2tMUFVOVk1xQ1otUW9DNWNEMVh3YTc5TDRKNEdmbHlRX3VBZEh4cUtjQ1kzdXhxTTV1bmFlVUtBcWJpS3RTQ1pKMkdqUThLMUlrTHpwd2w0NFg4ZTlhM0FNQVloekJwZXA0bjJrM3JZT2VWNHpEZHF3QlhXcm5ZUHFCQ2cwN0ZUYlc5RnlVZEJWSmJPd3Atbzh3bG1UYXUyNEtsVkdoR3hycUNGLVBQMVMxRkJJLXBQYjQiLCJpc3N1ZWRfYXQiOjEzMzI3MTE3NDcsInVzZXJfaWQiOiI2MTM5MjgzNjQifQ"
		userID: "613928364"
		*/
		include($_SERVER['DOCUMENT_ROOT'] . '/_inc/vendors/facebook/facebook.php');
		$facebook = new Facebook($fbConf);
		if (!empty($fbData) && isset($fbData['userID'])) {

			$myFacebookDetails = $facebook->api("/me?return_ssl_resources=1&fields=picture,first_name,last_name,email&type=square");

			$profile_pic_extention = pathinfo($myFacebookDetails['picture'], PATHINFO_EXTENSION);

			/*
			[first_name] => Roni
			[last_name] => Sapojnic
			[email] => roni@sourcecode.co.il
			[id] => 613928364
			[picture] => https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc4/260921_613928364_2044551948_t.jpg
			*/

			$res = $Db->query("SELECT * FROM `tb_users` WHERE `email`='{$myFacebookDetails['email']}' AND `fbid`='{$myFacebookDetails['id']}'");
			$fb_user_exists = mysql_fetch_assoc($res);

			$groups_ids = implode(",", array_keys($facebookGroups));
			$fql = "select gid, uid from group_member where uid={$myFacebookDetails['id']} AND gid IN ($groups_ids)";
			$userInGroups = $facebook->api(array('method' => 'fql.query', 'query' => $fql, 'callback' => ''));

			$pass = $myFacebookDetails['id'] . '_fb';

			if (!$fb_user_exists) {
				// new user

				$create_user = false;

				if (isset($_SESSION['fb_requests'])) {
					$create_user = true;
				} else {
					// not from invite
					if (validate_group) {
						if (empty($userInGroups)) {
							return array('citywall_id' => 0, 'new_pass' => '', 'fbDetails' => array(), 'err' => 'groups');
						} else {
							$create_user = true;
						}
					}
				}
				if ($create_user) {
					$db_fields = array(
						"email" => $myFacebookDetails['email'],
						"password" => md5('city_' . $pass . '_wall'),
						"status" => 1,
						"ip" => $_SERVER['REMOTE_ADDR'],
						"join_ts" => time(),
						"last_update" => time(),
						"fb_img" => $myFacebookDetails['picture'],
						"first_name" => $myFacebookDetails['first_name'],
						"last_name" => $myFacebookDetails['last_name'],
						"fbid" => $myFacebookDetails['id'],
						"approveFbShare" => '1',
					);


					if (!empty($userInGroups)) {
						$db_fields['active_fb_group'] = '1'; // user is using one of the groups
					}

					foreach ($db_fields AS $key => $value) {
						$db_fields[$key] = $Db->make_escape($value);
					}
					$query = "INSERT INTO `tb_users` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
					$res = $Db->query($query) or die($query . mysql_error());

					$new_user_id = mysql_insert_id();

					$cookie_hash = md5(self::prefix . trim($new_user_id) . self::suffix);
					$cookie_hash_query = "UPDATE `tb_users` SET `cookie_hash` = '". $cookie_hash ."' WHERE `id` = '". $new_user_id ."'";
					$cookie_hash_result = $Db->query($cookie_hash_query) or db_showError(__FILE__, __LINE__, $query);

					if (isset($_SESSION['fb_requests']) && !empty($_SESSION['fb_requests'])) {
						// update requests in DB
						$approve_ts = time();
						foreach ($_SESSION['fb_requests'] AS $index => $request_data) {
							$Db->query("UPDATE `tb_facebook_invites` SET `to_user_id`='{$new_user_id}',`approved`='{$approve_ts}' WHERE `fb_request_id`='{$request_data['fb_request_id']}' AND `id`='{$request_data['id']}'");
							// DELETE real facebook request
							$full_request_id = $request_data['fb_request_id'] . '_' . $myFacebookDetails['id'];
							$delete_success = $facebook->api("/$full_request_id", 'DELETE');
							if (!$delete_success) {
								mail("ronip@inmanage.net", "unable to delete invite", var_export($_SESSION['fb_requests'], true) . PHP_EOL . var_export($db_fields, true));
							}
						}
						unset($_SESSION['fb_requests']);
					}


					$profile_pic = file_get_contents($myFacebookDetails['picture']);
					if ($profile_pic_extention == 'gif') {
						file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/_media/users/' . $new_user_id . '_temp.gif', $profile_pic);
						$gd_im = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'] . '/_media/users/' . $new_user_id . '_temp.gif');
						imagejpeg($gd_im, $_SERVER['DOCUMENT_ROOT'] . '/_media/users/' . $new_user_id . '.jpg', 100);
						imagedestroy($gd_im);
					} else {
						file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/_media/users/' . $new_user_id . '.jpg', $profile_pic);
					}

					$query = "UPDATE `tb_users` SET `img_ext`='{$profile_pic_extention}' WHERE `id`='{$new_user_id}'";
					mysql_unbuffered_query($query);

					$_REQUEST['inner_id'] = $new_user_id;
					$UpdateStatic = new usersUpdateStaticFiles();
					$UpdateStatic->updateStatics();
				}
			} else {
				// user exists
				$new_user_id = $fb_user_exists['id'];
			}

			return array('citywall_id' => $new_user_id, 'new_pass' => $pass, 'fbDetails' => $myFacebookDetails);

		}
	}

	/*----------------------------------------------------------------------------------*/

	/**
	 * connect fb user
	 * if password is valid insert data to session
	 */
	static public function fb_connect($email, $password)
	{
		$Db = Database::getInstance();

		$ts = time();
		$md5_str = md5(self::prefix . trim($password) . self::suffix);
		$query = "
				SELECT id,email,first_name,last_name
					FROM `tb_users`	
						WHERE `email`='{$email}' 
							AND
							 `password`='{$md5_str}'
							AND 
								`fbid`!=''
							AND 
								`status`=1	
		 ";

		$result = $Db->query($query) or die($query);

		if ($result->num_rows > 0) {
			$row = $Db->get_stream($result);
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['userArr'] = $row;
			$md5 = md5(self::prefix . trim($row['id']) . self::suffix);
			self::setCookie(self::cookie_name, $md5);                    // uses for reload of user from cookie
			mysql_unbuffered_query("UPDATE `tb_users` 
										SET `last_login`='{$ts}' 
											WHERE `id`='{$row['id']}'");
			return true;
		} else {
			return false;
		}
	}


	private static function get_password_hash($password_string)
	{
		return md5(self::prefix . trim($password_string) . self::suffix);
	}
	/*----------------------------------------------------------------------------------*/
	/**
	 * connect reg user
	 * if password is valid insert data to session
	 */
	static public function user_connect($email = '', $password = '', $register_type = '', $user_id = '')
	{
		$ts = time();
		$Db = Database::getInstance();
		$user_id = intval($user_id);

		$email = $Db->make_escape($email);
		$password = $Db->make_escape($password);

		if ($register_type == registerEnum::FACEBOOK || $register_type == registerEnum::GOOGLE) {
			$social_external_id = $_REQUEST[registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD[$register_type]];
			$social_type_column = registerEnum::REGISTER_TYPE_TO_DB_COLUMN[$register_type];

			$where = "AND `email`='{$email}' AND `{$social_type_column}` = '{$social_external_id}'"; //connect by user id only.

		} elseif ($register_type == registerEnum::APPLE) {
			$social_external_id = $_REQUEST[registerEnum::REGISTER_TYPE_TO_REQUEST_FIELD[$register_type]];
			$social_type_column = registerEnum::REGISTER_TYPE_TO_DB_COLUMN[$register_type];

			$where = "AND `{$social_type_column}` = '{$social_external_id}'"; //connect by user id only.
		} else {
			$md5_str = self::get_password_hash($password);
			$where = "AND `email`='{$email}' AND `password`='{$md5_str}'";
		}

		if ($user_id) {
			$where = "AND `id` = '{$user_id}'"; //connect by user id only.
		}

		$query = "
                SELECT * FROM `tb_users` WHERE `id` <> 0 {$where}
         ";

		// the check if id is not zero is always true and is there only to concat the query($where)
		$result = $Db->query($query);

		if ($result->num_rows > 0) {
			$row = $Db->get_stream($result);
			self::set_user_session($row);
			$Db->query("UPDATE `tb_users`
                                        SET `last_login`='{$ts}'
                                            WHERE `id`='{$row['id']}'");

			return ['status' => 1, 'user_active' => $row['active'], 'userArr' => $row];
		} else {
			return ['status' => 0];
		}
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * update the tb_user_item_visits table and replace the udid / Cookie value to user id
	 */
	static public function update_user_visit_log($user_id)
	{
		$tb_name = 'tb_user_item_visits';
		$Db = Database::getInstance();

		if ($identifier = self::get_unconnected_user_identifier()) {
			$query = " update {$tb_name} SET `user_id`={$user_id}
 					WHERE `identifier` = '{$identifier}'
					";
			$Db->unbuffered_query($query);
		}
	}

	/*----------------------------------------------------------------------------------*/

	static public function remember_me($user_id)
	{
		$md5 = md5(self::prefix . trim($user_id) . self::suffix);
		self::setCookie(self::cookie_name, $md5);                    // uses for reload of user from cookie
	}


	/*----------------------------------------------------------------------------------*/

	static public function user_logout()
	{
		unset($_SESSION['user_id']);
		unset($_SESSION['userArr']);
		self::delCookie(self::cookie_name);
	}

	/*----------------------------------------------------------------------------------*/

	public function user_details_recovery($email)
	{
		/** send mail with uniuq hash **/
	}

	/*----------------------------------------------------------------------------------*/

	public function get_user_picture()
	{
		$is_base64_image = false;
		if ($this->picture) {
			$is_base64_image = true;
			$picture = $this->picture;
		} else if ($this->fbid) {
			$picture = $this->get_facebook_image($this->fbid, array("width" => self::$user_image_width, "hegiht" => self::$user_image_height));
		} else {
			global $Api;
			$Image = new mediaManager(self::$default_image_id);
			$picture = "https://" . $_SERVER['HTTP_HOST'] . str_replace(self::$default_image_id, self::$default_image_id, $Image->path);
		}
		return array("picture" => $picture, "is_base64_image" => $is_base64_image);

	}

	/*----------------------------------------------------------------------------------*/

	public function get_facebook_image($fbid, $facebook_params = array())
	{
		$fw_params = http_build_query($facebook_params);
		return 'https://graph.facebook.com/' . $fbid . '/picture?' . $fw_params;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * Method: get_login_data
	 *
	 * return user data
	 *
	 * @expect_values :
	 *    null
	 *
	 * @return Array
	 * @example:
	 * {
	 * "user_id": "16",
	 * "first_name": "Nechami",
	 * "last_name": "Karelitz",
	 * "email": "nechami@inmanage.net",
	 * "gender": "2",
	 * "picture": "", - link
	 * "show_option_to_send_call_log": true,
	 * }
	 */
	public function get_login_data()
	{
		global $project_dev_url, $project_stage_url;
		$Db = Database::getInstance();

		$answerArr = array();

		$query = "
                    SELECT *
                        FROM `tb_users`
                            WHERE `id` = {$this->id}
                                AND `active` = 1
                    ";
		$result = $Db->query($query);
		if ($result->num_rows) {
			$row = $Db->get_stream($result);

			$pictureArr = $this->get_user_picture();
			//$addressArr = $this->getUserAddress($row['id']);
			$cardsArr = self::getCreditCards($this->id, 0);
			//$favoritesArr = array_keys($this->getUserFavorites());

			//$push_notificationArr = $this->get_push_notifications_info();


			$answerArr = array(
				"user_id" => $row["id"],
				"first_name" => $row["first_name"],
				"last_name" => $row["last_name"],
				"fbid" => $row["fbid"],
				"apple_id" => $row["apple_id"],
				"picture" => $pictureArr["picture"],
				"email" => $row["email"],
				"phone" => $row["cellphone"],
				"gender" => $row["gender"],
				"cardsArr" => $cardsArr,
				"user_type" => $row["user_type"],
				//"addressArr" => array_values($addressArr),
				//"favoritesArr" => $favoritesArr,
			);

			$answerArr = $this->add_user_settings($answerArr);
		}

		return $answerArr;
	}


	/*----------------------------------------------------------------------------------*/


	public function get_push_notifications_info()
	{
		$push_notification_infoArr = array();
		$Db = Database::getInstance();

		$query = "
			SELECT `udid`, `idfa`, `token_device`, `device_type` FROM `tb_push__device`
				WHERE `user_id` = '{$this->id}'
		";
		$result = $Db->query($query);

		if($result->num_rows) {
			$row = $Db->get_stream($result);
			$push_notification_infoArr = $row;
		}

		return $push_notification_infoArr;
	}

	/*----------------------------------------------------------------------------------*/
	/*
	 * Method: startRestorePassword
	 *
	 * first step for resoring password.
	 * send email with a dipp link that is active for 30 minutes (editable), to fill the new password.
	 *
	 * @expect_values :
	 * $user_id; Required Integer: 43
	 * $email; Required String: test@test.com
	 *
	 * @return Boolean
	 */
	public static function startRestorePassword($user_id, $email, $try = 0)
	{
		global $website_url;
		$ts = time();
		$Db = Database::getInstance();

		$query = "INSERT INTO `tb_users_restore_password` (`user_id`, `send_ts`,`try`)
                        VALUES ({$user_id}, {$ts},{$try})";
		$Db->query($query);
		if (mysql_error()) {
			return false;
		} else {
			$expire_ts = intval($ts) + (self::restore_password_time * 60);
			$email_md5 = md5(self::prefix . $email . self::suffix);

			global $Seo;
			$site_url = configManager::$website_url . '/' . $Seo->getStaticUrl(4, 2) . '?param='; // TODO: set url by corresponding environment

			$paramsArr = array(
				'token' => $email_md5,
				'expire_ts' => $expire_ts
			);
			$link = $site_url . base64_encode(json_encode($paramsArr));

			$query = "SELECT `first_name` FROM `tb_users`
                WHERE `id`={$user_id}
            ";
			$row = mysql_fetch_assoc($Db->query($query));
			$name = $row['first_name'];

//			$mail_template_path = $_SERVER["DOCUMENT_ROOT"] . '/_media/emails/restore_password.inc.php';
			/*	$html = <<< HTML
						הכנס ללינק הבא:
						<br/>
						<br/>
						<a href="{$link}">שחזור סיסמא</a>
	HTML;*/
			$htmlArr['reset_password_link'] = $link;
			$htmlArr['to_name'] = $name;
			siteFunctions::send_mail($email, lang("restore_password_email_title"), '', $htmlArr, 'reset_password');
			return true;
		}
	}

	/*----------------------------------------------------------------------------------*/
	/*
	 * Method: restorePassword
	 *
	 * set new password for user if didn't pass 30 minuets from sending the restore email
	 *
	 * @expect_values :
	 * $password; Required String: "1234"
	 * $token; Required String - md5(self::prefix . $email . self::suffix)
	 *
	 * @return Boolean
	 */
	public static function restorePassword($password, $token)
	{
		$Db =  Database::getInstance();
		$query = "SELECT Users.`id`, TIMESTAMPDIFF(MINUTE, FROM_UNIXTIME(Restore.`send_ts`), NOW()) AS send_past_minutes, Restore.used_ts FROM `tb_users` AS Users
                        LEFT JOIN `tb_users_restore_password` AS Restore
                            ON (
                                Users.`id` = Restore.`user_id`
                            )
                        WHERE MD5(CONCAT('" . self::prefix . "',Users.`email`,'" . self::suffix . "'))='{$token}'
                            ORDER BY Restore.`send_ts` DESC
                                LIMIT 1";
		$result = $Db->query($query);
		if ($result->num_rows == 0) {
			return array("status" => 0, "err" => 180); // משתמש לא קיים במערכת
		}
		$row = $Db->get_stream($result);
		if ($row['used_ts'] > 0) {
			return array("status" => 0, "err" => 155); // קישור שחזור סיסמא לא תקין
		}
		if ($row["send_past_minutes"] === null || $row["send_past_minutes"] > self::restore_password_time) {
			return array("status" => 0, "err" => 200); // תוקף שחזור הסיסמא פג. התחל שחזור סיסמא מחדש
		}

		$md5_str = md5(self::prefix . $password . self::suffix);
		$query = "UPDATE `tb_users`
                    SET `password`='{$md5_str}'
                        WHERE `id` = {$row['id']}";
		$Db->query($query);
		if ($Db->query($query)) {
			return array("status" => 0, "err" => 181); // חלה תקלה במהלך שחזור הסיסמא, אנא נסה שוב
		} else {
			$now = time();
			$query = "
                UPDATE `tb_users_restore_password` SET `used_ts` = '{$now}' WHERE `user_id` = '{$row['id']}' AND `used_ts` = '0'
            ";
			$result = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
			return array("status" => 1);
		}
	}

	/*----------------------------------------------------------------------------------*/
	/*
	 * Method: editUserInformation
	 *
	 * set user details
	 *
	 * @expect_values :
	 * $userInformationArr; Required Array: array (
	"first_name": Nechami,
	"last_name": Karelitz,
	"multi_azrieli": 0,
	"newsletter": 0,
	"picture": - base64
	)
	 *
	 * @return Boolean
	 */
	public function editUserInformation($userInformationArr)
	{
		$Db = Database::getInstance();

		$db_fieldsArr = array();
		foreach ($userInformationArr as $key => $value) {
			unset($userInformationArr[$key]);
			$key = $Db->make_escape($key);
			if ($key !== "picture") {
				$value = $Db->make_escape($value);
			}
			if ($key !== "" && $value !== "00") {
				$userInformationArr[$key] = $value;

				$db_fieldsArr[] = "`{$key}` = '{$value}'";
			}

		}
		if (empty($db_fieldsArr)) {
			return false;
		}
		$query = "UPDATE `tb_users`
                    SET " . implode(",", array_values($db_fieldsArr)) . "
                        WHERE `id` = {$this->id}";
		$Db->query($query);
		if (mysql_error()) {
			return false;
		} else {
			$this->updateUserObject($userInformationArr, true);

			return true;
		}
	}

	/*----------------------------------------------------------------------------------*/

	public function updateUserObject($fieldsArr, $update_session = true)
	{
		foreach ($fieldsArr as $key => $value) {
			if ($update_session) {
				$_SESSION["userArr"][$key] = $value;
			}
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}

		$userArr = $_SESSION['userArr'];
		$this->userArr = $userArr;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * Method: save_user_login_information
	 *
	 * save user login information
	 *
	 * @return null
	 */

	public static function save_user_login_information($user_id, $info)
	{
		$ts = time();
		$Db = Database::getInstance();

		$db_fieldsArr = array(
			"user_id" => $user_id,
			"application_version" => $_SESSION['api']['application_version'],
			"server_version" => $info->version,
			"platform" => $info->platform,
			"ip" => $_SERVER['REMOTE_ADDR'],
			"last_update" => $ts
		);
		$Db->insert('tb_user_login_information',$db_fieldsArr);

	}

	/*----------------------------------------------------------------------------------*/

	public static function get_unconnected_user_identifier()
	{
		switch ($_SESSION['api']['platform']) {
			case "iphone":
			case "android":
				return $_SESSION['api']['udid'];
				break;

			case "website":
				return cookieManager::getCookie('identifier');
				break;
		}
	}


	/*----------------------------------------------------------------------------------*/
	public static function get_user_info($user_id)
	{
		$Db = Database::getInstance();

		$query = "SELECT `email`,`newsletter`,`birthday_ts`,`cellphone`,`gender` FROM `tb_users`
			WHERE `id`={$user_id}
		";

		$row = $Db->get_stream($Db->query($query));
		return $row;
	}


	/*----------------------------------------------------------------------------------*/

	/**
	 * Method Name: saveUserAddress
	 *
	 * This method inset or update user address
	 *
	 * @param $city_id - Required number: 1
	 * @param $street_id - Required number: 1
	 * @param $house_number - Required number: 1
	 * @param $apartment_number - Required number: 1
	 * @param $enter_number - Not Required string: 1
	 * @param $zip_code - Not Required number: 1
	 * @param int $address_id - Not Required (Only to update) number: 1
	 */
	public function saveUserAddress($city_id, $street_id, $house_number, $apartment_number, $enter_number = '', $zip_code = 0, $po_box = 0, $notes = '', $address_id = 0, $first_name = '', $last_name = '', $phone = '')
	{
		$Db = Database::getInstance();

		$PostManager = new PostManager();
		$post_zip_code = $PostManager->getZipCode($city_id, $street_id, $house_number, $enter_number);

		if ($post_zip_code) { // if found zip code from post override the zip code that get in method param
			$zip_code = $post_zip_code;
		}

		$db_fields = array(
			'user_id' => $this->id,
			'city_id' => $city_id,
			'street_id' => $street_id,
			'house_number' => $house_number,
			'apartment_number' => $apartment_number,
			'enter_number' => $enter_number,
			'zip_code' => $zip_code,
			'po_box' => $po_box,
			'notes' => $notes,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'phone' => $phone,
			'active' => 1,
			'last_update' => time()
		);

		if ($address_id) { // update
			$updateArr = array();
			foreach ($db_fields as $k => $v) {
				//$v = $Db->make_escape($v);
				$updateArr[] = "`$k` = '{$v}' ";
			}

			$query = "UPDATE `tb_user_address` SET  " . implode(',', $updateArr) . " WHERE `id` = {$address_id}";
			$Db->query($query);
		} else { // insert
			foreach ($db_fields AS $key => $value) {
				//$db_fields[$key] = $Db->make_escape($value);
				$db_fields[$key] = $value;
			}

			$query = "INSERT INTO `tb_user_address` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
			$Db->query($query);

			$address_id = mysql_insert_id();
		}

		return $address_id;
	}

	/*----------------------------------------------------------------------------------*/

	/**
	 * Method Name: getUserAddress
	 *
	 * @param $user_id
	 * @return array
	 */
	public function getUserAddress($user_id = 0)
	{
		$user_id = ($user_id) ? $user_id : $this->id;
		$user_addressArr = array();
		$Db = Database::getInstance();

		$query = "SELECT UserAddress.*, City.`name` AS `city_name`, Street.`name` AS `street_name`, UserAddress.`phone`
					FROM `tb_user_address` AS UserAddress
						LEFT JOIN `tb_post_cities` AS City ON (
							UserAddress.`city_id` = City.`id`
						)
						LEFT JOIN `tb_post_streets` AS Street ON (
							UserAddress.`street_id` = Street.`id`
						)
						WHERE `user_id` = {$user_id}
		";
		$res = $Db->query($query);

		if (mysql_num_rows($res)) {
			while ($line = mysql_fetch_assoc($res)) {
				$apartment = $line['apartment_number'] > 0 ? ' ' . lang('address_apartment') . ' ' . $line['apartment_number'] : '';
				$street = $line['street_id'] ? $line['street_name'] . ' ' . $line['house_number'] . $apartment : ' ת.ד: ' . $line['po_box'];

				$addressArr = array(
					'address_id' => $line['id'],
					'title' => $street . ', ' . $line['city_name'] . ($line['phone'] ? ', ' . siteFunctions::format_phone($line['phone']) : ''),
					'street_name' => $line['street_name'],
					'street_id' => $line['street_id'],
					'city_id' => $line['city_id'],
					'city_name' => $line['city_name'],
					'house_number' => $line['house_number'],
					'apartment_number' => $line['apartment_number'],
					'enter_number' => $line['enter_number'],
					'zip_code' => $line['zip_code'],
					'po_box' => $line['po_box'],
					'notes' => $line['notes'],
					'phone' => $line['phone'],
					'order_num' => $line['id']
				);

				$user_addressArr[$line['id']] = $addressArr;
			}
		}

		return $user_addressArr;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * Method: getCreditCards
	 *
	 * get credit cars for user
	 *
	 * @expect_values :
	 * $user_id; Required Integer: 123
	 * $payment_id; Optional Ineteger: 1 - 0 for all
	 * $full_info; Required Boolean: false - for paying use true
	 *
	 * @return Array
	 * @example:
	 * {
	 * "1": {
	 * "98": {
	 * "id": "98",
	 * "title": "Visa",
	 * "card_mask": "4580",
	 * "image": "/_media/media/55/505.png?t=1457940160",
	 * "payment_type": 1,
	 * "order_num": 1
	 * },
	 * "100": {
	 * "id": "100",
	 * "title": "Visa",
	 * "card_mask": "0000",
	 * "image": "/_media/media/55/505.png?t=1457940160",
	 * "payment_type": 1,
	 * "order_num": 2
	 * }
	 * }
	 * }
	 */

	public static function getCreditCards($user_id, $payment_id = 1, $full_info = false)
	{
		$itemsArr = array();
		$Db = Database::getInstance();

		if ($payment_id == 0) {
			foreach (User::$table_tokenArr AS $payment_id => $token_table) {
				$credit_cardsArr = User::getCreditCards($user_id, $payment_id, $full_info);
				if (count($credit_cardsArr) > 0) {
					$itemsArr[$payment_id] = $credit_cardsArr;
				}
			}
			return $itemsArr;
		}

		$query = "SELECT * FROM `" . User::$table_tokenArr[$payment_id] . "`
					WHERE `user_id`= {$user_id}
						AND `save_card` = 1
                    ORDER BY `last_update` DESC
				";
		$result = $Db->query($query);
		$sort_order = 0;

		if ($result->num_rows) {
			while ($row = $Db->get_stream($result)) {
				if ($payment_id == 1) {// cg cards
					if (!siteFunctions::check_if_card_expiration_is_valid($row['cardExpiration'])) {
						continue;
					}
				}

				$image_path = siteFunctions::get_credit_card_image($row['creditCompany']);

				if ($full_info === true) {
					$itemsArr[$row['id']] = $row;
				} else {
					$itemsArr[$row['id']] = array(
						"id" => $row['id'],
						"title" => $row['title'] ? $row['title'] : self::get_credit_card_default_name($row['creditCompany']),
						"card_mask" => $row['cardMask'], //substr($row['cardMask'], -4, 4),
						"last_digits" => substr($row['cardMask'], -4, 4),
						"image" => $image_path,
						"payment_type" => $payment_id,
						"order_num" => ++$sort_order
					);
				}
			}
		}

		return $itemsArr;
	}

	/*----------------------------------------------------------------------------------*/

	public static function get_credit_card_default_name($credit_company)
	{
		$key = 'cg_' . strtolower($credit_company);
		$title = lang($key);
		$title = $title != $key ? $title : $credit_company;

		return $title;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * edit_card
	 * @param $card_id
	 * @param string $title
	 * @return int
	 * update user card title
	 */
	public function edit_card($card_id, $title = '', $payment_id)
	{
		$Db = Database::getInstance();

		$title = $Db->make_escape($title);
		$query = "UPDATE `" . self::$table_tokenArr[$payment_id] . "`
						SET `title`='{$title}'
							WHERE `id`='{$card_id}'
								AND `user_id`='{$this->id}'
				  ";

		return $Db->query($query) or mail('netanel@inmanage.net', 'log-edit card title', print_r(array($query, mysql_error()), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");;
	}

	/*----------------------------------------------------------------------------------*/

	public static function card_belongs_to_user($user_id, $card_id)
	{
		$cardsArr = self::getCreditCards($user_id);

		$valid = false;
		if (array_key_exists($card_id, $cardsArr)) {
			$valid = true;
		}

		return $valid;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * remove_token
	 * @param $card_id
	 * @param $payment_id
	 * @return int
	 * remove card from list
	 */
	public function remove_token($payment_id = 1, $card_id)
	{
		$Db = Database::getInstance();

		$card_id = intval($card_id);

		$query = "
			UPDATE  `" . self::$table_tokenArr[$payment_id] . "`
			 	SET  `save_card` = 0
			 	WHERE `user_id` = {$this->id}  And `id`= {$card_id}
		";

		return $Db->query($query);
	}







	/*----------------------------------------------------------------------------------*/

	public function getObligoMoney($price_format = true)
	{
		include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/obligoManager.class.inc.php');

		$result = obligoManager::get_user_current_amount($this->id);
		if ($price_format) {
			return siteFunctions::format_price($result);
		} else {
			return $result;

		}
	}


	/*----------------------------------------------------------------------------------*/

	public static function get_full_address($city_id, $street_id, $house_number, $zip_code, $po_box = 0)
	{
		include($_SERVER['DOCUMENT_ROOT'] . '/_static/post_cities.he.inc.php'); //$post_citiesArr
		include($_SERVER['DOCUMENT_ROOT'] . '/_static/post_streets.he.inc.php'); //$post_streetsArr

		$city_name = $post_citiesArr[$city_id];
		if (!$po_box) {
			$street_name = $post_streetsArr[$street_id] . ($house_number > 0 ? ' ' . $house_number : '');
			$address = ($street_name ? $street_name : '') . ($street_name ? ', ' : '') . ($city_name ? $city_name : '') . ($zip_code ? ', ' . $zip_code : '');
		} else {
			$address = lang('address_mail_box_label') . ' ' . $po_box . ', ' . $city_name;
		}


		return $address;
	}

	/*----------------------------------------------------------------------------------*/

	public function register_idfa($idfa)
	{
		$Db = Database::getInstance();

		$ts = time();
		$Db =Database::getInstance();
		$query = "
			UPDATE `" . $this->tb_users . "`
                SET `idfa` = '{$idfa}',
                    `last_update` = {$ts}
                WHERE `id` = '{$this->id}'
        ";
		$Db->query($query);
	}


	/*----------------------------------------------------------------------------------*/

	public static function password_policy_verify($password)
	{
		$regex = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d$@$!%*#?&]{8,}$/i';
		return preg_match($regex, $password);
	}


	/*----------------------------------------------------------------------------------*/

	public function add_user_settings($userArr)
	{
		if(in_array($_SERVER['REMOTE_ADDR'],array('62.219.212.139','81.218.173.175','37.142.40.96','80.246.133.152','2.53.168.197','2.53.131.252','2.55.71.109','2.53.141.6'))) {
			if ($userArr['user_type'] & UsersTypesEnum::SHOW_TOASTS) {
				$userArr['show_toast'] = true;
			}
			if ($userArr['user_type'] & UsersTypesEnum::SHOW_CALLS_LOG) {
				$userArr['show_option_to_send_call_log'] = true;
			}
		}

		$userArr['show_dev_mode_option'] = false;
		if ($userArr['user_type'] & UsersTypesEnum::ENVIRONMENTS_ACCESS) {
			$userArr['show_dev_mode_option'] = true;
			$userArr['allowed_environmentsArr'] = siteFunctions::get_environments_settingsArr();
		}
		return $userArr;
	}


	/*----------------------------------------------------------------------------------*/
}

?>
