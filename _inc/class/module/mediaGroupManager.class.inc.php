<?

/**
 * @author : gal zalait
 * @desc : manage site media  - hold all information ,sort items
 * @var : 1.0
 * @last_update :  01/02/2012
 */
class mediaGroupManager extends BaseManager
{
    public $id = 0;
    public $imagesArr = array();


    /*----------------------------------------------------------------------------------*/

    function __construct($item_id)
    {
        parent::__construct(false);

        $this->ts = time();
        $mediaGroupsArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/mediaGroup/mediaGroup-' . $item_id . '.inc.php');//$mediaGroupsArr

        $this->id = $item_id;
        foreach ($mediaGroupsArr AS $media_id => $mediaArr) {
            $this->imagesArr[$media_id] = $mediaArr;
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


}


?>