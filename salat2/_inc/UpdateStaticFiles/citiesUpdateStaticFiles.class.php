<?php
//---------------------------------------------------------------------------//

class citiesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_cities';
        $this->_ProcessID = 85;
        $this->className = trim(get_class());
        $this->file_name = 'cities.inc.php';
        $this->itemsArr_name = 'citiesArr';
        $this->name = 'ערים';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartLangDirctory('/_static/cities/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/cities.inc.php');

        updateStaticFile("SELECT id,title FROM `{$this->_Proccess_Main_DB_Table}`",
            '/_static/cities.inc.php',
            'citiesArr', 'id', true, true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'city-' . $_REQUEST['inner_id'] . '.inc.php');

            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}`
               						      WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir . 'city-' . $_REQUEST['inner_id'] . '.inc.php',
                'cityArr');
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
