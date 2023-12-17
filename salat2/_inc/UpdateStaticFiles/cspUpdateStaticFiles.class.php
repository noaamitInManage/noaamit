<?php

class cspUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        global $languagesArr, $module_lang_id;

        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_csp';
        $this->_ProcessID = 141;
        $this->className = trim(get_class());
        $this->file_name = '/_static/csp.inc.php';
        $this->itemsArr_name = 'cspArr';
        $this->lang_id = $module_lang_id;
        $this->name = 'קישורים';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;

        $smart_dir = parent::smartLangDirctory('/_static/csp', $_REQUEST['inner_id'], $module_lang_id);
        @unlink($smart_dir . 'csp-' . $languagesArr[$module_lang_id]['title'] . '.inc.php');
        $itemsArr = self::getItems();
        updateStaticFile($itemsArr,
            '/_static/csp.inc.php',
            'cspArr', "id");

        if (memcached_is_on) {
            siteFunctions::save_to_memory('csp.', $itemsArr, memcached_default_time, array(__FILE__, __LINE__, __METHOD__, $_SERVER));
        }

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'csp-' . $_REQUEST['inner_id'] . '.inc.php');
            updateStaticFile("SELECT * FROM `tb_csp` AS Main
               						WHERE Main.id={$_REQUEST['inner_id']}",
                $smart_dir . 'csp-' . $_REQUEST['inner_id'] . '.inc.php',
                'cspArr');
        }
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {

        $Db = Database::getInstance();

        $this->updateStatics();


        $query = " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public static function getItems()
    {
        $query = "SELECT `id`,`url` FROM `tb_csp`";
        $Database = Database::getInstance();
        $result = $Database->query($query);
        $itemsArr = [];

        while($row = $Database->get_stream($result)) {
            $itemsArr[$row["id"]] =  $row["url"];

        }

        return $itemsArr;
    }

    public static function getItem($id)
    {
        if (is_numeric($id)) {

            $query = "
	        SELECT * FROM `tb_csp` AS Main
               		 WHERE Main.id={$id}";
            $Database = Database::getInstance();
            $result = $Database->query($query);
            $itemsArr = [];
            while ($row = $Database->get_stream($result)) {
                $itemsArr[] = $row;
            };

            return $itemsArr;
        }
        return false;

    }


}