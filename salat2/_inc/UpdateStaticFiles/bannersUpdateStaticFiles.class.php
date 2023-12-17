<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */
//---------------------------------------------------------------------------//

class bannersUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_banners';
        $this->_ProcessID = 32;
        $this->className = trim(get_class());
        $this->file_name = 'banners.inc.php';
        $this->itemsArr_name = 'bannersArr';
        $this->name = 'באנרים';
    }

    /*----------------------------------------------------------------------------------*/


    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;


        $smart_dir = parent::smartLangDirctory('/_static/banners/', $_REQUEST['inner_id'], $module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/banners.' . $languagesArr[$module_lang_id]['title'] . '.inc.php');

        updateStaticFile("SELECT Main.id,Lang.title FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}

         	",
            '/_static/banners.' . $languagesArr[$module_lang_id]['title'] . '.inc.php',
            'bannersArr', 'id', true, true);


        if ($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'banner-' . $_REQUEST['inner_id'] . '.inc.php');

            updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
               						LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						)
               								  WHERE Main.id='{$_REQUEST['inner_id']}' AND Lang.lang_id='{$module_lang_id}'",
                $smart_dir . 'banner-' . $_REQUEST['inner_id'] . '.inc.php',
                'bannerArr');
        }

        $this->update_group_per_lang();


    }

    /*----------------------------------------------------------------------------------*/
    function update_group_per_lang()
    {
        $Db = Database::getInstance();

        global $languagesArr, $module_lang_id;
        $bannsersGroup = array();
        $query = "SELECT * FROM  `{$this->_Proccess_Main_DB_Table}_slots` WHERE `is_active`=1";
        $result = $Db->query($query);


        while ($row = $Db->get_stream($result)) {
            foreach ($languagesArr AS $key => $value) {
                $q = "SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
										LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
											Main.`id`=Lang.`obj_id`
										)
											  WHERE Main.slot_id='{$row['id']}'
													AND Lang.lang_id='{$key}'
				";

                $res = $Db->query($q);
                while ($item = $Db->get_stream($res)) {
                    $bannsersGroup[$row['id']]['items'][$item['id']] = $item;

                }
                updateStaticFile($bannsersGroup,
                    '/_static/bannersGroup.' . $value['title'] . '.inc.php',
                    'bannersGroupArr');
            }

        }

    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $this->updateStatics();

        parent::writeUpdate();
    }

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




?>