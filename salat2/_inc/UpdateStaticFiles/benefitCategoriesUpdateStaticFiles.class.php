<?php

class benefitCategoriesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_benefits__categories';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'benefit_categories.inc.php';
        $this->itemsArr_name = 'benefit_categoriesArr';
        $this->name = 'Benefit Categories';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $Db = Database::getInstance();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/benefit_categories/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/benefit_categories.inc.php');
        updateStaticFile("SELECT `id`, `ms_id`, `media_id`, `title`, `order_num` FROM `{$this->_Proccess_Main_DB_Table}`",
            '/_static/benefit_categories.inc.php',
            'benefit_categoriesArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            $this->update_static_file($_REQUEST['inner_id']);
        }
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();


        $query = " SELECT id FROM `tb_benefits__categories`";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public function update_static_file($item_id)
    {
        $smart_dir = parent::smartDirctory('/_static/benefit_categories/', $item_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'benefit_category-' . $item_id . '.inc.php');
        $benefit_categoryArr = $this->get_item_staticArr($item_id);
        updateStaticFile($benefit_categoryArr,
            $smart_dir . 'benefit_category-' . $item_id . '.inc.php',
            'benefit_categoryArr');
    }

    /*----------------------------------------------------------------------------------*/

    public function get_item_staticArr($item_id)
    {
        $Db = Database::getInstance();
        $ts = time();

        $sql = "SELECT * FROM `tb_benefits__categories` WHERE `id` = {$item_id}";
        $result = $Db->query($sql);
        $benefit_categoryArr = $Db->get_stream($result);


        return $benefit_categoryArr;
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