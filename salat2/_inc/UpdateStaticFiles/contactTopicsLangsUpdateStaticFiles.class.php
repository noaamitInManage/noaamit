<?php

class contactTopicsLangsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_contact__topics';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'contact_topics.inc.php';
        $this->itemsArr_name = 'topicsArr';
        $this->name = 'topicArr';
    }


    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $Db = Database::getInstance();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;

        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/contact_topics.' . $languagesArr[$module_lang_id]['name'] . '.inc.php');

        updateStaticFile("SELECT Main.`id`, Lang.`title`, Main.`order_num` FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id} AND Main.`active` = 1

         	",
            '/_static/contact_topics.' . $languagesArr[$module_lang_id]['title'] . '.inc.php',
            'topicsArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            $this->update_static_file($_REQUEST['inner_id']);
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

    public function update_static_file($item_id)
    {
        global $languagesArr;

        foreach ($languagesArr as $lang_id => $languageArr) {
            $smart_dir = parent::smartLangDirctory('/_static/contact_topics/', $item_id, $lang_id);
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'topic-' . $item_id . '.inc.php');
            $topicArr = $this->get_item_staticArr($item_id, $lang_id);
            updateStaticFile($topicArr,
                $smart_dir . 'topic-' . $item_id . '.inc.php',
                'topicArr');
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function get_item_staticArr($item_id, $lang_id)
    {
        $Db = Database::getInstance();

        $sql = "SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
                    LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
                        Main.`id`=Lang.`obj_id`
                    )
                              WHERE Main.id='{$item_id}' AND Lang.lang_id='{$lang_id}'";
        $result = $Db->query($sql);
        $topicArr = $Db->get_stream($result);

        return $topicArr;
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