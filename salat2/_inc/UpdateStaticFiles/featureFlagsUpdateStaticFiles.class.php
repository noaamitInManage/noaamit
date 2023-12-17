<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 *
 */

class featureFlagsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className,$file_name,$itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_feature_flags';
        $this->_ProcessID = 110;
        $this->className= trim(get_class());
        $this->file_name='feature_flags.inc.php';
        $this->itemsArr_name='featureFlagsArr';
        $this->name='Feature Flags';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id=''){
        $Db = Database::getInstance();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
        $smart_dir=parent::smartDirctory('/_static/feature_flags/',$_REQUEST['inner_id']);

        $featureFlagsArr = array();
        $query = "SELECT * FROM {$this->_Proccess_Main_DB_Table}";
        $result = $Db->query($query);
        while ($row = $Db->get_stream($result)) {
            $featureFlagsArr[$row['flagKey']] = intval($row['active']);
        }

        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/feature_flags.inc.php');
        updateStaticFile($featureFlagsArr,
            '/_static/feature_flags.inc.php',
            'featureFlagsArr','id',true);

        // Update the groups static file
        $flagFeaturesArr = array();
        $query = "
          SELECT Main.flagKey, Main.active, Main.group_id, FeaturesGroup.title
          FROM `tb_feature_flags` AS Main
          LEFT JOIN `tb_feature_flags_groups` AS FeaturesGroup ON Main.group_id = FeaturesGroup.id
        ";
        $result = $Db->query($query);
        while ($row = $Db->get_stream($result)) {
            $flagFeaturesArr[$row['group_id']]['title'] = $row['title'];
            $flagFeaturesArr[$row['group_id']]['features'][$row['flagKey']] = intval($row['active']);
        }

        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/feature_flags_groups.inc.php');
        updateStaticFile($flagFeaturesArr,
            '/_static/feature_flags_groups.inc.php',
            'featureFlagsGroupsArr','id',true);

        if($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'feature_flag-'.$_REQUEST['inner_id'].'.inc.php');
            updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir.'feature_flag-'.$_REQUEST['inner_id'].'.inc.php',
                'featureFlagArr');
        }

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
        include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name(){
        include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
        $tmp=$this->itemsArr_name;
        return count($$tmp);
    }


}

//---------------------------------------------------------------------------//

?>