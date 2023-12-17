<?

/**
 * GENERAL SETTINGS
 */
class generalSettingsManager extends BaseManager
{

    public $id = 0;
    public $title = '';
    public $content = '';
    public $media_id = '';
    public $active = '';

    public $domain_site = '';

    /*----------------------------------------------------------------------------------*/

    function __construct($item_id)
    {

        parent::__construct(false);

        include($_SERVER['DOCUMENT_ROOT'] . "/_static/general_settings/" . get_item_dir($item_id) . "/" . "general_setting-" . $item_id . ".inc.php"); //$general_settingArr
        $this->id = $item_id;
        foreach ($general_settingArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        //http_host
        $this->domain_site = $_SERVER['HTTP_HOST'];
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;

    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

}

?>