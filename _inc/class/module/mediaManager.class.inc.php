<?

/**
 * @author : gal zalait
 * @desc : manage site media  - hold all information ,sort items
 * @var : 1.0
 * @last_update :  01/02/2012
 */
class mediaManager extends BaseManager
{

    public $id = 0;
    public $category_id = '';
    public $title = '';
    public $alt = '';
    public $credit = '';
    public $img_ext = '';
    public $width = '';
    public $height = '';
    public $path = '';
    public $last_update = '';

    /*----------------------------------------------------------------------------------*/

    function __construct($item_id)
    {

        $this->ts = time();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/media/' . get_item_dir($item_id) . '/media-' . $item_id . '.inc.php');//$imgArr

        $this->id = $item_id;
        foreach ($imgArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->path = "/_media/media/{$this->category_id}/{$this->id}.{$this->img_ext}?t={$this->last_update}";
        $full_path = $_SERVER['DOCUMENT_ROOT'] . "/_media/media/{$this->category_id}/{$this->id}.{$this->img_ext}";
        if (file_exists($full_path)) {
            list($this->width, $this->height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $this->path);

        } else {
            $this->path = '';
        }

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

    public static function get_path($media_id)
    {
        $Media = new self($media_id);

        return $Media->path;
    }

    /*----------------------------------------------------------------------------------*/


}


?>