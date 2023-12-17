<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 20/06/2017
 * Time: 9:45
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/vendors/LinkedIn.php');

class LinkedInManager extends BaseManager implements SocialProvider
{
    /**
     * LinkedIn configuration details
     * @var array
     */
    private $configArr = array(
        'api_key' => '',
        'api_secret' => '',
        'callback_url' => '',
    );

    /**
     * The table on the database
     * @var string
     */
    private $tb_name = 'tb_users__linkedin_information';

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
        $this->api = new \LinkedIn\LinkedIn($this->configArr);

        // Set access token if sent
        $this->api->setAccessToken($access_token);

        if ($access_token !== null) {
            $response = null;
            $has_linkedin_connection = true;

            // try getting the user. or throw error
            try {
                $response = $this->api->get('/people/~:(id,formatted-name,positions,picture-url,public-profile-url,date-of-birth,headline,summary,industry)');
            } catch (\Exception $e) {
                $has_linkedin_connection = false;
            }

            // Set the user graph object
            if ($has_linkedin_connection) {
                $this->me = $response;
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
            'name' => $this->me['formattedName'],
            'birthday' => $this->get_birthday(),
            'job_title' => $this->get_current_position(),
            'picture_url' => $this->get_profile_picture_url(),
            'profile_url' => $this->get_profile_url(),
            'one_liner' => $this->me['headline'],
            'about_me' => $this->me['summary'],
            'industry' => $this->me['industry'],
        );
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_birthday
     * @description Returns the user birthday timestamp
     * @return int
     */
    public function get_birthday()
    {
        // TODO: impelement method when have full access
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
     * @name get_date_timestamp
     * @description returns the timestamp for the given date
     * @param $year
     * @param $month
     * @param int $day
     * @return int
     */
    private function get_date_timestamp($year, $month, $day = 1)
    {
        // Build a DateTime object
        $Date = new DateTime();
        $Date->setDate($year, $month, $day);

        // Return the timestamp
        return $Date->getTimestamp();
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
        if (!isset($this->me['positions']['values'])) {
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
        if (!isset($this->me['positions']['values'])) {
            return $workArr;
        }

        // Run on the user work history
        foreach ($this->me['positions']['values'] as $jobArr) {
            $workArr[] = array(
                'position' => isset($jobArr['title']) ? $jobArr['title'] : '',
                'employer' => isset($jobArr['company']['name']) ? $jobArr['company']['name'] : '',
                'start_ts' => isset ($jobArr['startDate']) ? $this->get_date_timestamp($jobArr['startDate']['year'], $jobArr['startDate']['month']) : 0,
                'end_ts' => isset ($jobArr['endDate']) ? $this->get_date_timestamp($jobArr['endDate']['year'], $jobArr['endDate']['month']) : 0,
                'source' => 'linkedin',
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
        if (!isset($this->me['pictureUrl'])) {
            return '';
        }

        return $this->me['pictureUrl'];
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
        if (!isset($this->me['pictureUrl'])) {
            return '';
        }

        // Get facebook profile picture url
        $url = $this->me['pictureUrl'];

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
        return $this->me['publicProfileUrl'];
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
            return 214; // User is already connected to facebook
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