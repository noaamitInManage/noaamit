<?php

class cspManager extends BaseManager
{

    public $id = 0;
    public $url = '';
    public $last_update = 0;


    function __construct($item_id, $lang = '')
    {
        parent::__construct();

        global $languagesArr;
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $cspArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/csp/' . get_item_dir($item_id) . '/' . $lang . '/csp-' . $item_id . '.inc.php'); // cspArr

        foreach ($cspArr AS $key => $value) {
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

}