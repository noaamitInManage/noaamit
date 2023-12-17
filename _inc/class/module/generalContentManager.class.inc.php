<?

/**
 * @author : gal zalait
 * @desc : manage site transfer - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Transfer = new transferManager(1);
 *
 */
class generalContentManager extends BaseManager
{

    public $id = '';
    public $title = '';
    public $content = '';
    public $last_update = '';
    public $media_id = '';
    public $image = '';

    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/general_contents/' . get_item_dir($item_id) . '/' . $lang . '/general_content-' . $item_id . '.inc.php');//$general_contentArr

        foreach ($general_contentArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if ($this->media_id) {
            $this->image = new mediaManager($this->media_id);
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

    public static function get_content($obj_id, $field_name = '', $lang = '')
    {
        $field_name = !empty($field_name) ? $field_name : 'content';
        if (!$lang) {
            $lang = (!empty($_SESSION['lang']) ? $_SESSION['lang'] : default_lang);
        }
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/_static/general_contents/' . get_item_dir($obj_id) . '/' . $lang . '/general_content-' . $obj_id . '.inc.php')) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/general_contents/' . get_item_dir($obj_id) . '/' . $lang . '/general_content-' . $obj_id . '.inc.php'); //$general_contentArr
        } else {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/general_contents/' . get_item_dir($obj_id) . '/' . default_lang . '/general_content-' . $obj_id . '.inc.php'); //$general_contentArr
        }

        //if media, return the media object
        if ($field_name == 'media' && !empty($general_contentArr['media_id'])) {
            $image = new mediaManager($general_contentArr['media_id']);
            return (!empty($image) ? $image : '');
        }

        return (!empty($general_contentArr[$field_name]) ? $general_contentArr[$field_name] : '');
    }
    //-------------------------------------------------------------------------------------------------------------------//
}

?>
