<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */

class getMethodsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className,$file_name,$itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_get_methods';
        $this->_ProcessID = 110;
        $this->className= trim(get_class());
        $this->itemsArr_name='GET_methodsArr';
        $this->name='מתודות GET';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id=''){
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
        global $languagesArr,$module_lang_id;

        include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/class/siteFunctions.inc.class.php');//azrieliFunctions

        //$smart_dir=parent::smartLangDirctory('/_static/menu/',$_REQUEST['inner_id'],$module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/GET_methodsArr.inc.php');
        $GET_methodsArr = self::get_GET_methodsArr();
        updateStaticFile($GET_methodsArr,
            '/_static/GET_methodsArr.inc.php',
            $this->itemsArr_name, '', false, false, true, true);

        /**-----------------------------------------------------------------------------------------------------------------**/

        if (redis_is_on) {
            $CacheManager = new CacheManager('GET_methodsArr');
            $CacheManager->save_cache($GET_methodsArr, CacheType::REDIS);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_GET_methodsArr()
    {
        $GET_methodsArr = array();
        $Db = Database::getInstance();
        $query = "
                    SELECT * FROM `tb_get_methods` WHERE `api_name` = 'apiManager'
                ";
        $result = $Db->query($query);
        while($row = $Db->get_stream($result)){
            if ($row['active']) {
                $GET_methodsArr[] = $row["name"];
            }
        }
        return $GET_methodsArr;
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles(){
        $this->updateStatics();
        $Db = Database::getInstance();


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