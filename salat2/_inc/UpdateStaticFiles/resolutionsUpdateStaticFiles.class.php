<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class resolutionsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_resolutions';
		$this->_ProcessID = 61;
		$this->className= trim(get_class());
		$this->file_name='resolutions.inc.php';
		$this->itemsArr_name='resolutionsArr';
		$this->name='רזוליוציות';
	}

	function updateStatics($id=''){
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;


        $smart_dir=parent::smartDirctory('/_static/resolutions/',$_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);

        updateStaticFile("SELECT id,title,code FROM {$this->_Proccess_Main_DB_Table} ",
            '/_static/'.$this->file_name,
            $this->itemsArr_name,'id',true);


        if($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'resolution-'.$_REQUEST['inner_id'].'.inc.php');
            updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
                $smart_dir.'resolution-'.$_REQUEST['inner_id'].'.inc.php',
                'resolutionArr');
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

?>