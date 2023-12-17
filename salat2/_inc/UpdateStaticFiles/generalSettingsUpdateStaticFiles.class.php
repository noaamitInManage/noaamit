<?php

/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */
class generalSettingsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_general_settings';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'general_settings.inc.php';
        $this->itemsArr_name = 'general_settingsArr';
        $this->name = 'דפי תוכן';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/general_settings/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/general_settings.inc.php');

        updateStaticFile("SELECT id,title FROM `{$this->_Proccess_Main_DB_Table}` ",
            '/_static/general_settings.inc.php',
            'general_settingsArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'general_setting-' . $_REQUEST['inner_id'] . '.inc.php');
            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir . 'general_setting-' . $_REQUEST['inner_id'] . '.inc.php',
                'general_settingArr');
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
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name(){
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        $tmp = $this->itemsArr_name;
        return count($$tmp);
    }


}

?>