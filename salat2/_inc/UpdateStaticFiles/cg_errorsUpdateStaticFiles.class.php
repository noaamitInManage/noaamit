<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */


//---------------------------------------------------------------------------//

class cg_errorsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_cg_errors';
		$this->_ProcessID = 80;
		$this->className= trim(get_class());
		$this->file_name='cg_errors.inc.php';
		$this->itemsArr_name='cg_errorsArr';
		$this->name='שגיאות';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		global $languagesArr;

		foreach ($languagesArr as $lang_id => $lang) {
			@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/cg_errors.'.$lang['title'].'.inc.php');
			updateStaticFile("SELECT error_code,content FROM `{$this->_Proccess_Main_DB_Table}` WHERE `lang_id`={$lang_id}",
				'/_static/cg_errors.'.$lang['title'].'.inc.php',
				'cg_errorsArr','error_code',true);
		}
	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
		$this->updateStatics();
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