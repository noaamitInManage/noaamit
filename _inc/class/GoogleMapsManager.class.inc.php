<?php

class GoogleMapsManager
{
    private static $geocoding_base_url = 'https://maps.googleapis.com/maps/api/geocode/json?key=';

    private static $city_typesArr = array(
        'locality',
    );

    private static $district_typesArr = array(
        'administrative_area_level_1',
    );

    private static $country_typesArr = array(
        'country',
    );

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_base_url
     * @description Returns the base url of the geocoding api
     * @return string
     */
    private static function get_geociding_base_url()
    {
        return self::$geocoding_base_url . configManager::$google_maps_geocoding_api_key;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name reverese_geocode
     * @description Reverse Geocoging
     * @param $lat
     * @param $lng
     * @return array
     */
    public static function reverese_geocode($lat, $lng)
    {
        $url = self::get_geociding_base_url() . '&latlng=' . $lat . ',' . $lng;
        try {
            $responseArr = json_decode(file_get_contents($url), true)['results'][0]['address_components'];
        } catch (Exception $e) {
            $responseArr = array();
        }

        return $responseArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_city
     * @description Finds the city
     * @param $lat
     * @param $lng
     * @param $componentsArr
     * @return string
     */
    public static function get_city($lat, $lng, $componentsArr = array())
    {
        $componentsArr = count($componentsArr) ? $componentsArr : self::reverese_geocode($lat, $lng);
        if (empty($componentsArr)) {
            return "";
        }

        foreach ($componentsArr as $componentArr) {
            foreach ($componentArr['types'] as $type) {
                if (in_array($type, self::$city_typesArr)) {
                    return $componentArr['long_name'];
                }
            }
        }

        return "";
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_district
     * @description Finds the district
     * @param $lat
     * @param $lng
     * @param $componentsArr
     * @return string
     */
    public static function get_district($lat, $lng, $componentsArr = array())
    {
        $componentsArr = count($componentsArr) ? $componentsArr : self::reverese_geocode($lat, $lng);
        if (empty($componentsArr)) {
            return "";
        }

        foreach ($componentsArr as $componentArr) {
            foreach ($componentArr['types'] as $type) {
                if (in_array($type, self::$district_typesArr)) {
                    return $componentArr['long_name'];
                }
            }
        }

        return "";
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_country
     * @description Finds the country
     * @param $lat
     * @param $lng
     * @param $componentsArr
     * @return string
     */
    public static function get_country($lat, $lng, $componentsArr = array())
    {
        $componentsArr = count($componentsArr) ? $componentsArr : self::reverese_geocode($lat, $lng);
        if (empty($componentsArr)) {
            return "";
        }

        foreach ($componentsArr as $componentArr) {
            foreach ($componentArr['types'] as $type) {
                if (in_array($type, self::$country_typesArr)) {
                    return $componentArr['long_name'];
                }
            }
        }

        return "";
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_most_accurate_location
     * @description Gets the most accurate location for the coordinates
     * @param $lat
     * @param $lng
     * @return string
     */
    public static function get_most_accurate_location($lat, $lng)
    {
        $componentsArr = self::reverese_geocode($lat, $lng);

        // City
        $city = self::get_city($lat, $lng, $componentsArr);
        if ($city) {
            return $city;
        }

        // District
        $district = self::get_district($lat, $lng, $componentsArr);
        if ($district) {
            return $district;
        }

        // Country
        $country = self::get_country($lat, $lng, $componentsArr);
        if ($country) {
            return $country;
        }

        return "";
    }

    /*----------------------------------------------------------------------------------*/
}