<?php
//---------------------------------------------------------------------------//

class streetsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_streets';
        $this->_ProcessID = 86;
        $this->className = trim(get_class());
        $this->file_name = 'streets.inc.php';
        $this->itemsArr_name = 'streetsArr';
        $this->name = 'רחובות';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];

        $smart_dir = parent::smartLangDirctory('/_static/streets/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/streets.inc.php');
        $queryy = "SELECT id,title FROM `{$this->_Proccess_Main_DB_Table}`";
        updateStaticFile($queryy, '/_static/streets.inc.php',
            'streetsArr', 'id', true, true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'street-' . $_REQUEST['inner_id'] . '.inc.php');
            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}`
               						      WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir . 'street-' . $_REQUEST['inner_id'] . '.inc.php',
                'streetArr');

            $Db = Database::getInstance();


            $q = "SELECT `city_id` FROM `{$this->_Proccess_Main_DB_Table}` WHERE `id`='{$_REQUEST['inner_id']}'"; // group by city
            $res = $Db->query($q);

            $r = $Db->get_stream($res);
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/streetGroup/streetGroup-' . $r['city_id'] . '.inc.php');
            updateStaticFile("SELECT id,title
                                  FROM `{$this->_Proccess_Main_DB_Table}`
               						    WHERE city_id='{$r['city_id']}'",
                '/_static/streetGroup/streetGroup-' . $r['city_id'] . '.inc.php',
                'streetGroupArr', 'id', true);
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