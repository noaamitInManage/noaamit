<?php

/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */
class defaultMeetingTitlesLangsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_meeting_rooms__default_meeting_titles';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'default_meeting_titles.inc.php';
        $this->itemsArr_name = 'default_meeting_titlesArr';
        $this->name = 'Default Meeting Titles';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        global $languagesArr, $module_lang_id;

        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/default_meeting_titles.' . $languagesArr[$module_lang_id]['title'] . '.inc.php');

        updateStaticFile("SELECT Main.id,Lang.title,Main.`start_time`,Main.`end_time` FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}

         	",
            '/_static/default_meeting_titles.' . $languagesArr[$module_lang_id]['title'] . '.inc.php',
            'default_meeting_titlesArr', 'id', true);
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
        if (strstr($this->className, 'Langs')) {
            $file_nameArr = explode('.', $this->file_name);
            $file_nameArr[0] = $file_nameArr[0] . '.' . default_lang;
            $this->file_name = implode('.', $file_nameArr);
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        } else {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        }
        $tmp = $this->itemsArr_name;
        return count($$tmp);
    }


}

//---------------------------------------------------------------------------//

?>