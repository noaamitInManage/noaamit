<?php

/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */
class parametersUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_parameters';
        $this->_ProcessID = 110;
        $this->className = trim(get_class());
        $this->file_name = 'parameters.inc.php';
        $this->itemsArr_name = 'parametersArr';
        $this->name = 'פרמטרים';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        global $languagesArr, $module_lang_id;

        include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/siteFunctions.inc.class.php');//siteFunctions

        //$smart_dir=parent::smartLangDirctory('/_static/menu/',$_REQUEST['inner_id'],$module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/parameters.inc.php');

        $parametersArr = self::getParametersArr();

        updateStaticFile($parametersArr,
            '/_static/parameters.inc.php',
            'parametersArr', '', false, false, true, true);

        if (memcached_is_on) {
            //branch_menu_65_he
            siteFunctions::save_to_memory('parameters', $parametersArr, memcached_default_time, array(__FILE__, __LINE__, __METHOD__, $_SERVER));
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function getParametersArr()
    {
        global $Db;

        $parametersArr = array();
        $query = "SELECT * FROM `tb_parameters`";
        $result = $Db->query($query);
        while ($row = $Db->get_stream($result)) {
            if ($row["index_name"]) {
                switch (configManager::$parameters_types_codesArr[$row["value_type"]]) {
                    case "int":
                        $row["value"] = intval($row["value"]) ? intval($row["value"]) : 0;
                        break;
                    case "float":
                        $row["value"] = floatval($row["value"]) ? floatval($row["value"]) : 0;
                        break;
                    case "boolean":
                        $row["value"] = ($row["value"] == 1) ? true : false;
                        break;
                    case "string":
                    default:
                        $row["value"] = (string)$row["value"] ? (string)$row["value"] : "";
                        break;
                }
                $parametersArr[$row["index_name"]] = $row["value"];
            }
        }
        return $parametersArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        global $Db;

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

//---------------------------------------------------------------------------//

?>