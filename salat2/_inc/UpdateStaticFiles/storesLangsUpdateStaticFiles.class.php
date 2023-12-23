<?php
class storesLangsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_stores';
        $this->_ProcessID = 146;
        $this->className = trim(get_class());
        $this->file_name = 'stores.inc.php';
        $this->itemsArr_name = 'storesArr';
        $this->name = 'סניפים';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;

        $smart_dir = parent::smartLangDirctory('/_static/stores/', $_REQUEST['inner_id'], $module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/stores.' . $languagesArr[$module_lang_id]['title'] . '.inc.php');

        updateStaticFile("SELECT Main.id, Main.active, Main.open, Main.last_update, Main.bitwise_array, Lang.title FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}
         	",
            '/_static/stores.' . $languagesArr[$module_lang_id]['title'] . '.inc.php',
            'storesArr', 'id', true, true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'store-' . $_REQUEST['inner_id'] . '.inc.php');

            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
               						LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						)
               						      WHERE Main.id='{$_REQUEST['inner_id']}' AND Lang.lang_id='{$module_lang_id}'",
                $smart_dir . 'store-' . $_REQUEST['inner_id'] . '.inc.php',
                'storeArr');
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

    public function getItemsNumber()
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        $tmp = $this->itemsArr_name;
        return count($$tmp);
    }


    public static function getStoresArr() {

    }



}

//---------------------------------------------------------------------------//