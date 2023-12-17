<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */

class menuLangsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className,$file_name,$itemsArr_name;
    public static $tabbar_default_linksArr = array(
        2, 3, 11
    );

    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_side_menu';
        $this->_ProcessID = 101;
        $this->className= trim(get_class());
        $this->file_name='side_menu.inc.php';
        $this->itemsArr_name='side_menuArr';
        $this->name='תפריט';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id=''){
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
        global $languagesArr,$module_lang_id;

        include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/class/siteFunctions.inc.class.php');//siteFunctions

        $smart_dir=parent::smartLangDirctory('/_static/side_menu/',$_REQUEST['inner_id'],$module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/side_menu.'.$languagesArr[$module_lang_id]['title'].'.inc.php');

        $menuArr = self::getMenuArr();

        updateStaticFile($menuArr,
            '/_static/side_menu.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
            'side_menuArr', 'id', true, true);

        if(memcached_is_on){
            //branch_menu_65_he
            siteFunctions::save_to_memory('side_menu.'.$languagesArr[$module_lang_id]['title'],$menuArr,memcached_default_time,array(__FILE__,__LINE__,__METHOD__,$_SERVER));
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function getMenuArr(){
        global $module_lang_id;

        $Db = Database::getInstance();
        $menuArr = array();
        $query = "SELECT Main.id, Main.icon_id, Lang.title, Lang.link, Main.login_only, Main.is_website, Main.show_on_user_drop, Main.order_num FROM `tb_side_menu` AS Main
						         	LEFT JOIN `tb_side_menu_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}
                        ORDER BY Main.order_num
         	";
        $result = $Db->query($query);
        while($row = $Db->get_stream($result)){
            if($row["icon_id"]){
                $Icon = new mediaManager($row["icon_id"]);
                $row["icon"] = $Icon->path;
            } else {
                $row['icon'] = "";
            }

            unset($row["icon_id"]);
            $menuArr[$row['id']] = $row;
        }

        return $menuArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles(){
        $Db = Database::getInstance();

        $this->updateStatics();


        $query= " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
        $result=$Db->query($query);

        while($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public function getItemsNumber(){
        if(strstr($this->className,'Langs')){
            $file_nameArr=explode('.',$this->file_name);
            $file_nameArr[0]=$file_nameArr[0].'.'.default_lang;
            $this->file_name=implode('.',$file_nameArr);
            include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
        }else{
            include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
        }
        $tmp=$this->itemsArr_name;
        return count($$tmp);
    }


}

//---------------------------------------------------------------------------//

?>