<?
/**
 * @author : gal zalait
 * @desc : manage site conetnt psage - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Content = new contentManager(1);
 *
 */

class storesManager{

    public $id='';
    public $title='';
    public $active='';
    public $open='';
    public $bitwise_array='';
    public $last_update='';

    //$item_id of specific store, create an instance of storesManager with the data of the store
    function __construct($item_id,$lang=''){
        global $languagesArr;
        $this->ts = time();
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        //the file inside _static/stores/{num_dir}/{lang}/store-{store_id}.inc.php
        include($_SERVER['DOCUMENT_ROOT'].'/_static/stores/'.get_item_dir($item_id).'/'.$lang.'/store-'.$item_id.'.inc.php');//$storesArr

        //set the vakue of the fields
        foreach ($storeArr AS $key=>$value){
            if(property_exists($this,$key)){
                $this->{$key}=$value;
            }
        }

        if($this->content && strstr($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false){
            $direction = "rtl";
            foreach($languagesArr as $key => $langArr){
                if($langArr["title"] === $lang){
                    $direction = ($langArr["direction"] == 1) ? "ltr" : "rtl";
                }
            }
            $this->content = "<style>*{direction: {$direction};}</style>" . $this->content;
        }

    }


    //-------------------------------------------------------------------------------------------------------------------//

    function __destruct(){

    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function __set($var, $val){
        $this->$var = $val;
    }


    //-------------------------------------------------------------------------------------------------------------------//


    public function __get($var){
        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//


    public static function getAll($lang='') {
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $stores = siteFunctions::get_cached_moduleArr('stores.' . $lang, 'storesArr', 'storesLangsUpdateStaticFiles', 'storesLangsUpdateStaticFiles', 'getStoresArr', 2);
        return $stores;

    }
    //-------------------------------------------------------------------------------------------------------------------//

    //$item_id is the store_id
    //return the categories
    public static function getStoreCategories($item_id) {
        
        $main_table = 'tb_categories';
        $lang_table = 'tb_categories_lang';
        $link_table = 'tb_stores_link';

        $Db = Database::getInstance();

        //get categories of the store
        $query = "SELECT Main.id, Main.media_id, Main.active, Main.last_update, Lang.title
                    FROM `{$link_table}` as Store LEFT JOIN `{$main_table}` AS Main
                    ON Store.`category_id` = Main.`id` 
					LEFT JOIN `{$lang_table}` AS Lang ON (
            Main.`id`=Lang.`obj_id`
        ) WHERE Store.`store_id`={$item_id}";

        $res = $Db->query($query);
        $categories = [];
        while($row = $Db->get_stream($res)) {
            if ($row['id'] != 0) {
                $categories[] = $row;
            }

        }
        return $categories;
        
    }

    public function getWeather() {

        $city_name = $this->title;
        $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        $appid = 'f2cc8d2bbe780c1aa16ff0d9de39d437';
        $url = 'https://api.openweathermap.org/data/2.5/weather?lang=' . $lang . '&q=' . $city_name . '&appid=' . $appid;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            die('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        // Decode the JSON response to a PHP array.
        $data = json_decode($response, true);
        $res = [];
        if (!$data) {
            die("<pre>" .print_r(array('Error decoding JSON data.' , 'Here: ' . __LINE__ . ' at ' . __FILE__) ,true) ."</pre>");
        } else {
//            die("<pre>" .print_r(array($data , 'Here: ' . __LINE__ . ' at ' . __FILE__) ,true) ."</pre>");
            //Temperature in Celsius = Temperature in Kelvin−273.15
            // Temperature in Kelvin is $data['temp']
            $res['deg'] = $data['main']['temp'] - 273.15;
            $res['weather_title'] = $data['weather'][0]['main'];
            $res['weather_description'] = $data['weather'][0]['description'];
            $res['icon_path'] = "https://openweathermap.org/img/wn/" .  $data['weather'][0]['icon'] . "@2x.png";
        }

        return $res;

    }
}

?>
