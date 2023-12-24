<?
/**
 * @author : gal zalait
 * @desc : manage site conetnt psage - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Content = new contentManager(1);
 *
 */

class categoriesManager{

    public $id='';
    public $media_id='';
    public $title='';
    public $active='';
    public $last_update='';


    function __construct($item_id,$lang=''){
        global $languagesArr;
        $this->ts = time();
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'].'/_static/categories/'.get_item_dir($item_id).'/'.$lang.'/category-'.$item_id.'.inc.php');//$categoriesArr

        foreach ($categoryArr AS $key=>$value){
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

    //$item_id is obj_id on tb_categories_lang
    public static function getCategoryProducts($item_id) {
        $main_table = 'tb_products';
        $lang_table = 'tb_products_lang';
        $link_table = 'tb_products_link';

        $Db = Database::getInstance();

        //get category data and products
        $query = "SELECT Main.id, Main.media_id, Main.active, Main.last_update, Main.order_num, Lang.title, Lang.description
                    FROM `{$link_table}` as Link LEFT JOIN `{$main_table}` AS Main
                    ON Link.`product_id` = Main.`id`
                    LEFT JOIN `{$lang_table}` AS Lang ON (
            Main.`id`=Lang.`obj_id`
        ) WHERE Link.`category_id`={$item_id} ORDER BY Main.`order_num`";

        $res = $Db->query($query);
        $products = [];

        while($row = $Db->get_stream($res)) {
            if ($row['id'] != 0) {
                $products[] = $row;
            }

        }

        return $products;

    }

    //-------------------------------------------------------------------------------------------------------------------//
}

?>
