<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21/12/2015
 * Time: 12:58
 */

namespace modules;

use Facebook\Facebook as Facebook;

/**
 * Class FacebookUser
 * with this class we can integrate with facebook SDK API and get user information form there.
 * @package modules
 */
class FacebookUser {

    /**
     * @var string $_fb_app_version Facebook SDK Graph version
     *
     */
    private $_fb_app_version = '';

    /**
     * @var string $_access_token User Access token fo Authentication in facebook
     */
    private $_access_token  = '';

    /**
     * @var array $_fb_user_fields_arr fields that we want to get from Facebook
     */
    private $_fb_user_fields_arr      = array();

    /**
     * @var array $_allowed_fb_sdk_fields_arr allowed fields for send to Facebook
     */
    private $_allowed_fb_sdk_fields_arr = array('id', 'name', 'email', 'gender', 'birthday');

    /**
     * @var string $fb_user_url friendly url param for access user info
     */
    private $fb_user_url                = '/me';

    /**
     * @var int $id Facebook user ID
     */
    public $id              = 0;

    /**
     * @var string $name Facebook user full name
     */
    public $name            = '';

    /**
     * @var string $first_name Facebook user first name
     */
    public $first_name      = '';

    /**
     * @var string $last_name Facebook user last name
     */
    public $last_name       = '';

    /**
     * @var string $email Facebook user email
     */
    public $email           = '';

    /**
     * @var string $age_range Facebook user age range less than 18 for example
     */
    public $age_range       = '';

    /**
     * @var string $birthday Facebook user birthday
     */
    public $birthday        = '';

    /**
     * @var string $link Facebook link to the person's Timeline
     */
    public $link            = '';

    /**
     * @var string $gender Facebook user gender
     */
    public $gender          = '';

    /**
     * @var string $locale Facebook person's locale
     */
    public $locale          = '';

    /**
     * @var float (min: -24) (max: 24) $timezone Facebook user timezone
     */
    public $timezone        = '';

    /**
     * @var datetime $updated_time Updated time
     */
    public $updated_time    = '';

    /**
     * @var bool $verified Indicates whether the account has been verified
     */
    public $verified        = false;

    /**
     * @var array $fb_user_response_arr response array of Facebook user information
     */
    public $fb_user_response_arr = array();


    /**
     * @param $app_id
     * @param $app_secret_key
     */
    public function __construct($app_id, $app_secret_key)
    {
        $this->_fb_app_id      = $app_id;
        $this->_fb_app_secret  = $app_secret_key;
        $this->_fb_app_version = 'v2.5';//SDK Graph version configured in resource/FacebookSDK/api/lib/FacebookSDKCore.class.php
    }

    /**
     * prepareing facebook user fields, that we want to send to Facebook
     * all fields are contained in $this->_fb_user_fields_arr after we call this functions
     * @return $this
     */
    public function initFacebookSDKRequestFields()
    {
        $obj_properties = get_object_vars($this);

        foreach($obj_properties as $prop => $value) {
            if(in_array($prop, $this->_allowed_fb_sdk_fields_arr)) {
                array_push($this->_fb_user_fields_arr, $prop);
            }
        }

        return $this;
    }

    /**
     * Add more fields that we want to get from facebook
     * @param $field
     * @return $this
     */
    public function addFacebookSDKRequestField($field)
    {
        if(!property_exists($this, $field))
            $this->{$field} = '';

        array_push($this->_fb_user_fields_arr, $field);

        return $this;
    }

    /**
     * Getting imploded Array of facebook fields that we sending to the API
     * @return string
     */
    public function getFacebookSDKRequestFields()
    {
        return implode(',', $this->_fb_user_fields_arr);
    }

    /**
     * removing field from  $this->_allowed_fb_sdk_fields_arr that we do not want to be allowed
     * @param $field_name
     * @return $this
     */
    public function removeAllowedFbSdkFieldsArr($field_name)
    {
        if(!empty($this->_allowed_fb_sdk_fields_arr)) {
            for($i = 0; $i < count($this->_allowed_fb_sdk_fields_arr); $i++) {
                if(in_array($field_name, $this->_allowed_fb_sdk_fields_arr)) {
                    array_splice($this->_allowed_fb_sdk_fields_arr, 1, $i);
                }
            }
        }

        return $this;
    }

    /**
     * removing field from  $this->_fb_user_fields_arr that we do not want to get from API
     * @param $field_name
     * @return $this
     */
    public function removeFacebookSDKRequestFields($field_name)
    {
        if(!empty($this->_fb_user_fields_arr)) {
            for($i = 0; $i < count($this->_fb_user_fields_arr); $i++) {
                if(in_array($field_name, $this->_fb_user_fields_arr)) {
                    array_splice($this->_fb_user_fields_arr, 1, $i);
                }
            }
        }

        return $this;
    }

    /**
     * parsing Facebook response fields into FacebookUser class properties
     * @param $fields
     * @return $this
     */
    public function parseFacebookSDKResponseFields($fields)
    {
        foreach ($fields as $property => $value) {

            if(property_exists($this, $property) && !empty($value)) {
                if($property == 'birthday') {
                    $value = $value->format('d-m-Y');
                }

                $this->{$property} = $value;

            }
        }

        return $this;
    }


    /**
     * returning Facebook respons fields as array
     * @return array
     */
    public function returnRequestFieldsArray()
    {
        foreach($this as $property => $value) {
            if(in_array($property, $this->_allowed_fb_sdk_fields_arr) && !empty($value)) {
                $this->fb_user_response_arr[$property] = $value;
            }
        }

        return $this->fb_user_response_arr;

    }

    /**
     * @return string
     */
    public function getFbAppVersion()
    {
        return $this->_fb_app_version;
    }

    /**
     * @param string $fb_app_version
     */
    public function setFbAppVersion($fb_app_version)
    {
        $this->_fb_app_version = $fb_app_version;
    }



    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_access_token;
    }


    /**
     * @param $access_token
     * @return $this
     */
    public function setAccessToken($access_token)
    {
        $this->_access_token = $access_token;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getFbAppId()
    {
        return $this->_fb_app_id;
    }


    /**
     * @param $fb_app_id
     * @return $this
     */
    public function setFbAppId($fb_app_id)
    {
        $this->_fb_app_id = $fb_app_id;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getFbAppSecret()
    {
        return $this->_fb_app_secret;
    }


    /**
     * @param $fb_app_secret
     * @return $this
     */
    public function setFbAppSecret($fb_app_secret)
    {
        $this->_fb_app_secret = $fb_app_secret;

        return $this;
    }

    /**
     * @return string
     */
    public function getFbUserUrl()
    {
        return $this->fb_user_url;
    }


    /**
     * @param $fb_user_url
     * @return $this
     */
    public function setFbUserUrl($fb_user_url)
    {
        $this->fb_user_url = $fb_user_url;

        return $this;
    }

    /**
     * @return array
     */
    public function getFbUserResponseArr()
    {
        return $this->fb_user_response_arr;
    }


    /**
     * @param $fb_user_response_arr
     * @return $this
     */
    public function setFbUserResponseArr($fb_user_response_arr)
    {
        if(is_object($fb_user_response_arr)) {

            $this->fb_user_response_arr = (array)$fb_user_response_arr;
        } else {
            $this->fb_user_response_arr = $fb_user_response_arr;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedFbSdkFieldsArr()
    {
        return $this->_allowed_fb_sdk_fields_arr;
    }


    /**
     * @param $field string field name
     * @return $this
     */
    public function setAllowedFbSdkFieldsArr($field)
    {

        if(!property_exists($this, $field))
            $this->{$field} = '';

        array_push($this->_allowed_fb_sdk_fields_arr, $field);

        return $this;

    }
}