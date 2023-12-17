<?

/**
 * @author : gal zalait
 * @desc : manage site transfer - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Transfer = new transferManager(1);
 *
 */
class customer_campaignManager extends BaseManager
{

    public $id = '';
    public $campaign_code = '';
    public $title = '';
    public $descripation = '';
    public $media_id = '';
    public $google_analytics_code = '';
    public $active = '';
    public $open_direct = '';
    public $last_update = '';

    public $mediaObj = '';

    function __construct($item_id, $lang = '')
    {
        parent::__construct();
        
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/customer_campaign/' . get_item_dir($item_id) . '/customer_campaign-' . $item_id . '.inc.php');//$customer_campaignArr

        foreach ($customer_campaignArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if (intval($this->media_id)) {
            $this->mediaObj = new mediaManager($this->media_id);
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

    public function get_campaign_by_code($campaign_code, $lang = '')
    {
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/customer_campaigns.' . $lang . '.inc.php');//$customer_campaignsArr
        $id = array_search($campaign_code, $customer_campaignsArr);
        return new customer_campaignManager($id);
    }

    //-------------------------------------------------------------------------------------------------------------------//
}

?>
