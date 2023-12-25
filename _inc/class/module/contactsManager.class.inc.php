<?
/**
 * @author : gal zalait
 * @desc : manage site conetnt psage - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Content = new contentManager(1);
 *
 */

class contactsManager{

    public $id='';
    public $full_name='';
    public $title='';
    public $message='';
    public $email='';
    public $done='';
    public $last_update='';


    function __construct($item_id,$lang=''){
        global $languagesArr;
        $this->ts = time();
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'].'/_static/contacts/'.get_item_dir($item_id).'/contact-'.$item_id.'.inc.php');//$categoriesArr

        foreach ($contactArr AS $key=>$value){
            if(property_exists($this,$key)){
                $this->{$key}=$value;
            }
        }

        //content main image
        if(intval($this->media_id)){
            $this->image = new mediaManager($this->media_id);
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

    public static function send($data) {
        $Db = Database::getInstance();
        $data['last_update'] = time();
        $data['done'] = 0;
        $table = 'tb_contact';
        $query = "INSERT INTO `{$table}` (`" . implode('`,`', array_keys($data)) . "`) VALUES ('" . implode("','", array_values($data)) . "');";
        return $Db->query($query);

    }
    //-------------------------------------------------------------------------------------------------------------------//
}

?>
