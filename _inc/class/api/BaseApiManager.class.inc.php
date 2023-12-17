<?php

class BaseApiManager
{
    use  SendsApiResponses;

    /**
     * @var string
     */
    protected $platform = '';

    /**
     * @var string
     */
    protected $application_version = '';

    /**
     * @var string
     */
    protected $udid = '';

    /**
     * @var string
     */
    protected $version = '';

    /**
     * @var string
     */
    protected $method_name = '';

    /**
     * @var string
     */
    protected $write_log = true;

    /**
     * @var string
     */
    protected $media_server = ''; // application images load image from this url

    /**
     * For adding support for additional platform just add her to this array
     * the api call will have this value : http://www.inmanage.co.il/api/[PLATFORM_VALUE]/2.0/method_name
     * @var array
     */
    protected $active_platformArr = [];

    /**
     * hold in this array the active version , If a version is not in this array you can not continue
     * @var array
     */
    protected $active_versionArr = [];
    /**
     * The user can continue with this version but  he get a massage thet the version is deprecated and he should upgrade the version
     * @var array
     */
    protected $deprecated_versionArr = [];
    /**
     * Unsupported version the user can't continue if  his version in this array
     * @var array
     */
    protected $not_supported_versionArr = [];

    /**
     * In the case of deprecated / Unsupported version the user get  massage with referral to the store , each platform must to hold the link to the store !!!
     * @var array
     */
    protected $store_link = [];

    /**
     * @var array
     */
    protected $allowed_ipsArr = array();

    /**
     * @var array
     */
    protected $method_lock_by_ipArr = array();

    /**
     * @var array
     */
    protected $method_cachedArr = [];

    /**
     * limit user call to this methods
     * prevent abusing the api
     *
     * [METHOD_NAME] => [LIMIT_NUMBER]
     */
    protected $limit_methodArr = [];

    /**
     * @var int
     */
    protected $limit_second = 86400; //60 * 10

    /**
     * @var array
     */
    protected $familiar_ipsArr = array(
        '62.219.212.139', // Inmanage
        '81.218.173.175', // Inmanage
        '37.142.40.96', // Inmanage wifi
        '207.232.22.164', // Inmanage
    );

    /**
     * @var bool
     */
    protected $secure_token_request = true;

    /**
     * @var array
     */
    protected $exclude_secure_token_methodsArr = [];

    /**
     * Methods that should be called with a GET HTTP method
     * @var array
     */
    protected $get_methodsArr = [];

    /**
     * @var int
     */
    protected $ts = 0;
}