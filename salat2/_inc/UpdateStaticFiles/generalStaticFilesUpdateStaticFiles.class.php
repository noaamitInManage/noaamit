<?php

class generalStaticFilesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = '';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = '';
        $this->itemsArr_name = '';
        $this->name = '';
    }

    /*----------------------------------------------------------------------------------*/

    public function build_static_file($staticArr, $file_name, $array_var, $id_key = 'id')
    {
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.inc.php');
        updateStaticFile($staticArr,
            '/_static/' . $file_name . '.inc.php',
            $array_var, $id_key, true);
    }

    /*----------------------------------------------------------------------------------*/

    public function updateStatics()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function updateAllStaticsFiles()
    {

    }

    /*----------------------------------------------------------------------------------*/

}