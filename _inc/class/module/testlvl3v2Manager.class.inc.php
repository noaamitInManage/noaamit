<?php

class testlvl3v2Manager
{

    public $id='';

    /*public $media_id='';*/
    public $title='';
    public $order_id='';
    public $last_update='';

    private static $element_name = "testlvl3v2"; // in single form. ALWAYS in single form

    function __construct($item_id,$lang='')
    {
        if(!$lang){
            $lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        include($_SERVER['DOCUMENT_ROOT'].'/_static/' . self::$element_name . 's/'.get_item_dir($item_id).'/'.$lang.'/' . self::$element_name . '-'.$item_id.'.inc.php');//$itemsArr_name

        $itemsArr_name = self::$element_name . 'sArr';
        foreach ($$itemsArr_name AS $key=>$value){
            if(property_exists($this,$key)){
                $this->{$key}=$value;
            }
        }

        /*//content main image
        if(intval($this->media_id)){
            $this->image = new mediaManager($this->media_id);
        }*/

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