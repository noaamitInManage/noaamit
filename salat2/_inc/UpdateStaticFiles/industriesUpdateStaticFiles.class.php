<?php

class industriesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_industries';
        $this->_ProcessID = 105;
        $this->className = trim(get_class());
        $this->file_name = 'industries.inc.php';
        $this->itemsArr_name = 'industriesArr';
        $this->name = 'Industries';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/industries/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/industries.inc.php');
        updateStaticFile("SELECT `id`, `title` FROM {$this->_Proccess_Main_DB_Table} WHERE `active` = 1 ORDER BY `priority` DESC, `title` ASC",
            '/_static/industries.inc.php',
            'industriesArr', 'id', true);

        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/featured_industries.inc.php');
        updateStaticFile("SELECT `id`, `title` FROM {$this->_Proccess_Main_DB_Table} WHERE `active` = 1 AND `featured` = 1 ORDER BY `priority` DESC, `title` ASC",
            '/_static/featured_industries.inc.php',
            'industriesArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'industry-' . $_REQUEST['inner_id'] . '.inc.php');
            updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir . 'industry-' . $_REQUEST['inner_id'] . '.inc.php',
                'industryArr');
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