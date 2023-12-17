<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */
//---------------------------------------------------------------------------//

class gd_imagesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_gd_images';
        $this->_ProcessID = 93;
        $this->className = trim(get_class());
        $this->file_name = 'gd_images.inc.php';
        $this->itemsArr_name = 'gd_imagesArr';
        $this->name = 'תמונות GD';
    }

    function updateStatics($id = '')
    {
        global $resolutionsArr;
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];

        include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/siteFunctions.inc.class.php');//siteFunctions

        $smart_dir = parent::smartDirctory('/_static/gd_images/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);

        $imagesArr = self::getGdImagesStaticArray();

        updateStaticFile($imagesArr,
            '/_static/' . $this->file_name,
            $this->itemsArr_name, 'id', true);

        if (memcached_is_on) {
            //branch_menu_65_he
            siteFunctions::save_to_memory('gd_images', $imagesArr, memcached_default_time, array(__FILE__, __LINE__, __METHOD__, $_SERVER));
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function getGdImagesStaticArray()
    {
        global $Db, $resolutionsArr;
        include_once($_SERVER['DOCUMENT_ROOT'] . '/_static/resolutions.inc.php'); // $resolutionsArr
        $imagesArr = array();
        $query = $Db->query("SELECT `id`, `media_id`, `api_key`, `type`, `last_update`
                                    FROM `tb_gd_images`
                                        WHERE `active` = 1");
        while ($row = $Db->get_stream($query)) {
            $imagesArr[$row["api_key"]] = $row;

            if ($row["type"] == configManager::$gd_images_typesKeywordsArr["regular"]) {
                $Image = new mediaManager($row["media_id"]);
                $imagesArr[$row["api_key"]]["image_path"] = $Image->path;

            } elseif ($row["type"] == configManager::$gd_images_typesKeywordsArr["full_screen"]) {
                $dir_path = '/_media/gd_images/' . $row['id'] . '/';
                $imagesArr[$row["api_key"]]["imagesArr"] = array();
                $dir_filesArr = scandir($_SERVER['DOCUMENT_ROOT'] . $dir_path);
                $images_resolutionsArr = array_diff($dir_filesArr, array('.', '..'));
                foreach ($images_resolutionsArr as $image_resolution_path) {
                    $imageArr = explode("/", $image_resolution_path);
                    $image_name = end($imageArr);
                    $imageArr = explode(".", $image_name);
                    $resolution_id = $imageArr[0];
                    $imagesArr[$row["api_key"]]["imagesArr"][str_replace(" ", "_", $resolutionsArr[$resolution_id]["code"])] = $dir_path . $image_name . '?t=' . $row["last_update"];
                }
            }

            unset($imagesArr[$row["api_key"]]["media_id"]);
            unset($imagesArr[$row["api_key"]]["api_key"]);
            unset($imagesArr[$row["api_key"]]["last_update"]);
        }
        return $imagesArr;
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

?>