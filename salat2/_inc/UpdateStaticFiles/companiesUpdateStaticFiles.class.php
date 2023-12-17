<?php

class companiesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_companies';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'companies.inc.php';
        $this->itemsArr_name = 'companiesArr';
        $this->name = 'Companies';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $Db = Database::getInstance();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/companies/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/companies.inc.php');
        updateStaticFile("SELECT `id`, `name`, `salesforce_id` FROM `tb_companies` WHERE `active` = 1",
            '/_static/companies.inc.php',
            'companiesArr', 'id', true);

        if ($_REQUEST['inner_id']) {
            $this->update_static_file($_REQUEST['inner_id']);
        }
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();


        $query = " SELECT id FROM `tb_companies`";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public function update_static_file($item_id, $from_salesforce = false)
    {
        $smart_dir = parent::smartDirctory('/_static/companies/', $item_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'company-' . $item_id . '.inc.php');
        $companyArr = $this->get_item_staticArr($item_id, $from_salesforce);
        updateStaticFile($companyArr,
            $smart_dir . 'company-' . $item_id . '.inc.php',
            'companyArr');

        if (memcached_is_on) {
            siteFunctions::save_to_memory('company-' . $item_id, $companyArr);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function get_item_staticArr($item_id, $from_salesforce = false)
    {
        $Db = Database::getInstance();
        $ts = time();

        $sql = "SELECT * FROM `tb_companies` WHERE id={$item_id}";
        $result = $Db->query($sql);
        $companyArr = $Db->get_stream($result);

        $members_sql = "
            SELECT `id` FROM `tb_users` WHERE `company_id` = {$item_id} AND `active` = 1 AND `is_public` = 1
        ";
        $members_result = $Db->query($members_sql);
        $membersArr = array();
        while ($rowArr = $Db->get_stream($members_result)) {
            $User = User::get_user($rowArr['id']);
            $pictureArr = $User->get_user_picture();
            $membersArr[$User->id] = array(
                'id' => $User->id,
                'name' => $User->first_name . ' ' . $User->last_name,
                'picture' => $pictureArr['picture'],
                'is_base64_image' => $pictureArr['is_base64_image'],
                'job_title' => $User->get_job_title(),
            );
        }
        $membersArr = companiesManager::sort_members_list($membersArr);
        $companyArr['membersArr'] = $membersArr;

        $industries_sql = "
            SELECT `industry_id` FROM `tb_companies__industries` WHERE `company_id` = {$item_id}
        ";
        $industries_result = $Db->query($industries_sql);
        $industriesArr = array();
        while ($industryArr = $Db->get_stream($industries_result)) {
            $industriesArr[] = $industryArr['industry_id'];
        }
        $companyArr['industriesArr'] = $industriesArr;

        $rooms_sql = "
            SELECT `id`, `name`, `site_id`, `floor`, `room_number`, `end_time`
            FROM `tb_companies__rooms`
            WHERE `company_id` = {$item_id} AND `active` = 1 AND (`end_time` = 0 OR `end_time` > {$ts})
            ORDER BY `end_time` DESC
        ";
        $rooms_result = $Db->query($rooms_sql);
        $roomsArr = array();
        while ($roomArr = $Db->get_stream($rooms_result)) {
            $roomsArr[$roomArr['id']] = $roomArr;
        }
        $companyArr['roomsArr'] = $roomsArr;

        if (count($roomsArr) && $from_salesforce) {
            $company_roomArr = array_values($roomsArr)[0];
            $db_fieldsArr = array(
                'site_id' => $company_roomArr['site_id'],
                'floor' => $company_roomArr['floor'],
            );
            $Db->update('tb_companies', $db_fieldsArr, 'id', $item_id);

            $companyArr['site_id'] = $company_roomArr['site_id'];
            $companyArr['floor'] = $company_roomArr['floor'];
            $companyArr['room_number'] = $company_roomArr['room_number'];
        }

        return $companyArr;
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