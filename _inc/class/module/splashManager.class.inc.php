<?

/**
 * @author : gal zalait
 * @desc : manage site conetnt psage - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Content = new contentManager(1);
 *
 */
class splashManager extends BaseManager
{

    public $id = '';

    public $active = '';
    public $title = '';
    public $last_update = '';
    public $image = '';

    function __construct($item_id = 0, $resolution = '', $lang = '')
    {
        parent::__construct();

        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        if ($item_id) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/splash/' . get_item_dir($item_id) . '/' . $lang . '/splash-' . $item_id . '.inc.php');//$splashArr

            foreach ($splashArr AS $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }

            $path = "/_media/splash/" . $this->id . "-" . $resolution . ".png";
            if ($this->id && $resolution && is_file($_SERVER['DOCUMENT_ROOT'] . $path)) {
                $this->image = $path;
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
}

?>