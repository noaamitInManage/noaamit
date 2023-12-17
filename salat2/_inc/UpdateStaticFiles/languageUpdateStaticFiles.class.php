<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */
//--------------------- End advertisement Module ----------------------------//

class languageUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_languages';
        $this->_ProcessID = template_ID;
        $this->className = trim(get_class());
        $this->file_name = 'languages.inc.php'; // all Items  for count in moudle update_static
        $this->itemsArr_name = 'languagesArr';
    }

    function updateStatics()
    {
        updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE active=1",
            '/_static/' . $this->file_name,
            $this->itemsArr_name, 'id', true);


    }

    function updateAllStaticsFiles()
    {
        return false;


        parent::writeUpdate();
    }
}


?>