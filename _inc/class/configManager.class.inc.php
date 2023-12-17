<?

/**
 * @author : Nechami Karelitz
 * @desc : manage salat configuration
 * @var : 1.0
 * @last_update :  01/02/2016
 * @example : $Config = new configManager();
 *
 */
class configManager
{
    public static $familiar_ipsArr = array(
        '207.232.22.164', // inmanage office
    );
    public static $dev_url = "salat.inmanage.com"; //dev environment
    public static $adminEmail = ''; //mail displays from
    public static $adminEmailsArr = array(); //will send mail in those who in array

    public static $no_reply_name = "NAME";
    public static $no_replay_email = 'noreply@inmanage.com';
    public static $support_email = 'support@inmanage.com';

    public static $allow_sysadmin_autologin = false;

    public static $gd_images_typesArr = array(
        '1' => 'רגילה',
        '2' => 'מסך מלא',
    );

    public static $gd_images_typesKeywordsArr = array(
        'regular' => 1,
        'full_screen' => 2
    );

    public static $genderArr = array(
        'unknown' => 0,
        'male' => 1,
        'female' => 2,
    );

    public static $parameters_typesArr = array(
        '1' => 'Integer',
        '2' => 'Float',
        '3' => 'Text',
        '4' => 'Boolean - 1 / 0',
    );

    public static $parameters_types_codesArr = array(
        '1' => 'int',
        '2' => 'float',
        '3' => 'string',
        '4' => 'boolean',
    );

    public static $google_maps_api_key = 'AIzaSyBm_WbPNNhINtMXcULvUQrap6LIspz4-qQ';

    public static $sourceArr = array(
        "iphone" => 1,
        "android" => 2,
        "website" => 3
    );
    public static $default_platform = 'website';

    public static $default_country_code = 'IL'; // By GeoIP code

    public static $default_meeting_room_web_media_id = 601;
    public static $default_meeting_room_media_id = 602;

    public static $tags_search_ac_min_characters = 2;
    public static $tags_search_ac_max_results = 5;

    public static $search_results_per_page = 5;

    public static $cache_search_results = false;
    public static $search_results_cache_time = 14400; // In seconds
    public static $trending_search_items_nubmer = 3;

    public static $env_urlsArr = array( // The first url in each environment will be it's main one
        'main' => array(
            'dev' => array(
                'salat.inmanage.com',
            ),
            'live' => array(
                'salat.inmanage.com',
            ),
        ),

    );
    public static $internal_url = 'https://salat.inmanage.com';

    public static $modules_folderArr = array(
        'main' => '_modules',
        'meeting_rooms' => '_meeting_rooms',
    );



    public static $default_timezone = 'Asia/Tel_Aviv';

    public static $gd_media_picture_path = '_media/images/gd_media';

    public static $event_backgroundsArr = array(
        'editable' => '#FAE7C3',
        'locked' => '#F3F3F3',
    );

    public static $number_of_features_to_show_in_calendar = 3;

    public static $login_platformsArr = array(
        'iphone' => 1,
        'android' => 1,
        'website' => 2,
    );

    /**
     * Default cache drivers
     * @var array
     */
    public static $default_cache_driversArr = [
        MemcacheCacheDriver::class => [],
//        FileCacheDriver::class => [],
    ];


    public static $developersEmailsArr = array(
        'daniel@inmanage.net',
        'itay@inmanage.net',
        'gal@inmanage.net',
    );

    public static $default_user_email = 'noreply@inmanage.com';

    public static $google_maps_geocoding_api_key = '';  // get the key from gal@inmanage.net project only !!!   https://developers.google.com/maps/documentation/geocoding/get-api-key

    public static $firebase_key='';



    //-------------------------------------------------------------------------------------------------------------------//

    function __construct()
    {

    }


    //-------------------------------------------------------------------------------------------------------------------//

    function __destruct()
    {

    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function __set($var, $val)
    {
        $this->$var = $val;
    }


    //-------------------------------------------------------------------------------------------------------------------//


    public function __get($var)
    {
        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * Base method to check for dev mode.
     * @return bool
     */
    public static function is_dev_mode()
    {
        return (strpos($_SERVER["HTTP_HOST"], self::$dev_url) !== false);
    }

    //-------------------------------------------------------------------------------------------------------------------//
}

?>
