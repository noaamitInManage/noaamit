<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 20/06/2017
 * Time: 9:45
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/vendors/Facebook/autoload.php');

class FacebookManager extends BaseManager implements SocialProvider
{
    /**
     * Facebook configuration details
     * @var array
     */
    private $configArr = array(
        'app_id' => '1443478335706508',
        'app_secret' => 'f6365d8cd4af776c2468d26d8f2b41b2',
        'default_graph_version' => 'v2.9',
    );

    /**
     * The table on the database
     * @var string
     */
    private $tb_name = 'tb_users__facebook_information';

    /**
     * User access token
     * @var null
     */
    private $access_token = null;

    /**
     * Api object (set in the constructor)
     * @var null
     */
    public $api = null;

    /**
     * Api graph object (me)
     * @var null
     */
    public $me = null;

    /*----------------------------------------------------------------------------------*/
    /**
     * FacebookManager constructor.
     * @param null $access_token
     */
    public function __construct($access_token = null)
    {
        parent::__construct();

        // Initialize the facebook object with the configuration arrray
        $this->api = new \Facebook\Facebook($this->configArr);

        // Set access token if sent
        $this->access_token = $access_token;

        if ($access_token !== null) {
            $response = null;
            $has_facebook_connection = true;

            // try getting the user. or throw error
            try {
                $response = $this->api->get('/me?fields=id,name,birthday,gender,picture.width(160).height(160),work', $this->access_token);
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                $has_facebook_connection = false;
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                $has_facebook_connection = false;
            }

            // Set the user graph object
            if ($has_facebook_connection) {
                $this->me = $response->getDecodedBody();
            }
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name has_access
     * @description Checks if we have access to the user profile
     * @return bool
     */
    public function has_access()
    {
        return count($this->me) > 0;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_userArr
     * @description Returns user array
     * @return array
     */
    public function get_userArr()
    {
        return array(
            'social_id' => $this->me['id'],
            'name' => $this->me['name'],
            'gender' => $this->get_gender(),
            'birthday' => $this->get_birthday(),
            'job_title' => $this->get_current_position(),
            'picture_url' => $this->get_profile_picture_url(),
            'profile_url' => $this->get_profile_url(),
        );
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_gender
     * @description Returns the user gender id
     * @return int
     */
    public function get_gender()
    {
        // If no access, return 0
        if (!isset($this->me['gender'])) {
            return 0;
        }

        // Get the gender from the config array
        $gender = (array_key_exists($this->me['gender'], configManager::$genderArr)) ? configManager::$genderArr[$this->me['gender']] : 0;

        return $gender;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_birthday
     * @description Returns the user birthday timestamp
     * @return int
     */
    public function get_birthday()
    {
        // If no access, return 0
        if (!isset($this->me['birthday'])) {
            return 0;
        }

        // Get the birthday and explode to array
        $dobArr = explode('/', $this->me['birthday']);

        // Build a DateTime object
        $Dob = new DateTime();
        $Dob->setDate($dobArr[2], $dobArr[0], $dobArr[1]);

        // Return the timestamp
        return $Dob->getTimestamp();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_current_position
     * @description Returns the current work position
     * @return string
     */
    public function get_current_position()
    {
        // If no access, return empty
        if (!isset($this->me['work'])) {
            return '';
        }

        // Get the user work history
        $workArr = $this->get_work_historyArr();

        return $workArr[0]['position'];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_work_historyArr
     * @description Returns work history array from facebook
     * @return array
     */
    public function get_work_historyArr()
    {
        $workArr = array();

        // If no access, return empty array
        if (!isset($this->me['work'])) {
            return $workArr;
        }

        // Run on the user work history
        foreach ($this->me['work'] as $jobArr) {
            $workArr[] = array(
                'position' => isset($jobArr['position']['name']) ? $jobArr['position']['name'] : '',
                'employer' => isset($jobArr['employer']['name']) ? $jobArr['employer']['name'] : '',
                'start_ts' => isset ($jobArr['start_date']) ? strtotime($jobArr['start_date']) : 0,
                'end_ts' => isset($jobArr['end_date']) ? strtotime($jobArr['end_date']) : 0,
                'source' => 'facebook',
            );
        }

        return $workArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_profile_picture_url
     * @description Returns url of user profile picture
     * @return string
     */
    public function get_profile_picture_url()
    {
        // If no access, return empty string
        if (!isset($this->me['picture']['data']['url'])) {
            return '';
        }

        return $this->me['picture']['data']['url'];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_base64_profile_picture
     * @description Returns base64 of user profile picture
     * @return string
     */
    public function get_base64_profile_picture()
    {
        // If no access, return empty string
        if (!isset($this->me['picture']['data']['url'])) {
            return '';
        }

        // Get facebook profile picture url
        $url = $this->me['picture']['data']['url'];

        // Encode to base64
        return base64_encode(file_get_contents($url));
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_profile_url
     * @description Returns the url of the profile
     * @return string
     */
    public function get_profile_url()
    {
        return 'https://www.facebook.com/' . $this->me['id'];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name connect_to_user_account
     * @description Updates the user with social information
     * @param $User
     * @return array|int
     */
    public function connect_to_user_account($User)
    {
        $ts = time();

        // If user is already connected to Facebook, return error
        $sql = "
            SELECT `user_id` FROM `" . $this->tb_name . "` WHERE `user_id` = " . $User->id . "
        ";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return 212; // User is already connected to facebook
        }

        // Get user data
        $userArr = $this->get_userArr(); // Get user array
        $userArr['user_id'] = $User->id;
        $userArr['last_update'] = $ts;

        // Update the database
        $this->db->insert($this->tb_name, $userArr);

        return $userArr;
    }

    /*----------------------------------------------------------------------------------*/
}