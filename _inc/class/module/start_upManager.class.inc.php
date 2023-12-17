<?

/**
 * @author : gal zalait
 * @desc : manage site conetnt psage - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Content = new contentManager(1);
 *
 */
class start_upManager extends BaseManager
{

    public $id = '';

    public $active = '';
    public $title = '';
    public $url = '';
    public $content = '';
    public $media_id = '';
    public $media_iphone4_640_960 = '';
    public $media_iphone4_640_960_obj = '';
    public $media_iphone5_640_1136 = '';
    public $media_iphone5_640_1136_obj = '';
    public $media_iphone6_750_1334 = '';
    public $media_iphone6_750_1334_obj = '';
    public $media_iphone6_plus_1242_2208 = '';
    public $media_iphone6_plus_1242_2208_obj = '';
    public $media_android_1080_1920 = '';
    public $media_android_1080_1920_obj = '';
    public $last_update = '';
    public $mediaObj = '';

    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/startup_image/' . get_item_dir($item_id) . '/' . $lang . '/startup_image-' . $item_id . '.inc.php');//$startup_imageArr

        foreach ($startup_imageArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if ($this->media_id) {
            $this->mediaObj = new mediaManager($this->media_id);
        }

        if ($this->media_iphone4_640_960) {
            $this->media_iphone4_640_960_obj = new mediaManager($this->media_iphone4_640_960);
        }

        if ($this->media_iphone5_640_1136) {
            $this->media_iphone5_640_1136_obj = new mediaManager($this->media_iphone5_640_1136);
        }

        if ($this->media_iphone6_750_1334) {
            $this->media_iphone6_750_1334_obj = new mediaManager($this->media_iphone6_750_1334);
        }

        if ($this->media_iphone6_plus_1242_2208) {
            $this->media_iphone6_plus_1242_2208_obj = new mediaManager($this->media_iphone6_plus_1242_2208);
        }

        if ($this->media_android_1080_1920) {
            $this->media_android_1080_1920_obj = new mediaManager($this->media_android_1080_1920);
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
