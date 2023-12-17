<?php


class environments_settingsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{
    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name, $name, $lang_id;

    private static $element_name = "environments_settings"; // in single form. ALWAYS in single form
    public static $tb_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        global $module_lang_id;

        parent::__construct();
        self::$tb_name = 'tb_'. self::$element_name;
        $this->_ProcessID = 1;
        $this->className= trim(get_class());
        $this->file_name = '/_static/' . self::$element_name . '.inc.php';
        $this->itemsArr_name = self::$element_name . 'Arr';
        $this->lang_id = $module_lang_id;
        $this->name = 'הגדרות סביבות';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $id = ($id) ? $id : $_REQUEST['inner_id'] ;

        $itemsArr = $this->getItems();

        $smart_dir = parent::smartDirctory('/_static/' . self::$element_name . '/', $id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $this->file_name);

        updateStaticFile($itemsArr,
            $this->file_name,
            $this->itemsArr_name, 'id', true);

        if ($id) {

            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . self::$element_name . '-' . $id . '.inc.php');
            $itemArr = $this->getItem($id);

            updateStaticFile($itemArr,
                $smart_dir . self::$element_name . '-' . $id . '.inc.php',
                self::$element_name . 'Arr');
        }

    }

    /*----------------------------------------------------------------------------------*/
    public function getItem($item_id)
    {
        $Db = Database::getInstance();
        $tb_name = self::$tb_name;
        $query = "
	        SELECT * FROM `{$tb_name}`
	         WHERE `id` = {$item_id}
	    ";

        $result = $Db->query($query);
        $itemArr = $Db->get_stream($result);

        return $itemArr;
    }

    /*----------------------------------------------------------------------------------*/
    public static function getItems()
    {
        $Db = Database::getInstance();
        $tb_name = self::$tb_name;
        $query = "
	        SELECT * FROM `{$tb_name}`
	    ";

        $result = $Db->query($query);
        $itemsArr = $Db->get_query($result);
        return $itemsArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();

        $tb_name = self::$tb_name;

        $query= "SELECT id FROM {$tb_name}";
        $result=$Db->query($query);

        while($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

}