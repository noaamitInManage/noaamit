<?php

use Illuminate\Support\Str;

class sitesManager extends BaseManager
{

    public $id = 0;
    public $salesforce_id = '';
    public $name = '';
    public $country_id = 0;
    private $timezone = '';
    public $city_feed_key = '';
    public $order_num = 0;
    public $last_update = 0;


    public $floors_orderArr = array();
    public $meeting_roomsArr = array();
    public $meeting_room_floorsArr = array();

    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $siteArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/sites/' . get_item_dir($item_id) . '/' . $lang . '/site-' . $item_id . '.inc.php'); // $siteArr

        foreach ($siteArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
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
        $method_name = 'get_' . $var;
        if (method_exists($this, $method_name)) {
            return $this->{$method_name}();
        }

        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function update_all_static_files($inner_id = '')
    {
        if (!$inner_id) {
            return false;
        }

        $UpdateStaticFiles = new sitesLangsUpdateStaticFiles();
        $UpdateStaticFiles->updateStatics($inner_id);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function update_static_file($inner_id = 0)
    {
        if (!$inner_id) {
            return false;
        }

        $UpdateStaticFiles = new sitesLangsUpdateStaticFiles();
        $UpdateStaticFiles->update_static_file($inner_id);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function all($include_inactive = false, $lang = '')
    {
        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $file_name = $include_inactive ? 'sites_with_inactive' : 'sites';

        $sitesArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.' . $lang . '.inc.php'); // $sitesArr

        return $sitesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_gdArr()
    {
        $sitesArr = self::all();

        $gd_sitesArr = array();
        foreach ($sitesArr as $site_id => $siteArr) {
            $gd_sitesArr[$site_id] = array(
                'id' => $site_id,
                'name' => $siteArr['name'],
                'order_num' => $siteArr['order_num'],
            );
        }

        return $gd_sitesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_id_by_salesforce_id($salesforce_id)
    {
        $sitesArr = self::all(true);

        foreach ($sitesArr as $site_id => $siteArr) {
            if (strtolower(trim($siteArr['salesforce_id'])) == strtolower(trim($salesforce_id))) {
                return $site_id;
            }
        }

        return 0;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_country_code()
    {
        $Country = new countriesManager($this->country_id);

        return $Country->geoip_country_code;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_timezone()
    {
        return $this->timezone ?: configManager::$default_timezone;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_floor_order_num($floor)
    {
        if (empty($this->floors_orderArr)) {
            return 999999999;
        }

        $floorsCol = collect($this->floors_orderArr);
        $floor = $floorsCol->where('floor', $floor);

        if (!$floor->count()) {
            return 999999999;
        }

        return $floor->first()['order_num'];
    }

    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * @name get_floor_text
     * @description Returns the floor formatted text
     * @param $floor
     * @return int|string
     */
    public static function get_floor_text($floor)
    {
        $floor_text = $floor;

        if (is_numeric($floor)) {
            try {
                $fmt = numfmt_create('en_US', NumberFormatter::ORDINAL);
                $floor_text = numfmt_format($fmt, $floor);
            } catch (Exception $e) {
                $floor_text = $floor;
            }
        }

        $floor_text = Str::upper($floor_text . ' ' . lang('meeting_rooms_floor_text_suffix'));

        return $floor_text;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_meeting_roomsArr($floor = "all")
    {
        $meeting_roomsArr = array();
        $order_num = 1;
        foreach ($this->meeting_roomsArr as $room_id) {
            $MeetingRoom = new meetingRoomsManager($room_id);
            if ($floor != "all" && $floor != $MeetingRoom->floor) {
                continue;
            }

            $meeting_roomsArr[$room_id] = array(
                'id' => $room_id,
                'picture' => $MeetingRoom->get_photo(),
                'title' => $MeetingRoom->get_title(),
                'floor' => $MeetingRoom->floor,
                'order_num' => $order_num,
            );
            $order_num++;
        }

        return $meeting_roomsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_meeting_room_floorsArr($floor = "all")
    {
        $User = User::getInstance();
        $Company = new companiesManager($User->company_id);
        $selected_floor = $this->get_closest_floor_with_meeting_rooms($floor ?: $Company->floor);
        $floorsArr = collect($this->meeting_room_floorsArr)->map(function($floorArr) use ($floor, $selected_floor)  {
            $floorArr['floor_text'] = sitesManager::get_floor_text($floorArr['floor']);
            $floorArr['selected'] = $floor != "all" && $floorArr['floor'] == $selected_floor ? 1 : 0;

            return $floorArr;
        })->prepend(array(
            'floor' => 'all',
            'order_num' => 0,
            'floor_text' => Str::upper(lang('meeting_rooms_all_floors')),
            'selected' => $floor == "all" ? 1 : 0,
        ))->toArray();

        return $floorsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_closest_floor_with_meeting_rooms($floor)
    {
        $floorsArr = collect($this->meeting_room_floorsArr)->map(function($floorArr) {
            return $floorArr['floor'];
        })->unique()->toArray();

        return siteFunctions::get_closest_number($floor, $floorsArr);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_feeds()
    {
        return array(
            array(
                'type' => 'site',
                'id' => $this->id,
            ),
            array(
                'type' => 'city',
                'id' => $this->city_feed_key,
            ),
            array(
                'type' => 'benefit',
                'id' => $this->city_feed_key,
            ),
        );
    }

    //-------------------------------------------------------------------------------------------------------------------//
}