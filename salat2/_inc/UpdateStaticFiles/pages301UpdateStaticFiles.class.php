<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 27/12/2016
 * Time: 15:18
 */

class pages301UpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_301';
		$this->_ProcessID = 122;
		$this->className= trim(get_class());
		$this->file_name='redirect_301.inc.php';
		$this->itemsArr_name='redirects301Arr';
		$this->name='301';
	}

	/*----------------------------------------------------------------------------------*/


	function updateStatics($id=''){
		$_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
		//$smart_dir=parent::smartLangDirctory('/_static/ads/',$_REQUEST['inner_id'], $module_lang_id);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/redirect_301.inc.php');
		updateStaticFile("SELECT `from_url`, `to_url` FROM `{$this->_Proccess_Main_DB_Table}`",
			'/_static/redirect_301.inc.php',
			'redirects301Arr', 'from_url', true, true);
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