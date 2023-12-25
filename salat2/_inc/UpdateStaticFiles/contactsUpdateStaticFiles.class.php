<?php
class contactsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_contact';
        $this->_ProcessID = 148;
        $this->className = trim(get_class());
        $this->file_name = 'contacts.inc.php';
        $this->itemsArr_name = 'contactsArr';
        $this->name = 'יצירת קשר';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id=''){
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
        $smart_dir=parent::smartDirctory('/_static/contacts/',$_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/contacts.inc.php');
        updateStaticFile("SELECT id,title, full_name, message, email, last_update,done FROM {$this->_Proccess_Main_DB_Table} ",
            '/_static/contacts.inc.php',
            'contactsArr','id',true);

        if($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'contact-'.$_REQUEST['inner_id'].'.inc.php');
            updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir.'contact-'.$_REQUEST['inner_id'].'.inc.php',
                'contactArr');
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