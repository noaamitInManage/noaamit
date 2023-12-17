<?

/**
 * @author : gal zalait
 * @desc : manage site transfer - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Transfer = new transferManager(1);
 *
 */
class tool_tipManager extends BaseManager
{

    public $id = '';
    public $title = '';
    public $text = '';
    public $last_update = '';

    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/tool_tips/' . get_item_dir($item_id) . '/' . $lang . '/tool_tip-' . $item_id . '.inc.php');//$tool_tipArr

        foreach ($tool_tipArr AS $key => $value) {
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


    //-------------------------------------------------------------------------------------------------------------------//
}

?>
