<?php


class UpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{
    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name, $name, $lang_id;

    private static $element_name = ""; // in single form. ALWAYS in single form


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        global $languagesArr,$module_lang_id;

        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_'. self::$element_name .'s';
        $this->_ProcessID = 1;
        $this->className= trim(get_class());
        $this->file_name = '/_static/' . self::$element_name . 's.' . $languagesArr[$module_lang_id]['title'] . '.inc.php';
        $this->itemsArr_name = self::$element_name . 'sArr';
        $this->lang_id = $module_lang_id;
        $this->name = 'בדיקה רמה3';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $id = ($id) ? $id : $_REQUEST['inner_id'] ;

        $itemsArr = $this->getItems();

        $smart_dir = parent::smartDirctory('/_static/' . self::$element_name . 's/', $id, $this->lang_id);
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
        $query = "
	        SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
	              LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
                        Main.`id` = Lang.`obj_id`
                    )
	         WHERE `id` = {$item_id} AND Lang.lang_id = '{$this->lang_id}'
	    ";

        $result = $this->Db->query($query);
        $itemArr = $this->Db->get_stream($result);

        return $itemArr;
    }

    /*----------------------------------------------------------------------------------*/
    public function getItems()
    {
        $query = "
	        SELECT Main.`id`, Lang.`title`, Main.`order_num` FROM `{$this->_Proccess_Main_DB_Table}` AS Main
	              LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
                        Main.`id` = Lang.`obj_id`
                    ) WHERE Lang.`lang_id` = {$this->lang_id}
	    ";

        $result = $this->Db->query($query);
        $itemsArr = $this->Db->get_stream($result);

        return $itemsArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();


        $query= "SELECT id FROM {$this->_Proccess_Main_DB_Table}";
        $result=$Db->query($query);

        while($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

}