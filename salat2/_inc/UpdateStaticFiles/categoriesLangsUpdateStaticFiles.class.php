<?php
class categoriesLangsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_categories';
        $this->_ProcessID = 144;
        $this->className = trim(get_class());
        $this->file_name = 'categories.inc.php';
        $this->itemsArr_name = 'categoriesArr';
        $this->name = 'קטגוריות';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;

        $smart_dir = parent::smartLangDirctory('/_static/categories/', $_REQUEST['inner_id'], $module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/categories.' . $languagesArr[$module_lang_id]['title'] . '.inc.php');

        updateStaticFile("SELECT Main.id, Main.active, Main.last_update, Main.media_id, Lang.title FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}
         	",
            '/_static/categories.' . $languagesArr[$module_lang_id]['title'] . '.inc.php',
            'categoriesArr', 'id', true, true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'category-' . $_REQUEST['inner_id'] . '.inc.php');

            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
               						LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						)
               						      WHERE Main.id='{$_REQUEST['inner_id']}' AND Lang.lang_id='{$module_lang_id}'",
                $smart_dir . 'category-' . $_REQUEST['inner_id'] . '.inc.php',
                'categoryArr');
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


}

//---------------------------------------------------------------------------//