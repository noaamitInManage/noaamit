<?php

class countriesManager extends BaseManager
{

    public $id = 0;

    public $name = '';
    public $geoip_country_code = '';
    public $phone_prefix = '';
    public $min_length = 0;
    public $max_length = 0;

    public $order_num = 0;
    public $last_update = 0;


    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $countryArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/countries/' . get_item_dir($item_id) . '/' . $lang . '/country-' . $item_id . '.inc.php'); // $countryArr

        foreach ($countryArr AS $key => $value) {
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
        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function all($by = '', $lang = '')
    {
        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $file_name = 'countries';
        if ($by == 'code') {
            $file_name = 'countries_by_code';
        }

        $countriesArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.' . $lang . '.inc.php'); // $countriesArr

        return $countriesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function by_code($code, $lang = '')
    {
        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $countriesArr = self::all('code', $lang);

        $id = 0;
        if (isset($countriesArr[$code])) {
            $id = $countriesArr[$code]['id'];
        }

        return new self($id);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_country_code($country_id)
    {
        $Country = new self($country_id);

        return $Country->geoip_country_code;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_gdArr()
    {
        $countriesArr = self::all('code');

        $selected_country = siteFunctions::get_country_by_ip($_SERVER['REMOTE_ADDR']);
        $selected_country = ($selected_country !== false && array_key_exists($selected_country, $countriesArr)) ? $selected_country : configManager::$default_country_code;

        $gd_countriesArr = array();
        foreach ($countriesArr as $countryArr) {
            $selected = $countryArr['geoip_country_code'] == $selected_country ? 1 : 0;
            $gd_countriesArr[] = array(
                'id' => $countryArr['id'],
                'name' => $countryArr['name'],
                'country_code' => $countryArr['geoip_country_code'],
                'phone_prefix' => $countryArr['phone_prefix'],
                'min_length' => $countryArr['min_length'],
                'max_length' => $countryArr['max_length'],
                'selected' => $selected,
                'order_num' => $countryArr['order_num'],
            );
        }

        return $gd_countriesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function get_feeds()
    {
        return array(
            array(
                'type' => 'country',
                'id' => $this->id,
            ),
        );
    }

    //-------------------------------------------------------------------------------------------------------------------//
}