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

    function __construct($item_id,$lang=''){
        global $languagesArr;
        $this->ts = time();
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'].'/_static/stores/'.get_item_dir($item_id).'/'.$lang.'/store-'.$item_id.'.inc.php');//productsArr

        foreach ($storesArr AS $key=>$value){
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
        
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/or_logs.txt', DateTime::createFromFormat('U.u',sprintf("%.6F", microtime(true)))->format("m-d-Y H:i:s.u")." : ". print_r(array(
        '$this->content' => $this->content,
        'Here: ' . __LINE__ . ' at ' . __FILE__
        ), true) . PHP_EOL, FILE_APPEND | LOCK_EX);
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


    //-------------------------------------------------------------------------------------------------------------------//
}

?>
