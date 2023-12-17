<?php

class eventsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_events';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'events.inc.php';
        $this->itemsArr_name = 'eventsArr';
        $this->name = 'Events';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $Db = Database::getInstance();
        $ts = time();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/events/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/events.inc.php');
        updateStaticFile("
          SELECT `id`, `title`, `timezone`, `start_ts`, `end_ts` FROM `{$this->_Proccess_Main_DB_Table}` WHERE `end_ts` > {$ts}
        ",
            '/_static/events.inc.php',
            'eventsArr', 'id', true);

        $events_by_siteArr = $this->get_events_by_siteArr();
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/events_by_site.inc.php');
        updateStaticFile($events_by_siteArr,
            '/_static/events_by_site.inc.php',
            'events_by_siteArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            $this->update_static_file($_REQUEST['inner_id']);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function get_events_by_siteArr()
    {
        $Db = Database::getInstance();
        $ts = time();
        $eventsArr = array();

        $sql = "
            SELECT `id`, `site_id` FROM `tb_events` WHERE `end_ts` > {$ts} ORDER BY `start_ts`
        ";
        $result = $Db->query($sql);
        while ($eventArr = $Db->get_stream($result)) {
            $eventsArr[$eventArr['site_id']][] = $eventArr['id'];
        }

        return $eventsArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();


        $query = "SELECT `id` FROM `tb_events`";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public function update_static_file($item_id)
    {
        $smart_dir = parent::smartDirctory('/_static/events/', $item_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'event-' . $item_id . '.inc.php');
        $eventArr = $this->get_item_staticArr($item_id);
        updateStaticFile($eventArr,
            $smart_dir . 'event-' . $item_id . '.inc.php',
            'eventArr');
    }

    /*----------------------------------------------------------------------------------*/

    public function get_item_staticArr($item_id)
    {
        $Db = Database::getInstance();
        $ts = time();

        $sql = "SELECT * FROM `tb_events` WHERE `id` = {$item_id}";
        $result = $Db->query($sql);
        $eventArr = $Db->get_stream($result);


        return $eventArr;
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